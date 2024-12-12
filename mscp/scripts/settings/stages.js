
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
        $("#BtnExport").button({ icons:{ primary:'ui-icon-disk' } });
	
	$("#BtnExport").click(function( )
	{
		document.location = ($(this).attr("rel") + "?District=" + $("div.toolbar #District").val( ) + "&DesignType=" + $("div.toolbar #DesignType").val( ) + "&StoreyType=" + $("div.toolbar #StoreyType").val( ) + "&Type=" + $("div.toolbar #Type").val( ) + "&WorkType=" + $("div.toolbar #WorkType").val( ) + "&Status=" + $("div.toolbar #Status").val( ) + "&Keywords=" + $("div.dataTables_filter input").val( ));
	});
        
	$("#txtName, #ddParent, #ddType").blur(function( )
	{
		var sName = $("#txtName").val( );
		var sType = $("#ddType").val( );

		if (sName == "" || sType == "")
			return;


		$.post("ajax/settings/check-stage.php",
			{ Name:sName, Type:sType, ParentId:$("#ddParent").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The Stage Name is already used. Please specify another Name.");

					$("#DuplicateStage").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateStage").val("0");
				}
			},

			"text");
	});


	$("#ddNature").change(function( )
	{
		if ($(this).val( ) == "S")
			$("#Parent").show( );

		else
			$("#Parent").hide( );
	});
	
	
	$("#ddType").change(function( )
	{
		if ($("#ddType").val( ) == "")
		{
			$("#ddParent").html("");
			
			return false;
		}
		
		
		$.post("ajax/settings/get-parent-stages.php",
			{ Type:$("#ddType").val( ) },

			function (sResponse)
			{
				$("#ddParent").html(sResponse);
			},

			"text");
	});	


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#ddType").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		if (!objFV.validate("ddType", "B", "Please enter the School Type."))
			return false;
		
		if (!objFV.validate("ddNature", "B", "Please enter the Stage Nature."))
			return false;

		if (objFV.value("ddNature") == "S")
		{
			if (!objFV.validate("ddParent", "B", "Please select the Parent Stage."))
				return false;
		}

		if (!objFV.validate("txtName", "B", "Please enter the Stage Name."))
			return false;

		if (!objFV.validate("txtWeightage", "F", "Please enter the valid Stage Weightage."))
			return false;

		if (objFV.value("DuplicateStage") == "1")
		{
			showMessage("#RecordMsg", "info", "The Stage Name is already used. Please specify another Name.");

			objFV.focus("txtSefUrl");
			objFV.select("txtSefUrl");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});






	if (parseInt($("#TotalRecords").val( )) > 50)
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
											   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4,5,6] } ],
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,
											   bServerSide     : true,
											   sAjaxSource     : "ajax/settings/get-stages.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #Stage").length > 0)
																		aoData.push({ name:"Stage", value:$("div.toolbar #Stage").val( ) });
																	
																	if ($("div.toolbar #Type").length > 0)
																		aoData.push({ name:"Type", value:$("div.toolbar #Type").val( ) });
																	
																	if ($("div.toolbar #Work").length > 0)
																		aoData.push({ name:"Work", value:$("div.toolbar #Work").val( ) });

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

											   fnDrawCallback  : function( ) { /* initTableSorting("#DataGrid", "#GridMsg", objTable); */ },

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/settings/get-stage-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");


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
											   aoColumnDefs    : [ { asSorting:["asc"], aTargets:[0] }, { bSortable:false, aTargets:[1,2,3,4,5,6] } ],
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false,

											   fnDrawCallback  : function( ) { /* setTimeout(function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); }, 0); */ },

											   fnInitComplete  : function( )
																 {
																	$.post("ajax/settings/get-stage-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																			$("div.toolbar").html(sResponse);
																		   },

																		   "text");
																 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;

		for (var i = 0; i < objRows.length; i ++)
		{
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


	$(document).on("change", "div.toolbar #Stage", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );

		
		objTable.fnFilter($(this).val( ), 0);
	});
	

	$(document).on("change", "div.toolbar #Type", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );

		
		$("div.toolbar #Stage").val("");

		$.post("ajax/settings/get-stage-filters.php",
			   { Type:$(this).val( ), Work:$("div.toolbar #Work").val( ) },

			   function (sResponse)
			   {
				$("div.toolbar").html(sResponse);
			   },

			   "text");


		objTable.fnFilter($(this).val( ), 0);
	});
	
	
	$(document).on("change", "div.toolbar #Work", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		objTable.fnFilter($(this).val( ), 0);
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
		var sStages         = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sStages != "")
					sStages += ",";

				sStages += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sStages != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
						           width     : 420,
						      	   height    : 110,
						           modal     : true,
						           buttons   : { "Delete" : function( )
									            {
											     $.post("ajax/settings/delete-stage.php",
												    { Stages:sStages },

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
		var iStageId = this.id;
		var iIndex   = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("settings/edit-stage.php?StageId=" + iStageId + "&Index=" + iIndex), width:"600px", height:"700px", maxHeight:"80%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iStageId = this.id;

		$.colorbox({ href:("settings/view-stage.php?StageId=" + iStageId), width:"600px", height:"700px", maxHeight:"80%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/settings/toggle-stage-status.php",
			{ StageId:objIcon.id },

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#GridMsg", sParams[0], sParams[1]);


				if (sParams[0] == "success")
				{
					if (objIcon.src.indexOf("success.png") != -1)
						objIcon.src = objIcon.src.replace("success.png", "error.png");

					else
						objIcon.src = objIcon.src.replace("error.png", "success.png");
				}

				$(objIcon).removeClass("icon").addClass("icnToggle");
			},

			"text");

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iStageId = this.id;
		var objRow   = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/settings/delete-stage.php",
																		{ Stages:iStageId },

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


function updateRecord(iStageId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "#")
				objTable.fnUpdate(sFields[0], iRow, iIndex);
			
			else if ($(this).text( ) == "Stage")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Parent Stage")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Unit")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Weightage")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Duration")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[6], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );


	$.post("ajax/settings/get-stage-filters.php",
		   { Type:$("div.toolbar #Type").val( ) },

		   function (sResponse)
		   {
				var sType  = "";
				var sStage = "";
				var sWork  = "";
				
				if ($("div.toolbar #Type").length == 1)
					sType = $("div.toolbar #Type").val( );
				
				if ($("div.toolbar #Stage").length == 1)
					sStage = $("div.toolbar #Stage").val( );
				
				if ($("div.toolbar #Work").length == 1)
					sWork = $("div.toolbar #Work").val( );

				
				$("div.toolbar").html(sResponse);

				
				if ($("div.toolbar #Type").length == 1)
					$("div.toolbar #Type").val(sType);
				
				if ($("div.toolbar #Stage").length == 1)
					$("div.toolbar #Stage").val(sStage);
				
				if ($("div.toolbar #Work").length == 1)
					$("div.toolbar #Work").val(sWork);
		   },

		   "text");
}