<?
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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iSorId      = IO::intValue("SorId");
	$iSectionId  = IO::intValue("SectionId");
	$iPictureId  = IO::intValue("PictureId");
	$sPicture    = IO::strValue("Picture");


	$sSQL = "DELETE FROM tbl_sor_documents WHERE sor_id='$iSorId' AND section_id='$iSectionId' AND id='$iPictureId'";

	if ($objDb->execute($sSQL) == true)
	{
		@unlink($sRootDir.SORS_DOC_DIR.$sPicture);
		
		redirect($_SERVER['HTTP_REFERER'], "SOR_DELETED");
	}


	redirect($_SERVER['HTTP_REFERER'], "DB_ERROR");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>