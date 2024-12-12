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
	$objDb2      = new Database( );


	$sSefMode = getDbValue("sef_mode", "tbl_settings", "id='1'");


	header("Content-type: text/xml");

	print ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
  xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\"
  xmlns:video=\"http://www.google.com/schemas/sitemap-video/1.1\">\n");

	print ("<url><loc>".SITE_URL."</loc></url>\n");


	$sSQL = "SELECT id, php_url, sef_url FROM tbl_web_pages WHERE id>'1' AND (sef_url LIKE '%.html' OR sef_url LIKE '%/') AND php_url!='sitemap.php' AND status='P' AND placements!='' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iPage   = $objDb->getField($i, "id");
		$sPhpUrl = $objDb->getField($i, "php_url");
		$sSefUrl = $objDb->getField($i, "sef_url");


		print ("<url><loc>".getPageUrl($iPage, $sSefUrl)."</loc></url>\n");
	}

	print ("</urlset>");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>