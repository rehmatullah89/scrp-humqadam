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
	$sOldPicture   = IO::strValue("Picture");
	$sPicture      = "";
	$sOldFlash     = IO::strValue("Flash");
	$sFlash        = "";
	$sBannerSql    = "";
	$sLink         = "";

	if ($sTitle == "" || $sLinkType == "" || $sPlacements == "" || $sStatus == "" || ($iPage == -1 && $iCategory == -1 && $iPost == -1) ||
	    ($sLinkType == "W" && $iLinkPage == 0) || ($sLinkType == "C" && $iLinkCategory == 0) || ($sLinkType == "P" && $iLinkPost == 0) ||
	    ($sLinkType == "U" && $sUrl == "") || ($sLinkType == "S" && $sScript == "") || $iWidth == 0 || $iHeight == 0 || ($iPost == 1 && $iSelectedPost == 0))
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iBannerId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.BANNERS_IMG_DIR.$sPicture)))
				$sBannerSql = ", banner='$sPicture'";
		}


		if ($_FILES['fileFlash']['name'] != "")
		{
			$sFlash = ($iBannerId."-".IO::getFileName($_FILES['fileFlash']['name']));

			if (@move_uploaded_file($_FILES['fileFlash']['tmp_name'], ($sRootDir.BANNERS_IMG_DIR.$sFlash)))
				$sBannerSql = ", banner='$sFlash'";
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


		$sSQL = "UPDATE tbl_banners SET title       = '$sTitle',
									    `type`      = '$sLinkType',
									    `link`      = '$sLink',
									    width       = '$iWidth',
									    height      = '$iHeight',
									    placements  = '$sPlacements',
									    page_id     = '$iPage',
									    category_id = '$iCategory',
									    post_id     = '".(($iPost == 1) ? $iSelectedPost : $iPost)."',
									    status      = '$sStatus'
								        $sBannerSql
		         WHERE id='$iBannerId'";

		if ($objDb->execute($sSQL) == true)
		{
			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.BANNERS_IMG_DIR.$sOldPicture);

			if ($sOldFlash != "" && $sFlash != "" && $sOldFlash != $sFlash)
				@unlink($sRootDir.BANNERS_IMG_DIR.$sOldFlash);


			if (@in_array($sLinkType, array("W", "C", "P")))
			{
				if ($sLinkType == "W")
					$sUrl = getDbValue("sef_url", "tbl_web_pages", "id='$iLinkPage'");

				else if ($sLinkType == "C")
					$sUrl = getDbValue("sef_url", "tbl_blog_categories", "id='$iLinkCategory'");

				else if ($sLinkType == "P")
					$sUrl = getDbValue("sef_url", "tbl_blog_posts", "id='$iLinkPost'");

				$sUrl = (SITE_URL.$sUrl);
			}

			else if ($sLinkType == "U")
				$sUrl = $sLink;

			else if ($sLinkType == "I")
				$sUrl = "Image Banner";

			else if ($sLinkType == "F")
				$sUrl = "Flash Banner";

			else if ($sLinkType == "S")
				$sUrl = "Script Banner";
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sTitle) ?>";
		sFields[1] = "<?= $sUrl ?>";
		sFields[2] = "<?= $iWidth ?> x <?= $iHeight ?>";
		sFields[3] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[4] = "";
<?
			if ($sUserRights['Edit'] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnToggle" id="<?= $iBannerId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[4] = (sFields[4] + '<img class="icnEdit" id="<?= $iBannerId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights['Delete'] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnDelete" id="<?= $iBannerId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sOldPicture))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnPicture" id="<?= (SITE_URL.BANNERS_IMG_DIR.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sPicture))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnPicture" id="<?= (SITE_URL.BANNERS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}


			if ($sLinkType == "F" && $sOldFlash != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sOldFlash))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnFlash" id="<?= $iBannerId ?>" rel="<?= $iWidth ?>|<?= $iHeight ?>" src="images/icons/flash.gif" alt="Flash" title="Flash" /> ');
<?
			}

			else if ($sLinkType == "F" && $sFlash != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sFlash))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnFlash" id="<?= $iBannerId ?>" rel="<?= $iWidth ?>|<?= $iHeight ?>" src="images/icons/flash.gif" alt="Flash" title="Flash" /> ');
<?
			}


			if ($sLinkType == "S")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnScript" id="<?= $iBannerId ?>" rel="<?= $iWidth ?>|<?= $iHeight ?>" src="images/icons/script.png" alt="Script" title="Script" /> ');
<?
			}
?>
		sFields[4] = (sFields[4] + '<img class="icnView" id="<?= $iBannerId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iBannerId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Banner has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$_SESSION['Flag'] = "DB_ERROR";

			if ($sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.BANNERS_IMG_DIR.$sPicture);

			if ($sFlash != "" && $sOldFlash != $sFlash)
				@unlink($sRootDir.BANNERS_IMG_DIR.$sFlash);
		}
	}
?>