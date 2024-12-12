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

	@session_start( );
	@ob_start( );

	//@error_reporting(E_ALL);
	@ini_set('display_errors', 0);

	@ini_set("max_execution_time", 0);
	@ini_set("mysql.connect_timeout", -1);

	@putenv("TZ=Asia/Karachi");
	@date_default_timezone_set("Asia/Karachi");
	@ini_set("date.timezone", "Asia/Karachi");

	@header("Content-type: text/html; charset=utf-8");


	$sCurPage  = substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/") + 1));
	$sAdminDir = "";
	$sRootDir  = "../";

	if (@strpos($_SERVER['DOCUMENT_ROOT'], ":") === FALSE)
		$sPath = @explode("/", getcwd( ));

	else
		$sPath = @explode("\\", getcwd( ));

	$sCurDir = $sPath[(count($sPath) - 1)];


	if ($sCurDir != "mscp")
	{
		$sAdminDir .= "../";
		$sRootDir  .= "../";
	}

	if ($sPath[(count($sPath) - 2)] == "ajax")
	{
		$sAdminDir .= "../";
		$sRootDir  .= "../";
	}


	@require_once("{$sRootDir}requires/configs.php");
	@require_once("{$sRootDir}requires/db.class.php");
	@require_once("{$sRootDir}requires/io.class.php");
	@require_once("{$sRootDir}requires/phpmailer/class.phpmailer.php");
	@require_once("{$sAdminDir}requires/functions.php");
	@require_once("{$sAdminDir}requires/db-functions.php");



	$sUserRights = array( );

	$sUserRights["Add"]    = "N";
	$sUserRights["Edit"]   = "N";
	$sUserRights["Delete"] = "N";
	$sUserRights["View"]   = "Y";


	if ($sCurDir == ADMIN_CP_DIR)
	{
		if (@in_array($sCurPage, array("index.php", "password.php")))
			checkLogin(false);

		else
			checkLogin( );
	}

	else if (@in_array($sCurDir, array("contents", "surveys", "tracking", "modules", "management", "settings")))
	{
		checkLogin( );


		if ($sCurPage != "index.php")
		{
			$sUserRights = getUserRights( );

			if ($sUserRights["View"] != "Y")
				redirect((SITE_URL.ADMIN_CP_DIR."/dashboard.php"), "ACCESS_DENIED");
		}
	}


	if (@in_array("ajax", $sPath))
	{
		if (!@strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
			die("ERROR: Invalid Request, system is unable to process your request.");
	}
?>