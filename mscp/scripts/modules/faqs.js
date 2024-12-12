
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

var objFaqsTable;
var objCategoriesTable;

$(document).ready(function( )
{
	$("#frmFaq #BtnSave").button({ icons:{ primary:'ui-icon-disk' } });
	$("#frmFaq #BtnReset").button({ icons:{ primary:'ui-icon-refresh' } });
	$("#frmCategory #BtnSave").button({ icons:{ primary:'ui-icon-disk' } });
	$("#frmCategory #BtnReset").button({ icons:{ primary:'ui-icon-refresh' } });

	$("#BtnFaqSelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnFaqSelectNone").button({ icons:{ primary:'ui-icon-cancel' } });
	$("#BtnCategorySelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnCategorySelectNone").button({ icons:{ primary:'ui-icon-cancel' } });

	if ($("#txtAnswer").length > 0)
		$("#txtAnswer").ckeditor({ height:"250px" }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });


	$("#frmFaq #BtnReset").click(function( )
	{
		$("#frmFaq")[0].reset( );
		$("#FaqMsg").hide( );
		$("#txtQuestion").focus( );

		$("#txtAnswer").val("");

		return false;
	});


	$("#frmFaq").submit(function( )
	{
		var objFV = new FormValidator("frmFaq", "FaqMsg");


		if (!objFV.validate("txtQuestion", "B", "Please enter the Question."))
			return false;

		if ($("#txtAnswer").val() == "")
		{
			showMessage("#FaqMsg", "alert", "Please enter the Answer.");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#FaqMsg").hide( );
	});






	objFaqsTable = $("#FaqsGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
						   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
						   bJQueryUI       : true,
						   sPaginationType : "full_numbers",
						   bPaginate       : false,
						   bLengthChange   : false,
						   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
						   bFilter         : true,
						   bSort           : true,
						   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4] } ],
						   bInfo           : true,
						   bStateSave      : false,
						   bProcessing     : false,
						   bAutoWidth      : false,
						   
						   fnDrawCallback  : function( ) { setTimeout(function( ) { initTableSorting("#FaqsGrid", "#FaqsGridMsg", objFaqsTable); }, 0); },

						   fnInitComplete  : function( )
											 {
												$.post("ajax/modules/get-faq-filters.php",
													   {},

													   function (sResponse)
													   {
														$("#tabs-1 div.toolbar").html(sResponse);
													   },

													   "text");


												var iCategory = 0;

												$("#FaqsGrid thead tr th").each(function(iIndex)
												{
													if ($(this).text( ) == "Category")
														iCategory = iIndex;
												});

												this.fnFilter("", iCategory);
											 }
						 } );


	$("#BtnFaqSelectAll").click(function( )
	{
		var iCategory = 0;

		$("#FaqsGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Category")
				iCategory = iIndex;
		});


		var objRows   = objFaqsTable.fnGetNodes( );
		var bSelected = false;
		var sCategory = "";

		if ($("#tabs-1 div.toolbar #Category").length > 0)
			sCategory = $("#tabs-1 div.toolbar #Category").val( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (sCategory == "" || objFaqsTable.fnGetData(objRows[i])[iCategory] == sCategory)
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

		if (bSelected == true)
			$("#BtnFaqMultiDelete").show( );
	});


	$("#BtnFaqSelectNone").click(function( )
	{
		var objRows = objFaqsTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnFaqMultiDelete").hide( );
	});


	$(document).on("change", "#tabs-1 div.toolbar #Category", function( )
	{
		var objRows = objFaqsTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnFaqMultiDelete").hide( );


		var iColumn = 0;

		$("#FaqsGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Category")
				iColumn = iIndex;
		});


		objFaqsTable.fnFilter($(this).val( ), iColumn);


		$("#FaqsGrid td.position").each(function(iIndex)
		{
			var objRow = objFaqsTable.fnGetPosition($(this).closest('tr')[0]);

			objFaqsTable.fnUpdate((iIndex + 1), objRow, 0);
		});

		objFaqsTable.fnDraw( );
	});


	$(document).on("click", "#FaqsGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objFaqsTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnFaqMultiDelete").show( );

		else
			$("#BtnFaqMultiDelete").hide( );
	});


	$("#tabs-1 .TableTools").prepend('<button id="BtnFaqMultiDelete">Delete Selected Rows</button>')
	$("#BtnFaqMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnFaqMultiDelete").hide( );


	$("#BtnFaqMultiDelete").click(function( )
	{
		var sFaqs           = "";
		var objSelectedRows = new Array( );

		var objRows = objFaqsTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sFaqs != "")
					sFaqs += ",";

				sFaqs += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sFaqs != "")
		{
			$("#ConfirmMultiFaqDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/modules/delete-faq.php",
												    { Faqs:sFaqs },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#FaqsGridMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objFaqsTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnFaqMultiDelete").hide( );


														  if ($("#SelectFaqButtons").length == 1)
														  {
														  	if (objFaqsTable.fnGetNodes( ).length > 5 && $("#FaqsGrid .icnDelete").length > 0)
																$("#SelectFaqButtons").show( );

														  	else
																$("#SelectFaqButtons").hide( );
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



	$(document).on("click", "#FaqsGrid .icnEdit", function(event)
	{
		var iFaqId = this.id;
		var iIndex = objFaqsTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("modules/edit-faq.php?FaqId=" + iFaqId + "&Index=" + iIndex), width:"90%", height:"90%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#FaqsGrid .icnView", function(event)
	{
		var iFaqId = this.id;

		$.colorbox({ href:("modules/view-faq.php?FaqId=" + iFaqId), width:"90%", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", "#FaqsGrid .icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objFaqsTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/modules/toggle-faq-status.php",
			{ FaqId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#FaqsGridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					var iColumn = 0;

					$("#FaqsGrid thead tr th").each(function(iIndex)
					{
						if ($(this).text( ) == "Status")
							iColumn = iIndex;
					});


					if (objIcon.src.indexOf("success.png") != -1)
					{
						objIcon.src = objIcon.src.replace("success.png", "error.png");

						objFaqsTable.fnUpdate("In-Active", objRow, iColumn);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objFaqsTable.fnUpdate("Active", objRow, iColumn);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", "#FaqsGrid .icnDelete", function(event)
	{
		var iFaqId = this.id;
		var objRow = objFaqsTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmFaqDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/modules/delete-faq.php",
											{ Faqs:iFaqId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#FaqsGridMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objFaqsTable.fnDeleteRow(objRow);


											  	if ($("#SelectFaqButtons").length == 1)
											  	{
											  		if (objFaqsTable.fnGetNodes( ).length > 5 && $("#FaqsGrid .icnDelete").length > 0)
														$("#SelectFaqButtons").show( );

											  		else
														$("#SelectFaqButtons").hide( );
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









	$("#txtName").blur(function( )
	{
		if ($(this).val( ) == "")
			return;


		$.post("ajax/modules/check-faq-category.php",
			{ Name:$(this).val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The specified Category Name is already used. Please specify another Name.");

					$("#DuplicateCategory").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateCategory").val("0");
				}
			},

			"text");
	});


	$("#frmCategory #BtnReset").click(function( )
	{
		$("#frmCategory")[0].reset( );
		$("#CategoryMsg").hide( );
		$("#txtName").focus( );

		return false;
	});


	$("#frmCategory").submit(function( )
	{
		var objFV = new FormValidator("frmCategory", "CategoryMsg");


		if (!objFV.validate("txtName", "B", "Please enter the Category Name."))
			return false;

		if (objFV.value("DuplicateCategory") == "1")
		{
			showMessage("#RecordMsg", "info", "The specified Category Name is already used. Please specify another Name.");

			objFV.focus("txtName");
			objFV.select("txtName");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#CategoryMsg").hide( );
	});



	objCategoriesTable = $("#CategoriesGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
								   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
								   bJQueryUI       : true,
								   sPaginationType : "full_numbers",
								   bPaginate       : false,
								   bLengthChange   : false,
								   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
								   bFilter         : true,
								   bSort           : true,
								   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3] } ],
								   bInfo           : true,
								   bStateSave      : false,
								   bProcessing     : false,
								   bAutoWidth      : false,
								   fnDrawCallback  : function( ) { setTimeout(function( ) { initTableSorting("#CategoriesGrid", "#CategoriesGridMsg", objCategoriesTable); }, 0); }
							      } );


	$("#BtnCategorySelectAll").click(function( )
	{
		var objRows = objCategoriesTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnCategoryMultiDelete").show( );
	});


	$("#BtnCategorySelectNone").click(function( )
	{
		var objRows = objCategoriesTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnCategoryMultiDelete").hide( );
	});


	$(document).on("click", "#CategoriesGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objCategoriesTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnCategoryMultiDelete").show( );

		else
			$("#BtnCategoryMultiDelete").hide( );
	});


	$("#tabs-3 .TableTools").prepend('<button id="BtnCategoryMultiDelete">Delete Selected Rows</button>')
	$("#BtnCategoryMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnCategoryMultiDelete").hide( );


	$("#BtnCategoryMultiDelete").click(function( )
	{
		var sCategories     = "";
		var objSelectedRows = new Array( );

		var objRows = objCategoriesTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sCategories != "")
					sCategories += ",";

				sCategories += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sCategories != "")
		{
			$("#ConfirmMultiCategoryDelete").dialog( { resizable : false,
													   width     : 420,
													   height    : 110,
													   modal     : true,
													   buttons   : { "Delete" : function( )
																	{
																		 $.post("ajax/modules/delete-faq-category.php",
																			{ Categories:sCategories },

																			function (sResponse)
																			{
																				var sParams = sResponse.split("|-|");

																				showMessage("#CategoriesGridMsg", sParams[0], sParams[1]);

																				if (sParams[0] == "success")
																				{
																					 for (var i = 0; i < objSelectedRows.length; i ++)
																					  objCategoriesTable.fnDeleteRow(objSelectedRows[i]);

																					  $("#BtnCategoryMultiDelete").hide( );


																				  if ($("#SelectCategoryButtons").length == 1)
																				  {
																					if (objCategoriesTable.fnGetNodes( ).length > 5 && $("#CategoriesGrid .icnDelete").length > 0)
																						$("#SelectCategoryButtons").show( );

																					else
																						$("#SelectCategoryButtons").hide( );
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




	$(document).on("click", "#CategoriesGrid .icnEdit", function(event)
	{
		var iCategoryId = this.id;
		var iIndex      = objCategoriesTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("modules/edit-faq-category.php?CategoryId=" + iCategoryId + "&Index=" + iIndex), width:"500", height:"450", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#CategoriesGrid .icnView", function(event)
	{
		var iCategoryId = this.id;

		$.colorbox({ href:("modules/view-faq-category.php?CategoryId=" + iCategoryId), width:"500", height:"400", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", "#CategoriesGrid .icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objCategoriesTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/modules/toggle-faq-category-status.php",
			{ CategoryId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#CategoriesGridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					var iColumn = 0;

					$("#CategoriesGrid thead tr th").each(function(iIndex)
					{
						if ($(this).text( ) == "Status")
							iColumn = iIndex;
					});


					if (objIcon.src.indexOf("success.png") != -1)
					{
						objIcon.src = objIcon.src.replace("success.png", "error.png");

						objCategoriesTable.fnUpdate("In-Active", objRow, iColumn);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objCategoriesTable.fnUpdate("Active", objRow, iColumn);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", "#CategoriesGrid .icnDelete", function(event)
	{
		var iCategoryId = this.id;
		var objRow      = objCategoriesTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmCategoryDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/modules/delete-faq-category.php",
																		{ Categories:iCategoryId },

																		function (sResponse)
																		{
																			var sParams = sResponse.split("|-|");

																			showMessage("#CategoriesGridMsg", sParams[0], sParams[1]);

																			if (sParams[0] == "success")
																				objCategoriesTable.fnDeleteRow(objRow);


																			if ($("#SelectCategoryButtons").length == 1)
																			{
																				if (objCategoriesTable.fnGetNodes( ).length > 5 && $("#CategoriesGrid .icnDelete").length > 0)
																					$("#SelectCategoryButtons").show( );

																				else
																					$("#SelectCategoryButtons").hide( );
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


function updateFaqRecord(iFaqId, iRow, sFields)
{
	$("#FaqsGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Question")
			objFaqsTable.fnUpdate(sFields[0], iRow, iIndex);

		else if ($(this).text( ) == "Category")
			objFaqsTable.fnUpdate(sFields[1], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objFaqsTable.fnUpdate(sFields[2], iRow, iIndex);
	});


	$("#FaqsGrid .icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iFaqId)
			$(this).attr("src", sFields[3]);
	});
}


function updateCategoryRecord(iCategoryId, iRow, sFields)
{
	$("#CategoriesGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Name")
			objCategoriesTable.fnUpdate(sFields[0], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objCategoriesTable.fnUpdate(sFields[1], iRow, iIndex);
	});


	$("#CategoriesGrid .icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iCategoryId)
			$(this).attr("src", sFields[2]);
	});
}