
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

$(document).ready(function( )
{
	var objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
											   aoColumnDefs    : [ { bSortable:false, aTargets:[4] } ],
											   aaSorting       : [ [ 0, "asc" ] ],
											   oLanguage       : { sEmptyTable:"No record found", sInfoEmpty:"0 records", sZeroRecords:"No matching record found" },
											   bJQueryUI       : true,
											   sPaginationType : "full_numbers",
											   bPaginate       : false,
											   bLengthChange   : false,
											   iDisplayLength  : parseInt($("#RecordsPerPage").val( )),
											   bFilter         : true,
											   bSort           : true,
											   bInfo           : true,
											   bStateSave      : false,
											   bProcessing     : false,
											   bAutoWidth      : false } );


	$(document).on("click", ".icnEdit", function( )
	{
		var iPageId = this.id;

		$.colorbox({ href:("contents/edit-page-contents.php?PageId=" + iPageId), width:"95%", height:"95%", iframe:true, opacity:"0.50", overlayClose:false });
	});


	$(document).on("click", ".icnView", function( )
	{
		var iPageId = this.id;

		$.colorbox({ href:("contents/view-page-contents.php?PageId=" + iPageId), width:"95%", height:"95%", iframe:true, opacity:"0.50", overlayClose:true });
	});
});