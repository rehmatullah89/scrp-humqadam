
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
	$("#txtStartDate, #txtEndDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});


    $("#txtSchools").tokenInput("ajax/get-schools-list.php",
    {
		queryParam         :  "School",
		minChars           :  2,
		tokenLimit         :  200,
		hintText           :  "Search the School (EMIS Code)",
		noResultsText      :  "No matching School found",
		theme              :  "facebook",
		preventDuplicates  :  true,
		prePopulate        :  eval($("#Schools").html( )),
		onAdd              :  function( ) { $("html, body").animate( { scrollTop:$(document).height( ) }, 'slow'); }
    });


	$("#txtTitle").blur(function( )
	{
		if ($(this).val( ) == "")
			return;


		$.post("ajax/tracking/check-contract.php",
			{ ContractId:$("#ContractId").val( ), Title:$(this).val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Contract Title is already used. Please specify another Title.");

					$("#DuplicateContract").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateContract").val("0");
				}
			},

			"text");
	});



	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the Title."))
			return false;

		if (!objFV.validate("ddContractor", "B", "Please select the Contractor."))
			return false;

		if (!objFV.validate("txtStartDate", "B", "Please enter the Start Date."))
			return false;

		if (!objFV.validate("txtEndDate", "B", "Please enter the End Date."))
			return false;

		if (!objFV.validate("txtSchools", "B", "Please select Schools included in this Contract."))
			return false;


		if (objFV.value("DuplicateContract") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Contract Title is already used. Please specify another Title.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});