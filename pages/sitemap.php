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
?>
		      <?= $sPageContents ?>
		      <br />

		      <div id="Sitemap">
			    <a href="<?= SITE_URL ?>"><b style="font-size:13px;"><?= $sSiteTitle ?></b></a><br />

			    <ul class="noMargin">
<?
	$sSQL = "SELECT id, title, php_url, sef_url FROM tbl_web_pages WHERE id>'1' AND (sef_url LIKE '%.html' OR sef_url LIKE '%/') AND php_url!='sitemap.php' AND status='P' AND placements!='' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iPageId = $objDb->getField($i, "id");
		$sTitle  = $objDb->getField($i, "title");
		$sPhpUrl = $objDb->getField($i, "php_url");
		$sSefUrl = $objDb->getField($i, "sef_url");
?>
			      <li><a href="<?= getPageUrl($iPageId, $sSefUrl) ?>"><?= $sTitle ?></a></li>
<?
	}
?>
				</ul>
	          </div>
