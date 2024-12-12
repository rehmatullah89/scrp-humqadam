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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iBannerId = IO::intValue("BannerId");
	$iIndex    = IO::intValue("Index");

	if ($_POST)
		@include("update-banner.php");


	$sSQL = "SELECT * FROM tbl_banners WHERE id='$iBannerId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle        = $objDb->getField(0, "title");
	$sLinkType     = $objDb->getField(0, "type");
	$sLink         = $objDb->getField(0, "link");
	$sBanner       = $objDb->getField(0, "banner");
	$sPlacements   = $objDb->getField(0, "placements");
	$iWidth        = $objDb->getField(0, "width");
	$iHeight       = $objDb->getField(0, "height");
	$iPage         = $objDb->getField(0, "page_id");
	$sStatus       = $objDb->getField(0, "status");

	$sPlacements       = @explode(",", $sPlacements);
	$iLinkPage         = 0;
	$sUrl              = "";
	$sPicture          = "";
	$sFlash            = "";
	$sScript           = "";

	if (@in_array($sLinkType, array("W", "C", "P")))
	{
		if ($sLinkType == "W")
			$iLinkPage = $sLink;

		$sPicture = $sBanner;
	}

	else if ($sLinkType == "U")
	{
		$sUrl     = $sLink;
		$sPicture = $sBanner;
	}

	else if ($sLinkType == "I")
		$sPicture = $sBanner;


	$iPost = (($iSelectedPost >= 1) ? 1 : $iSelectedPost);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-banner.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
	<input type="hidden" name="BannerId" id="BannerId" value="<?= $iBannerId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="Picture" value="<?= $sPicture ?>" />
	<div id="RecordMsg" class="hidden"></div>

    <label for="txtTitle">Title</label>
    <div><input type="text" name="txtTitle" id="txtTitle" value="<?= $sTitle ?>" maxlength="100" size="44" class="textbox" /></div>

	<div class="br10"></div>

	<label for="txtUrl">URL</label>
	<div><input type="text" name="txtUrl" id="txtUrl" value="<?= $sUrl ?>" maxlength="250" size="44" class="textbox" /></div>

    <div class="br10"></div>

	<label for="filePicture">Picture <span><?= (($sPicture == "") ? '' : ('(<a href="'.(SITE_URL.BANNERS_IMG_DIR.$sPicture).'" class="colorbox">'.substr($sPicture, strlen("{$iBannerId}-")).'</a>)')) ?></span></label>
	<div><input type="file" name="filePicture" id="filePicture" value="" size="40" class="textbox" /></div>

    <label for="ddStatus">Status</label>

    <div>
	  <select name="ddStatus" id="ddStatus">
	    <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
	    <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
    </div>

    <br />
    <button id="btnSave">Save Banner</button>
    <button id="btnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>