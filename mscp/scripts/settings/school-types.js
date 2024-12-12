
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
	objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
										   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
										   bJQueryUI       : true,
										   sPaginationType : "full_numbers",
										   bPaginate       : true,
										   bLengthChange   : false,
										   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
										   bFilter         : true,
										   bSort           : true,
										   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3] } ],
										   bInfo           : true,
										   bStateSave      : false,
										   bProcessing     : false,
										   bAutoWidth      : false
										} );


	$("#BtnSelectAll").click(function( )
	{
		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).find("img.icnDelete").length == 0)
			{
				$(objRows[i]).removeClass("selected");

				continue;
			}


			if (!$(objRows[i]).hasClass("selected"))
			{
				$(objRows[i]).addClass("selected");

				bSelected = true;
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


	$("#tabs-1 .TableTools").prepend('<button id="BtnMultiDelete">Delete Selected Rows</button>')
	$("#BtnMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnMultiDelete").hide( );


	$(document).on("click", "#BtnMultiDelete", function( )
	{
		var sTypes          = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sTypes != "")
					sTypes += ",";

				sTypes += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sTypes != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
																		{
																			 $.post("ajax/settings/delete-school-type.php",
																				{ Types:sTypes },

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
		var iTypeId = this.id;
		var iIndex  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("settings/edit-school-type.php?TypeId=" + iTypeId + "&Index=" + iIndex), width:"400px", height:"300px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnDelete", function(event)
	{
		var iTypeId = this.id;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                                  width     : 420,
		                                  height    : 110,
		                                  modal     : true,
		                                  buttons   : { "Delete" : function( )
		                                                           {
																		$.post("ajax/settings/delete-school-type.php",
																			{ Types:iTypeId },

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


	$(document).on("click", "#DataGrid .icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/settings/toggle-school-type-status.php",
			{ TypeId:objIcon.id },

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

						objTable.fnUpdate("In-Active", objRow, iColumn, false);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objTable.fnUpdate("Active", objRow, iColumn, false);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});



	$("#frmRecord #txtType").blur(function( )
	{
		if ($("#frmRecord #txtType").val( ) == "")
			return;

		$.post("ajax/settings/check-school-type.php",
			{ Type:$("#frmRecord #txtType").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified School Type already exists in the System.");

					$("#DuplicateType").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateType").val("0");
				}
			},

			"text");
	});


	$("#frmRecord #BtnReset").click(function( )
	{
		$('#frmRecord')[0].reset( );
		$("#RecordMsg").hide( );
		
		$("#txtType").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("txtType", "B", "Please enter the School Type."))
			return false;

		if (objFV.value("DuplicateType") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified School Type already exists in the System.");

			objFV.focus("txtType");
			objFV.select("txtType");

			return false;
		}


		$("#frmRecord #BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});
});


function updateRecord(iTypeId, iRow, sFields)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Type")
			objTable.fnUpdate(sFields[0], iRow, iIndex, false);

		else if ($(this).text( ) == "Status")
			objTable.fnUpdate(sFields[1], iRow, iIndex, false);
	});


	$(".icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iTypeId)
			$(this).attr("src", sFields[2]);
	});
}