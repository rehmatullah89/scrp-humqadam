
/*********************************************************************************************\
***********************************************************************************************
**                                                                                           **
**  SCRP - School Construction and Rehabilitation Programme                                  **
**  Version 1.0                                                                              **
**                                                                                           **
**  http://www.3-tree.com/imc/                                                               **
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
	if (parseInt($("#TotalRecords").val( )) > 100)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
						       aoColumnDefs    : [ { bSortable:false, aTargets:[6] } ],
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
						       sAjaxSource     : "ajax/management/get-messages.php",

						       fnServerData    : function (sSource, aoData, fnCallback)
									 {
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
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
						       aoColumnDefs    : [ { bSortable:false, aTargets:[6] } ],
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
						       bAutoWidth      : false } );
	}


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
		var sMessages       = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sMessages != "")
					sMessages += ",";

				sMessages += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sMessages != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/management/delete-message.php",
												    { Messages:sMessages },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#PageMsg", sParams[0], sParams[1]);

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


	$(document).on("click", ".icnView", function(event)
	{
		var iMessageId = this.id;

		$.colorbox({ href:("management/message-details.php?MessageId=" + iMessageId), width:"800px", height:"560px", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iMessageId = this.id;
		var objRow     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/management/delete-message.php",
											{ Messages:iMessageId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#PageMsg", sParams[0], sParams[1]);

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