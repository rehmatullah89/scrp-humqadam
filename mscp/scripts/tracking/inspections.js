
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

var objTable;

$(document).ready(function( )
{
	$("#BtnExport").button({ icons:{ primary:'ui-icon-disk' } });
        $("#BtnInspecExport").button({ icons:{ primary:'ui-icon-disk' } });
	
        $("#BtnExport").click(function( )
	{
		document.location = $(this).attr("rel");
	});

        $("#BtnInspecExport").click(function( )
	{
		document.location = ($(this).attr("rel") + "?Province=" + $("#ddProvince").val( ) + "&District=" + $("#ddDistrict2").val( ) + "&FromDate=" + $("#txtFromDate").val( ) + "&ToDate=" + $("#txtToDate").val( ));
	});
	
	$("#txtDate, #txtReInspection, #txtFromDate, #txtToDate").datepicker(
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
			{ Title:$("#txtTitle").val( ), District:$("#ddDistrict").val( ), School:$("#ddSchool").val( ), Stage:$("#ddStage").val( ) },

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



	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#ddUser").focus( );

		objPlUpload.splice( );
		$("#Pictures_filelist").html("");
		objPlUpload.refresh( );

		return false;
	});



	var sUploadScript = new String(document.location);

	sUploadScript = sUploadScript.replace("inspections.php", "upload-inspection-document.php");


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


	var objPlUpload = $("#Files").plupload("getUploader");


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
/*
		if (!objFV.validate("filePicture", "B", "Please select the Picture."))
			return false;
*/
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
/*
		if (!objFV.validate("fileDocument", "B", "Please select the Inspection."))
			return false;
*/

		if (!objFV.validate("txtDate", "B", "Please select the Date."))
			return false;

		if (objFV.value("ddStatus") == "F")
		{
			if (!objFV.validate("ddReason", "B", "Please select the Failure Reason."))
				return false;
		}

		else if (objFV.value("ddStatus") == "R")
		{
			if (!objFV.validate("txtReInspection", "B", "Please select the Re-Inspection Date."))
				return false;


			if (objFV.value("ddReason") == "5")
			{
				if (!objFV.validate("txtComments", "B", "Please enter the Failure Reason."))
					return false;
			}
		}


		if (objFV.value("DuplicateInspection") == "1")
		{
			showMessage("#RecordMsg", "info", "The Inspection Title/School/Stage is already used. Please specify another Title/School/Stage.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		if (objPlUpload.files.length > 0)
		{
			if (objPlUpload.files.length == (objPlUpload.total.uploaded + objPlUpload.total.failed))
			{
				$("#BtnSave").attr('disabled', true);
				$("#RecordMsg").hide( );

				$("#frmRecord")[0].submit( );
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
			return true;
	});







	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   aoColumnDefs    : [ { bSortable:false, aTargets:[7] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   bJQueryUI       : true,
											   sPaginationType : "full_numbers",
											   bPaginate       : true,
											   bLengthChange   : false,
											   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
											   bFilter         : true,
											   bSort           : true,
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,
											   bServerSide     : true,
											   sAjaxSource     : "ajax/tracking/get-inspections.php",

											 fnDrawCallback  : function( )
															   {
																   $(".details").tipTip( );
															   },

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #District").length > 0)
																		aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });

																	if ($("div.toolbar #Type").length > 0)
																		aoData.push({ name:"Type", value:$("div.toolbar #Type").val( ) });

																	if ($("div.toolbar #Stage").length > 0)
																		aoData.push({ name:"Stage", value:$("div.toolbar #Stage").val( ) });

																	if ($("div.toolbar #Status").length > 0)
																		aoData.push({ name:"Status", value:$("div.toolbar #Status").val( ) });

																	if ($("div.toolbar #Completed").length > 0)
																		aoData.push({ name:"Completed", value:$("div.toolbar #Completed").val( ) });


																	$.getJSON(sSource, aoData, function(jsonData)
																	{
																		fnCallback(jsonData);


																		$("#DataGrid tbody tr").each(function(iIndex)
																		{
																			$(this).attr("id", $(this).find("img:first-child").attr("id"));
																			$(this).find("td:first-child").addClass("position");
																		});
																	});
																 },

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/tracking/get-inspection-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict  = 0;
																	var iStage     = 0;
																	var iStatus    = 0;
																	var iCompleted = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;

																		if ($(this).text( ) == "Stage")
																			iStage = iIndex;

																		if ($(this).text( ) == "Status")
																			iStatus = iIndex;

																		if ($(this).text( ) == "Completed")
																			iCompleted = iIndex;
																	});


																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iStage);
																	this.fnFilter("", iStatus);
																	this.fnFilter("", iCompleted);


																	if ($("#SelectButtons").length == 1)
																	{
																		if (this.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
																			$("#SelectButtons").show( );

																		else
																			$("#SelectButtons").hide( );
																	}
																 }
										   } );
	}

	else
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   aoColumnDefs    : [ { bSortable:false, aTargets:[7] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   bJQueryUI       : true,
											   sPaginationType : "full_numbers",
											   bPaginate       : true,
											   bLengthChange   : false,
											   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
											   bFilter         : true,
											   bSort           : true,
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,

												 fnDrawCallback  : function( )
																   {
																	   $(".details").tipTip( );
																   },

											   fnInitComplete  : function( )
													 {
														$.post("ajax/tracking/get-inspection-filters.php",
															   {},

															   function (sResponse)
															   {
																$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iDistrict  = 0;
														var iStage     = 0;
														var iStatus    = 0;
														var iCompleted = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "District")
																iDistrict = iIndex;

															if ($(this).text( ) == "Stage")
																iStage = iIndex;

															if ($(this).text( ) == "Status")
																iStatus = iIndex;

															if ($(this).text( ) == "Completed")
																iCompleted = iIndex;
														});


														this.fnFilter("", iDistrict);
														this.fnFilter("", iStage);
														this.fnFilter("", iStatus);
														this.fnFilter("", iCompleted);
													 }
											  } );
	}


	$("#BtnSelectAll").click(function( )
	{
		var iDistrict  = 0;
		var iStage     = 0;
		var iStatus    = 0;
		var iCompleted = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;

			if ($(this).text( ) == "Stage")
				iStage = iIndex;

			if ($(this).text( ) == "Status")
				iStatus = iIndex;

			if ($(this).text( ) == "Completed")
				iCompleted = iIndex;
		});


		var objRows    = objTable.fnGetNodes( );
		var bSelected  = false;
		var sDistrict  = "";
		var sStage     = "";
		var sStatus    = "";
		var sCompleted = "";

		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );

		if ($("div.toolbar #Stage").length > 0)
			sStage = $("div.toolbar #Stage").val( );

		if ($("div.toolbar #Status").length > 0)
			sStatus = $("div.toolbar #Status").val( );

		if ($("div.toolbar #Completed").length > 0)
			sCompleted = $("div.toolbar #Completed").val( );


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ((sDistrict == "" || sDistrict == objTable.fnGetData(objRows[i])[iDistrict]) &&
				    (sStage == "" || sStage == objTable.fnGetData(objRows[i])[iStage]) &&
				    (sStatus == "" || sStatus == objTable.fnGetData(objRows[i])[iStatus]) &&
				    (sCompleted == "" || sCompleted == objTable.fnGetData(objRows[i])[iCompleted]))
				{
					if (!$(objRows[i]).hasClass("selected"))
					{
						$(objRows[i]).addClass("selected");

						bSelected = true;
					}
				}

				else
					$(objRows[i]).removeClass("selected");
			}
		}

		else
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if (!$(objRows[i]).hasClass("selected"))
				{
					$(objRows[i]).addClass("selected");

					bSelected = true;
				}
			}
		}

		if (bSelected == true)
			$("#BtnMultiDelete").show( );
	});


	$("#BtnSelectNone").click(function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );
	});


	$(document).on("change", "div.toolbar #District", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});


	$(document).on("change", "div.toolbar #Type", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );

		
		var sDistrict  = "";
		var sStatus    = "";
		var sCompleted = "";
		
		if ($("div.toolbar #District").length == 1)
			sDistrict = $("div.toolbar #District").val( );
		
		if ($("div.toolbar #Status").length == 1)
			sStatus = $("div.toolbar #Status").val( );
		
		if ($("div.toolbar #Completed").length == 1)
			sCompleted = $("div.toolbar #Completed").val( );		
		
		$("div.toolbar #Stage").val("");

		
		$.post("ajax/tracking/get-inspection-filters.php",
			   { Type:$(this).val( ) },

			   function (sResponse)
			   {
					$("div.toolbar").html(sResponse);
					
					$("div.toolbar #District").val(sDistrict);
					$("div.toolbar #Status").val(sStatus);
					$("div.toolbar #Completed").val(sCompleted);
			   },

			   "text");


		objTable.fnFilter($(this).val( ), 0);
	});


	$(document).on("change", "div.toolbar #Stage", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Stage")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});


	$(document).on("change", "div.toolbar #Status", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Status")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});


	$(document).on("change", "div.toolbar #Completed", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		objTable.fnFilter($(this).val( ), 0);
	});


	$(document).on("click", "#DataGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnMultiDelete").show( );

		else
			$("#BtnMultiDelete").hide( );
	});


	$(".TableTools").prepend('<button id="BtnMultiDelete">Delete Selected Rows</button>')
	$("#BtnMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnMultiDelete").hide( );


	$("#BtnMultiDelete").click(function( )
	{
		var sInspections    = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sInspections != "")
					sInspections += ",";

				sInspections += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sInspections != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
															{
															 $.post("ajax/tracking/delete-inspection.php",
																{ Inspections:sInspections },

																function (sResponse)
																{
																	var sParams = sResponse.split("|-|");

																	showMessage("#GridMsg", sParams[0], sParams[1]);

																	if (sParams[0] == "success")
																	{
																		 for (var i = 0; i < objSelectedRows.length; i ++)
																		  objTable.fnDeleteRow(objSelectedRows[i]);

																		  $("#BtnMultiDelete").hide( );


																	  if ($("#SelectButtons").length == 1)
																	  {
																		if (objTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
																			$("#SelectButtons").show( );

																		else
																			$("#SelectButtons").hide( );
																	  }
																	}
																},

																"text");

																$(this).dialog("close");
															},

													  Cancel  : function( )
															{
																$(this).dialog("close");
															}
												   }
											 });
		}
	});


	$(document).on("click", ".icnEdit", function(event)
	{
		var iInspectionId = this.id;
		var iIndex        = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-inspection.php?InspectionId=" + iInspectionId + "&Index=" + iIndex), width:"90%", height:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnMeasurements", function(event)
	{
		var iInspectionId = this.id;
		var iIndex        = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-inspection-measurements.php?InspectionId=" + iInspectionId), width:"90%", height:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iInspectionId = this.id;

		$.colorbox({ href:("tracking/view-inspection.php?InspectionId=" + iInspectionId), width:"90%", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iInspectionId = this.id;
		var objRow        = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/tracking/delete-inspection.php",
																		{ Inspections:iInspectionId },

																		function (sResponse)
																		{
																			var sParams = sResponse.split("|-|");

																			showMessage("#GridMsg", sParams[0], sParams[1]);

																			if (sParams[0] == "success")
																				objTable.fnDeleteRow(objRow);


																			if ($("#SelectButtons").length == 1)
																			{
																				if (objTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
																					$("#SelectButtons").show( );

																				else
																					$("#SelectButtons").hide( );
																			}
																		},

																		"text");

		                                                      	    	$(this).dialog("close");
		                                                       },

		                                             Cancel  : function( )
		                                                       {
		                                                            	$(this).dialog("close");
		                                                       }
		                                          }
		                            });

		event.stopPropagation( );
	});
});


function updateRecord(iInspectionId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Title")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Date")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Stage")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Completed")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
		});


		$(".details").tipTip( );
	}

	else
		objTable.fnStandingRedraw( );
}