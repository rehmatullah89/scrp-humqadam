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

	$iEmailId = IO::intValue("EmailId");

	$sSQL = "SELECT * FROM tbl_email_templates WHERE id='$iEmailId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle   = $objDb->getField(0, "title");
	$sSubject = $objDb->getField(0, "subject");
	$sMessage = $objDb->getField(0, "message");
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
	<label for="txtTitle">Title</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" class="textbox" style="width:99.5%;" /></div>

	<div class="br10"></div>

	<label for="txtSubject">Subject</label>
	<div><input type="text" name="txtSubject" id="txtSubject" value="<?= formValue($sSubject) ?>" class="textbox" style="width:99.5%;" /></div>

	<div class="br10"></div>

	<label for="Message">Message</label>
	<div id="Message" class="textbox" style="width:99.5%; height:350px;"><?= $sMessage ?></div>
  </form>

  <script type="text/javascript">
  <!--
  	 $(document).ready(function( )
  	 {
  	 	$("#Message").css("height", (($(window).height( ) - 170) + "px"));

  	 	$("#Message a").click(function( )
  	 	{
  	 		return false;
  	 	});
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