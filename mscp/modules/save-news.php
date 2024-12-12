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

	$sTitle   = IO::strValue("txtTitle");
	$sSefUrl  = IO::strValue("txtSefUrl");
	$sDetails = IO::strValue("txtDetails");
	$sDate    = IO::strValue("txtDate");
	$sStatus  = IO::strValue("ddStatus");
	$sPicture = "";
	$bError   = true;


	if ($sTitle == "" || $sSefUrl == "" || $sDetails == "" || $sDate == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_news WHERE sef_url LIKE '$sSefUrl'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "NEWS_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$iNewsId = getNextId("tbl_news");

		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iNewsId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture)))
				createImage(($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture), ($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sPicture), NEWS_IMG_WIDTH, NEWS_IMG_HEIGHT);

			if (!@file_exists($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture))
				$sPicture = "";
		}


		$sSQL = "INSERT INTO tbl_news SET id        = '$iNewsId',
		                                  title     = '$sTitle',
		                                  sef_url   = '$sSefUrl',
		                                  details   = '$sDetails',
		                                  picture   = '$sPicture',
                                          `date`    = '$sDate',
                                          status    = '$sStatus',
                                          date_time = NOW( )";

		if ($objDb->execute($sSQL) == true)
			redirect("news.php", "NEWS_ADDED");

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "")
			{
				@unlink($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sPicture);
				@unlink($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture);
			}
		}
	}
?>