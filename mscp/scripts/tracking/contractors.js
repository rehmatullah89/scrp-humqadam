
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
	$("#txtCompany").blur(function( )
	{
		if ($("#txtCompany").val( ) == "")
			return;


		$.post("ajax/tracking/check-contractor.php",
			{ Company:$("#txtCompany").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided Company Name is already in use. Please provide another Name.");

					$("#DuplicateContractor").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateContractor").val("0");
				}
			},

			"text");
	});


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#txtFirstName").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtCompany", "B", "Please enter the Company Name."))
			return false;

//		if (!objFV.validate("txtAddress", "B", "Please enter the Address."))
//			return false;

		if (!objFV.validate("txtCity", "B", "Please enter the City Name."))
			return false;

		if (objFV.value("fileLogo") != "")
		{
			if (!checkImage(objFV.value("fileLogo")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("fileLogo");
				objFV.select("fileLogo");

				return false;
			}
		}


		if (!objFV.validate("txtFirstName", "B", "Please enter the First Name."))
			return false;

		if (!objFV.validate("txtLastName", "B", "Please enter the Last Name."))
			return false;

		if (!objFV.validate("txtPhone", "B", "Please enter the Phone Number."))
			return false;

//		if (!objFV.validate("txtMobile", "B", "Please enter the Mobile Number."))
//			return false;

		if (!objFV.validate("txtEmail", "E", "Please enter a valid Email Address."))
			return false;

		if (objFV.value("filePicture") != "")
		{
			if (!checkImage(objFV.value("filePicture")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("filePicture");
				objFV.select("filePicture");

				return false;
			}
		}


		if (objFV.value("DuplicateContractor") == "1")
		{
			showMessage("#RecordMsg", "info", "The provided Company Name is already in use. Please provide another Name.");

			objFV.focus("txtCompany");
			objFV.select("txtCompany");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});



	if (parseInt($("#TotalRecords").val( )) > 100)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
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
											   bAutoWidth      : false,
											   bServerSide     : true,
											   sAjaxSource     : "ajax/tracking/get-contractors.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #City").length > 0)
																		aoData.push({ name:"City", value:$("div.toolbar #City").val( ) });


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
																	$.post("ajax/tracking/get-contractor-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iCity = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "City")
																			iCity = iIndex;
																	});


																	this.fnFilter("", iCity);


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
											   bAutoWidth      : false,

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/tracking/get-contractor-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iCity = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "City")
																			iCity = iIndex;
																	});


																	this.fnFilter("", iCity);
																 }
													   } );
	}


	$("#BtnSelectAll").click(function( )
	{
		var iCity = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "City")
				iCity = iIndex;
		});



		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sCity     = "";

		if ($("div.toolbar #City").length > 0)
			sCity = $("div.toolbar #City").val( );


		if (parseInt($("#TotalRecords").val( )) <= 100)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if (sCity == "" || sCity == objTable.fnGetData(objRows[i])[iCity])
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


	$(document).on("change", "div.toolbar #City", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "City")
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


	$(document).on("click", "#BtnMultiDelete", function( )
	{
		var sContractors    = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sContractors != "")
					sContractors += ",";

				sContractors += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sContractors != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
							       width     : 420,
							       height    : 110,
							       modal     : true,
							       buttons   : { "Delete" : function( )
															{
																 $.post("ajax/tracking/delete-contractor.php",
																	{ Contractors:sContractors },

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


		return false;
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/tracking/toggle-contractor-status.php",
			{ ContractorId:objIcon.id },

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


	$(document).on("click", "#DataGrid .icnEdit", function(event)
	{
		var iContractorId = this.id;
		var iIndex        = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-contractor.php?ContractorId=" + iContractorId + "&Index=" + iIndex), width:"900px", height:"620px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnBoqs", function(event)
	{
		var iContractorId = this.id;

		$.colorbox({ href:("tracking/edit-contractor-boqs.php?ContractorId=" + iContractorId), width:"800px", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iContractorId = this.id;

		$.colorbox({ href:("tracking/view-contractor.php?ContractorId=" + iContractorId), width:"900px", height:"600px", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnDelete", function(event)
	{
		var iContractorId = this.id;
		var objRow        = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
									  width     : 420,
									  height    : 110,
									  modal     : true,
									  buttons   : { "Delete" : function( )
															   {
																	$.post("ajax/tracking/delete-contractor.php",
																		{ Contractors:iContractorId },

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


function updateRecord(iContractorId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 100)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Company")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "City")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Phone")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Mobile")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Email")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}