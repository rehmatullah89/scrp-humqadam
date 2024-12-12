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

	@require_once("requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/my-account.js"></script>
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

      <div id="AccountTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Account Settings</b></a></li>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Account Password</a></li>
	    </ul>

	    <div id="tabs-1" class="tab">
		  <form name="frmMyAccount" id="frmMyAccount" onsubmit="return false;">
		    <input type="hidden" id="Theme" value="<?= $_SESSION["CmsTheme"] ?>" />
			<div id="AccountMsg" class="alert hidden"></div>

			<label for="txtName">Name</label>
			<div><input type="text" name="txtName" id="txtName" value="<?= formValue($_SESSION["AdminName"]) ?>" maxlength="50" class="textbox" /></div>

			<div class="br10"></div>

			<label for="txtMobile">Mobile</label>
			<div><input type="text" name="txtMobile" id="txtMobile" value="<?= getDbValue("mobile", "tbl_admins", "id='{$_SESSION['AdminId']}'") ?>" maxlength="11" class="textbox" /></div>

			<div class="br10"></div>

			<label for="txtEmail">Email Address</label>
			<div><input type="text" name="txtEmail" id="txtEmail" value="<?= $_SESSION["AdminEmail"] ?>" maxlength="100" class="textbox" /></div>

			<div class="br10"></div>

			<label for="ddRecords">Records per page</label>

			<div>
			  <select name="ddRecords" id="ddRecords">
				<option value="10"<?= (($_SESSION["PageRecords"] == 10) ? ' selected' : '') ?>>10</option>
				<option value="25"<?= (($_SESSION["PageRecords"] == 25) ? ' selected' : '') ?>>25</option>
				<option value="50"<?= (($_SESSION["PageRecords"] == 50) ? ' selected' : '') ?>>50</option>
				<option value="100"<?= (($_SESSION["PageRecords"] == 100) ? ' selected' : '') ?>>100</option>
			  </select>
			</div>

			<div class="br10"></div>

			<label for="ddTheme">CMS Theme</label>

			<div>
			  <select name="ddTheme" id="ddTheme">
				<option value="smoothness"<?= (($_SESSION["CmsTheme"] == "smoothness") ? ' selected' : '') ?>>Black</option>
				<option value="redmond"<?= (($_SESSION["CmsTheme"] == "redmond") ? ' selected' : '') ?>>Blue</option>
				<option value="blitzer"<?= (($_SESSION["CmsTheme"] == "blitzer") ? ' selected' : '') ?>>Red</option>
			  </select>
			</div>

			<br />
			<button id="BtnSave">Save Settings</button>
		  </form>
		</div>


		<div id="tabs-2" class="tab">
		  <form name="frmMyPassword" id="frmMyPassword" onsubmit="return false;">
			<div id="PasswordMsg" class="hidden"></div>

			<label for="txtNewPassword">New Password</label>
			<div><input type="password" name="txtNewPassword" id="txtNewPassword" value="" maxlength="30" class="textbox" /></div>

			<div class="br10"></div>

			<label for="txtConfirmPassword">Confirm Password</label>
			<div><input type="password" name="txtConfirmPassword" id="txtConfirmPassword" value="" maxlength="30" class="textbox" /></div>

			<br />
			<button id="BtnPassword">Update Password</button>
		  </form>
	    </div>

	  </div>

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