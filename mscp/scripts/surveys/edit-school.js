
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

	
	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;


		$.post("ajax/surveys/check-school.php",
			{ SchoolId:$("#SchoolId").val( ), Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The School EMIS Code is already used. Please specify another Code.");

					$("#DuplicateSchool").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateSchool").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the School Name."))
			return false;

		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;

		if (!objFV.validate("ddType", "B", "Please select the School Type."))
			return false;

		if (!objFV.validate("txtStudents", "B,N", "Please enter the No of Students."))
			return false;

		if (!objFV.validate("txtCost", "F", "Please enter the Estimated Cost."))
			return false;

		if (!objFV.validate("ddDistrict", "B", "Please select the School District."))
			return false;

		if (!objFV.validate("txtAddress", "B", "Please enter the Address."))
			return false;

		if (!objFV.validate("txtLatitude", "B", "Please enter the Map Coordinates (Latitude)."))
			return false;

		if (!objFV.validate("txtLongitude", "B", "Please enter the Map Coordinates (Longitude)."))
			return false;

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



		if (!objFV.validate("txtCoveredArea1", "F", "Please enter the Covered Area (SFT)."))
			return false;
	
		if (!objFV.validate("txtClassRooms1", "N", "Please enter the No of Classrooms."))
			return false;

		if (!objFV.validate("txtStudentToilets1", "N", "Please enter the No of Student Toilets."))
			return false;

		if (!objFV.validate("txtStaffRooms1", "N", "Please enter the No of Staff Rooms."))
			return false;

		if (!objFV.validate("txtStaffToilets1", "N", "Please enter the No of Satff Toilets."))
			return false;

		if (!objFV.validate("txtScienceLabs1", "N", "Please enter the No of Science Labs."))
			return false;

		if (!objFV.validate("txtItLabs1", "N", "Please enter the No of IT Labs."))
			return false;

		if (!objFV.validate("txtExamHalls1", "N", "Please enter the No of Exam Halls."))
			return false;

		if (!objFV.validate("txtLibrary1", "N", "Please enter the No of Libraries."))
			return false;

		if (!objFV.validate("txtClerkOffices1", "N", "Please enter the No of Clerk Offices."))
			return false;

		if (!objFV.validate("txtPrincipalOffice1", "N", "Please enter the No of Principal Office."))
			return false;

		if (!objFV.validate("txtParkingStand1", "N", "Please enter the No of Parking Stands."))
			return false;

		if (!objFV.validate("txtChowkidarHut1", "N", "Please enter the No of Chowkidar Hut."))
			return false;

		if (!objFV.validate("txtSoakagePit1", "N", "Please enter the No of Soakage Pit."))
			return false;

		if (!objFV.validate("txtWaterSupply1", "N", "Please enter the No of Water Supply."))
			return false;

		

		if (objFV.value("DuplicateSchool") == "1")
		{
			showMessage("#RecordMsg", "info", "The School EMIS Code is already used. Please specify another Code.");

			objFV.focus("txtCode");
			objFV.select("txtCode");

			return false;
		}

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});