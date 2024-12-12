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
?>
  <title><?= $_SESSION["SiteTitle"] ?> | Control Panel |</title>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />

  <meta name="revisit-after" content="1 Weeks" />
  <meta name="distribution" content="global" />
  <meta name="robots" content="noindex,nofollow,noarchive" />
  <meta name="rating" content="general" />
  <meta http-equiv="imagetoolbar" content="no" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="Tue, 01 Jan 2009 12:12:12 GMT" />
  <meta http-equiv="Cache-Control" content="no-cache" />

  <meta name="copyright" content="SW3 Solutions" />
  <meta name="author" content="Muhammad Tahir Shahzad" />
  <link rev="made" href="mailto:info@sw3solutions.com" />

  <base href="<?= (SITE_URL.ADMIN_CP_DIR) ?>/" rel="<?= ADMIN_CP_DIR ?>" />

  <link rel="Shortcut Icon" href="images/icons/favicon.ico" type="image/icon" />
  <link rel="icon" href="images/icons/favicon.ico" type="image/icon" />

<?
	if ($_SESSION["WebsiteMode"] == "L")
	{
?>
  <link type="text/css" rel="stylesheet" href="css/<?= $_SESSION["CmsTheme"] ?>.css" />

  <script type="text/javascript" src="scripts/default.js"></script>
<?
	}

	else
	{
		@include("{$sAdminDir}css/files.php");

		foreach($sFiles as $sFile)
		{
?>
  <link type="text/css" rel="stylesheet" href="css/<?= str_replace("[Theme]", $_SESSION["CmsTheme"], $sFile['Name']) ?>" />
<?
		}


		@include("{$sAdminDir}scripts/files.php");

		foreach($sFiles as $sFile)
		{
?>
  <script type="text/javascript" src="scripts/<?= $sFile['Name'] ?>"></script>
<?
		}
	}
?>
