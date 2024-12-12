
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
    
        $("#txtDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});

        $("#txtCode").autocomplete({ source:"ajax/get-emis-codes-list.php", minLength:3 });
	
	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;


		$.post("ajax/tracking/check-document.php",
			{ Code:$("#txtCode").val( ), DocType:$("#ddDocType option:selected").val( ) },

			function (sResponse)
			{
				if (sResponse == "INVALID")
				{
					showMessage("#RecordMsg", "info", "The provided EMIS Code is Invalid, No school found in the system.");

					$("#DuplicateDocument").val("0");
				}
				
				else if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "This School Document already Exists. Please specify another EMIS Code.");

					$("#DuplicateDocument").val("1");
				}				

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateDocument").val("0");
				}
			},

			"text");
	});

	var sUploadScript = new String(document.location);

	sUploadScript = sUploadScript.replace("edit-document.php", "upload-documents.php");


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


		if (!objFV.validate("ddDocType", "B", "Please select the Document Type."))
			return false;

		if (!objFV.validate("txtCode", "B", "Please enter the School Code."))
			return false;

		if (objFV.value("DuplicateDocument") == "1")
		{
			showMessage("#RecordMsg", "info", "The Document for selected School and doc type already exists in System.");

			objFV.focus("ddDocType");
			objFV.select("ddDocType");

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