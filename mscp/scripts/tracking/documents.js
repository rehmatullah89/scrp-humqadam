
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



	$("#frmRecord #BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
                $("#txtCode").val('');
                $("#txtComments").val('');
		$("#RecordMsg").hide( );
		$("#frmRecord #ddDocType").focus( );

		return false;
	});

        var sUploadScript = new String(document.location);

	sUploadScript = sUploadScript.replace("documents.php", "upload-documents.php");


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


		if (!objFV.validate("ddDocType", "B", "Please select the Document Type."))
			return false;

		if (!objFV.validate("txtCode", "B", "Please enter the School Code."))
			return false;

                if (!objFV.validate("txtDate", "B", "Please select the Date."))
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

		if (objFV.value("DuplicateDocument") == "1")
		{
			showMessage("#RecordMsg", "info", "The Document for selected School already exists in System.");

			objFV.focus("ddDocType");
			objFV.select("ddDocType");

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

		else{

                        if (objFV.value("filePicture") == "" && objFV.value("fileDocument") == "")
                        {
                            showMessage("#RecordMsg", "error", "Please, Select at least one file to save.");
                            return false;
                        }   
                        
                        return true;
                }

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
											   sAjaxSource     : "ajax/tracking/get-documents.php",
                                                                                           
                                                                                           fnDrawCallback  : function( )
															   {
																   $(".details").tipTip( );
															   },
                                                                                                                           
											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
                                                                                                                    if ($("div.toolbar #District").length > 0)
                                                                                                                            aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });
                                                                                                                    if ($("div.toolbar #DocType").length > 0)
                                                                                                                            aoData.push({ name:"DocType", value:$("div.toolbar #DocType").val( ) });
                                                                                                                                                                    

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
														$.post("ajax/tracking/get-document-filters.php",
															   {},

															   function (sResponse)
															   {
																	$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iDistrict   = 0;
														var iDocType    = 0;
	
                                                                                                                $("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "District")
																iDistrict = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "DocType")
																iDocType = iIndex;
                                                                                                                        
														});

                                                                                                                this.fnFilter("", iDistrict);
                                                                                                                this.fnFilter("", iDocType);
														
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
																	$.post("ajax/tracking/get-document-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict   = 0;
                                                                                                                                        var iDocType   = 0;
                                                                                                                                                
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
                                                                                                                                                    
                                                                                                                                                if ($(this).text( ) == "DocType")
																			iDocType = iIndex;
                                                                                                                                                
																	});

                                                                                                                                        this.fnFilter("", iDistrict);
                                                                                                                                        this.fnFilter("", iDocType);
																 }
													   } );
	}


	$("#BtnSelectAll").click(function( )
	{
                var iDistrict   = 0;
                var iDocType    = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
                            
                        if ($(this).text( ) == "DocType")
				iDocType = iIndex;
		});



		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sDistrict   = "";
                var sDocType    = "";
                
                if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
		
                if ($("div.toolbar #DocType").length > 0)
			sDocType = $("div.toolbar #DocType").val( );
                    
                
		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ((sDocType == "" || objTable.fnGetData(objRows[i])[iDocType] == sDocType) &&
                                        (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict))
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
        
        $(document).on("change", "div.toolbar #DocType", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Document Type")
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


	$(document).on("click", "#BtnMultiDelete", function( )
	{
		var sDocuments      = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sDocuments != "")
					sDocuments += ",";

				sDocuments += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sDocuments != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
							       width     : 420,
							       height    : 110,
							       modal     : true,
							       buttons   : { "Delete" : function( )
										        {
											     $.post("ajax/tracking/delete-document.php",
												    { Documents:sDocuments },

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


		return false;
	});
	
	
	
	$(document).on("click", "#DataGrid .icnEdit", function(event)
	{
		var iDocumentId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-document.php?DocumentId=" + iDocumentId + "&Index=" + iIndex), width:"90%", height:"70%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iDocumentId = this.id;

		$.colorbox({ href:("tracking/view-document.php?DocumentId=" + iDocumentId), width:"90%", height:"70%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});



	$(document).on("click", "#DataGrid .icnDelete", function(event)
	{
		var iDocumentId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
									  width     : 420,
									  height    : 110,
									  modal     : true,
									  buttons   : { "Delete" : function( )
															   {
																	$.post("ajax/tracking/delete-document.php",
																		{ Documents:iDocumentId },

																		function (sResponse)
																		{
																			var sParams = sResponse.split("|-|");

																			showMessage("#GridMsg", sParams[0], sParams[1]);

																			if (sParams[0] == "success")
																				objTable.fnDeleteRow(objRow);
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


function updateRecord(iDocumentId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Code")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Document Type")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "User")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Date Time")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
		});


		$(".details").tipTip( );
	}

	else
		objTable.fnStandingRedraw( );
}