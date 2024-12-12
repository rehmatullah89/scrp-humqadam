
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
	$("#txtTitle").change(function( )
	{
		var sUrl = $("#txtTitle").val( );

		$("#txtSefUrl").val(sUrl.getSefUrl(".html"));
	});


	$("#txtTitle, #txtSefUrl").blur(function( )
	{
		var sUrl = $("#txtSefUrl").val( );

		if (sUrl == "")
			return;


		sUrl = sUrl.getSefUrl(".html");

		$("#txtSefUrl").val(sUrl);


		$.post("ajax/contents/check-web-page.php",
			{ SefUrl:sUrl },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The SEF URL is already used. Please specify another URL.");

					$("#DuplicatePage").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicatePage").val("0");
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


		if (!objFV.validate("txtTitle", "B", "Please enter the Page Name."))
			return false;

		if (!objFV.validate("txtSefUrl", "B", "Please enter the SEF URL."))
			return false;

		if (objFV.value("DuplicatePage") == "1")
		{
			showMessage("#RecordMsg", "info", "The SEF URL is already used. Please specify another URL.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});


	objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
					       oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
					       bJQueryUI       : true,
					       sPaginationType : "full_numbers",
					       bPaginate       : false,
					       bLengthChange   : false,
					       iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
					       bFilter         : true,
					       bSort           : true,
					       aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4,5] } ],
					       bInfo           : true,
					       bStateSave      : false,
					       bProcessing     : false,
					       bAutoWidth      : false,
					       fnDrawCallback  : function( ) { setTimeout(function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); }, 0); }
					     } );



	$(document).on("click", "#DataGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("noDelete"))
			return;


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
		var sWebPages       = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sWebPages != "")
					sWebPages += ",";

				sWebPages += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sWebPages != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/contents/delete-web-page.php",
												    { Pages:sWebPages },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#GridMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnMultiDelete").hide( );
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
		var iPageId = this.id;
		var iIndex  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("contents/edit-web-page.php?PageId=" + iPageId + "&Index=" + iIndex), width:"450", height:"400", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/contents/toggle-web-page-status.php",
			{ PageId:objIcon.id },

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

						objTable.fnUpdate("Draft", objRow, iColumn);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objTable.fnUpdate("Published", objRow, iColumn);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iPageId = this.id;
		var objRow   = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/contents/delete-web-page.php",
											{ Pages:iPageId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#GridMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objTable.fnDeleteRow(objRow);
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


function updateRecord(iPageId, iRow, sFields)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Page Title")
			objTable.fnUpdate(sFields[0], iRow, iIndex);

		else if ($(this).text( ) == "SEF URL" || $(this).text( ) == "PHP URL")
			objTable.fnUpdate(sFields[1], iRow, iIndex);

		else if ($(this).text( ) == "Placement")
			objTable.fnUpdate(sFields[2], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objTable.fnUpdate(sFields[3], iRow, iIndex);
	});


	$(".icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iPageId)
			$(this).attr("src", sFields[4]);
	});
}