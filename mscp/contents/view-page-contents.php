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

	$iPageId = IO::intValue("PageId");

	$sSQL = "SELECT title FROM tbl_web_pages WHERE id='$iPageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle = $objDb->getField(0, "title");
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
	<div id="RecordMsg" class="hidden"></div>

	<label for="txtTitle">Page Title</label>
	<div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="100" size="40" class="textbox" /></div>

	<div class="br10"></div>

	<br />
	<label>Page Contents</label>
	<iframe id="Contents" frameborder="1" width="100%" height="350" src="editor-contents.php?Table=tbl_web_pages&Field=contents&Id=<?= $iPageId ?>"></iframe>
  </form>

  <script type="text/javascript">
  <!--
  	 $(document).ready(function( )
  	 {
  	 	$("#Contents").css("height", (($(window).height( ) - 125) + "px"));
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