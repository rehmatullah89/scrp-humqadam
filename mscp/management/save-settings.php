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

	$_SESSION["Flag"] = "";

	$sSiteTitle    = IO::strValue("txtSiteTitle");
	$sCopyright    = IO::strValue("txtCopyright");
	$sDateFormat   = IO::strValue("ddDateFormat");
	$sTimeFormat   = IO::strValue("ddTimeFormat");
	$sImageResize  = IO::strValue("ddImageResize");
	$sTheme        = IO::strValue("ddTheme");
	$sSefMode      = IO::strValue("ddSefMode");
	$sWebsiteMode  = IO::strValue("ddWebsiteMode");
	$sGeneralName  = IO::strValue("txtGeneralName");
	$sGeneralEmail = IO::strValue("txtGeneralEmail");
	$sHeader       = IO::strValue("txtHeader");
	$sFooter       = IO::strValue("txtFooter");

	if ($sSiteTitle == "" || $sCopyright == "" || $sDateFormat == "" || $sTimeFormat == "" || $sImageResize == "" || $sTheme == "" ||
	    $sSefMode == "" || $sWebsiteMode == "" ||
	    $sGeneralName == "" || $sGeneralEmail == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_settings SET site_title    = '$sSiteTitle',
		                                 copyright     = '$sCopyright',
		                                 date_format   = '$sDateFormat',
		                                 time_format   = '$sTimeFormat',
		                                 image_resize  = '$sImageResize',
		                                 theme         = '$sTheme',
		                                 sef_mode      = '$sSefMode',
		                                 website_mode  = '$sWebsiteMode',
		                                 general_name  = '$sGeneralName',
		                                 general_email = '$sGeneralEmail',
		                                 header        = '$sHeader',
		                                 footer        = '$sFooter'
		         WHERE id='1'";

		if ($objDb->execute($sSQL) == true)
		{
			if (IO::strValue("WebsiteMode") == "D" && $sWebsiteMode == "L")
			{
				$sHandle = @curl_init(SITE_URL."css/index.php");
				@curl_setopt($sHandle, CURLOPT_HEADER, FALSE);
				@curl_setopt($sHandle, CURLOPT_RETURNTRANSFER, TRUE);
				@curl_exec($sHandle);
				@curl_close($sHandle);


				$sHandle = @curl_init(SITE_URL.ADMIN_CP_DIR."/css/index.php");
				@curl_setopt($sHandle, CURLOPT_HEADER, FALSE);
				@curl_setopt($sHandle, CURLOPT_RETURNTRANSFER, TRUE);
				@curl_exec($sHandle);
				@curl_close($sHandle);


				$sHandle = @curl_init(SITE_URL."scripts/index.php");
				@curl_setopt($sHandle, CURLOPT_HEADER, FALSE);
				@curl_setopt($sHandle, CURLOPT_RETURNTRANSFER, TRUE);
				@curl_exec($sHandle);
				@curl_close($sHandle);


				$sHandle = @curl_init(SITE_URL.ADMIN_CP_DIR."/scripts/index.php");
				@curl_setopt($sHandle, CURLOPT_HEADER, FALSE);
				@curl_setopt($sHandle, CURLOPT_RETURNTRANSFER, TRUE);
				@curl_exec($sHandle);
				@curl_close($sHandle);
			}


			$_SESSION["WebsiteMode"] = $sWebsiteMode;
			$_SESSION["SiteTitle"]   = $sSiteTitle;
			$_SESSION["DateFormat"]  = $sDateFormat;
			$_SESSION["TimeFormat"]  = $sTimeFormat;
			$_SESSION["ImageResize"] = $sImageResize;


			redirect("settings.php", "SETTINGS_UPDATED");
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>