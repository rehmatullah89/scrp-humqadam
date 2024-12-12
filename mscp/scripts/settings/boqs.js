
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
	$("#txtTitle, #ddParent").blur(function( )
	{
		var sTitle = $("#txtTitle").val( );

		if (sTitle == "")
			return;


		$.post("ajax/settings/check-boq.php",
			{ Title:sTitle },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The BOQ Title is already used. Please specify another Title.");

					$("#DuplicateBoq").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateBoq").val("0");
				}
			},

			"text");
	});



	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#txtTitle").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("txtTitle", "B", "Please enter the BOQ Title."))
			return false;

		if (!objFV.validate("ddUnit", "B", "Please select the BOQ Unit."))
			return false;


		if (objFV.value("DuplicateBoq") == "1")
		{
			showMessage("#RecordMsg", "info", "The BOQ Title is already used. Please specify another Title.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});






	if (parseInt($("#TotalRecords").val( )) > 100)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
						       oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
						       bJQueryUI       : true,
						       sPaginationType : "full_numbers",
						       bPaginate       : true,
						       bLengthChange   : false,
						       iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
						       bFilter         : true,
						       bSort           : true,
						       aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4] } ],
						       bInfo           : true,
						       bStateSave      : false,
						       bProcessing     : false,
						       bAutoWidth      : false,
						       bServerSide     : true,
						       sAjaxSource     : "ajax/settings/get-boqs.php",

						       fnServerData    : function (sSource, aoData, fnCallback)
									 {
										if ($("div.toolbar #Unit").length > 0)
											aoData.push({ name:"Unit", value:$("div.toolbar #Unit").val( ) });


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

						       fnDrawCallback  : function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); },

						       fnInitComplete  : function( )
									 {
										$.post("ajax/settings/get-boq-filters.php",
										       {},

										       function (sResponse)
										       {
												$("div.toolbar").html(sResponse);
										       },

										       "text");


										var iUnit = 0;

										$("#DataGrid thead tr th").each(function(iIndex)
										{
											if ($(this).text( ) == "Unit")
												iUnit = iIndex;
										});

										this.fnFilter("", iUnit);


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
						       bJQueryUI       : true,
						       sPaginationType : "full_numbers",
						       bPaginate       : true,
						       bLengthChange   : false,
						       iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
						       bFilter         : true,
						       bSort           : true,
						       aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4] } ],
						       bInfo           : true,
						       bStateSave      : false,
						       bProcessing     : false,
						       bAutoWidth      : false,

						       fnDrawCallback  : function( ) { setTimeout(function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); }, 0); },

						       fnInitComplete  : function( )
									 {
										$.post("ajax/settings/get-boq-filters.php",
										       {},

										       function (sResponse)
										       {
												$("div.toolbar").html(sResponse);
										       },

										       "text");


										var iUnit = 0;

										$("#DataGrid thead tr th").each(function(iIndex)
										{
											if ($(this).text( ) == "Unit")
												iUnit = iIndex;
										});

										this.fnFilter("", iUnit);
									 }
						     } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iUnit = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Unit")
				iUnit = iIndex;
		});


		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sUnit     = "";

		if ($("div.toolbar #Unit").length > 0)
			sUnit = $("div.toolbar #Unit").val( );

		if (parseInt($("#TotalRecords").val( )) <= 100)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if (sUnit == "" || objTable.fnGetData(objRows[i])[iUnit] == sUnit)
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


	$(document).on("change", "div.toolbar #Unit", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Unit")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 100)
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


	$("#BtnMultiDelete").click(function( )
	{
		var sBoqs           = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sBoqs != "")
					sBoqs += ",";

				sBoqs += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sBoqs != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/settings/delete-boq.php",
												    { Boqs:sBoqs },

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
		var iBoqId = this.id;
		var iIndex = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("settings/edit-boq.php?BoqId=" + iBoqId + "&Index=" + iIndex), width:"500px", height:"400px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/settings/toggle-boq-status.php",
			{ BoqId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#GridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					var iColumn = 0;

					$("#DataGrid thead tr th").each(function(iIndex)
					{
						if ($(this).text( ) == "Status")
							iColumn = iIndex;
					});


					if (objIcon.src.indexOf("success.png") != -1)
					{
						objIcon.src = objIcon.src.replace("success.png", "error.png");

						objTable.fnUpdate("In-Active", objRow, iColumn);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objTable.fnUpdate("Active", objRow, iColumn);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iBoqId = this.id;
		var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/settings/delete-boq.php",
																		{ Boqs:iBoqId },

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


function updateRecord(iBoqId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 100)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Title")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Unit")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[3], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}