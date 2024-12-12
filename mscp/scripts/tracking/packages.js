
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
    $("#txtSchools").tokenInput("ajax/get-schools-list.php",
    {
		queryParam         :  "School",
		minChars           :  2,
		tokenLimit         :  1000,
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


		$.post("ajax/tracking/check-package.php",
			{ Title:$(this).val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Package Title is already used. Please specify another Title.");

					$("#DuplicatePackage").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicatePackage").val("0");
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

		if (!objFV.validate("txtSchools", "B", "Please select Schools included in this Package."))
			return false;


		if (objFV.value("DuplicatePackage") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Package Title is already used. Please specify another Title.");

			objFV.focus("txtTitle");
			objFV.select("txtTitle");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});





	objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
										   aoColumnDefs    : [ { bSortable:false, aTargets:[7] } ],
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
		var sPackages       = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sPackages != "")
					sPackages += ",";

				sPackages += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sPackages != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
													 $.post("ajax/tracking/delete-package.php",
														{ Packages:sPackages },

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
		var iPackageId = this.id;
		var iIndex     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-package.php?PackageId=" + iPackageId + "&Index=" + iIndex), width:"950", height:"520", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iPackageId = this.id;

		$.colorbox({ href:("tracking/view-package.php?PackageId=" + iPackageId), width:"950", height:"500", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});

	
    $(document).on("click", ".icnLots", function(event)
	{
		var iPackageId = this.id;
        var iLots      = $(this).attr("lots");

		$.colorbox({ href:("tracking/package-lots.php?PackageId=" + iPackageId+'&Lots='+iLots), width:"700px", height:"85%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});

	
	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/tracking/toggle-package-status.php",
			{ PackageId:objIcon.id },

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
		var iPackageId = this.id;
		var objRow     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/tracking/delete-package.php",
																		{ Packages:iPackageId },

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


function updateRecord(iPackageId, iRow, iLots, sFields)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Title")
			objTable.fnUpdate(sFields[0], iRow, iIndex);

                else if ($(this).text( ) == "Lots")
			objTable.fnUpdate(sFields[1], iRow, iIndex);
                
                else if ($(this).text( ) == "Classes")
			objTable.fnUpdate(sFields[2], iRow, iIndex);

		else if ($(this).text( ) == "Toilets")
			objTable.fnUpdate(sFields[3], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objTable.fnUpdate(sFields[4], iRow, iIndex);
	});


	$(".icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iPackageId)
			$(this).attr("src", sFields[5]);
	});
        
        $(".icnLots").each(function(iIndex)
	{
		if ($(this).attr("id") == iPackageId)
			$(this).attr("lots", iLots);
	});
       
}