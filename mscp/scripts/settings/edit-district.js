
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
	$("#txtDescription").ckeditor({ height:"300px" }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });


	$("#txtName, #ddProvince").change(function( )
	{
		var sUrl = $("#txtName").val( );

		sUrl = sUrl.getSefUrl( );

		$("#txtSefUrl").val(sUrl);

		if (sUrl != "")
		{
			if (parseInt($("#ddProvince").val( )) > 0)
				sUrl = ($("#ddProvince :selected").attr("sefUrl") + sUrl);

			$("#Url").val(sUrl);
			$("#SefUrl").html("/" + sUrl);
		}
	});


	$("#txtName, #ddProvince, #txtSefUrl").blur(function( )
	{
		var sUrl = $("#txtSefUrl").val( );

		if (sUrl == "")
			return;


		sUrl = sUrl.getSefUrl( );

		$("#txtSefUrl").val(sUrl);


		if (parseInt($("#ddProvince").val( )) > 0)
			sUrl = ($("#ddProvince :selected").attr("sefUrl") + sUrl);

		$("#Url").val(sUrl);
		$("#SefUrl").html("/" + sUrl);


		$.post("ajax/settings/check-district.php",
			{ DistrictId:$("#DistrictId").val( ), SefUrl:sUrl },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The District SEF URL is already used. Please specify another URL.");

					$("#DuplicateDistrict").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateDistrict").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the District Name."))
			return false;

		if (!objFV.validate("ddProvince", "B", "Please select the District Province."))
			return false;

		if (!objFV.validate("txtSefUrl", "B", "Please enter the SEF URL."))
			return false;

		if (!objFV.validate("txtLatitude", "B", "Please enter the Map Coordinates (Latitude)."))
			return false;

		if (!objFV.validate("txtLongitude", "B", "Please enter the Map Coordinates (Longitude)."))
			return false;

		if (objFV.value("filePicture") != "")
		{
			if (!checkImage(objFV.value("filePicture")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("filePicture");
				objFV.select("filePicture");

				return false;
			}
		}

		if (objFV.value("DuplicateDistrict") == "1")
		{
			showMessage("#RecordMsg", "info", "The District SEF URL is already used. Please specify another URL.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});