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

	$iMessageId = IO::intValue("MessageId");

	if ($_POST)
		@include("reply-message.php");


	$sSQL = "SELECT * FROM tbl_web_messages WHERE id='$iMessageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sName      = $objDb->getField(0, "name");
	$sEmail     = $objDb->getField(0, "email");
	$sPhone     = $objDb->getField(0, "phone");
	$sSubject   = $objDb->getField(0, "subject");
	$sMessage   = $objDb->getField(0, "message");
	$sIpAddress = $objDb->getField(0, "ip_address");
	$sDateTime  = $objDb->getField(0, "date_time");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/message-details.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>

  <div>
    <h2>Message Details</h2>

    <table width="100%" border="1" bordercolor="#ffffff" cellpadding="5" cellspacing="0">
	  <tr bgcolor="#eeeeee">
	    <td width="100"><b>Date / Time</b></td>
	    <td><?= date("{$_SESSION["DateFormat"]} {$_SESSION["TimeFormat"]}", strtotime($sDateTime)) ?></td>
	  </tr>

	  <tr bgcolor="#f6f6f6">
		<td><b>IP Address</b></td>
		<td><?= $sIpAddress ?></td>
	  </tr>

 	  <tr bgcolor="#eeeeee">
	    <td><b>Name</b></td>
	    <td><?= $sName ?></td>
	  </tr>

	  <tr bgcolor="#f6f6f6">
	    <td><b>Email</b></td>
	    <td><?= $sEmail ?></td>
	  </tr>

	  <tr bgcolor="#eeeeee">
	    <td><b>Phone</b></td>
	    <td><?= $sPhone ?></td>
	  </tr>

	  <tr bgcolor="#f6f6f6">
	    <td><b>Subject</b></td>
	    <td><?= $sSubject ?></td>
	  </tr>

	  <tr bgcolor="#eeeeee" valign="top">
	    <td><b>Message</b></td>
	    <td><?= nl2br($sMessage) ?></td>
	  </tr>
    </table>
  </div>

<?
	$sSQL = "SELECT * FROM tbl_web_message_replies WHERE message_id='$iMessageId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
?>
  <br />
  <h2>Message Replies</h2>
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sSubject  = $objDb->getField($i, "subject");
			$sMessage  = $objDb->getField($i, "message");
			$sDateTime = $objDb->getField($i, "date_time");
?>
  <div style="background:#f3f3f3; padding:10px;">
    <h4><?= $sSubject ?></h4>
    <i><?= formatDate($sDateTime, "{$_SESSION["DateFormat"]} {$_SESSION["TimeFormat"]}") ?></i><br />
    <div class="br10"></div>

    <?= nl2br($sMessage) ?>
  </div>

  <br />
<?
		}
	}
?>

  <br />
  <br />
  <h2>Post A Reply</h2>

  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="MessageId" id="MessageId" value="<?= $iMessageId ?>" />
	<div id="RecordMsg" class="hidden"></div>

    <label for="txtSubject"><b>Subject</b></label>
    <div><input type="text" name="txtSubject" id="txtSubject" value="<?= ((IO::strValue('txtSubject') == '') ? formValue('Re: '.$sSubject) : IO::strValue('txtSubject', true)) ?>" maxlength="250" class="textbox" style="width:99%;" /></div>

    <div class="br10"></div>

    <label for="txtMessage"><b>Message</b></label>
    <div><textarea name="txtMessage" id="txtMessage" rows="8" style="width:99%;"><?= IO::strValue('txtMessage') ?></textarea></div>

    <div class="br10"></div>

    <button id="BtnSave">Post Reply</button>
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