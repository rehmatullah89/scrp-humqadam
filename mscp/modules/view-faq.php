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

	$iFaqId = IO::intValue("FaqId");

	$sSQL = "SELECT * FROM tbl_faqs WHERE id='$iFaqId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iCategory = $objDb->getField(0, "category_id");
	$sQuestion = $objDb->getField(0, "question");
	$sStatus   = $objDb->getField(0, "status");


	$sCategories = getList("tbl_faq_categories", "id", "name");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord">
	<label>Question</label>
	<div class="textbox" style="width:99.5%; height:65px;"><?= nl2br($sQuestion) ?></div>

	<div class="br10"></div>

	<label for="Answer">Answer</label>
	<iframe id="Answer" frameborder="1" width="100%" height="350" src="editor-contents.php?Table=tbl_faqs&Field=answer&Id=<?= $iFaqId ?>"></iframe>

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
  </form>

  <script type="text/javascript">
  <!--
  	 $(document).ready(function( )
  	 {
  	 	$("#Answer").css("height", (($(window).height( ) - 270) + "px"));
  	 });
  -->
  </script>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>