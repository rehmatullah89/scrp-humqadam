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

	$iNewsId = IO::intValue("NewsId");

	$sSQL = "SELECT * FROM tbl_news WHERE id='$iNewsId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle   = $objDb->getField(0, "title");
	$sSefUrl  = $objDb->getField(0, "sef_url");
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
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord">
<?
	if ($sPicture != "")
	{
?>
    <div style="position:absolute; right:20px;">
      <div style="border:solid 1px #888888; padding:1px;"><img src="<?= (SITE_URL.NEWS_IMG_DIR.'thumbs/'.$sPicture) ?>" alt="" title="" /></div>
    </div>
<?
	}
?>
	<label>Title</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="100" size="40" class="textbox" style="width:85%;" /></div>

	<div class="br10"></div>

	<label for="txtSefUrl">SEF URL</label>
	<div><input type="text" name="txtSefUrl" id="txtSefUrl" value="<?= $sSefUrl ?>" maxlength="100" size="40" class="textbox" style="width:85%;" /></div>

	<div class="br10"></div>

	<label for="Details">Details</label>
	<iframe id="Details" frameborder="1" width="100%" height="350" src="editor-contents.php?Table=tbl_news&Field=details&Id=<?= $iNewsId ?>"></iframe>

	<div class="br10"></div>

	<label for="txtDate">Date</label>
	<div><input type="text" name="txtDate" id="txtDate" value="<?= formatDate($sDate, $_SESSION["DateFormat"]) ?>" maxlength="10" size="15" class="textbox" /></div>

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
  	 	$("#Details").css("height", (($(window).height( ) - 260) + "px"));
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