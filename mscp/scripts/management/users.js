
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

var objUserTable;
var objTypeTable;

$(document).ready(function( )
{
	$("#frmUser #BtnSave").button({ icons:{ primary:'ui-icon-disk' } });
	$("#frmUser #BtnReset").button({ icons:{ primary:'ui-icon-refresh' } });
	$("#frmType #BtnSave").button({ icons:{ primary:'ui-icon-disk' } });
	$("#frmType #BtnReset").button({ icons:{ primary:'ui-icon-refresh' } });

	$("#BtnUserSelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnUserSelectNone").button({ icons:{ primary:'ui-icon-cancel' } });
	$("#BtnTypeSelectAll").button({ icons:{ primary:'ui-icon-check' } });
	$("#BtnTypeSelectNone").button({ icons:{ primary:'ui-icon-cancel' } });


	$("#txtMobile").mask("03nnnnnnnnn", { placeholder:"" });

	
    setSchoolsSelection( );


    $(document).on("click", ".province, .district", function( )
    {
		setSchoolsSelection( );
	});	


	$("#txtEmail").blur(function( )
	{
		if ($("#txtEmail").val( ) == "")
			return;


		$.post("ajax/management/check-user.php",
			{ Email:$("#txtEmail").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#UserMsg", "info", "The provided email address is already in use. Please provide another email address.");

					$("#DuplicateEmail").val("1");
				}

				else
				{
					$("#UserMsg").hide( );
					$("#DuplicateEmail").val("0");
				}
			},

			"text");
	});


	$("#frmUser input.province").click(function( )
	{
		$("#frmUser input.province").each(function( )
		{
			var iProvince = $(this).val( );
			var sClass    = ("province" + iProvince);


			if ($(this).prop("checked") == true)
			{
				$("#frmUser .district").each(function( )
				{
					if ($(this).hasClass(sClass))
						$(this).prop("checked", true);
				});
			}

			else
			{
				$("#frmUser .district").each(function( )
				{
					if ($(this).hasClass(sClass))
						$(this).prop("checked", false);
				});
			}
		});
	});
	
	
	$(document).on("click", "label span a", function( )
	{
		var sData   = $(this).attr("rel");
		var sParams = sData.split("|");

		$("." + sParams[1]).each(function( )
		{
			if (sParams[0] == "Check")
				$(this).prop("checked", true);

			else
				$(this).prop("checked", false);
		});


		setSchoolsSelection( );


		return false;
	});	


	$("#frmUser #BtnReset").click(function( )
	{
		$("#frmUser")[0].reset( );
		$("#UserMsg").hide( );
		$("#txtName").focus( );

		return false;
	});


	$("#frmUser").submit(function( )
	{
		var objFV = new FormValidator("frmUser", "UserMsg");


		if (!objFV.validate("txtName", "B", "Please enter the Name."))
			return false;

		if (!objFV.validate("txtMobile", "B,N,L(11)", "Please enter a valid Mobile Number."))
			return false;

		if (!objFV.validate("txtEmail", "B,E", "Please enter a valid Email Address."))
			return false;

		if (!objFV.validate("txtPassword", "B,L(4)", "Please enter a valid password. (Min Length: 4 Characters)"))
			return false;

		if (!objFV.validate("ddType", "B", "Please select the User Type"))
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


		var bProvinces = false;
		var bDistricts = false;

		$("input.province").each(function( )
		{
				if ($(this).prop("checked") == true)
					bProvinces = true;
		});

		$("input.district").each(function( )
		{
				if ($(this).prop("checked") == true)
					bDistricts = true;
		});


		if (bProvinces == false)
		{
			showMessage("#UserMsg", "info", "Please select at-least One Province.");

			return false;
		}
/*
		if (bDistricts == false)
		{
			showMessage("#UserMsg", "info", "Please select at-least One District.");

			return false;
		}
*/

		if (objFV.value("DuplicateEmail") == "1")
		{
			showMessage("#UserMsg", "info", "The provided email address is already in use. Please provide another email address.");

			objFV.focus("txtEmail");
			objFV.select("txtEmail");

			return false;
		}


		$("#frmUser #BtnSave").attr('disabled', true);
		$("#UserMsg").hide( );
	});





	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objUserTable = $("#UserGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
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
											   bAutoWidth      : false,
											   bServerSide     : true,
											   sAjaxSource     : "ajax/management/get-users.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #Province").length > 0)
																		aoData.push({ name:"Province", value:$("div.toolbar #Province").val( ) });

																	if ($("div.toolbar #District").length > 0)
																		aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });

																	if ($("div.toolbar #Type").length > 0)
																		aoData.push({ name:"Type", value:$("div.toolbar #Type").val( ) });


																	$.getJSON(sSource, aoData, function(jsonData)
																	{
																		fnCallback(jsonData);


																		$("#UserGrid tbody tr").each(function(iIndex)
																		{
																			$(this).attr("id", $(this).find("img:first-child").attr("id"));
																			$(this).find("td:first-child").addClass("position");
																		});
																	});
																 },

											   fnInitComplete  : function( )
													 {
														$.post("ajax/management/get-user-filters.php",
															   {},

															   function (sResponse)
															   {
																	$("div.toolbar").html(sResponse);
															   },

															   "text");


														if ($("#SelectUserButtons").length == 1)
														{
															if (this.fnGetNodes( ).length > 5 && $("#UserGrid .icnDelete").length > 0)
																$("#SelectUserButtons").show( );

															else
																$("#SelectUserButtons").hide( );
														}
													 }
										   } );
	}

	else
	{
		objUserTable = $("#UserGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
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
											   bAutoWidth      : false,

											   fnInitComplete  : function( )
																 {

																 }
													   } );
	}


	$("#BtnUserSelectAll").click(function( )
	{
		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnUserMultiDelete").show( );
	});


	$("#BtnUserSelectNone").click(function( )
	{
		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnUserMultiDelete").hide( );
	});


	$(document).on("change", "div.toolbar #Province", function( )
	{
		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnUserMultiDelete").hide( );


		objUserTable.fnFilter($(this).val( ), 0);
	});


	$(document).on("change", "div.toolbar #District", function( )
	{
		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnUserMultiDelete").hide( );


		objUserTable.fnFilter($(this).val( ), 0);
	});


	$(document).on("change", "div.toolbar #Type", function( )
	{
		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnUserMultiDelete").hide( );


		var iColumn = 0;

		$("#UserGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Type")
				iColumn = iIndex;
		});


		objUserTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 100)
		{
			$("#UserGrid td.position").each(function(iIndex)
			{
				var objRow = objUserTable.fnGetPosition($(this).closest('tr')[0]);

				objUserTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objUserTable.fnDraw( );
		}
	});


	$(document).on("click", "#UserGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnUserMultiDelete").show( );

		else
			$("#BtnUserMultiDelete").hide( );
	});


	$("#tabs-1 .TableTools").prepend('<button id="BtnUserMultiDelete">Delete Selected Rows</button>')
	$("#BtnUserMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnUserMultiDelete").hide( );


	$("#BtnUserMultiDelete").click(function(event)
	{
		var sUsers          = "";
		var objSelectedRows = new Array( );

		var objRows = objUserTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sUsers != "")
					sUsers += ",";

				sUsers += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}


		if (sUsers != "")
		{
			$("#ConfirmUserMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/management/delete-user.php",
												    { Users:sUsers },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#UserGridMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objUserTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnUserMultiDelete").hide( );


														  if ($("#SelectUserButtons").length == 1)
														  {
														  	if (objUserTable.fnGetNodes( ).length > 5 && $("#UserGrid .icnDelete").length > 0)
																$("#SelectUserButtons").show( );

														  	else
																$("#SelectUserButtons").hide( );
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

		event.stopPropagation( );
	});


	$(document).on("click", "#UserGrid .icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objUserTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/management/toggle-user-status.php",
			{ UserId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#UserGridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					var iColumn = 0;

					$("#UserGrid thead tr th").each(function(iIndex)
					{
						if ($(this).text( ) == "Status")
							iColumn = iIndex;
					});



					if (objIcon.src.indexOf("success.png") != -1)
					{
						objIcon.src = objIcon.src.replace("success.png", "error.png");

						objUserTable.fnUpdate("Disabled", objRow, iColumn, false);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objUserTable.fnUpdate("Active", objRow, iColumn, false);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", "#UserGrid .icnEdit", function(event)
	{
		var iUserId = this.id;
		var iIndex  = objUserTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("management/edit-user.php?UserId=" + iUserId + "&Index=" + iIndex), width:"80%", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#UserGrid .icnView", function(event)
	{
		var iUserId = this.id;

		$.colorbox({ href:("management/view-user.php?UserId=" + iUserId), width:"80%", height:"85%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", "#UserGrid .icnDelete", function(event)
	{
		var iUserId = this.id;
		var objRow  = objUserTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmUserDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/management/delete-user.php",
											{ Users:iUserId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#UserGridMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objUserTable.fnDeleteRow(objRow);


											  	if ($("#SelectUserButtons").length == 1)
											  	{
											  		if (objUserTable.fnGetNodes( ).length > 5 && $("#UserGrid .icnDelete").length > 0)
														$("#SelectUserButtons").show( );

											  		else
														$("#SelectUserButtons").hide( );
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









	$("#View, #Add, #Edit, #Delete, #All").click(function( )
	{
		var iCount  = $("#PageCount").val( );
		var bStatus = true;

		for (var i = 0; i < iCount; i ++)
		{
			if ($("#cb" + this.id + i).is(":checked") == false)
			{
				bStatus = false;

				break;
			}
		}


		bStatus = ((bStatus == false) ? true : false);


		for (var i = 0; i < iCount; i ++)
		{
			if (this.id == "All")
			{
				$("#cb" + this.id + i).prop("checked", ((bStatus == true) ? false : true));
				$("#cb" + this.id + i).trigger("click");
			}

			else
				$("#cb" + this.id + i).prop("checked", bStatus);


			if ($("#cbAdd" + i).is(":checked") || $("#cbEdit" + i).is(":checked") || $("#cbDelete" + i).is(":checked"))
				$("#cbView" + i).prop("checked", true);

			if ($("#cbView" + i).is(":checked") && $("#cbAdd" + i).is(":checked") && $("#cbEdit" + i).is(":checked") && $("#cbDelete" + i).is(":checked"))
				$("#cbAll" + i).prop("checked", true);
		}

		return false;
	});


	$("input[type='checkbox']").click(function( )
	{
		var iId = this.id.replace("cbView", "").replace("cbAdd", "").replace("cbEdit", "").replace("cbDelete", "").replace("cbAll", "");

		if (this.id == ("cbAll" + iId))
		{
			if ($("#cbAll" + iId).is(":checked"))
			{
				$("#cbView" + iId).prop("checked", true);
				$("#cbAdd" + iId).prop("checked", true);
				$("#cbEdit" + iId).prop("checked", true);
				$("#cbDelete" + iId).prop("checked", true);
			}

			else
			{
				$("#cbView" + iId).prop("checked", false);
				$("#cbAdd" + iId).prop("checked", false);
				$("#cbEdit" + iId).prop("checked", false);
				$("#cbDelete" + iId).prop("checked", false);
			}
		}

		else
		{
			if ($("#cbAdd" + iId).is(":checked") || $("#cbEdit" + iId).is(":checked") || $("#cbDelete" + iId).is(":checked"))
				$("#cbView" + iId).prop("checked", true);


			if ($("#cbView" + iId).is(":checked") && $("#cbAdd" + iId).is(":checked") && $("#cbEdit" + iId).is(":checked") && $("#cbDelete" + iId).is(":checked"))
				$("#cbAll" + iId).prop("checked", true);

			else
				$("#cbAll" + iId).prop("checked", false);
		}
	});



	$("#txtTitle").blur(function( )
	{
		if ($("#txtTitle").val( ) == "")
			return;


		$.post("ajax/management/check-user-type.php",
			{ Title:$("#txtTitle").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#TypeMsg", "info", "The provided Title is already in use. Please provide another Title.");

					$("#DuplicateType").val("1");
				}

				else
				{
					$("#TypeMsg").hide( );
					$("#DuplicateType").val("0");
				}
			},

			"text");
	});




	$("#frmType #BtnReset").click(function( )
	{
		$("#frmType")[0].reset( );
		$("#TypeMsg").hide( );
		$("#txtTitle").focus( );

		return false;
	});


	$("#frmType").submit(function( )
	{
		var objFV = new FormValidator("frmType", "TypeMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the Title."))
			return false;


		$("#frmType #BtnSave").attr('disabled', true);
		$("#TypeMsg").hide( );
	});




	objTypeTable = $("#TypeGrid").dataTable( {     sDom            : '<"H"fCR>t<"F"ip>',
						       aoColumnDefs    : [ { bSortable:false, aTargets:[3] } ],
						       oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
						       bJQueryUI       : true,
						       sPaginationType : "full_numbers",
						       bPaginate       : true,
						       bLengthChange   : false,
						       iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
						       bFilter         : true,
						       bSort           : true,
						       bInfo           : true,
						       bStateSave      : true,
						       bProcessing     : false,
						       bAutoWidth      : false } );


	$("#BtnTypeSelectAll").click(function( )
	{
		var objRows = objTypeTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if (!$(objRows[i]).hasClass("selected"))
				$(objRows[i]).addClass("selected");
		}

		$("#BtnTypeMultiDelete").show( );
	});


	$("#BtnTypeSelectNone").click(function( )
	{
		var objRows = objTypeTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnTypeMultiDelete").hide( );
	});


	$(document).on("click", "#TypeGrid tr", function( )
	{
		if ($(this).find("img.icnDelete").length == 0)
			return false;


		if ($(this).hasClass("selected"))
			$(this).removeClass("selected");

		else
			$(this).addClass("selected");


		var bSelected = false;
		var objRows   = objTypeTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				bSelected = true

				break;
			}
		}

		if (bSelected == true)
			$("#BtnTypeMultiDelete").show( );

		else
			$("#BtnTypeMultiDelete").hide( );
	});


	$("#tabs-3 .TableTools").prepend('<button id="BtnTypeMultiDelete">Delete Selected Rows</button>')
	$("#BtnTypeMultiDelete").button({ icons:{ primary:'ui-icon-trash' } });
	$("#BtnTypeMultiDelete").hide( );


	$("#BtnTypeMultiDelete").click(function(event)
	{
		var sTypes          = "";
		var objSelectedRows = new Array( );

		var objRows = objTypeTable.fnGetNodes( );

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
			$("#ConfirmTypeMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/management/delete-user-type.php",
												    { Types:sTypes },

												    function (sResponse)
												    {
													    var sParams = sResponse.split("|-|");

													    showMessage("#TypeGridMsg", sParams[0], sParams[1]);

													    if (sParams[0] == "success")
													    {
													         for (var i = 0; i < objSelectedRows.length; i ++)
														      objTypeTable.fnDeleteRow(objSelectedRows[i]);

													          $("#BtnTypeMultiDelete").hide( );


														  if ($("#SelectTypeButtons").length == 1)
														  {
														  	if (objTypeTable.fnGetNodes( ).length > 5 && $("#TypeGrid .icnDelete").length > 0)
																$("#SelectTypeButtons").show( );

														  	else
																$("#SelectTypeButtons").hide( );
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

		event.stopPropagation( );
	});


	$(document).on("click", "#TypeGrid .icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTypeTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/management/toggle-user-type-status.php",
			{ TypeId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#TypeGridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					var iColumn = 0;

					$("#TypeGrid thead tr th").each(function(iIndex)
					{
						if ($(this).text( ) == "Status")
							iColumn = iIndex;
					});



					if (objIcon.src.indexOf("success.png") != -1)
					{
						objIcon.src = objIcon.src.replace("success.png", "error.png");

						objTypeTable.fnUpdate("In-Active", objRow, iColumn, false);
					}

					else
					{
						objIcon.src = objIcon.src.replace("error.png", "success.png");

						objTypeTable.fnUpdate("Active", objRow, iColumn, false);
					}
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", "#TypeGrid .icnEdit", function(event)
	{
		var iTypeId = this.id;
		var iIndex  = objTypeTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("management/edit-user-type.php?TypeId=" + iTypeId + "&Index=" + iIndex), width:"80%", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#TypeGrid .icnView", function(event)
	{
		var iTypeId = this.id;

		$.colorbox({ href:("management/view-user-type.php?TypeId=" + iTypeId), width:"80%", height:"85%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", "#TypeGrid .icnDelete", function(event)
	{
		var iTypeId = this.id;
		var objRow  = objTypeTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmTypeDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
										$.post("ajax/management/delete-user-type.php",
											{ Types:iTypeId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#TypeGridMsg", sParams[0], sParams[1]);

												if (sParams[0] == "success")
													objTypeTable.fnDeleteRow(objRow);


											  	if ($("#SelectTypeButtons").length == 1)
											  	{
											  		if (objTypeTable.fnGetNodes( ).length > 5 && $("#TypeGrid .icnDelete").length > 0)
														$("#SelectTypeButtons").show( );

											  		else
														$("#SelectTypeButtons").hide( );
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


function updateUser(iUserId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#UserGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Name")
				objUserTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Email")
				objUserTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Mobile")
				objUserTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "User Type")
				objUserTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Status")
				objUserTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objUserTable.fnUpdate(sFields[5], iRow, iIndex);
		});
	}

	else
		objUserTable.fnStandingRedraw( );
}


function updateType(iTypeId, iRow, sFields)
{
	$("#TypeGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Title")
			objTypeTable.fnUpdate(sFields[0], iRow, iIndex);

		else if ($(this).text( ) == "Status")
			objTypeTable.fnUpdate(sFields[1], iRow, iIndex);
	});


	$("#TypeGrid .icnToggle").each(function(iIndex)
	{
		if ($(this).attr("id") == iTypeId)
			$(this).attr("src", sFields[2]);
	});
}


function setSchoolsSelection( )
{
	var sProvinces = "0";
	var sDistricts = "0";

	$("input.province").each(function( )
	{
			if ($(this).prop("checked") == true)
				sProvinces += ("," + $(this).val( ));
	});

	$("input.district").each(function( )
	{
			if ($(this).prop("checked") == true)
				sDistricts += ("," + $(this).val( ));
	});


	$("ul.token-input-list").remove( );
	$("ul.token-input-list-facebook").remove( );

	$("#txtSchools").tokenInput(("ajax/get-schools-list.php?Provinces=" + sProvinces + "&Districts=" + sDistricts),
	{
		queryParam         :  "School",
		minChars           :  2,
		tokenLimit         :  2000,
		hintText           :  "Search the School (EMIS Code)",
		noResultsText      :  "No matching School found",
		theme              :  "facebook",
		preventDuplicates  :  true,
		prePopulate        :  $("#Schools").val( ),
		onAdd              :  function( ) {   }
	});
}