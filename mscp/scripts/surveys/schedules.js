
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


		$.post("ajax/surveys/check-schedule.php",
			{ Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				if (sResponse == "INVALID")
				{
					showMessage("#RecordMsg", "info", "The provided EMIS Code is Invalid, No school found in the system.");

					$("#DuplicateSchedule").val("0");
				}
				
				else if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "This School Survey is already scheduled. Please specify another EMIS Code.");

					$("#DuplicateSchedule").val("1");
				}				

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateSchedule").val("0");
				}
			},

			"text");
	});


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );

		$("#ddEnumerator").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddEnumerator", "B", "Please select the Enumerator."))
			return false;
		
		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;
		
		if (!objFV.validate("txtDate", "B", "Please select the Survey Date."))
			return false;
		
		if (objFV.value("DuplicateSchedule") == "1")
		{
			showMessage("#RecordMsg", "info", "This School Survey is already scheduled. Please specify another EMIS Code.");

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
											   sAjaxSource     : "ajax/surveys/get-schedules.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																		if ($("div.toolbar #Enumerator").length > 0)
																			aoData.push({ name:"Enumerator", value:$("div.toolbar #Enumerator").val( ) });
																		
																		if ($("div.toolbar #District").length > 0)
																			aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });
					
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
																	$.post("ajax/surveys/get-schedule-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iEnumerator = 0;
																	var iDistrict   = 0;
																	var iStatus     = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "Enumerator")
																			iEnumerator = iIndex;
																		
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Status")
																			iStatus = iIndex;
																	});

																	this.fnFilter("", iEnumerator);
																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iStatus);

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
																	$.post("ajax/surveys/get-schedule-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iEnumerator = 0;
																	var iDistrict   = 0;
																	var iStatus     = 0;
																															
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "Enumerator")
																			iEnumerator = iIndex;
																		
																		if ($(this).text( ) == "District")
																			iDistrict = iIndex;
																		
																		if ($(this).text( ) == "Status")
																			iStatus = iIndex;
																	});

																	this.fnFilter("", iEnumerator);
																	this.fnFilter("", iDistrict);
																	this.fnFilter("", iStatus);
																 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iEnumerator = 0;
		var iDistrict   = 0;
		var iStatus     = 0;
                
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Enumerator")
				iEnumerator = iIndex;
			
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
                        
			if ($(this).text( ) == "Status")
				iStatus = iIndex;
		});


		var objRows     = objTable.fnGetNodes( );
		var bSelected   = false;
		var sEnumerator = "";
		var sDistrict   = "";
		var sStatus     = "";

		if ($("div.toolbar #Enumerator").length > 0)
			sEnumerator = $("div.toolbar #Enumerator").val( );
		
		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );

		if ($("div.toolbar #Status").length > 0)
			sStatus = $("div.toolbar #Status").val( );

		
		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sEnumerator == "" || objTable.fnGetData(objRows[i])[iEnumerator] == sEnumerator) ||
                     (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) ||
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


	$(document).on("change", "div.toolbar #Enumerator", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Enumerator")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
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
		var sSchedules      = "";
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
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-schedule.php?ScheduleId=" + iScheduleId + "&Index=" + iIndex), width:"400px", height:"400px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iScheduleId = this.id;

		$.colorbox({ href:("surveys/view-schedule.php?ScheduleId=" + iScheduleId), width:"400px", height:"400px", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iScheduleId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

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
            if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Code")
				objTable.fnUpdate(sFields[1], iRow, iIndex);
			
			else if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[2], iRow, iIndex);
			
            if ($(this).text( ) == "Enumerator")
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