
/*********************************************************************************************\
***********************************************************************************************
**                                                                                           **
**  SCRP - School Construction and Rehabilitation Programme                                  **
**  Version 1.0                                                                              **
**                                                                                           **
**  http://www.humdaqam.pk                                                                   **
**                                                                                           **
**  Copyright 2015 (C) Triple Tree Solutions                                                 **
**  http://www.3-tree.com                                                                    **
**                                                                                           **
**  ***************************************************************************************  **
**                                                                                           **
**  Project Manager:                                                                         **
**                                                                                           **
**      Name  :  Muhammad Tahir Shahzad                                                      **
**      Email :  mtshahzad@sw3solutions.com                                                  **
**      Phone :  +92 333 456 0482                                                            **
**      URL   :  http://www.mtshahzad.com                                                    **
**                                                                                           **
***********************************************************************************************
\*********************************************************************************************/

$(document).ready(function( )
{
	if ($("#frmRecord #BtnBack").length == 1)
		$("#frmRecord #BtnBack").button({ icons:{ primary:'ui-icon-closethick' } });
	
	
	$("#frmRecord #BtnBack").click(function( )
	{
		document.location = $(this).attr("rel");
		
		return false;
	});
	
	
	$("#ddBoqItem").change(function( )
	{
		var sUnit = $("#ddBoqItem :selected").attr("rel");

		if (sUnit == "cft")
		{
			$("#lblLength").html("Length");
			$("#txtLengthFeet").attr("placeholder", "Feet");
			$("#txtLengthInches").show( );

			$("#Width").show( );
			$("#Height").show( );
		}

		else if (sUnit == "sft")
		{
			$("#lblLength").html("Length");
			$("#txtLengthFeet").attr("placeholder", "Feet");
			$("#txtLengthInches").show( );

			$("#Width").show( );
			$("#Height").hide( );

			$("#Height input").val("");
		}

		else if (sUnit == "rft")
		{
			$("#lblLength").html("Length");
			$("#txtLengthFeet").attr("placeholder", "Feet");
			$("#txtLengthInches").show( );

			$("#Width").hide( );
			$("#Height").hide( );

			$("#Width input").val("");
			$("#Height input").val("");
		}

		else
		{
			$("#lblLength").html(sUnit.toUpperCase( ));
			$("#txtLengthFeet").attr("placeholder", "");
			$("#txtLengthInches").val("").hide( );

			$("#Width").hide( );
			$("#Height").hide( );

			$("#Width input").val("");
			$("#Height input").val("");
		}
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

                var boqItem = $("#ddBoqItem").val( );
                
                if(!boqItem.length){
                    if (!objFV.validate("ddBoqItem", "B", "Please select the BOQ Item."))
                            return false;
                }
                
		if (!objFV.validate("txtTitle", "B", "Please enter the Measurement Title."))
			return false;

                if(boqItem.length){        
                    var sUnit = $("#ddBoqItem").attr("rel");
                }else{
                    var sUnit = $("#ddBoqItem :selected").attr("rel");
                }
                
		if (sUnit == "cft" || sUnit == "sft" || sUnit == "rft")
		{
			if (!objFV.validate("txtLengthFeet", "B,N", "Please enter a valid Length (Feet)."))
				return false;

			if (!objFV.validate("txtLengthInches", "N", "Please enter a valid Length (Inches)."))
				return false;


			var iInches = parseInt($("#txtLengthInches").val( ));

			if (iInches > 11)
			{
				showMessage("#RecordMsg", "alert", "Please enter a valid value for Length (Inches).");

				$("#txtLengthInches").focus( );
			}
		}

		else if (sUnit == "sft")
		{
			if (!objFV.validate("txtLengthFeet", "B,F", "Please enter a valid Measurement."))
				return false;
		}


		if (sUnit == "cft" || sUnit == "sft")
		{
			if (!objFV.validate("txtWidthFeet", "B,N", "Please enter a valid Width (Feet)."))
				return false;

			if (!objFV.validate("txtWidthInches", "N", "Please enter a valid Width (Inches)."))
				return false;


			var iInches = parseInt($("#txtWidthInches").val( ));

			if (iInches > 11)
			{
				showMessage("#RecordMsg", "alert", "Please enter a valid value for Width (Inches).");

				$("#txtWidthInches").focus( );
			}
		}


		if (sUnit == "cft")
		{
			if (!objFV.validate("txtHeightFeet", "B,N", "Please enter a valid Height (Feet)."))
				return false;

			if (!objFV.validate("txtHeightInches", "N", "Please enter a valid Height (Inches)."))
				return false;


			var iInches = parseInt($("#txtHeightInches").val( ));

			if (iInches > 11)
			{
				showMessage("#RecordMsg", "alert", "Please enter a valid value for Height (Inches).");

				$("#txtHeightInches").focus( );
			}
		}


		if (!objFV.validate("txtMultiplier", "B,F", "Please enter a valid Multiplier of Measurements."))
			return false;



		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});