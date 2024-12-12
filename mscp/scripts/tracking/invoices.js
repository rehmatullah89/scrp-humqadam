
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


	$("#txtInvoiceNo").blur(function( )
	{
		if ($("#txtInvoiceNo").val( ) == "")
			return;


		$.post("ajax/tracking/check-invoice.php",
			{ InvoiceNo:$("#txtInvoiceNo").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided Invoice No already exists in System.");

					$("#DuplicateInvoice").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateInvoice").val("0");
				}
			},

			"text");
	});


	$("#ddContract").change(function( )
	{
		$.post("ajax/get-contract-schools.php",
			{ Contract:$("#ddContract").val( ) },

			function (sResponse)
			{
				$("#ddSchool").html(sResponse);
			},

			"text");
	});


	$("#ddSchool").change(function( )
	{
		if ($("#ddSchool").val( ) == "")
			return;


		$.post("ajax/tracking/get-invoice-inspections.php",
			{ School:$("#ddSchool").val( ) },

			function (sResponse)
			{
				$("#Inspections").html(sResponse);
			},
			"text");
	});


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#ddContract").focus( );

		$("#Inspections").html("");

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddContract", "B", "Please select the Contract."))
			return false;

		if (!objFV.validate("ddSchool", "B", "Please select the School."))
			return false;

		if (!objFV.validate("txtInvoiceNo", "B", "Please enter the Invoice No."))
			return false;
		
		if (!objFV.validate("txtTitle", "B", "Please enter the Invoice Title."))
			return false;

		if (!objFV.validate("txtDate", "B", "Please enter the Date."))
			return false;


		if (objFV.value("DuplicateInvoice") == "1")
		{
			showMessage("#RecordMsg", "info", "The provided Invoice No already exists in System.");

			objFV.focus("ddContract");
			objFV.select("ddContract");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});




	if (parseInt($("#TotalRecords").val( )) > 100)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   aoColumnDefs    : [ { bSortable:false, aTargets:[8] } ],
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
											   sAjaxSource     : "ajax/tracking/get-invoices.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #Contract").length > 0)
																		aoData.push({ name:"Contract", value:$("div.toolbar #Contract").val( ) });

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

											   fnInitComplete  : function( )
													 {
														$.post("ajax/tracking/get-invoice-filters.php",
															   {},

															   function (sResponse)
															   {
																	$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iContract = 0;
														var iStatus   = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "Contract")
																iContract = iIndex;

															else if ($(this).text( ) == "Status")
																iStatus = iIndex;
														});


														this.fnFilter("", iContract);
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
											   aoColumnDefs    : [ { bSortable:false, aTargets:[8] } ],
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

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/tracking/get-invoice-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																		var iContract = 0;
																		var iStatus   = 0;

																		$("#DataGrid thead tr th").each(function(iIndex)
																		{
																			if ($(this).text( ) == "Contract")
																				iContract = iIndex;

																			else if ($(this).text( ) == "Status")
																				iStatus = iIndex;
																		});


																		this.fnFilter("", iContract);
																		this.fnFilter("", iStatus);
																 }
													   } );
	}


	$("#BtnSelectAll").click(function( )
	{
		var iContract = 0;
		var iStatus   = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Contract")
				iContract = iIndex;

			else if ($(this).text( ) == "Status")
				iStatus = iIndex;
		});



		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sContract = "";
		var sStatus   = "";

		if ($("div.toolbar #Contract").length > 0)
			sContract = $("div.toolbar #Contract").val( );

		if ($("div.toolbar #Status").length > 0)
			sStatus = $("div.toolbar #Status").val( );


		if (parseInt($("#TotalRecords").val( )) <= 100)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sContract == "" || sContract == objTable.fnGetData(objRows[i])[iContract]) ||
				     (sStatus == "" || sStatus == objTable.fnGetData(objRows[i])[iStatus]) )
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


	$(document).on("change", "div.toolbar #Contract", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Contract")
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
		var sInvoices       = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sInvoices != "")
					sInvoices += ",";

				sInvoices += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sInvoices != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
															{
															 $.post("ajax/tracking/delete-invoice.php",
																{ Invoices:sInvoices },

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
		var iInvoiceId = this.id;
		var iIndex     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-invoice.php?InvoiceId=" + iInvoiceId + "&Index=" + iIndex), width:"90%", height:"650px", maxWidth:"1100px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iInvoiceId = this.id;

		$.colorbox({ href:("tracking/view-invoice.php?InvoiceId=" + iInvoiceId), width:"90%", height:"630px", maxWidth:"1100px", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iInvoiceId = this.id;
		var objRow     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/tracking/delete-invoice.php",
																		{ Invoices:iInvoiceId },

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
	
	
	$(document).on("click", ".icnReleased", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/tracking/toggle-invoice-released-status.php",
			{ InvoiceId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#GridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					if (objIcon.src.indexOf("green.png") != -1)
						objIcon.src = objIcon.src.replace("green.png", "blue.png");

					else
						objIcon.src = objIcon.src.replace("blue.png", "green.png");
				}

				$(objIcon).addClass("icnReleased");
			},

			"text");

		event.stopPropagation( );
	});	
});


function updateRecord(iInvoiceId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 100)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Invoice #")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Title")
				objTable.fnUpdate(sFields[1], iRow, iIndex);
			
			else if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[2], iRow, iIndex);
			
			else if ($(this).text( ) == "Contract")
				objTable.fnUpdate(sFields[3], iRow, iIndex);
			
			else if ($(this).text( ) == "Date")
				objTable.fnUpdate(sFields[4], iRow, iIndex);
				  
			else if ($(this).text( ) == "Amount")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}