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


	$sSQL = "SELECT * FROM tbl_maintenance WHERE id='1'";
	$objDb->query($sSQL);

	$sOffline = $objDb->getField(0, "offline");
	$sMessage = $objDb->getField(0, "message");


	if ($_POST)
		@include("save-maintenance.php");
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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/maintenance.js"></script>
</head>

<body>

<div id="MainDiv">

<!--  Header Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/header.php");
?>
<!--  Header Section Ends Here  -->


<!--  Navigation Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/navigation.php");
?>
<!--  Navigation Section Ends Here  -->


<!--  Body Section Starts Here  -->
  <div id="Body">
<?
	@include("{$sAdminDir}includes/breadcrumb.php");
?>

    <div id="Contents">
<?
	@include("{$sAdminDir}includes/messages.php");
?>

	  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		<div id="RecordMsg" class="hidden"></div>

        <div id="PageTabs">
	      <ul>
	        <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Website Maintenance</b></a></li>
	      </ul>

	      <div id="tabs-1">
			<label for="ddOffline">Offline</label>

			<div>
			  <select name="ddOffline" id="ddOffline">
				<option value="N"<?= (($sOffline == 'N') ? ' selected' : '') ?>>No</option>
				<option value="Y"<?= (($sOffline == 'Y') ? ' selected' : '') ?>>Yes</option>
			  </select>
			</div>

			<br />
			<label for="txtMessage">Offline Message</label>
			<div><textarea name="txtMessage" id="txtMessage" style="width:100%; height:400px;"><?= $sMessage ?></textarea></div>

<?
	if ($sUserRights['Add'] == "Y" && $sUserRights['Edit'] == "Y")
	{
?>
			<div class="br10"></div>

			<button id="BtnSave">Save Mode</button>
<?
	}
?>
		  </div>
		</div>
	  </form>

    </div>
  </div>
<!--  Body Section Ends Here  -->


<!--  Footer Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/footer.php");
?>
<!--  Footer Section Ends Here  -->

</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>