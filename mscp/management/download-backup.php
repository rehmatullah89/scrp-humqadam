<?
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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	$sFile = IO::strValue("File");
	$sType = IO::strValue("Type");

	$sDir      = (($sType == "Database") ? "db/" : "www/");
	$fFileSize = @filesize($sRootDir.BACKUPS_DIR.$sDir.$sFile);

	if(ini_get('zlib.output_compression'))
		@ini_set('zlib.output_compression', 'Off');

	header('Content-Description: File Transfer');
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Type: application/force-download');
	header("Content-Type: application/download");
	header("Content-Type: text/sql");
	header("Content-Disposition: attachment; filename=\"".@basename($sFile)."\";");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $fFileSize");

	@readfile($sRootDir.BACKUPS_DIR.$sDir.$sFile);

	$objDb->close( );
	$objDbGlobal->close( );
?>