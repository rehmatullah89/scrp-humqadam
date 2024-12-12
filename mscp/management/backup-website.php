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


	$sFile = WEBSITE_FILE_NAME_FORMAT;
	$sFile = str_replace("%Y", gmdate("Y"), $sFile);
	$sFile = str_replace("%m", gmdate("m"), $sFile);
	$sFile = str_replace("%d", gmdate("d"), $sFile);
	$sFile = str_replace("%H", gmdate("H"), $sFile);
	$sFile = str_replace("%i", gmdate("i"), $sFile);
	$sFile = str_replace("%s", gmdate("s"), $sFile);
	$sFile = ($sRootDir.BACKUPS_DIR."www/".$sFile);


	function getFilesList($sDir)
	{
		$sList = array( );

		$objDir = @opendir("{$sDir}/");

		while(false !== ($sFile = @readdir($objDir)))
		{
			if ($sFile == "." || $sFile == "..")
				continue;

			if (@is_dir("{$sDir}/{$sFile}"))
			{
				$sList[] = "{$sDir}/{$sFile}/";

				if (@is_readable("{$sDir}/{$sFile}/"))
					$sList = @array_merge($sList, getFilesList("{$sDir}/{$sFile}"));
			}

			else if (@is_readable("{$sDir}/{$sFile}"))
				$sList[] = "{$sDir}/{$sFile}";
		}

		@closedir($objDir);

		return $sList;
	}


 	$sList  = getFilesList(substr($sRootDir, 0, -1));
 	$objZip = new ZipArchive( );

 	if ($objZip->open($sFile, ZIPARCHIVE::CREATE) === TRUE)
 	{
 		foreach ($sList as $sSourceFile)
 		{
 			$sFile = str_replace($sRootDir, "", $sSourceFile);

 			if (@substr($sFile, 0, strlen(BACKUPS_DIR)) == BACKUPS_DIR)
 				continue;

 			if (@is_dir($sSourceFile))
 				$objZip->addEmptyDir($sFile);

 			else
 				$objZip->addFile($sSourceFile, $sFile);
 		}
 	}

 	$objZip->close( );


	redirect($_SERVER['HTTP_REFERER'], "BACKUP_WEBSITE_TAKEN");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>