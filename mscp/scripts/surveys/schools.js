
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

	
	if ($("#txtDescription").length > 0)
		$("#txtDescription").ckeditor({ height:"300px" }, function( ) { CKFinder.setupCKEditor(this, ($("base").attr("href") + "plugins/ckfinder/")); });


	$("#txtCode").blur(function( )
	{
		if ($("#txtCode").val( ) == "")
			return;


		$.post("ajax/surveys/check-school.php",
			{ Code:$("#txtCode").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The School EMIS Code is already used. Please specify another Code.");

					$("#DuplicateSchool").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateSchool").val("0");
				}
			},

			"text");
	});


	$("#BtnReset").click(function( )
	{
		$("#frmRecord")[0].reset( );
		$("#RecordMsg").hide( );

		$("#txtDescription").val("");
		$("#txtName").focus( );

		return false;
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the School Name."))
			return false;

		if (!objFV.validate("txtCode", "B,N", "Please enter the EMIS Code."))
			return false;

		if (!objFV.validate("ddType", "B", "Please select the School Type."))
			return false;

		if (!objFV.validate("txtStudents", "N", "Please enter the No of Students."))
			return false;

		if (!objFV.validate("txtCoveredArea", "F", "Please enter the Covered Area (SFT)."))
			return false;

		if (!objFV.validate("txtCost", "F", "Please enter the Estimated Cost."))
			return false;

		if (!objFV.validate("ddDistrict", "B", "Please select the School District."))
			return false;

		if (!objFV.validate("txtAddress", "B", "Please enter the Address."))
			return false;

		if (!objFV.validate("txtLatitude", "B", "Please enter the Map Coordinates (Latitude)."))
			return false;

		if (!objFV.validate("txtLongitude", "B", "Please enter the Map Coordinates (Longitude)."))
			return false;

		if (!objFV.validate("txtEmail", "E", "Please enter a valid Email Address."))
			return false;

/*
		if (!objFV.validate("filePicture", "B", "Please select the School Picture."))
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


		if (!objFV.validate("txtClassRooms", "N", "Please enter the No of Classrooms."))
			return false;

		if (!objFV.validate("txtStudentToilets", "N", "Please enter the No of Student Toilets."))
			return false;

		if (!objFV.validate("txtStaffRooms", "N", "Please enter the No of Staff Rooms."))
			return false;

		if (!objFV.validate("txtStaffToilets", "N", "Please enter the No of Satff Toilets."))
			return false;

		if (!objFV.validate("txtScienceLabs", "N", "Please enter the No of Science Labs."))
			return false;

		if (!objFV.validate("txtItLabs", "N", "Please enter the No of IT Labs."))
			return false;

		if (!objFV.validate("txtExamHalls", "N", "Please enter the No of Exam Halls."))
			return false;

		if (!objFV.validate("txtLibrary", "N", "Please enter the No of Libraries."))
			return false;

		if (!objFV.validate("txtClerkOffices", "N", "Please enter the No of Clerk Offices."))
			return false;

		if (!objFV.validate("txtPrincipalOffice", "N", "Please enter the No of Principal Office."))
			return false;

		if (!objFV.validate("txtParkingStand", "N", "Please enter the No of Parking Stands."))
			return false;

		if (!objFV.validate("txtChowkidarHut", "N", "Please enter the No of Chowkidar Hut."))
			return false;

		if (!objFV.validate("txtSoakagePit", "N", "Please enter the No of Soakage Pit."))
			return false;

		if (!objFV.validate("txtWaterSupply", "N", "Please enter the No of Water Supply."))
			return false;


		if (objFV.value("DuplicateSchool") == "1")
		{
			showMessage("#RecordMsg", "info", "The School EMIS Code is already used. Please specify another Code.");

			objFV.focus("txtCode");
			objFV.select("txtCode");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
	});






	if (parseInt($("#TotalRecords").val( )) > 50)
	{
		objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"toolbar"><"TableTools">>t<"F"ip>',
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   aoColumnDefs    : [ { bSortable:false, aTargets:[9] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
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
											   sAjaxSource     : "ajax/surveys/get-schools.php",

											   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																		if ($("div.toolbar #District").length > 0)
																			aoData.push({ name:"District", value:$("div.toolbar #District").val( ) });

																		if ($("div.toolbar #Type").length > 0)
																			aoData.push({ name:"Type", value:$("div.toolbar #Type").val( ) });

																		if ($("div.toolbar #WorkType").length > 0)
																			aoData.push({ name:"WorkType", value:$("div.toolbar #WorkType").val( ) });
																		
                                                                                                                                                if ($("div.toolbar #Status").length > 0)
																			aoData.push({ name:"Status", value:$("div.toolbar #Status").val( ) });
																		
																		if ($("div.toolbar #DesignType").length > 0)
																			aoData.push({ name:"DesignType", value:$("div.toolbar #DesignType").val( ) });
																		
																		if ($("div.toolbar #StoreyType").length > 0)
																			aoData.push({ name:"StoreyType", value:$("div.toolbar #StoreyType").val( ) });


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

											   fnDrawCallback  : function( ) {  /* initTableSorting("#DataGrid", "#GridMsg", objTable); */ },

											   fnInitComplete  : function( )
																 {
																		$.post("ajax/surveys/get-school-filters.php",
																			   {},

																			   function (sResponse)
																			   {
																				$("div.toolbar").html(sResponse);
																			   },

																			   "text");


																		var iDistrict   = 0;
																		var iType       = 0;
																		var iDesignType = 0;
																		var iStoreyType = 0;

																		$("#DataGrid thead tr th").each(function(iIndex)
																		{
																			if ($(this).text( ) == "District")
																				iDistrict = iIndex;
																			
																			if ($(this).text( ) == "Type")
																				iType = iIndex;
																			
																			if ($(this).text( ) == "Storey")
																				iDesignType = iIndex;
																			
																			if ($(this).text( ) == "Design")
																				iStoreyType = iIndex;
																		});

																		this.fnFilter("", iDistrict);
																		this.fnFilter("", iType);
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
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   aoColumnDefs    : [ { bSortable:false, aTargets:[9] } ],
											   aaSorting       : [ [ 0, "desc" ] ],
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

											   fnDrawCallback  : function( ) { /* setTimeout(function( ) { initTableSorting("#DataGrid", "#GridMsg", objTable); }, 0); */ },

											   fnInitComplete  : function( )
																 {
																		$.post("ajax/surveys/get-school-filters.php",
																			   {},

																			   function (sResponse)
																			   {
																				$("div.toolbar").html(sResponse);
																			   },

																			   "text");


																		var iDistrict   = 0;
																		var iType       = 0;
																		var iDesignType = 0;
																		var iStoreyType = 0;

																		$("#DataGrid thead tr th").each(function(iIndex)
																		{
																			if ($(this).text( ) == "District")
																				iDistrict = iIndex;
																			
																			if ($(this).text( ) == "Type")
																				iType = iIndex;
																			
																			if ($(this).text( ) == "Storey")
																				iDesignType = iIndex;
																			
																			if ($(this).text( ) == "Design")
																				iStoreyType = iIndex;
																		});

																		this.fnFilter("", iDistrict);
																		this.fnFilter("", iType);
																		this.fnFilter("", iDesignType);
																		this.fnFilter("", iStoreyType);
																 }
											 } );
	}



	$("#BtnSelectAll").click(function( )
	{
		var iDistrict   = 0;
		var iType       = 0;
		var iDesignType = 0;
		var iStoreyType = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "District")
				iDistrict = iIndex;
			
			if ($(this).text( ) == "Type")
				iType = iIndex;
			
			if ($(this).text( ) == "Storey")
				iDesignType = iIndex;
			
			if ($(this).text( ) == "Design")
				iStoreyType = iIndex;
		});


		var objRows     = objTable.fnGetNodes( );
		var bSelected   = false;
		var sDistrict   = "";
		var sType       = "";
		var sDesignType = "";
		var StoreyType  = "";

		if ($("div.toolbar #District").length > 0)
			sDistrict = $("div.toolbar #District").val( );
		
		if ($("div.toolbar #Type").length > 0)
			sType = $("div.toolbar #Type").val( );
		
		if ($("div.toolbar #DesignType").length > 0)
			sDesignType = $("div.toolbar #DesignType").val( );
		
		if ($("div.toolbar #StoreyType").length > 0)
			sStoreyType = $("div.toolbar #StoreyType").val( );
		

		if (parseInt($("#TotalRecords").val( )) <= 50)
		{
			for (var i = 0; i < objRows.length; i ++)
			{
				if ( (sDistrict == "" || objTable.fnGetData(objRows[i])[iDistrict] == sDistrict) &&
					 (sType == "" || objTable.fnGetData(objRows[i])[iType] == sType) &&
					 (sDesignType == "" || objTable.fnGetData(objRows[i])[iDesignType] == sDesignType) &&
					 (sStoreyType == "" || objTable.fnGetData(objRows[i])[iStoreyType] == sStoreyType) )
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
	});	


	$(document).on("change", "div.toolbar #Type", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Type")
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


	$(document).on("change", "div.toolbar #WorkType", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		objTable.fnFilter($(this).val( ), 0);
	});
        
        $(document).on("change", "div.toolbar #Status", function( )
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
		var sSchools        = "";
		var objSelectedRows = new Array( );

		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
		{
			if ($(objRows[i]).hasClass("selected"))
			{
				if (sSchools != "")
					sSchools += ",";

				sSchools += objRows[i].id;

				objSelectedRows.push(objRows[i]);
			}
		}

		if (sSchools != "")
		{
			$("#ConfirmMultiDelete").dialog( { resizable : false,
											   width     : 420,
											   height    : 110,
											   modal     : true,
											   buttons   : { "Delete" : function( )
															{
															 $.post("ajax/surveys/delete-school.php",
																{ Schools:sSchools },

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
		var iSchoolId = this.id;
		var iIndex    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-school.php?SchoolId=" + iSchoolId + "&Index=" + iIndex), width:"85%", height:"85%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnView", function(event)
	{
		var iSchoolId = this.id;

		$.colorbox({ href:("surveys/view-school.php?SchoolId=" + iSchoolId), width:"85%", height:"85%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});
        
        
        $(document).on("click", ".icnMembers", function(event)
	{
		var iSchoolId = this.id;
		var iIndex    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("surveys/edit-school-members.php?SchoolId=" + iSchoolId + "&Index=" + iIndex), width:"40%", height:"60%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;
		var objRow  = objTable.fnGetPosition($(this).closest('tr')[0]);

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/surveys/toggle-school-status.php",
			{ SchoolId:objIcon.id },

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
		var iSchoolId = this.id;
		var objRow    = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmDelete").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Delete" : function( )
		                                                       {
																	$.post("ajax/surveys/delete-school.php",
																		{ Schools:iSchoolId },

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


function updateRecord(iSchoolId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 50)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "School")
				objTable.fnUpdate(sFields[0], iRow, iIndex);

			else if ($(this).text( ) == "Code")
				objTable.fnUpdate(sFields[1], iRow, iIndex);

			else if ($(this).text( ) == "Type")
				objTable.fnUpdate(sFields[2], iRow, iIndex);

			else if ($(this).text( ) == "Storey")
				objTable.fnUpdate(sFields[3], iRow, iIndex);

			else if ($(this).text( ) == "Design")
				objTable.fnUpdate(sFields[4], iRow, iIndex);

			else if ($(this).text( ) == "Students")
				objTable.fnUpdate(sFields[5], iRow, iIndex);

			else if ($(this).text( ) == "Revised Cost")
				objTable.fnUpdate(sFields[6], iRow, iIndex);

			else if ($(this).text( ) == "District")
				objTable.fnUpdate(sFields[7], iRow, iIndex);

			else if ($(this).text( ) == "Options")
				objTable.fnUpdate(sFields[8], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}


function updateOptions(iRow, sOptions)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Options")
			objTable.fnUpdate(sOptions, iRow, iIndex);
	});
}