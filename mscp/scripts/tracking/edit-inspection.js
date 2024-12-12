
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
	$("#txtDate, #txtReInspection").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});


	$("#txtTitle, #ddDistrict, #ddSchool, #ddStage").blur(function( )
	{
		if ($("#txtTitle").val( ) == "" || $("#ddDistrict").val( ) == "" || $("#ddSchool").val( ) == "" || $("#ddStage").val( ) == "")
			return;


		$.post("ajax/tracking/check-inspection.php",
			{ InspectionId:$("#InspectionId").val( ), Title:$("#txtTitle").val( ), District:$("#ddDistrict").val( ), School:$("#ddSchool").val( ), Stage:$("#ddStage").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The Inspection Title/School/Stage is already used. Please specify another Title/School/Stage.");

					$("#DuplicateInspection").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateInspection").val("0");
				}
			},

			"text");
	});


	$("#ddDistrict").change(function( )
	{
		$.post("ajax/get-district-schools.php",
			{ District:$("#ddDistrict").val( ) },

			function (sResponse)
			{
				$("#ddSchool").html(sResponse);
			},

			"text");
	});


	$("#ddSchool").change(function( )
	{
		if ($("#ddSchool").val( ) == "")
		{
			$("#ddStage").html("");
			
			return false;
		}

		
		$.post("ajax/tracking/get-inspection-stages.php",
			{ School:$("#ddSchool").val( ) },

			function (sResponse)
			{
				$("#ddStage").html(sResponse);
			},

			"text");
	});	

	
	$("#ddStage").change(function( )
	{
		$.post("ajax/tracking/get-stage-reasons.php",
			{ Stage:$("#ddStage").val( ) },

			function (sResponse)
			{
				$("#ddReason").html(sResponse);
			},

			"text");
	});


	$("#ddStatus").change(function( )
	{
		if ($(this).val( ) == "P" && $("#Passed").css("display") != "block")
				$("#Passed").show("blind");

		else if ($("#Passed").css("display") == "block")
		{
			$("#Passed").hide("blind");
			$("#ddCompleted").val("N");
		}


		if ($(this).val( ) == "F" && $("#Failed").css("display") != "block")
				$("#Failed").show("blind");

		else if ($("#Failed").css("display") == "block")
		{
			$("#Failed").hide("blind");

			if ($("#Comments").css("display") == "block")
				$("#Comments").hide("blind");

			$("#txtComments").val("");
		}


		if ($(this).val( ) == "R" && $("#ReInspection").css("display") != "block")
				$("#ReInspection").show("blind");

		else if ($("#ReInspection").css("display") == "block")
		{
			$("#ReInspection").hide("blind");
			$("#txtReInspection").val("");
		}
	});


	$("#ddReason").change(function( )
	{
		if ($(this).val( ) == "5" && $("#Comments").css("display") != "block")
				$("#Comments").show("blind");

		else if ($("#Comments").css("display") == "block")
		{
			$("#Comments").hide("blind");
			$("#Comments").hide("blind");

			$("#txtComments").val("");
		}
	});



	var sUploadScript = new String(document.location);

	sUploadScript = sUploadScript.replace("edit-inspection.php", "upload-inspection-document.php");


	$("#Files").plupload(
	{
		container           : "Files",
		runtimes            : "html5,flash,silverlight,html4",
		url                 : sUploadScript,
		chunk_size          : '1mb',
		unique_names        : false,
		rename              : true,
		sortable            : true,
		dragdrop            : true,
		filters             : { prevent_duplicates:true, max_file_size:'10mb', mime_types:[{ title:"Audit files", extensions:"jpg,jpeg,gif,png,zip,doc,docx,pdf,xls,xslx,ppt" }] },
		views               : { list:true, thumbs:true, active:'thumbs' },
		flash_swf_url       : "plugins/plupload/Moxie.swf",
		silverlight_xap_url : "plugins/plupload/Moxie.xap"
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddUser", "B", "Please select the Inspector."))
			return false;

		if (!objFV.validate("ddDistrict", "B", "Please select the District."))
			return false;

		if (!objFV.validate("ddSchool", "B", "Please select the School."))
			return false;

		if (!objFV.validate("ddStage", "B", "Please select the Stage."))
			return false;

		if (!objFV.validate("txtTitle", "B", "Please enter the Inspection Title."))
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


		if (!objFV.validate("txtDate", "B", "Please select the Date."))
			return false;
		
		if (!objFV.validate("ddStatus", "B", "Please select the Status."))
			return false;		

		if (objFV.value("ddStatus") == "F")
		{
			if (!objFV.validate("ddReason", "B", "Please select the Failure Reason."))
				return false;


			if (objFV.value("ddReason") == "5")
			{
				if (!objFV.validate("txtComments", "B", "Please enter the Failure Reason."))
					return false;
			}
		}

		else if (objFV.value("ddStatus") == "R")
		{
			if (!objFV.validate("txtReInspection", "B", "Please select the Re-Inspection Date."))
				return false;
		}


		if (objFV.value("DuplicateInspection") == "1")
		{
			showMessage("#RecordMsg", "info", "The Inspection Title/School/Stage is already used. Please specify another Title/School/Stage.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		var objPlUpload = $("#Files").plupload("getUploader");

		if (objPlUpload.files.length > 0)
		{
			if (objPlUpload.files.length == (objPlUpload.total.uploaded + objPlUpload.total.failed))
			{
				$("#BtnSave").attr('disabled', true);
				$("#RecordMsg").hide( );

				return true;
			}

			else
			{
				objPlUpload.start( );

				objPlUpload.bind('UploadComplete', function( )
				{
					$("#BtnSave").attr('disabled', true);
					$("#RecordMsg").hide( );


					$("#frmRecord")[0].submit( );
				});


				return false;
			}
		}

		else
		{
			$("#BtnSave").attr('disabled', true);
			$("#RecordMsg").hide( );

			return true;
		}
	});
});