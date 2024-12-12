
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
	if ($("#txtDescription").length > 0)
		$("#txtDescription").ckeditor({ height:"300px" }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });


	$("#txtName, #ddProvince").change(function( )
	{
		var sUrl = $("#txtName").val( );

		sUrl = sUrl.getSefUrl( );

		$("#txtSefUrl").val(sUrl);

		if (sUrl != "")
		{
			if (parseInt($("#ddProvince").val( )) > 0)
				sUrl = ($("#ddProvince :selected").attr("sefUrl") + sUrl);

			$("#Url").val(sUrl);
			$("#SefUrl").html("/" + sUrl);
		}
	});


	$("#txtName, #ddProvince, #txtSefUrl").blur(function( )
	{
		var sUrl = $("#txtSefUrl").val( );

		if (sUrl == "")
			return;


		sUrl = sUrl.getSefUrl( );

		$("#txtSefUrl").val(sUrl);


		if (parseInt($("#ddProvince").val( )) > 0)
			sUrl = ($("#ddProvince :selected").attr("sefUrl") + sUrl);

		$("#Url").val(sUrl);
		$("#SefUrl").html("/" + sUrl);


		$.post("ajax/settings/check-district.php",
			{ SefUrl:sUrl },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The District SEF URL is already used. Please specify another URL.");

					$("#DuplicateDistrict").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateDistrict").val("0");
				}
			},

			"text");
	});


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );

		$("#txtDescription").val("");
		$("#Url").val("");
		$("#SefUrl").html("");

		$("#txtName").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the District Name."))
			return false;

		if (!objFV.validate("ddProvince", "B", "Please select the District Province."))
			return false;

		if (!objFV.validate("txtSefUrl", "B", "Please enter the SEF URL."))
			return false;

		if (!objFV.validate("txtLatitude", "B", "Please enter the Map Coordinates (Latitude)."))
			return false;

		if (!objFV.validate("txtLongitude", "B", "Please enter the Map Coordinates (Longitude)."))
			return false;
/*
		if (!objFV.validate("filePicture", "B", "Please select the District Picture."))
			return false;
*/
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

		if (objFV.value("DuplicateDistrict") == "1")
		{
			showMessage("#RecordMsg", "info", "The District SEF URL is already used. Please specify another URL.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

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
											   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4,5] } ],
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,
											   bServerSide     : true,
											   sAjaxSource     : "ajax/settings/get-districts.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
													 {
														if ($("div.toolbar #Province").length > 0)
															aoData.push({ name:"Province", value:$("div.toolbar #Province").val( ) });


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
														$.post("ajax/settings/get-district-filters.php",
															   {},

															   function (sResponse)
															   {
																$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iProvince = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "Province")
																iProvince = iIndex;
														});

														this.fnFilter("", iProvince);


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
											   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4,5] } ],
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,

											   fnDrawCallback  : function( ) { setTimeout(function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); }, 0); },

											   fnInitComplete  : function( )
													 {
														$.post("ajax/settings/get-district-filters.php",
															   {},

															   function (sResponse)
															   {
																$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iProvince = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "Province")
																iProvince = iIndex;
														});

														this.fnFilter("", iProvince);
													 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iProvince = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Province")
				iProvince = iIndex;
		});


		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sProvince = "";

		if ($("div.toolbar #Province").length > 0)
			sProvince = $("div.toolbar #Province").val( );

		if (parseInt($("#TotalRecords").val( )) <= 100)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if (sProvince == "" || objTable.fnGetData(objRows[i])[iProvince] == sProvince)
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


	$(document).on("change", "div.toolbar #Province", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Province")
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
		var sDistricts      = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sDistricts != "")
					sDistricts += ",";

				sDistricts += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sDistricts != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/settings/delete-district.php",
												    { Districts:sDistricts },

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
		var iDistrictId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("settings/edit-district.php?DistrictId=" + iDistrictId + "&Index=" + iIndex), width:"85%", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iDistrictId = this.id;

		$.colorbox({ href:("settings/view-district.php?DistrictId=" + iDistrictId), width:"85%", height:"85%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/settings/toggle-district-status.php",
			{ DistrictId:objIcon.id },

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
		var iDistrictId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/settings/delete-district.php",
											{ Districts:iDistrictId },

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


function updateRecord(iDistrictId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 100)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "SEF URL")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Province")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[4], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );


	$.post("ajax/settings/get-district-filters.php",
	       {},

	       function (sResponse)
	       {
	       		var sProvince = $("div.toolbar #Province").val( );

				$("div.toolbar").html(sResponse);

	       		$("div.toolbar #Province").val(sProvince);
	       },

	       "text");
}


function updateOptions(iRow, sOptions)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Options")
			objTable.fnUpdate(sOptions, iRow, iIndex);
	});
}