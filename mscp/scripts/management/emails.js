
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

var objTable;

$(document).ready(function( )
{
	objTable = $("#DataGrid").dataTable( { sDom            : '<"H"f<"TableTools">>t<"F"ip>',
										   aoColumnDefs    : [ { bSortable:false, aTargets:[3] } ],
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



	$(document).on("click", "#DataGrid .icnEdit", function(event)
	{
		var iEmailId = this.id;
		var iIndex   = objTable.fnGetPosition($(this).closest('tr')[0]);

		$.colorbox({ href:("management/edit-email.php?EmailId=" + iEmailId + "&Index=" + iIndex), width:"90%", height:"95%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});


	$(document).on("click", "#DataGrid .icnView", function(event)
	{
		var iEmailId = this.id;

		$.colorbox({ href:("management/view-email.php?EmailId=" + iEmailId), width:"70%", height:"75%", iframe:true, opacity:"0.50", overlayClose:true });

		event.stopPropagation( );
	});
	
	
	$(document).on("click", ".icnToggle", function(event)
	{
		var objIcon = this;

		$(objIcon).removeClass( ).addClass("icon");

		$.post("ajax/management/toggle-email-status.php",
			{ EmailId:objIcon.id },

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
	
	
	$(document).on("click", ".icnReset", function(event)
	{
		var iEmailId = this.id;
		var iRow     = objTable.fnGetPosition($(this).closest('tr')[0]);

		$("#ConfirmReset").dialog( { resizable : false,
		                              width     : 420,
		                              height    : 110,
		                              modal     : true,
		                              buttons   : { "Reset" : function( )
		                                                       {
										$.post("ajax/management/reset-email.php",
											{ EmailId:iEmailId },

											function (sResponse)
											{
												var sParams = sResponse.split("|-|");

												showMessage("#GridMsg", sParams[0], sParams[1]);
												
												
												if (sParams[0] == "success")
													updateRecord(iEmailId, iRow, sParams[2])
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

function updateRecord(iEmailId, iRow, sSubject)
{
	$("#DataGrid thead tr th").each(function(iIndex)
	{
		if ($(this).text( ) == "Subject")
			objTable.fnUpdate(sSubject, iRow, iIndex);
	});
}