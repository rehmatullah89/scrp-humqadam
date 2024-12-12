
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

$(document).ready(function( )
{
	 $("#uploadFile").button({ icons:{ primary:'ui-icon-disk' } });

	$("#uploadFile").click(function(event)
	{
		window.location.reload();
		/*var iSectionId = $(this).attr("section");
		var iSorId     = $(this).val("sor");
		//var iIndex     = objSections.fnGetPosition($(this).closest('tr')[0]); + "&Index=" + iIndex
		
		$("#frmRecord").submit();
		$.colorbox({ href:("surveys/edit-sor-section.php?SorId=" + iSorId + "&SectionId=" + iSectionId), width:"80%", height:"90%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );*/
		
	});	

});