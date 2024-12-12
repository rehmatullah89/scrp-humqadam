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

	$iPageId         = IO::intValue("PageId");
	$iDistrictId     = IO::intValue("DistrictId");

	if ($iDistrictId > 0)
	{
		$iId      = $iDistrictId;
		$sTable   = "tbl_districts";
		$sField   = "name";
		$sSection = "District";
	}

	else
	{
		$iId      = $iPageId;
		$sTable   = "tbl_web_pages";
		$sField   = "title";
		$sSection = "Web Page";
	}


	$sSQL = "SELECT * FROM {$sTable} WHERE id='$iId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );


	$sLabel       = $objDb->getField(0, $sField);
	$sTitle       = $objDb->getField(0, "title_tag");
	$sDescription = $objDb->getField(0, "description_tag");
	$sKeywords    = $objDb->getField(0, "keywords_tag");
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
	<label for="txtLabel"><?= $sSection ?></label>
	<div><input type="text" name="txtLabel" id="txtLabel" value="<?= formValue($sLabel) ?>" maxlength="100" size="40" class="textbox" disabled style="width:98.5%;" /></div>

	<div class="br10"></div>

	<label for="txtTitle">Page Title</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="250" class="textbox" style="width:98.5%;" /></div>

	<br />
	<label for="txtDescription">Description</label>
	<div><textarea name="txtDescription" id="txtDescription" rows="10" style="width:98.5%;"><?= $sDescription ?></textarea></div>

	<br />
	<label for="txtKeywords">Keywords</label>
	<div><textarea name="txtKeywords" id="txtKeywords" rows="8" style="width:98.5%;"><?= $sKeywords ?></textarea></div>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>