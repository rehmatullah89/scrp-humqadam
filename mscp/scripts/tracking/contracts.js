
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
	$("#txtStartDate, #txtEndDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});


    $("#txtSchools").tokenInput("ajax/get-schools-list.php",
    {
		queryParam         :  "School",
		minChars           :  2,
		tokenLimit         :  200,
		hintText           :  "Search the School (EMIS Code)",
		noResultsText      :  "No matching School found",
		theme              :  "facebook",
		preventDuplicates  :  true,
		prePopulate        :  $("#Schools").val( ),
		onAdd              :  function( ) { $("html, body").animate( { scrollTop:$(document).height( ) }, 'slow'); }
    });


	$("#txtTitle").blur(function( )
	{
		if ($(this).val( ) == "")
			return;


		$.post("ajax/tracking/check-contract.php",
			{ Title:$(this).val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Contract Title is already used. Please specify another Title.");

					$("#DuplicateContract").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateContract").val("0");
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

		if (!objFV.validate("txtTitle", "B", "Please enter the Title."))
			return false;

		if (!objFV.validate("ddContractor", "B", "Please select the Contractor."))
			return false;

		if (!objFV.validate("txtStartDate", "B", "Please enter the Start Date."))
			return false;

		if (!objFV.validate("txtEndDate", "B", "Please enter the End Date."))
			return false;

		if (!objFV.validate("txtSchools", "B", "Please select Schools included in this Contract."))
			return false;


		if (objFV.value("DuplicateContract") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Contract Title is already used. Please specify another Title.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});





	objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
										   aoColumnDefs    : [ { bSortable:false, aTargets:[6] } ],
										   aaSorting       : [ [ 0, "asc" ] ],
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
										   bAutoWidth      : false } );


	$("#BtnSelectAll").click(function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnMultiDelete").show( );
	});


	$("#BtnSelectNone").click(function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );
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
		var sContracts      = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sContracts != "")
					sContracts += ",";

				sContracts += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sContracts != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
													 $.post("ajax/tracking/delete-contract.php",
														{ Contracts:sContracts },

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
		var iContractId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-contract.php?ContractId=" + iContractId + "&Index=" + iIndex), width:"950", height:"520", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDetails", function(event)
	{
		var iContractId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-contract-details.php?ContractId=" + iContractId + "&Index=" + iIndex), width:"95%", height:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnBoqs", function(event)
	{
		var iContractId = this.id;

		$.colorbox({ href:("tracking/edit-contract-boqs.php?ContractId=" + iContractId), width:"800px", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iContractId = this.id;

		$.colorbox({ href:("tracking/view-contract.php?ContractId=" + iContractId), width:"95%", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});



	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/tracking/toggle-contract-status.php",
			{ ContractId:objIcon.id },

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
		var iContractId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/tracking/delete-contract.php",
																		{ Contracts:iContractId },

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


function updateRecord(iContractId, iRow, sFields)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Title")
			objTable.fnUpdate(sFields[0], iRow, iIndex);

		else if ($(this).text( ) == "Contractor")
			objTable.fnUpdate(sFields[1], iRow, iIndex);

		else if ($(this).text( ) == "Start Date")
			objTable.fnUpdate(sFields[2], iRow, iIndex);

		else if ($(this).text( ) == "End Date")
			objTable.fnUpdate(sFields[3], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objTable.fnUpdate(sFields[4], iRow, iIndex);
	});


	$(".icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iContractId)
			$(this).attr("src", sFields[5]);
	});
}