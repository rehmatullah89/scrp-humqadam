
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
	$("#BtnExport").button({ icons:{ primary:'ui-icon-disk' } }).css("margin-left", "10px");
	$('#BtnExport').hide( );
	
    $("#BtnExport").click(function( )
	{
		document.location = ($(this).attr("rel") + "?Contract=" + $("div.toolbar #Contract").val( ) + "&Province=" + $("div.toolbar #Province").val( ) + "&District=" + $("div.toolbar #District").val( ) + "&Package=" + $("div.toolbar #Package").val( ) + "&DesignType=" + $("div.toolbar #DesignType").val( ) + "&StoreyType=" + $("div.toolbar #StoreyType").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
	});
        

	$("#BtnExportMileStone").button({ icons:{ primary:'ui-icon-disk' } }).css("margin-left", "10px");
    $('#BtnExportMileStone').hide( );
	
    $("#BtnExportMileStone").click(function( )
	{
		document.location = ($(this).attr("rel") + "?Contract=" + $("div.toolbar #Contract").val( ) + "&Province=" + $("div.toolbar #Province").val( ) + "&District=" + $("div.toolbar #District").val( ) + "&Package=" + $("div.toolbar #Package").val( ) + "&DesignType=" + $("div.toolbar #DesignType").val( ) + "&StoreyType=" + $("div.toolbar #StoreyType").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
	});
        
    
	$("#BtnExportNonSchedules").button({ icons:{ primary:'ui-icon-disk' } })
    
	$("#BtnExportNonSchedules").click(function( )
	{
		document.location = ($(this).attr("rel") + "?Contract=" + $("div.toolbar #Contract").val( ) + "&Province=" + $("div.toolbar #Province").val( ) + "&District=" + $("div.toolbar #District").val( ) + "&Package=" + $("div.toolbar #Package").val( ) + "&DesignType=" + $("div.toolbar #DesignType").val( ) + "&StoreyType=" + $("div.toolbar #StoreyType").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
	});
        

	$("#txtStartDate, #txtEndDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});


	$("#frmRecord #ddContract, #frmRecord #ddSchool").blur(function( )
	{
		if ($("#frmRecord #ddContract").val( ) == "" || $("#frmRecord #ddSchool").val( ) == "")
			return;


		$.post("ajax/tracking/check-schedule.php",
			{ Contract:$("#frmRecord #ddContract").val( ), School:$("#frmRecord #ddSchool").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The Construction Schedule of selected Contract/School already exists in System.");

					$("#DuplicateSchedule").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateSchedule").val("0");
				}
			},

			"text");
	});


	$("#frmRecord #ddContract").change(function( )
	{
		$.post("ajax/get-contract-schools.php",
			{ Contract:$("#frmRecord #ddContract").val( ) },

			function (sResponse)
			{
				$("#frmRecord #ddSchool").html(sResponse);
			},

			"text");
	});


	$("#frmRecord #BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#frmRecord #ddContract").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddContract", "B", "Please select the Contract."))
			return false;

		if (!objFV.validate("ddSchool", "B", "Please select the School."))
			return false;

		if (!objFV.validate("txtStartDate", "B", "Please enter the Start Date."))
			return false;

		if (!objFV.validate("txtEndDate", "B", "Please enter the End Date."))
			return false;


		if (objFV.value("DuplicateSchedule") == "1")
		{
			showMessage("#RecordMsg", "info", "The Construction Schedule of selected Contract/School already exists in System.");

			objFV.focus("ddContract");
			objFV.select("ddContract");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});





	$("#frmCopy #BtnCopy").button({ icons:{ primary:'ui-icon-disk' } });

	$("#frmCopy #ddContract").change(function( )
	{
		$.post("ajax/get-contract-schools.php",
			{ Contract:$("#frmCopy #ddContract").val( ), List:"Y" },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				$("#frmCopy #ddSchool").html(sParams[0]);
				$("#frmCopy #Schools").html(sParams[1]);
			},

			"text");
	});


	$(document).on("click", "#frmCopy label span a", function( )
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


		return false;
	});


	$("#frmCopy").submit(function( )
	{
		var objFV = new FormValidator("frmCopy", "CopyMsg");


		if (!objFV.validate("ddContract", "B", "Please select the Contract."))
			return false;

		if (!objFV.validate("ddSchool", "B", "Please select the School."))
			return false;


		var bSchools = false;

		$("input.school").each(function( )
		{
				if ($(this).prop("checked") == true)
					bSchools = true;
		});

		if (bSchools == false)
		{
			showMessage("#CopyMsg", "alert", "Please select at-least One School to Copy Schedule.");

			return false;
		}


		$("#BtnCopy").attr('disabled', true);
		$("#CopyMsg").hide( );
	});






	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
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
											   sAjaxSource     : "ajax/tracking/get-schedules.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #Contract").length > 0)
																		aoData.push({ name:"Contract", value:$("div.toolbar #Contract").val( ) });
                                                                                                                                        if ($("div.toolbar #DesignType").length > 0)
                                                                                                                                                aoData.push({ name:"DesignType", value:$("div.toolbar #DesignType").val( ) });
                                                                                                                                        if ($("div.toolbar #StoreyType").length > 0)
																		aoData.push({ name:"StoreyType", value:$("div.toolbar #StoreyType").val( ) });
                                                                                                                                        if ($("div.toolbar #Province").length > 0)
																		aoData.push({ name:"Province", value:$("div.toolbar #Province").val( ) });
                                                                                                                                        if ($("div.toolbar #District").length > 0)
																		aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });
                                                                                                                                        if ($("div.toolbar #Package").length > 0)
																		aoData.push({ name:"Package", value:$("div.toolbar #Package").val( ) });
                                                                                                                                                                    

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
														$.post("ajax/tracking/get-schedule-filters.php",
															   {},

															   function (sResponse)
															   {
																	$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iContract   = 0;
                                                                                                                var iDesignType = 0;
        													var iStoreyType = 0;
                                                                                                                var iProvince   = 0;
                                                                                                                var iDistrict   = 0;
														var iPackage    = 0;
	
                                                                                                                $("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "Contract")
																iContract = iIndex;
                                                                                                                            
                                                                                                                        if ($(this).text( ) == "Province")
																iProvince = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "District")
																iDistrict = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "Package")
																iPackage = iIndex;
                                                                                                                        
                                                                                                                        if ($(this).text( ) == "Storey")
                                                                                                                                iDesignType = iIndex;
															
                                                                                                                        if ($(this).text( ) == "Design")
																iStoreyType = iIndex;
														});

                                                                                                                this.fnFilter("", iProvince);
                                                                                                                this.fnFilter("", iDistrict);
                                                                                                                this.fnFilter("", iPackage);
														this.fnFilter("", iContract);
                                                                                                                this.fnFilter("", iDesignType);
														this.fnFilter("", iStoreyType);

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

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/tracking/get-schedule-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iContract = 0;
                                                                                                                                        var iDesignType = 0;
                                                                                                                                        var iStoreyType = 0;
                                                                                                                                        var iProvince   = 0;
                                                                                                                                        var iDistrict   = 0;
                                                                                                                                        var iPackage   = 0;
                                                                                                                                                
																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "Contract")
																			iContract = iIndex;
                                                                                                                                                
                                                                                                                                                if ($(this).text( ) == "District")
																			iDistrict = iIndex;
                                                                                                                                                    
                                                                                                                                                if ($(this).text( ) == "Province")
																			iProvince = iIndex;
                                                                                                                                                    
                                                                                                                                                if ($(this).text( ) == "Package")
																			iPackage = iIndex;
                                                                                                                                                    
                                                                                                                                                if ($(this).text( ) == "Storey")
																				iDesignType = iIndex;
																			
																		if ($(this).text( ) == "Design")
																				iStoreyType = iIndex;
																	});


																	this.fnFilter("", iContract);
                                                                                                                                        this.fnFilter("", iDesignType);
																	this.fnFilter("", iStoreyType);
                                                                                                                                        this.fnFilter("", iProvince);
                                                                                                                                        this.fnFilter("", iDistrict);
                                                                                                                                        this.fnFilter("", iPackage);
																 }
													   } );
	}


	$("#BtnSelectAll").click(function( )
	{
                var iProvince   = 0;
                var iDistrict   = 0;
                var iPackage    = 0;
		var iContract   = 0;
                var iDesignType = 0;
		var iStoreyType = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Contract")
				iContract = iIndex;
                        
                        if ($(this).text( ) == "Storey")
				iDesignType = iIndex;
			
			if ($(this).text( ) == "Design")
				iStoreyType = iIndex;
                        
                        if ($(this).text( ) == "Province")
				iProvince = iIndex;
                            
                        if ($(this).text( ) == "District")
				iDistrict = iIndex;
                            
                        if ($(this).text( ) == "Package")
				iPackage = iIndex;
		});



		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sContract = "";
                var sDesignType = "";
		var StoreyType  = "";
                var sDistrict   = "";
                var sProvince   = "";
                var sPackage    = "";
                
                if ($("div.toolbar #Province").length > 0)
			sProvince = $("div.toolbar #Province").val( );

                if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
		
                if ($("div.toolbar #Package").length > 0)
			sPackage = $("div.toolbar #Package").val( );
                    
                if ($("div.toolbar #Contract").length > 0)
			sContract = $("div.toolbar #Contract").val( );
                
                if ($("div.toolbar #DesignType").length > 0)
			sDesignType = $("div.toolbar #DesignType").val( );
		
		if ($("div.toolbar #StoreyType").length > 0)
			sStoreyType = $("div.toolbar #StoreyType").val( );


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ((sPackage == "" || objTable.fnGetData(objRows[i])[iPackage] == sPackage) &&
                                        (sProvince == "" || objTable.fnGetData(objRows[i])[iProvince] == sProvince) &&
                                        (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) &&
                                        (sContract == "" || sContract == objTable.fnGetData(objRows[i])[iContract]) && (sDesignType == "" || objTable.fnGetData(objRows[i])[iDesignType] == sDesignType) &&
					 (sStoreyType == "" || objTable.fnGetData(objRows[i])[iStoreyType] == sStoreyType))
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
        
        $(document).on("change", "div.toolbar #Package", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Package")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
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


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});
        
        $(document).on("change", "div.toolbar #District", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});

	$(document).on("change", "div.toolbar #Contract", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Contract")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
	});
        
        $(document).on("change", "div.toolbar #DesignType", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Design")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
                
                if (($("#DesignType").val( ) != '') && ($("#StoreyType").val( ) != '')){
                    $('#BtnExport').show();
                    $('#BtnExportMileStone').show();
                }
                else{
                    $('#BtnExport').hide();
                    $('#BtnExportMileStone').hide();
                }
	});

	$(document).on("change", "div.toolbar #StoreyType", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Storey")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			$("#DataGrid td.position").each(function(iIndex)
			{
				var objRow = objTable.fnGetPosition($(this).closest('tr')[0]);

				objTable.fnUpdate((iIndex + 1), objRow, 0);
			});

			objTable.fnDraw( );
		}
                
                if (($("#DesignType").val( ) != '') && ($("#StoreyType").val( ) != '')){
                    $('#BtnExport').show();
                    $('#BtnExportMileStone').show();
                }
                else{
                    $('#BtnExport').hide();
                    $('#BtnExportMileStone').hide();
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
		var sSchedules      = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sSchedules != "")
					sSchedules += ",";

				sSchedules += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sSchedules != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
							       width     : 420,
							       height    : 110,
							       modal     : true,
							       buttons   : { "Delete" : function( )
										        {
											     $.post("ajax/tracking/delete-schedule.php",
												    { Schedules:sSchedules },

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
	
	
	
	$(document).on("click", "#DataGrid .icnEdit", function(event)
	{
		var iScheduleId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("tracking/edit-schedule.php?ScheduleId=" + iScheduleId + "&Index=" + iIndex), width:"500px", height:"400px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnEditDetails", function(event)
	{
		var iScheduleId = this.id;

		$.colorbox({ href:("tracking/edit-schedule-details.php?ScheduleId=" + iScheduleId), width:"800px", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iScheduleId = this.id;

		$.colorbox({ href:("tracking/view-schedule.php?ScheduleId=" + iScheduleId), width:"800px", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});



	$(document).on("click", "#DataGrid .icnDelete", function(event)
	{
		var iScheduleId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
									  width     : 420,
									  height    : 110,
									  modal     : true,
									  buttons   : { "Delete" : function( )
															   {
																	$.post("ajax/tracking/delete-schedule.php",
																		{ Schedules:iScheduleId },

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


function updateRecord(iScheduleId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Start Date")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "End Date")
				objTable.fnUpdate(sFields[1], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}