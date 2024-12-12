
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
	$("#txtCompany").blur(function( )
	{
		if ($("#txtCompany").val( ) == "")
			return;


		$.post("ajax/tracking/check-contractor.php",
			{ ContractorId:$("#ContractorId").val( ), Company:$("#txtCompany").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided Company Name is already in use. Please provide another Name.");

					$("#DuplicateContractor").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateContractor").val("0");
				}
			},

			"text");
	});




	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtCompany", "B", "Please enter the Company Name."))
			return false;

//		if (!objFV.validate("txtAddress", "B", "Please enter the Address."))
//			return false;

		if (!objFV.validate("txtCity", "B", "Please enter the City Name."))
			return false;

		if (objFV.value("fileLogo") != "")
		{
			if (!checkImage(objFV.value("fileLogo")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("fileLogo");
				objFV.select("fileLogo");

				return false;
			}
		}


		if (!objFV.validate("txtFirstName", "B", "Please enter the First Name."))
			return false;

		if (!objFV.validate("txtLastName", "B", "Please enter the Last Name."))
			return false;

		if (!objFV.validate("txtPhone", "B", "Please enter the Phone Number."))
			return false;

//		if (!objFV.validate("txtMobile", "B", "Please enter the Mobile Number."))
//			return false;

		if (!objFV.validate("txtEmail", "E", "Please enter a valid Email Address."))
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


		if (objFV.value("DuplicateContractor") == "1")
		{
			showMessage("#RecordMsg", "info", "The provided Company Name is already in use. Please provide another Name.");

			objFV.focus("txtCompany");
			objFV.select("txtCompany");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});