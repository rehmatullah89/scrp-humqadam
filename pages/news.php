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

	if ($iNewsId > 0)
	{
		$sSQL = "SELECT * FROM tbl_news WHERE id='$iNewsId'";
		$objDb->query($sSQL);

		$sTitle   = $objDb->getField(0, "title");
		$sDetails = $objDb->getField(0, "details");
		$sDate    = $objDb->getField(0, "date");
?>
              <h2><?= $sTitle ?></h2>
              <span class="fRight">[ Back to <a href="<?= getPageUrl(getDbValue("id", "tbl_web_pages", "php_url='news.php'")) ?>">News Page</a> ]</span>
              <?= formatDate($sDate, $sDateFormat) ?><br />
              <div class="hr"></div>
              <?= $sDetails ?>
              <div class="br5"></div>
<?
	}

	else
	{
?>
              <?= $sPageContents ?>
<?
		$sSQL = "SELECT * FROM tbl_news WHERE status='A' ORDER BY id DESC LIMIT 15";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		if ($iCount > 0)
		{
?>
              <br />

              <table border="0" cellspacing="0" cellpadding="0" width="100%">
<?
			for ($i = 0; $i < $iCount; $i ++)
			{
				$iNews    = $objDb->getField($i, "id");
				$sTitle   = $objDb->getField($i, "title");
				$sSefUrl  = $objDb->getField($i, "sef_url");
				$sDate    = $objDb->getField($i, "date");
				$sPicture = $objDb->getField($i, "picture");

				if ($sPicture == "" || !@file_exists(NEWS_IMG_DIR.'thumbs/'.$sPicture))
					$sPicture = "default.jpg";
?>
                <tr valign="top">
                  <td width="94"><a href="<?= getNewsUrl($iNews, $sSefUrl) ?>" class="newsPic"><img src="<?= (NEWS_IMG_DIR.'thumbs/'.$sPicture) ?>" width="<?= NEWS_IMG_WIDTH ?>" height="<?= NEWS_IMG_HEIGHT ?>" alt="" title="" /></a></td>

                  <td>
                    <b><?= formatDate($sDate) ?></b><br />
                    <a href="<?= getNewsUrl($iNews, $sSefUrl) ?>" class="news"><?= $sTitle ?></a><br />
                  </td>
                </tr>

                <tr>
                  <td colspan="2" height="12"></td>
                </tr>
<?
			}
?>
              </table>
<?
		}

		else
		{
?>
			  <div class="info noHide">No News Available at the moment!</div>
<?
		}
	}
?>