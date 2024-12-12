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

	@ini_set('display_errors', 0);
	//@error_reporting(E_ALL);

	@require_once("../../requires/cssmin.class.php");
	@require_once("files.php");

	$sThemes = @glob("themes/*");


	foreach ($sThemes as $sTheme)
	{
		if (!@is_dir($sTheme))
			continue;


		$sTheme = @basename($sTheme);


		@ob_start( );
		@header("Content-type: text/css; charset=UTF-8");


		foreach($sFiles as $sFile)
		{
			$sName = str_replace("[Theme]", $sTheme, $sFile["Name"]);
			$sCss  = @file_get_contents($sName);
			$sCss  = str_replace("../../../", "../", $sCss);

			if ($sFile["Minified"] == FALSE)
				$sCss = CssMin::minify($sCss);

			print $sCss;
		}


		$hFile = @fopen("{$sTheme}.css", "w");

		@fwrite($hFile, @ob_get_contents( ));
		@fclose($hFile);
	}


	@ob_end_flush( );
?>