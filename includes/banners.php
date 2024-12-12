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
<section id="Banners">
  <ul id="Slippry">
<?
	$sSQL = "SELECT title, picture, `link` FROM tbl_banners WHERE status='A' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sTitle   = $objDb->getField($i, "title");
		$sPicture = $objDb->getField($i, "picture");
		$sLink    = $objDb->getField($i, "link");

		if ($sPicture == "" || !@file_exists(BANNERS_IMG_DIR.$sPicture))
			continue;
?>
    <li><?= (($sLink != "") ? "<a href='{$sLink}'>" : '') ?><img src="<?= (BANNERS_IMG_DIR.$sPicture) ?>" alt="" title="" /><?= (($sLink != "") ? "</a>" : '') ?></li>
<?
	}
?>
  </ul>
</section>
