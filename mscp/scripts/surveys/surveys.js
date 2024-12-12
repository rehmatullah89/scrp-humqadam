
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
var objSections;
var sTabTemplate = "";
var sTabName = "tab-3";
var iTabs = 2;

$(document).ready(function( )
{
    $("#BtnExport").button({ icons:{ primary:'ui-icon-disk' } });
    $("#BtnVfExport").button({ icons:{ primary:'ui-icon-disk' } });

	$("#BtnExport").click(function( )
	{       
		document.location = ($(this).attr("rel") + "?District=" + $("div.toolbar #District").val( ) + "&SyncStatus=" + $("div.toolbar #SyncStatus").val( ) + "&SurveyStatus=" + $("div.toolbar #SurveyStatus").val( ) + "&Qualified=" + $("div.toolbar #Qualified").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
	});

    $("#BtnVfExport").click(function( )
	{       
		document.location = ($(this).attr("rel") + "?District=" + $("div.toolbar #District").val( ));
	});
	
	
	sTabTemplate = ("<li><a href='" + $("#PageTabs").attr("rel") + "#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation' style='float:right; cursor:pointer; margin:5px 8px 0px 0px;'>Remove Tab</span></li>");
	
	if ($("#frmRecord").length == 0)
	{
		sTabName = "tab-2";
		iTabs    = 1;
	}
	
	
	
	$("#txtDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd",
		minDate         : -6,
		maxDate         : 0		
	});
		
	
	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;


		$.post("ajax/surveys/check-survey.php",
			{ Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				$("#Province").val(sResponse);
				
				if (sResponse == "1")
					$("#PefProgramme").show("blind");
				
				else
					$("#PefProgramme").hide("blind").val("");
			},

			"text");
	});

	
	$("#frmRecord select").change(function( )
	{
		var sData = $(this).attr("rel");
		
		if (typeof sData !== "undefined" && sData != "")
		{
			var sParams    = sData.split("|");
			var bException = false;
			
			if ($(this).attr("id") == "ddLandAvailable" && $("#Province").val( ) == "2")
				bException = true;
			
			
			if ($(this).val( ) == sParams[1] && bException == false)
			{
				$("#frmRecord div." + sParams[0]).each(function( )
				{
					if ($(this).css("display") == "block")
						$(this).hide("blind");
					
					$("#frmRecord div." + sParams[0] + " select, #frmRecord div." + sParams[0] + " input").val("");
				});
			}
			
			else
			{
				$("#frmRecord div." + sParams[0]).each(function( )
				{
					if ($(this).css("display") != "block")
						$(this).show("blind");
				});
			}
		}
	});
	
	
	$("#ddOperational").change(function( )
	{
		if ($(this).val( ) == "N")
			$("#ddNonOperational").show( );
		
		else
			$("#ddNonOperational").hide( ).val("");
	});
	
	
	$("#ddLandDispute").change(function( )
	{
		if ($(this).val( ) == "Y")
			$("#ddDispute").show( );
		
		else
			$("#ddDispute").hide( ).val("");
	});

	
	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );

		$("Province").val("0");
		$("#txtCode").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;
		
		if ($("#Province").val( ) == "0")
		{
			showMessage("#RecordMsg", "alert", "Invalid EMIS Code, no school found. Please enter a valid EMIS Code.");
			
			return false;
		}
		

		if (!objFV.validate("txtEnumerator", "B,C", "Please enter the Enumerator Name."))
			return false;

		if (!objFV.validate("txtDate", "B", "Please select the Survey Date."))
			return false;

		if (!objFV.validate("ddOperational", "B", "Is the school operational?"))
			return false;

		if (objFV.value("ddOperational") == "Y")
		{
			if ($("#Province").val( ) == "1")
			{
				if (!objFV.validate("ddPefProgramme", "B", "Is the school part of the PEF (Punjab Education Foundation) Programme?"))
					return false;
			}

			if (objFV.value("ddPefProgramme") == "N" || objFV.value("ddPefProgramme") == "")
			{
				if (!objFV.validate("ddLandAvailable", "B", "Does the school have enough land for new construction?"))
					return false;
				
				if (objFV.value("ddLandAvailable") == "Y" || $("#Province").val( ) == "2")
				{
					if (!objFV.validate("ddLandDispute", "B", "Is the school having any land dispute?"))
						return false;
					
					if (objFV.value("ddLandDispute") == "N")
					{
						if (!objFV.validate("ddOtherFunding", "B", "Is the school involved in any other project providing funding for classroom infrastructure?"))
							return false;
						
						if (objFV.value("ddOtherFunding") == "N")
						{
							if (!objFV.validate("txtClassRooms", "B,N", "How many classrooms does your school have?"))
								return false;
							
							if (!objFV.validate("txtEducationRooms", "B,N", "Out of the total number how many classrooms are in use for educational purposes?"))
								return false;
							
							if (parseInt(objFV.value("txtEducationRooms")) > parseInt(objFV.value("txtClassRooms")))
							{
								showMessage("#RecordMsg", "info", "Please enter valid No of Classrooms that are in use for educational purposes?.");
								
								return false;
							}					
							
							if (!objFV.validate("ddShelterLess", "B", "Are there any shelter-less grades being taught?"))
								return false;
							
							if (!objFV.validate("ddMultiGrading", "B", "Are there more than 2 grades being taught in one classroom (multi-grading)?"))
								return false;
							
							if (!objFV.validate("txtAvgAttendance", "B,N", "What is the average attendance of school?"))
								return false;
							
							if (!objFV.validate("ddPreSelection", "B", "Does the School Qualify Pre-Selection?"))
								return false;
						}
					}
					
					else
					{
						if (!objFV.validate("ddDispute", "B", "Please select the Land Dispute."))
							return false;
					}
				}
			}
		}
		
		else
		{
			if (!objFV.validate("ddNonOperational", "B", "Please select the Non-Operational Reason."))
				return false;
		}		
		

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});






	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   aoColumnDefs    : [ { bSortable:false, aTargets:[8] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
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
											   sAjaxSource     : "ajax/surveys/get-surveys.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																		if ($("div.toolbar #District").length > 0)
																			aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });                                                                                                                   
				   
																		if ($("div.toolbar #SyncStatus").length > 0)
																			aoData.push({ name:"SyncStatus", value:$("div.toolbar #SyncStatus").val( ) });
																		
																		if ($("div.toolbar #SurveyStatus").length > 0)
																			aoData.push({ name:"SurveyStatus", value:$("div.toolbar #SurveyStatus").val( ) });
																		
																		if ($("div.toolbar #Qualified").length > 0)
																			aoData.push({ name:"Qualified", value:$("div.toolbar #Qualified").val( ) });


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

											   fnDrawCallback  : function( )
															     {
																    $(".details").tipTip( );
															     },

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/surveys/get-survey-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict     = 0;
																	var iSyncStatus   = 0;
																	var iSurveyStatus = 0;
																	var iQualified    = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Sync Status")
																			iSyncStatus = iIndex;
																		
																		if ($(this).text( ) == "Survey Status")
																			iQualified = iIndex;
																		
																		if ($(this).text( ) == "Qualified")
																			iQualified = iIndex;
																	});

																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iSyncStatus);
																	this.fnFilter("", iQualified);
																	this.fnFilter("", iSurveyStatus);

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
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   aoColumnDefs    : [ { bSortable:false, aTargets:[8] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
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
																	$.post("ajax/surveys/get-survey-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict     = 0;
																	var iSyncStatus   = 0;
																	var iSurveyStatus = 0;
																															
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Sync Status")
																			iSyncStatus = iIndex;

																		if ($(this).text( ) == "Survey Status")
																			iSurveyStatus = iIndex;
																	});

																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iSyncStatus);
																	this.fnFilter("", iSurveyStatus);
																 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iDistrict     = 0;
		var iSyncStatus   = 0;
		var iSurveyStatus = 0;
																
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
			
			if ($(this).text( ) == "Sync Status")
				iSyncStatus = iIndex;

			if ($(this).text( ) == "Survey Status")
				iSurveyStatus = iIndex;
		});


		var objRows       = objTable.fnGetNodes( );
		var bSelected     = false;
		var sDistrict     = "";
		var sSyncStatus   = "";
		var sSurveyStatus = "";

		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
                    
		if ($("div.toolbar #SyncStatus").length > 0)
			sSyncStatus = $("div.toolbar #SyncStatus").val( );

		if ($("div.toolbar #SurveyStatus").length > 0)
			sSurveyStatus = $("div.toolbar #SurveyStatus").val( );

		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) ||
                     (sSyncStatus == "" || objTable.fnGetData(objRows[i])[iSyncStatus] == sSyncStatus) ||
					 (sSurveyStatus == "" || objTable.fnGetData(objRows[i])[iSurveyStatus] == sSurveyStatus) )
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
	});

        
    $(document).on("change", "div.toolbar #SyncStatus", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Sync Status")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
	});
	
	
    $(document).on("change", "div.toolbar #SurveyStatus", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Survey Status")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
	});
	
	
    $(document).on("change", "div.toolbar #Qualified", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		objTable.fnFilter($(this).val( ), 0);
	});


	$(document).on("click", "#DataGrid tr a", function( )
	{
		document.location = $(this).attr("href");
		
		return false;
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
		var sSurveys        = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sSurveys != "")
					sSurveys += ",";

				sSurveys += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sSurveys != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
															{
																 $.post("ajax/surveys/delete-survey.php",
																	{ Surveys:sSurveys },

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




	$(document).on("click", "#DataGrid .icnEdit", function(event)
	{
		var iSurveyId = this.id;
		var iIndex    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-survey.php?SurveyId=" + iSurveyId + "&Index=" + iIndex), width:"700px", height:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iSurveyId = this.id;

		$.colorbox({ href:("surveys/view-survey.php?SurveyId=" + iSurveyId), width:"700px", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});
	
	
	$(document).on("click", ".icnSurvey", function(event)
	{
		if ($("span.ui-icon-close").length == 1)
			$("span.ui-icon-close").trigger("click");

		
		var iSurveyId = $(this).attr("id");
		var sSchool   = $(this).attr("rel");
		var objTabLi  = $(sTabTemplate.replace(/#\{href\}/g, ("#" + sTabName)).replace(/#\{label\}/g, sSchool));
		
		
		var objTabs = $("#PageTabs").tabs(
		{
			fx        :  [ {width:'toggle', duration:'normal'}, {width:'toggle', duration:'fast'} ],

			activate  :  function(event, ui)
			{
				var sHtml = ui.newPanel.html( );			

				if (sHtml.indexOf("images/waiting.gif") >= 0)
					setupSectionsGrid(iSurveyId);
			}
		});	


		objTabs.delegate("span.ui-icon-close", "click", function( )
		{
			var sTab = $(this).closest("li").remove( ).attr("aria-controls");
			
			$("#" + sTab).remove( );
			
			objTabs.tabs("refresh");			
			objTabs.tabs("option", "active", 0);
		});


		if ($('#PageTabs ul li a:visible').length == iTabs)
		{		
			objTabs.find(".ui-tabs-nav").append(objTabLi);
			objTabs.append("<div id='" + sTabName + "'><center><img src='images/waiting.gif' vspace='100' alt='' title='' /></center></div>");
			objTabs.tabs("refresh");
		}
		
		else
		{
			$("#tab-" + iTabs).html("<center><img src='images/waiting.gif' vspace='100' alt='' title='' /></center>");
			
			setupSectionsGrid(iSurveyId);
		}
		
		
		objTabs.tabs("option", "active", iTabs);
	

		event.stopPropagation( );
	});	


	$(document).on("click", ".icnDelete", function(event)
	{
		var iSurveyId = this.id;
		var objRow    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/surveys/delete-survey.php",
																		{ Surveys:iSurveyId },

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
	
	
	$(document).on("click", "#SectionsGrid .icnEdit", function(event)
	{
		var iSurveyId  = $(this).attr("survey");
		var iSectionId = $(this).attr("section");
		var iIndex     = objSections.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-survey-section.php?SurveyId=" + iSurveyId + "&SectionId=" + iSectionId + "&Index=" + iIndex), width:"80%", height:"90%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#SectionsGrid .icnView", function(event)
	{
		var iSurveyId  = $(this).attr("survey");
		var iSectionId = $(this).attr("section");

		$.colorbox({ href:("surveys/view-survey-section.php?SurveyId=" + iSurveyId + "&SectionId=" + iSectionId), width:"80%", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});	
});


function updateRecord(iSurveyId, iRow, sFields)
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

			else if ($(this).text( ) == "Enumerator")
				objTable.fnUpdate(sFields[3], iRow, iIndex);
			
			else if ($(this).text( ) == "Date")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Sync Status")
				objTable.fnUpdate(sFields[5], iRow, iIndex);
			
			else if ($(this).text( ) == "Survey Status")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
			
			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[7], iRow, iIndex);
		});
		
		
		$(".details").tipTip( );
	}

	else
		objTable.fnStandingRedraw( );
}


function setupSectionsGrid(iSurveyId)
{
	$.post("ajax/surveys/get-survey-sections.php",
		{ SurveyId:iSurveyId },

		function (sResponse)
		{
			$("#" + sTabName).html(sResponse);
			
			if (objSections)
				objSections.fnDestroy( );								
			
			
			objSections = $("#SectionsGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
														   aoColumnDefs    : [ { bSortable:false, aTargets:[3] } ],
														   aaSorting       : [ [ 0, "asc" ] ],
														   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
														   bJQueryUI       : true,
														   sPaginationType : "full_numbers",
														   bPaginate       : true,
														   bLengthChange   : false,
														   iDisplayLength  : 50,
														   bFilter         : true,
														   bSort           : true,
														   bInfo           : true,
														   bStateSave      : false,
														   bProcessing     : false,
														   bAutoWidth      : false,
														   fnDrawCallback  : function( ) { $(".details").tipTip( );  }
														} );
		},

		"text");	
}


function updateSectionRecord(iRow, sFields)
{
	$("#SectionsGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Status")
			objSections.fnUpdate(sFields[0], iRow, iIndex);
		
		else if ($(this).text( ) == "Options")
			objSections.fnUpdate(sFields[1], iRow, iIndex);
	});
	
	
	$(".details").tipTip( );
}