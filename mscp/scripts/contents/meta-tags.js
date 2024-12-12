
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

var objWebPagesTable;
var objDistrictsTable;

$(document).ready(function( )
{
	objWebPagesTable = $("#WebPagesGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
													   aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
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
													   bAutoWidth      : false } );


	$(document).on("click", "#WebPagesGrid .icnEdit", function( )
	{
		var iPageId = this.id;
		var iIndex  = objWebPagesTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("contents/edit-meta-tags.php?PageId=" + iPageId + "&Index=" + iIndex), width:"800", height:"560", iframe:true, opacity:"0.50", overlayClose:false });
	});


	$(document).on("click", "#WebPagesGrid .icnView", function( )
	{
		var iPageId = this.id;

		$.colorbox({ href:("contents/view-meta-tags.php?PageId=" + iPageId), width:"800", height:"560", iframe:true, opacity:"0.50", overlayClose:true });
	});








	if (parseInt($("#DistrictRecords").val( )) > 100)
	{
		objDistrictsTable = $("#DistrictsGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
															   aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
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
															   sAjaxSource     : "ajax/contents/get-districts.php",

															   fnServerData    : function (sSource, aoData, fnCallback)
																 {
																	$.getJSON(sSource, aoData, function(jsonData)
																	{
																	fnCallback(jsonData);
																	});
																 }
													   } );
	}

	else
	{
		objDistrictsTable = $("#DistrictsGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
															   aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
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
															   bAutoWidth      : false } );
	}


	$(document).on("click", "#DistrictsGrid .icnEdit", function( )
	{
		var iDistrictId = this.id;
		var iIndex      = objDistrictsTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("contents/edit-meta-tags.php?DistrictId=" + iDistrictId + "&Index=" + iIndex), width:"800", height:"560", iframe:true, opacity:"0.50", overlayClose:false });
	});


	$(document).on("click", "#DistrictsGrid .icnView", function( )
	{
		var iDistrictId = this.id;

		$.colorbox({ href:("contents/view-meta-tags.php?DistrictId=" + iDistrictId), width:"800", height:"560", iframe:true, opacity:"0.50", overlayClose:true });
	});
});


function updatePageTitle(iRow, sTitle)
{
	$("#WebPagesGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Title Tag")
			objWebPagesTable.fnUpdate(sTitle, iRow, iIndex);
	});
}


function updateDistrictTitle(iRow, sTitle)
{
	$("#DistrictsGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Title Tag")
			objDistrictsTable.fnUpdate(sTitle, iRow, iIndex);
	});
}