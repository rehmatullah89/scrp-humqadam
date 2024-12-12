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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sSQL = "SELECT site_title, copyright, sef_mode FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$sSiteTitle = $objDb->getField(0, "site_title");
	$sCopyright = $objDb->getField(0, "copyright");
	$sSefMode   = $objDb->getField(0, "sef_mode");



	header("Content-type: text/xml");

	print ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n");
	print ("<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\r\n");
	print ("<channel>\r\n");
	print ("<title>{$sSiteTitle}</title>\r\n");
	print ("<description>".@utf8_encode(getDbValue("description_tag", "tbl_web_pages", "id='1'"))."</description>\r\n");
	print ("<link>".SITE_URL."</link>\r\n");
	print ("<atom:link href=\"".SITE_URL."news/\" rel=\"self\" type=\"application/rss+xml\" />");
	print ("<copyright>Copyright ".date('Y')." &amp;copy; {$sCopyright}</copyright>\r\n");
	print ("<pubDate>".date('r')."</pubDate>\r\n");


	$sSQL = "SELECT * FROM tbl_news WHERE status='A' ORDER BY id DESC LIMIT 25";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iNews    = $objDb->getField($i, "id");
		$sTitle   = $objDb->getField($i, "title");
		$sSefUrl  = $objDb->getField($i, "sef_url");
		$sDetails = $objDb->getField($i, "details");
		$sDate    = $objDb->getField($i, "date");
		$sPicture = $objDb->getField($i, "picture");


		print ("<item>\r\n");
		print ("<title>".@utf8_encode(str_replace("&", "&amp;", strip_tags($sTitle)))."</title>\r\n");
		print ("<description>".@utf8_encode(str_replace("&", "&amp;", substr(strip_tags($sDetails), 0, 500))).((strlen(strip_tags($sDetails)) > 500) ? '...' : '')."</description>\r\n");

		if ($sPicture != "" && @file_exists('../'.NEWS_IMG_DIR.'thumbs/'.$sPicture))
		{
			$sSize = @getimagesize('../'.NEWS_IMG_DIR.'thumbs/'.$sPicture);

			print ("<enclosure url=\"".(SITE_URL.NEWS_IMG_DIR.'thumbs/'.$sPicture)."\" length=\"".@filesize("../".NEWS_IMG_DIR.'thumbs/'.$sPicture)."\" type=\"{$sSize['mime']}\" />\r\n");
		}

		print ("<link>".getNewsUrl($iNews, $sSefUrl)."</link>\r\n");
		print ("<guid isPermaLink=\"false\">NEWS{$iNews}</guid>\r\n");
		print ("<pubDate>".date('r', strtotime($sDate))."</pubDate>\r\n");
		print ("</item>\r\n");
	}


	print ("</channel>\r\n");
	print ("</rss>");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>