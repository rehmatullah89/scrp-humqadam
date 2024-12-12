
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

var objDbTable;
var objWebTable;

$(document).ready(function( )
{
	$("#BtnDbBackup").button({ icons:{ primary:'ui-icon-disk' } });
	$("#BtnWebBackup").button({ icons:{ primary:'ui-icon-disk' } });

	$("#BtnDbSelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnDbSelectNone").button({ icons:{ primary:'ui-icon-cancel' } });

	$("#BtnWebSelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnWebSelectNone").button({ icons:{ primary:'ui-icon-cancel' } });


	$("#BtnDbBackup, #BtnWebBackup").click(function( )
	{
		$("body").append('<div id="Overlay"></div>');
		$("#Overlay").addClass("ui-widget-overlay");
		$("#Overlay").css({ width:($(window).width( ) + "px"), height:($(window).height( ) + "px"), zIndex:99999999 });
	});





	objDbTable = $("#DbGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
					       aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
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


	$("#BtnDbSelectAll").click(function( )
	{
		var objRows = objDbTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnMultiDbDelete").show( );
	});


	$("#BtnDbSelectNone").click(function( )
	{
		var objRows = objDbTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDbDelete").hide( );
	});


	$(document).on("click", "#DbGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objDbTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnMultiDbDelete").show( );

		else
			$("#BtnMultiDbDelete").hide( );
	});


	$("#tabs-1 .TableTools").prepend('<button id="BtnMultiDbDelete">Delete Selected Rows</button>')
	$("#BtnMultiDbDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnMultiDbDelete").hide( );


	$("#BtnMultiDbDelete").click(function( )
	{
		var sBackups        = "";
		var objSelectedRows = new Array( );

		var objRows = objDbTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sBackups != "")
					sBackups += ",";

				sBackups += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sBackups != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/management/delete-backup.php",
												    { Type:"Database", Backups:sBackups },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#PageMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objDbTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnMultiDbDelete").hide( );


														  if ($("#SelectDbButtons").length == 1)
														  {
														  	if (objDbTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
																$("#SelectDbButtons").show( );

														  	else
																$("#SelectDbButtons").hide( );
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


	$(document).on("click", "#DbGrid .icnRestore", function(event)
	{
		var sActionUrl = this.id;

		$("#ConfirmRestore").dialog( { resizable : false,
		                               width     : 420,
		                               height    : 135,
		                               modal     : true,
		                               buttons   : { "Restore" : function( )
		                                                         {
										$("body").append('<div id="Overlay"></div>');
										$("#Overlay").addClass("ui-widget-overlay");
										$("#Overlay").css({ width:($(window).width( ) + "px"), height:($(window).height( ) + "px"), zIndex:99999999 });

		                                                       		document.location = sActionUrl;
		                                                         },

		                                              Cancel  : function( )
		                                                        {
		                                                            	$(this).dialog("close");

		                                                            	return false;
		                                                        }
		                                          }
		                            });

		event.stopPropagation( );
	});


	$(document).on("click", "#DbGrid .icnDelete", function(event)
	{
		var sBackup = this.id;
		var objRow  = objDbTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/management/delete-backup.php",
											{ Type:"Database", Backups:sBackup },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#PageMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objDbTable.fnDeleteRow(objRow);


											  	if ($("#SelectDbButtons").length == 1)
											  	{
											  		if (objDbTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
														$("#SelectDbButtons").show( );

											  		else
														$("#SelectDbButtons").hide( );
												}
											},

											"text");

		                                                      	    	$(this).dialog("close");
		                                                       },

		                                             Cancel  : function( )
		                                                       {
		                                                            	$(this).dialog("close");

		                                                            	return false;
		                                                       }
		                                          }
		                            });

		event.stopPropagation( );
	});









	objWebTable = $("#WebGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
					         aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
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


	$("#BtnWebSelectAll").click(function( )
	{
		var objRows = objWebTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnMultiWebDelete").show( );
	});


	$("#BtnWebSelectNone").click(function( )
	{
		var objRows = objWebTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiWebDelete").hide( );
	});


	$(document).on("click", "#WebGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objWebTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnMultiWebDelete").show( );

		else
			$("#BtnMultiWebDelete").hide( );
	});


	$("#tabs-2 .TableTools").prepend('<button id="BtnMultiWebDelete">Delete Selected Rows</button>')
	$("#BtnMultiWebDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnMultiWebDelete").hide( );


	$("#BtnMultiWebDelete").click(function( )
	{
		var sBackups        = "";
		var objSelectedRows = new Array( );

		var objRows = objWebTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sBackups != "")
					sBackups += ",";

				sBackups += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sBackups != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/management/delete-backup.php",
												    { Type:"Website", Backups:sBackups },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#PageMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objWebTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnMultiWebDelete").hide( );


														  if ($("#SelectWebButtons").length == 1)
														  {
														  	if (objWebTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
																$("#SelectWebButtons").show( );

														  	else
																$("#SelectWebButtons").hide( );
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


	$(document).on("click", "#WebGrid .icnDelete", function(event)
	{
		var sBackup = this.id;
		var objRow  = objWebTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/management/delete-backup.php",
											{ Type:"Website", Backups:sBackup },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#PageMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objWebTable.fnDeleteRow(objRow);


											  	if ($("#SelectWebButtons").length == 1)
											  	{
											  		if (objWebTable.fnGetNodes( ).length > 5 && $("#DataGrid .icnDelete").length > 0)
														$("#SelectWebButtons").show( );

											  		else
														$("#SelectWebButtons").hide( );
												}
											},

											"text");

		                                                      	    	$(this).dialog("close");
		                                                       },

		                                             Cancel  : function( )
		                                                       {
		                                                            	$(this).dialog("close");

		                                                            	return false;
		                                                       }
		                                          }
		                            });

		event.stopPropagation( );
	});
});