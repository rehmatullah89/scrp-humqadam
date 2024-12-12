
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

$(document).ready(function( )
{
    
       sTabTemplate = ("<li><a href='" + $("#PageTabs").attr("rel") + "#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation' style='float:right; cursor:pointer; margin:5px 8px 0px 0px;'>Remove Tab</span></li>");
	
	
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
					}
				}
				
				else
				{
					if (!objFV.validate("ddDispute", "B", "Please select the Land Dispute."))
						return false;
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
											   aoColumnDefs    : [ { bSortable:false, aTargets:[7] } ],
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
                                                                                                                    
                                                                                                                if ($("div.toolbar #Type").length > 0)
															aoData.push({ name:"Type", value:$("div.toolbar #Type").val( ) });
    
														if ($("div.toolbar #Status").length > 0)
															aoData.push({ name:"Status", value:$("div.toolbar #Status").val( ) });


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


														var iDistrict = 0;
														var iStatus   = 0;
                                                                                                                var iType     = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "District")
																iDistrict = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "Type")
																iType = iIndex;
															
															if ($(this).text( ) == "Status")
																iStatus = iIndex;
														});

														this.fnFilter("", iDistrict);
														this.fnFilter("", iStatus);
                                                                                                                this.fnFilter("", iType);

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
											   aoColumnDefs    : [ { bSortable:false, aTargets:[7] } ],
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


														var iDistrict = 0;
														var iStatus   = 0;
                                                                                                                var iType = 0;   
                                                                                                                
														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "District")
																iDistrict = iIndex;
															
															if ($(this).text( ) == "Status")
																iStatus = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "Type")
																iType = iIndex;
														});

														this.fnFilter("", iDistrict);
                                                                                                                this.fnFilter("", iType);
														this.fnFilter("", iStatus);
													 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iDistrict = 0;
		var iStatus   = 0;
                var iType     = 0;
                
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
                        
                        if ($(this).text( ) == "Type")
				iType = iIndex;
			
			if ($(this).text( ) == "Status")
				iStatus = iIndex;
		});


		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sDistrict = "";
		var sStatus   = "";
                var sType     = "";

		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
                    
                if ($("div.toolbar #Type").length > 0)
			sType = $("div.toolbar #Type").val( );    
                		
		if ($("div.toolbar #Status").length > 0)
			sStatus = $("div.toolbar #Status").val( );

		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) ||
				     (sType == ""    || objTable.fnGetData(objRows[i])[iType] == sType) ||
                                     (sStatus == "" || objTable.fnGetData(objRows[i])[iStatus] == sStatus) )
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


	$(document).on("change", "div.toolbar #Type", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Type")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
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
		var sSchedules        = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sSchedules != "")
					sSchedules += ",";

				sSchedules += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sSchedules != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/surveys/delete-schedule.php",
												    { Schedules:sSchedules },

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
		var iScheduleId = this.id;
		var iIndex    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-schedule.php?ScheduleId=" + iScheduleId + "&Index=" + iIndex), width:"700px", height:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iScheduleId = this.id;

		$.colorbox({ href:("surveys/view-schedule.php?ScheduleId=" + iScheduleId), width:"700px", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});
	
	
	$(document).on("click", ".icnSurvey", function(event)
	{
		var iSurveyId = $(this).attr("id");
		var sSchool   = $(this).attr("rel");
		var objTabLi  = $(sTabTemplate.replace(/#\{href\}/g, "#tab-3").replace(/#\{label\}/g, sSchool));
		
		
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


		if ($('#PageTabs ul li a:visible').length == 2)
		{		
			objTabs.find(".ui-tabs-nav").append(objTabLi);
			objTabs.append("<div id='tab-3'><center><img src='images/waiting.gif' vspace='100' alt='' title='' /></center></div>");
			objTabs.tabs("refresh");
		}
		
		else
		{
			$("#tab-2").html("<center><img src='images/waiting.gif' vspace='100' alt='' title='' /></center>");
			
			setupSectionsGrid(iSurveyId);
		}
		
		
		objTabs.tabs("option", "active", 2);
	

		event.stopPropagation( );
	});	


	$(document).on("click", ".icnDelete", function(event)
	{
		var iScheduleId = this.id;
		var objRow    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/surveys/delete-schedule.php",
																		{ Schedules:iScheduleId },

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


function updateRecord(iSurveyId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
                        if ($(this).text( ) == "Enumerator")
				objTable.fnUpdate(sFields[0], iRow, iIndex);
			
                        if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Code")
				objTable.fnUpdate(sFields[2], iRow, iIndex);
			
			else if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Date")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[5], iRow, iIndex);
			
			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
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
			$("#tab-3").html(sResponse);
			
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