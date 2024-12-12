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

	@ob_start( );

	@ini_set('display_errors', 0);
	//@error_reporting(E_ALL);

	@header("Content-type: text/css; charset=UTF-8");


	@require_once("../requires/cssmin.class.php");
	@require_once("files.php");


	foreach($sFiles as $sFile)
	{
		if ($sFile["Minified"] == FALSE)
			print CssMin::minify(@file_get_contents($sFile["Name"]));

		else
			print @file_get_contents($sFile["Name"]);
	}


	$hFile = @fopen("default.css", "w");

	@fwrite($hFile, @ob_get_contents( ));
	@fclose($hFile);


	@ob_end_flush( );
?>