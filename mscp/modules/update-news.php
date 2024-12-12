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
	$sDetails    = IO::strValue("txtDetails");
	$sDate       = IO::strValue("txtDate");
	$sStatus     = IO::strValue("ddStatus");
	$sOldPicture = IO::strValue("Picture");
	$sPicture    = "";
	$sPictureSql = "";


	if ($sTitle == "" || $sSefUrl == "" || $sDetails == "" || $sDate == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_news WHERE sef_url LIKE '$sSefUrl' AND id!='$iNewsId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "NEWS_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iNewsId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture)))
			{
				createImage(($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture), ($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sPicture), NEWS_IMG_WIDTH, NEWS_IMG_HEIGHT);

				$sPictureSql = ", picture='$sPicture'";
			}
		}


		$sSQL = "UPDATE tbl_news SET title   = '$sTitle',
		                             sef_url = '$sSefUrl',
		                             details = '$sDetails',
		                             `date`  = '$sDate',
		                             status  = '$sStatus'
		                             $sPictureSql
		         WHERE id='$iNewsId'";

		if ($objDb->execute($sSQL) == true)
		{
			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sOldPicture);
				@unlink($sRootDir.NEWS_IMG_DIR.'originals/'.$sOldPicture);
			}
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sTitle) ?>";
		sFields[1] = "<?= formatDate($sDate, $_SESSION["DateFormat"]) ?>";
		sFields[2] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[3] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnToggle" id="<?= $iNewsId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[3] = (sFields[3] + '<img class="icnEdit" id="<?= $iNewsId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnDelete" id="<?= $iNewsId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.NEWS_IMG_DIR.'originals/'.$sOldPicture))
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnPicture" id="<?= (SITE_URL.NEWS_IMG_DIR.'originals/'.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
		sFields[3] = (sFields[3] + '<img class="icnThumb" id="<?= $iNewsId ?>" rel="News" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture))
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnPicture" id="<?= (SITE_URL.NEWS_IMG_DIR.'originals/'.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
		sFields[3] = (sFields[3] + '<img class="icnThumb" id="<?= $iNewsId ?>" rel="News" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" /> ');
<?
			}
?>
		sFields[3] = (sFields[3] + '<img class="icnView" id="<?= $iNewsId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iNewsId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected News has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sPicture);
				@unlink($sRootDir.NEWS_IMG_DIR.'originals/'.$sPicture);
			}
		}
	}
?>