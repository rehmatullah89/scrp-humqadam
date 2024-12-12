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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sType    = IO::strValue("Type");
	$sBackups = IO::strValue("Backups");

	if ($sBackups != "")
	{
		$sBackups = @explode(",", $sBackups);
		$bFlag    = true;

		for ($i = 0; $i < count($sBackups); $i ++)
		{
			$sDir = (($sType == "Database") ? "db/" : "www/");

			if (!@file_exists($sRootDir.BACKUPS_DIR.$sDir.$sBackups[$i]))
				$bFlag = false;

			else if (!@unlink($sRootDir.BACKUPS_DIR.$sDir.$sBackups[$i]))
				$bFlag = false;

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			if (count($sBackups) > 1)
				print "success|-|The selected Backup Files have been Deleted successfully.";

			else
				print "success|-|The selected Backup File has been Deleted successfully.";
		}

		else
			print "error|-|An error occured while processing your request, please re-load your page and try again.";
	}

	else
		print "info|-|Inavlid Backup File Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>