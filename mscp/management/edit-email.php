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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iEmailId = IO::intValue("EmailId");
	$iIndex   = IO::intValue("Index");

	if ($_POST)
		@include("save-email.php");


	$sSQL = "SELECT * FROM tbl_email_templates WHERE id='$iEmailId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle     = $objDb->getField(0, "title");
	$sSubject   = $objDb->getField(0, "subject");
	$sMessage   = $objDb->getField(0, "message");
	$sVariables = $objDb->getField(0, "variables");
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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-email.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="EmailId" id="EmailId" value="<?= $iEmailId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<label for="txtTitle">Email</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" size="40" class="textbox" style="width:99.2%;" disabled /></div>

	<div class="br10"></div>

	<label for="txtSubject">Subject</label>
	<div><input type="text" name="txtSubject" id="txtSubject" value="<?= formValue($sSubject) ?>" maxlength="250" size="40" class="textbox" style="width:99.2%;" /></div>

	<br />

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
	    <td width="70%">
		  <label for="txtMessage">Message</label>
		  <div><textarea name="txtMessage" id="txtMessage" style="width:100%; height:310px;"><?= $sMessage ?></textarea></div>
		</td>

		<td width="4%"></td>

		<td width="26%">
		  <h3>Variables</h3>

		  <div id="Variables" style="overflow:auto;">
<?
	$sVariables = @explode(",", $sVariables);

	foreach ($sVariables as $sVariable)
	{
		@list($sReference, $sKey) = @explode(":", $sVariable);
?>
		    <b style="display:inline-block; width:120px; padding:2px 0px 2px 0px;"><?= $sReference ?></b> <?= $sKey ?><br />
<?
	}
?>
		  </div>
		</td>
	  </tr>
	</table>

	<br />
	<button id="BtnSave">Save Email</button>
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