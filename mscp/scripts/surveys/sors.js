
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
        $("#BtnAddRow").button({ icons:{ primary:'ui-icon-disk' } });
        $("#BtnDelRow").button({ icons:{ primary:'ui-icon-disk' } });

        $("#BtnExport").click(function( )
        {       
                document.location = ($(this).attr("rel") + "?District=" + $("div.toolbar #District").val( ) + "&SyncStatus=" + $("div.toolbar #SyncStatus").val( ) + "&SorStatus=" + $("div.toolbar #SorStatus").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
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
		
	$("#txtCode").autocomplete({ source:"ajax/get-emis-codes-list.php", minLength:3 });
        
        $("#txtCode").blur(function( )
                {
                        if ($("#txtCode").val( ) == "")
                                return;


                        $.post("ajax/surveys/check-sor.php",
                                { Code:$("#txtCode").val( ) },

                                function (sResponse)
                                {
                                    //var obj = $.parseJSON(sResponse);

                                        if (sResponse == "INVALID")
                                        {
                                                showMessage("#RecordMsg", "info", "The provided EMIS Code is Invalid, No school found in the system.");

                                                $("#DuplicateSor").val("0");
                                        }

                                        else if (sResponse == "USED")
                                        {
                                                showMessage("#RecordMsg", "info", "This School SOR is already exist in system. Please specify another EMIS Code.");

                                                $("#DuplicateSor").val("1");
                                        }				

                                        else
                                        {

                                                $("#RecordMsg").hide( );
                                                $("#DuplicateSor").val("0");
                                        }
                                },

                                "text");
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


		if (!objFV.validate("txtPrincipal", "B", "Please enter the Header Teacher / Principal."))
			return false;
		
                if (!objFV.validate("ddEngineer", "B", "Please Select the District Engineer."))
			return false;
		
                if (!objFV.validate("txtPtc", "B", "Please enter the PTC Representative Name."))
			return false;
		
                if (!objFV.validate("txtCcsi", "B", "Please Enter CCSI Representative Name"))
			return false;
		
		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;
		
		if (!objFV.validate("txtDate", "B", "Please select the SOR Date."))
			return false;
		
		if (objFV.value("DuplicateSor") == "1")
		{
			showMessage("#RecordMsg", "info", "This School SOR is already exists in system. Please specify another EMIS Code.");

			objFV.focus("txtCode");
			objFV.select("txtCode");

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
											   sAjaxSource     : "ajax/surveys/get-sors.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																		if ($("div.toolbar #District").length > 0)
																			aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });                                                                                                                   
				   
																		if ($("div.toolbar #SyncStatus").length > 0)
																			aoData.push({ name:"SyncStatus", value:$("div.toolbar #SyncStatus").val( ) });
																		
																		if ($("div.toolbar #SorStatus").length > 0)
																			aoData.push({ name:"SorStatus", value:$("div.toolbar #SorStatus").val( ) });
																		
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
																	$.post("ajax/surveys/get-sor-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict     = 0;
																	var iSyncStatus   = 0;
																	var iSorStatus = 0;
																	
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Sync Status")
																			iSyncStatus = iIndex;
																		
																		if ($(this).text( ) == "Sor Status")
																			iSorStatus = iIndex;
																		
																	});

																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iSyncStatus);
																	this.fnFilter("", iSorStatus);

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
																	$.post("ajax/surveys/get-sor-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iDistrict     = 0;
																	var iSyncStatus   = 0;
																	var iSorStatus = 0;
																															
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Sync Status")
																			iSyncStatus = iIndex;

																		if ($(this).text( ) == "Sor Status")
																			iSorStatus = iIndex;
																	});

																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iSyncStatus);
																	this.fnFilter("", iSorStatus);
																 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iDistrict     = 0;
		var iSyncStatus   = 0;
		var iSorStatus = 0;
																
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
			
			if ($(this).text( ) == "Sync Status")
				iSyncStatus = iIndex;

			if ($(this).text( ) == "Sor Status")
				iSorStatus = iIndex;
		});


		var objRows       = objTable.fnGetNodes( );
		var bSelected     = false;
		var sDistrict     = "";
		var sSyncStatus   = "";
		var sSorStatus = "";

		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
                    
		if ($("div.toolbar #SyncStatus").length > 0)
			sSyncStatus = $("div.toolbar #SyncStatus").val( );

		if ($("div.toolbar #SorStatus").length > 0)
			sSorStatus = $("div.toolbar #SorStatus").val( );

		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) ||
                     (sSyncStatus == "" || objTable.fnGetData(objRows[i])[iSyncStatus] == sSyncStatus) ||
					 (sSorStatus == "" || objTable.fnGetData(objRows[i])[iSorStatus] == sSorStatus) )
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
	
	
    $(document).on("change", "div.toolbar #SorStatus", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Sor Status")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
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
		var sSors      = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sSors != "")
					sSors += ",";

				sSors += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sSors != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
																		{
																			 $.post("ajax/surveys/delete-sor.php",
																				{ SORs:sSors },

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
		var iSorId = this.id;
                var iIndex = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-sor.php?SorId=" + iSorId + "&Index=" + iIndex), width:"900px", height:"600px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iSorId = this.id;

		$.colorbox({ href:("surveys/view-sor.php?SorId=" + iSorId), width:"900px", height:"600px", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iSorId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/surveys/delete-sor.php",
																		{ SORs:iSorId },

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
	
	
	$(document).on("click", ".icnSor", function(event)
	{
		if ($("span.ui-icon-close").length == 1)
			$("span.ui-icon-close").trigger("click");

		
		var iSorId = $(this).attr("id");
		var sSchool   = $(this).attr("rel");
		var objTabLi  = $(sTabTemplate.replace(/#\{href\}/g, ("#" + sTabName)).replace(/#\{label\}/g, sSchool));
		
		
		var objTabs = $("#PageTabs").tabs(
		{
			fx        :  [ {width:'toggle', duration:'normal'}, {width:'toggle', duration:'fast'} ],

			activate  :  function(event, ui)
			{
				var sHtml = ui.newPanel.html( );			

				if (sHtml.indexOf("images/waiting.gif") >= 0)
					setupSectionsGrid(iSorId);
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
			
			setupSectionsGrid(iSorId);
		}
		
		
		objTabs.tabs("option", "active", iTabs);
	

		event.stopPropagation( );
	});	


	$(document).on("click", "#SectionsGrid .icnEdit", function(event)
	{
		var iSorId  = $(this).attr("sor");
		var iSectionId = $(this).attr("section");
		var iIndex     = objSections.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-sor-section.php?SorId=" + iSorId + "&SectionId=" + iSectionId + "&Index=" + iIndex), width:"80%", height:"90%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#SectionsGrid .icnView", function(event)
	{
		var iSorId  = $(this).attr("sor");
		var iSectionId = $(this).attr("section");

		$.colorbox({ href:("surveys/view-sor-section.php?SorId=" + iSorId + "&SectionId=" + iSectionId), width:"80%", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});	
});


function updateRecord(iSorId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
                        if ($(this).text( ) == "#")
                            objTable.fnUpdate(sFields[0], iRow, iIndex);
                        
                        else if ($(this).text( ) == "School")
                            objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Code")
				objTable.fnUpdate(sFields[2], iRow, iIndex);
			
			else if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[3], iRow, iIndex);
			
                        else if ($(this).text( ) == "CDC")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Date")
				objTable.fnUpdate(sFields[5], iRow, iIndex);
                         
                        else if ($(this).text( ) == "Sync Status")
				objTable.fnUpdate(sFields[6], iRow, iIndex);

                        else if ($(this).text( ) == "Sor Status")
				objTable.fnUpdate(sFields[7], iRow, iIndex);
                            
			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[8], iRow, iIndex);
		});
		
		
		$(".details").tipTip( );
	}

	else
		objTable.fnStandingRedraw( );
}

function setupSectionsGrid(iSorId)
{
	$.post("ajax/surveys/get-sor-sections.php",
		{ SorId:iSorId },

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