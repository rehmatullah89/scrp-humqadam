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

	$_SESSION["Flag"] = "";

	$sTitle      = IO::strValue("txtTitle");
	$sSefUrl     = IO::strValue("txtSefUrl");
	$sPlacements = @implode(",", IO::getArray("cbPlacements"));
	$sStatus     = IO::strValue("ddStatus");
	$bError      = true;


	if ($sTitle == "" || $sSefUrl == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_web_pages WHERE sef_url LIKE '$sSefUrl'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "WEB_PAGE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iPageId   = getNextId("tbl_web_pages");
		$iPosition = (getDbValue("MAX(position)", "tbl_web_pages", "") + 1);


		$sSQL = "INSERT INTO tbl_web_pages SET id         = '$iPageId',
		                                       php_url    = '',
		                                       sef_url    = '$sSefUrl',
		                                       title      = '$sTitle',
		                                       contents   = '',
		                                       placements = '$sPlacements',
		                                       title_tag  = '{$_SESSION["SiteTitle"]} | $sTitle',
		                                       position   = '$iPosition',
		                                       status     = '$sStatus',
		                                       date_time  = NOW( )";

		if ($objDb->execute($sSQL) == true)
			redirect("web-pages.php", "WEB_PAGE_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>