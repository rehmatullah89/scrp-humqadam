
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
						       sAjaxSource     : "ajax/settings/get-measurements.php",

						       fnServerData    : function (sSource, aoData, fnCallback)
									 {
										if ($("div.toolbar #Stage").length > 0)
											aoData.push({ name:"Stage", value:$("div.toolbar #Stage").val( ) });


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
										$.post("ajax/settings/get-measurement-filters.php",
										       {},

										       function (sResponse)
										       {
												$("div.toolbar").html(sResponse);
										       },

										       "text");


										var iStage = 0;

										$("#DataGrid thead tr th").each(function(iIndex)
										{
											if ($(this).text( ) == "Parent Stage")
												iStage = iIndex;
										});

										this.fnFilter("", iStage);
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

						       fnInitComplete  : function( )
									 {
										$.post("ajax/settings/get-measurement-filters.php",
										       {},

										       function (sResponse)
										       {
												$("div.toolbar").html(sResponse);
										       },

										       "text");


										var iStage = 0;

										$("#DataGrid thead tr th").each(function(iIndex)
										{
											if ($(this).text( ) == "Parent Stage")
												iStage = iIndex;
										});

										this.fnFilter("", iStage);
									 }
						     } );
	}


	$(document).on("change", "div.toolbar #Stage", function( )
	{
		var objRows = objTable.fnGetNodes( );

		for (var i = 0; i < objRows.length; i ++)
			$(objRows[i]).removeClass("selected");

		$("#BtnMultiDelete").hide( );


		var iColumn = 0;

		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Parent Stage")
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


	$(document).on("click", ".icnEdit", function(event)
	{
		var iStageId = this.id;
		var iIndex   = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("settings/edit-measurement.php?StageId=" + iStageId + "&Index=" + iIndex), width:"600px", height:"400px", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});
});


function updateRecord(iStageId, iRow, sFields)
{
	if (parseInt($("#TotalRecords").val( )) <= 100)
	{
		$("#DataGrid thead tr th").each(function(iIndex)
		{
			if ($(this).text( ) == "Quantity")
				objTable.fnUpdate(sFields[0], iRow, iIndex);
		});
	}

	else
		objTable.fnStandingRedraw( );
}