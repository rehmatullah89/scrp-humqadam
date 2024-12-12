
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
	$("#txtQuestion, #ddSection").blur(function( )
	{
		if ($("#txtQuestion").val( ) == "" || $("#ddSection").val( ) == "")
			return;


		$.post("ajax/surveys/check-question.php",
			{ Title:$("#txtQuestion").val( ), Section:$("#ddSection").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The Survey Section/Question is already entered. Please specify another Question.");

					$("#DuplicateQuestion").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateQuestion").val("0");
				}
			},

			"text");
	});


	$("#ddType").change(function( )
	{
		if ($(this).val( ) == "MS" || $(this).val( ) == "SS")
		{
			if ($("#Options").css("display") != "block")
				$("#Options").show("blind");
		}

		else
		{
			if ($("#Options").css("display") == "block")
				$("#Options").hide("blind");

			$("#txtOptions").val("");
		}
		
		
		if ($(this).val( ) == "SL")
		{
			if ($("#InputType").css("display") != "block")
				$("#InputType").show("blind");
		}

		else
		{
			if ($("#InputType").css("display") == "block")
				$("#InputType").hide("blind");

			$("#ddInputType").val("T");
		}
	});



	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );
		$("#ddSection").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddSection", "B", "Please select the Survey Section."))
			return false;

		if (!objFV.validate("ddType", "B", "Please select the Question Type."))
			return false;
		
		if (!objFV.validate("txtQuestion", "B", "Please enter the Question."))
			return false;

		if (objFV.value("ddType") == "MS" || objFV.value("ddType") == "SS")
		{
			if (!objFV.validate("txtOptions", "B", "Please enter the Question Options (One per Line)."))
				return false;			
		}
		
		if (!objFV.validate("txtLink", "N", "Please enter the Linked Question ID."))
			return false;

		if (objFV.value("txtLink") != "")
		{
			if (!objFV.validate("ddLink", "B", "Please select the Linked Question Value."))
				return false;
		}
		
		if (!objFV.validate("txtPosition", "N", "Please enter the Question Position."))
			return false;		


		if (objFV.value("DuplicateQuestion") == "1")
		{
			showMessage("#RecordMsg", "info", "This Survey Section/Question is already entered. Please specify another Question.");

			objFV.focus("txtQuestion");
			objFV.select("txtQuestion");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});







	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   aoColumnDefs    : [ { bSortable:false, aTargets:[5] } ],
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
											   sAjaxSource     : "ajax/surveys/get-questions.php",

											 fnDrawCallback  : function( )
															   {
																   $(".details").tipTip( );
															   },

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	if ($("div.toolbar #Section").length > 0)
																		aoData.push({ name:"Section", value:$("div.toolbar #Section").val( ) });


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
																	$.post("ajax/surveys/get-question-filters.php",
																		   {},

																		   function (sResponse)
																		   {
																				$("div.toolbar").html(sResponse);
																		   },

																		   "text");


																	var iSection = 0;

																	$("#DataGrid thead tr th").each(function(iIndex)
																	{
																		if ($(this).text( ) == "Section")
																			iSection = iIndex;
																	});


																	this.fnFilter("", iSection);


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
											   aoColumnDefs    : [ { bSortable:false, aTargets:[5] } ],
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

												 fnDrawCallback  : function( )
																   {
																	   $(".details").tipTip( );
																   },

											   fnInitComplete  : function( )
													 {
														$.post("ajax/surveys/get-question-filters.php",
															   {},

															   function (sResponse)
															   {
																$("div.toolbar").html(sResponse);
															   },

															   "text");


														var iSection = 0;

														$("#DataGrid thead tr th").each(function(iIndex)
														{
															if ($(this).text( ) == "Section")
																iSection = iIndex;
														});


														this.fnFilter("", iSection);
													 }
											  } );
	}


	$("#BtnSelectAll").click(function( )
	{
		var iSection = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Section")
				iSection = iIndex;
		});


		var objRows   = objTable.fnGetNodes( );
		var bSelected = false;
		var sSection  = "";


		if ($("div.toolbar #Section").length > 0)
			sSection = $("div.toolbar #Section").val( );


		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if (sSection == "" || sSection == objTable.fnGetData(objRows[i])[iSection])
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


	$(document).on("change", "div.toolbar #Section", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Section")
				iColumn = iIndex;
		});


		objTable.fnFilter($(this).val( ), iColumn);
	});


	$(document).on("change", "div.toolbar #Completed", function( )
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
		var sQuestions    = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sQuestions != "")
					sQuestions += ",";

				sQuestions += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sQuestions != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
															{
															 $.post("ajax/surveys/delete-question.php",
																{ Questions:sQuestions },

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
		var iQuestionId = this.id;
		var iIndex      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-question.php?QuestionId=" + iQuestionId + "&Index=" + iIndex), width:"600px", height:"70%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iQuestionId = this.id;

		$.colorbox({ href:("surveys/view-question.php?QuestionId=" + iQuestionId), width:"600px", height:"70%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnDelete", function(event)
	{
		var iQuestionId = this.id;
		var objRow      = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/surveys/delete-question.php",
																		{ Questions:iQuestionId },

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
	
	
	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/surveys/toggle-question-status.php",
			{ QuestionId:objIcon.id },

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
});


function updateRecord(iQuestionId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Question")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Section")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Position")
				objTable.fnUpdate(sFields[2], iRow, iIndex);
			
			else if ($(this).text( ) == "Status")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[4], iRow, iIndex);
		});


		$(".details").tipTip( );
	}

	else
		objTable.fnStandingRedraw( );
}