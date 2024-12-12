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

	$sSQL = "SELECT website_mode, site_title, copyright, date_format, time_format, header, footer FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$sWebsiteMode = $objDb->getField(0, "website_mode");
	$sSiteTitle   = $objDb->getField(0, "site_title");
	$sCopyright   = $objDb->getField(0, "copyright");
	$sDateFormat  = $objDb->getField(0, "date_format");
	$sTimeFormat  = $objDb->getField(0, "time_format");
	$sHeaderCode  = $objDb->getField(0, "header");
	$sFooterCode  = $objDb->getField(0, "footer");
?>
  <title><?= formValue($sSiteTitle) ?></title>

  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <meta http-equiv="Content-Language" content="en-us" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta name="format-detection" content="telephone=no" />
  <meta name="apple-mobile-web-app-capable" content="yes" />

  <meta name="revisit-after" content="1 Weeks" />
  <meta name="distribution" content="global" />
  <meta name="robots" content="all" />
  <meta name="rating" content="general" />
  <meta http-equiv="imagetoolbar" content="no" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="Tue, 01 Jan 2009 12:12:12 GMT" />
  <meta http-equiv="Cache-Control" content="no-cache" />

  <meta name="copyright" content="Triple Tree Solutions" />
  <meta name="author" content="Muhammad Tahir Shahzad" />
  <link rev="made" href="mailto:tahir@3-tree.com" />

  <base href="<?= SITE_URL ?>" />

  <link rel="Shortcut Icon" href="images/icons/favicon.ico" type="image/icon" />
  <link rel="icon" href="images/icons/favicon.ico" type="image/icon" />
<?
	$sFiles   = array( );
	$sFiles[] = array("Name" => "common.css", "Minified" => FALSE);

	foreach($sFiles as $sFile)
	{
?>
  <link type="text/css" rel="stylesheet" href="css/<?= $sFile['Name'] ?>" />
<?
	}


	$sFiles   = array( );
	$sFiles[] = array("Name" => "jquery.js", "Minified" => TRUE);
	$sFiles[] = array("Name" => "jquery.ui.js", "Minified" => TRUE);

	foreach($sFiles as $sFile)
	{
?>
  <script type="text/javascript" src="scripts/<?= $sFile['Name'] ?>"></script>
<?
	}
?>