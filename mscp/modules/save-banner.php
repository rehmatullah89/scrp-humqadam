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

	$sTitle        = IO::strValue("txtTitle");
	$sLinkType     = IO::strValue("ddLinkType");
	$iLinkPage     = IO::intValue("ddLinkPage");
	$iLinkCategory = IO::intValue("ddLinkCategory");
	$iLinkPost     = IO::intValue("ddLinkPost");
	$sUrl          = IO::strValue("txtUrl");
	$iWidth        = IO::intValue("txtWidth");
	$iHeight       = IO::intValue("txtHeight");
	$sScript       = IO::strValue("txtScript");
	$sPlacements   = @implode(",", IO::getArray("cbPlacements"));
	$sStatus       = IO::strValue("ddStatus");
	$iPage         = IO::intValue("ddPage");
	$iCategory     = IO::intValue("ddCategory");
	$iPost         = IO::intValue("ddPost");
	$iSelectedPost = IO::intValue("ddSelectedPost");
	$sPicture      = "";
	$sFlash        = "";
	$bError        = true;


	if ($sTitle == "" || $sLinkType == "" || $sPlacements == "" || $sStatus == "" || ($iPage == -1 && $iCategory == -1 && $iPost == -1) ||
	    ($sLinkType == "W" && $iLinkPage == 0) || ($sLinkType == "C" && $iLinkCategory == 0) || ($sLinkType == "P" && $iLinkPost == 0) ||
	    ($sLinkType == "U" && $sUrl == "") || ($sLinkType == "S" && $sScript == "") || $iWidth == 0 || $iHeight == 0 || ($iPost == 1 && $iSelectedPost == 0))
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$iBannerId = getNextId("tbl_banners");
		$sBanner   = "";
		$sLink     = "";


		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iBannerId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (!@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.BANNERS_IMG_DIR.$sPicture)))
				$sPicture = "";

			else
				$sBanner = $sPicture;
		}


		if ($_FILES['fileFlash']['name'] != "")
		{
			$sFlash = ($iBannerId."-".IO::getFileName($_FILES['fileFlash']['name']));

			if (!@move_uploaded_file($_FILES['fileFlash']['tmp_name'], ($sRootDir.BANNERS_IMG_DIR.$sFlash)))
				$sFlash = "";

			else
				$sBanner = $sFlash;
		}


		if ($sLinkType == "W")
			$sLink = $iLinkPage;

		else if ($sLinkType == "C")
			$sLink = $iLinkCategory;

		else if ($sLinkType == "P")
			$sLink = $iLinkPost;

		else if ($sLinkType == "U")
		{
			$sLink = $sUrl;

			if (substr($sUrl, 0, 7) != "http://" && substr($sUrl, 0, 8) != "https://")
				$sLink = "http://{$sUrl}";
		}

		else if ($sLinkType == "S")
			$sLink = $sScript;


		$sSQL = ("INSERT INTO tbl_banners SET id          = '$iBannerId',
										      title       = '$sTitle',
										      `type`      = '$sLinkType',
										      banner      = '$sBanner',
									    	  width       = '$iWidth',
									    	  height      = '$iHeight',
										      `link`      = '$sLink',
										      placements  = '$sPlacements',
										      page_id     = '$iPage',
										      category_id = '$iCategory',
										      post_id     = '".(($iPost == 1) ? $iSelectedPost : $iPost)."',
										      position    = '$iBannerId',
										      status      = '$sStatus',
										      date_time   = NOW( )");

		if ($objDb->execute($sSQL) == true)
			redirect("banners.php", "BANNER_ADDED");

		else
		{
			$_SESSION['Flag'] = "DB_ERROR";

			if ($sPicture != "")
				@unlink($sRootDir.BANNERS_IMG_DIR.$sPicture);

			if ($sFlash != "")
				@unlink($sRootDir.BANNERS_IMG_DIR.$sFlash);
		}
	}
?>