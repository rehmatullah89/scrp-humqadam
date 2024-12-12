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


	$iNewsId = IO::intValue("NewsId");
	$iIndex  = IO::intValue("Index");

	if ($_POST)
		@include("update-news.php");


	$sSQL = "SELECT * FROM tbl_news WHERE id='$iNewsId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle   = $objDb->getField(0, "title");
	$sSefUrl  = $objDb->getField(0, "sef_url");
	$sDetails = $objDb->getField(0, "details");
	$sPicture = $objDb->getField(0, "picture");
	$sDate    = $objDb->getField(0, "date");
	$sStatus  = $objDb->getField(0, "status");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="plugins/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="plugins/ckeditor/adapters/jquery.js"></script>
  <script type="text/javascript" src="plugins/ckfinder/ckfinder.js"></script>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-news.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
	<input type="hidden" name="NewsId" id="NewsId" value="<?= $iNewsId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="Picture" value="<?= $sPicture ?>" />
	<input type="hidden" name="DuplicateNews" id="DuplicateNews" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<label for="txtTitle">Title</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="225" size="60" class="textbox" style="width:99.2%;" /></div>

    <div class="br10"></div>

    <label for="txtSefUrl">SEF URL</label>
    <div><input type="text" name="txtSefUrl" id="txtSefUrl" value="<?= $sSefUrl ?>" maxlength="250" size="60" class="textbox" style="width:99.2%;" /></div>

	<br />
	<label for="txtDetails">Details</label>
	<div><textarea name="txtDetails" id="txtDetails" style="width:100%; height:300px;"><?= $sDetails ?></textarea></div>

	<div class="br10"></div>

    <label for="filePicture">Picture <span><?= (($sPicture == "") ? ('(optional)') : ('(<a href="'.(SITE_URL.NEWS_IMG_DIR.'originals/'.$sPicture).'" class="colorbox">'.substr($sPicture, strlen("{$iNewsId}-")).'</a> - <a href="'.$sCurDir.'/delete-news-picture.php?NewsId='.$iNewsId.'&Index='.$iIndex.'">Delete</a>)')) ?></span></label>
    <div><input type="file" name="filePicture" id="filePicture" value="" size="60" class="textbox" /></div>

    <div class="br10"></div>

	<label for="txtDate">Date</label>
	<div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

	<div class="br10"></div>

	<label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
		<option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
	</div>

	<br />
	<button id="BtnSave">Save News</button>
	<button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>