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

	$sOffline = getDbValue("offline", "tbl_maintenance", "id='1'");

	if ($sOffline == "Y" && $sCurPage != "offline.php")
		redirect(SITE_URL."offline.php");

	else if ($sOffline == "N" && $sCurPage == "offline.php")
		redirect(SITE_URL);


	if ($sOffline == "Y")
		$sCurPage = "index.php";


	$sSQL = "SELECT website_mode, site_title, copyright, sef_mode, date_format, time_format, header, footer FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$sWebsiteMode = $objDb->getField(0, "website_mode");
	$sSiteTitle   = $objDb->getField(0, "site_title");
	$sCopyright   = $objDb->getField(0, "copyright");
	$sSefMode     = $objDb->getField(0, "sef_mode");
	$sDateFormat  = $objDb->getField(0, "date_format");
	$sTimeFormat  = $objDb->getField(0, "time_format");
	$sHeaderCode  = $objDb->getField(0, "header");
	$sFooterCode  = $objDb->getField(0, "footer");


	$sTitleTag       = $sSiteTitle;
	$sDescriptionTag = "";
	$sKeywordsTag    = "";
	$sPageContents   = "";


	if ($sSefMode == "Y")
	{
		$sPage   = IO::strValue("Page");
		$sNews   = IO::strValue("News");
		$iNewsId = 0;


		$sPage = (($sPage == "" && $sCurPage != "index.php") ? $sCurPage : $sPage);

		$sSQL = "SELECT id, php_url, contents, title_tag, IF(description_tag='', title, description_tag) AS description_tag, keywords_tag FROM tbl_web_pages WHERE sef_url='$sPage'";
	}

	else
	{
		$sPage   = IO::strValue("Page");
		$sPageId = IO::strValue("PageId");
		$iNewsId = IO::intValue("NewsId");


		 if ($sPageId == "" && $sPage == "" && $sCurPage != "index.php")
			$iPageId = getDbValue("id", "tbl_web_pages", "php_url='$sCurPage'");

		 else if ($sPageId == "" && $sPage != "")
			$iPageId = getDbValue("id", "tbl_web_pages", "php_url='$sPage'");

		 else
			$iPageId = (($sPageId == "") ? 1 : intval($sPageId));


		$sSQL = "SELECT php_url, contents, title_tag, IF(description_tag='', title, description_tag) AS description_tag, keywords_tag FROM tbl_web_pages WHERE id='$iPageId'";
	}


	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 0)
	{
		$iPage = 0;

		if (@in_array($sCurPage, array("offline.php", "inspection-details.php", "inspections.php", "failed-inspections.php", "dropped-schools.php")) || $sCurDir == "api")
			$iPage = 1;


		$sSQL = "SELECT contents, title_tag, description_tag, keywords_tag FROM tbl_web_pages WHERE id='$iPage'";
		$objDb->query($sSQL);
	}


	$sTitleTag       = $objDb->getField(0, 'title_tag');
	$sDescriptionTag = $objDb->getField(0, 'description_tag');
	$sKeywordsTag    = $objDb->getField(0, 'keywords_tag');


	if ($sSefMode == "Y")
	{
		$iPageId       = $objDb->getField(0, "id");
		$sPhpUrl       = $objDb->getField(0, "php_url");
		$sPageContents = $objDb->getField(0, "contents");


		if ($sNews != "")
		{
			$sSQL = "SELECT id, title FROM tbl_news WHERE sef_url='$sNews'";
			$objDb->query($sSQL);

			if ($objDb->getCount( ) == 1)
			{
				$iNewsId   = $objDb->getField(0, 'id');
				$sTitleTag = $objDb->getField(0, 'title');
			}
		}
	}

	else
	{
		$sPhpUrl       = $objDb->getField(0, "php_url");
		$sPageContents = $objDb->getField(0, "contents");


		if ($iNewsId > 0)
			$sTitleTag = getDbValue("title", "tbl_news", "id='$iNewsId'");
	}
?>
  <title><?= formValue($sTitleTag) ?></title>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <meta name="description" content="<?= formValue($sDescriptionTag) ?>" />
  <meta name="keywords" content="<?= formValue($sKeywordsTag) ?>" />

  <meta name="revisit-after" content="1 Weeks" />
  <meta name="distribution" content="global" />
  <meta name="rating" content="general" />
  <meta http-equiv="imagetoolbar" content="no" />

  <meta name="copyright" content="Triple Tree Solutions" />
  <meta name="author" content="Muhammad Tahir Shahzad" />
  <link rev="made" href="mailto:tahir@3-tree.com" />

<?
	if ($sHeaderCode != "" && @strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
		print $sHeaderCode;
?>

  <base href="<?= SITE_URL ?>" />

  <link rel="alternate" type="application/rss+xml" title="<?= $sSiteTitle ?>" href="<?= SITE_URL ?>news/" />

  <link rel="Shortcut Icon" href="images/icons/favicon.ico" type="image/icon" />
  <link rel="icon" href="images/icons/favicon.ico" type="image/icon" />

  <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald" />
<?
	if ($sWebsiteMode == "L")
	{
?>
  <link type="text/css" rel="stylesheet" href="css/default.css" />

  <script type="text/javascript" src="scripts/default.js"></script>
<?
	}

	else
	{
		@include("css/files.php");

		foreach($sFiles as $sFile)
		{
?>
  <link type="text/css" rel="stylesheet" href="css/<?= $sFile['Name'] ?>" />
<?
		}


		@include("scripts/files.php");

		foreach($sFiles as $sFile)
		{
?>
  <script type="text/javascript" src="scripts/<?= $sFile['Name'] ?>"></script>
<?
		}
	}
?>