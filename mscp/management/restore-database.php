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


	function removeSqlComments($sSQL)
	{
		return @preg_replace('/\n{2,}/', "\n", preg_replace('/^-- .*$/m', "\n", $sSQL));
	}


	function splitSqlFile($sSQL, $sDelimiter)
	{
		$sSQL  = str_replace("\r" , '', $sSQL);
		$sData = @preg_split('/' . preg_quote($sDelimiter, '/') . '$/m', $sSQL);

		$sData    = array_map('trim', $sData);
		$sEndData = end($sData);

		if (empty($sEndData))
			unset($sData[key($sData)]);

		return $sData;
	}


	$sFile = IO::strValue("File");

	if (!@file_exists($sRootDir.BACKUPS_DIR."db/".$sFile))
		redirect("backups.php", "BACKUP_READ_ERROR");


	$objZip = new ZipArchive( );

	if ($objZip->open($sRootDir.BACKUPS_DIR."db/".$sFile) === TRUE)
	{
		$sZipEntry = $objZip->statIndex(0);
		$sSqlFile  = $sZipEntry['name'];

		$objZip->extractTo($sRootDir.TEMP_DIR);
		$objZip->close( );
	}

	else
		redirect("backups.php", "BACKUP_READ_ERROR");


	$sAbsoluteFile = ($_SERVER['DOCUMENT_ROOT']."/".TEMP_DIR.$sSqlFile);

	@exec("mysql --user=".DB_USER." --password=".DB_PASSWORD." --host=".DB_SERVER." ".DB_NAME." < {$sAbsoluteFile}", $sOutput, $iStatus);

	if ($iStatus == 0)
		$_SESSION["Flag"] = "BACKUP_RESTORED";

	else
	{
		$sSqlData = @file_get_contents($sRootDir.TEMP_DIR.$sSqlFile);
		$sSqlData = @trim($sSqlData);
		$sSqlData = @removeSqlComments($sSqlData);
		$sSqlData = @splitSqlFile($sSqlData, ";");
		$bFlag    = true;


		$objDb->execute("BEGIN");

		$sSQL = ("SHOW TABLES FROM `".DB_NAME."`;");

		if ($objDb->query($sSQL) == false)
			redirect("backups.php", "DB_ERROR");

		$iCount = $objDb->getCount( );

		// Deleting Previous Tables
		$sTables = "";

		for ($i = 0; $i < $iCount; $i ++)
		{
			$sTables .= ("`".$objDb->getField($i, 0)."`");

			if ($i < ($iCount - 1))
				$sTables .= ", ";
		}

		if ($iCount > 0)
		{
			$sSQL  = "DROP TABLE {$sTables};";
			$bFlag = $objDb->execute($sSQL);
		}


		// Inserting New Tables & Data
		if ($bFlag == true)
		{
			$iCount = count($sSqlData);

			for ($i = 0; $i < $iCount; $i ++)
			{
				if ($objDb->execute($sSqlData[$i]) == false)
				{
					$bFlag = false;

					break;
				}
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			$_SESSION["Flag"] = "BACKUP_RESTORED";
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}


	@unlink($sRootDir.TEMP_DIR.$sSqlFile);

	redirect("backups.php");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>