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


	$iFaqId = IO::intValue("FaqId");
	$iIndex = IO::intValue("Index");

	if ($_POST)
		@include("update-faq.php");


	$sSQL = "SELECT * FROM tbl_faqs WHERE id='$iFaqId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iCategory = $objDb->getField(0, "category_id");
	$sQuestion = $objDb->getField(0, "question");
	$sAnswer   = $objDb->getField(0, "answer");
	$sStatus   = $objDb->getField(0, "status");


	$sCategories = getList("tbl_faq_categories", "id", "name");
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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-faq.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="FaqId" id="FaqId" value="<?= $iFaqId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<label for="txtQuestion">Question</label>
	<div><textarea name="txtQuestion" id="txtQuestion" rows="3" style="width:99.2%;"><?= $sQuestion ?></textarea></div>

	<br />
	<label for="txtAnswer">Answer</label>
	<div><textarea name="txtAnswer" id="txtAnswer" style="width:100%; height:300px;"><?= $sAnswer ?></textarea></div>

	<div class="br10"></div>

	<label for="ddCategory">Category <span>(Optional)</span></label>

	<div>
	  <select name="ddCategory" id="ddCategory">
		<option value=""></option>
<?
	foreach ($sCategories as $iCategoryId => $sCategory)
	{
?>
		<option value="<?= $iCategoryId ?>"<?= (($iCategoryId == $iCategory) ? ' selected' : '') ?>><?= $sCategory ?></option>
<?
	}
?>
	  </select>
	</div>

	<div class="br10"></div>

	<label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
		<option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
	</div>

	<br />
	<button id="BtnSave">Save FAQ</button>
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