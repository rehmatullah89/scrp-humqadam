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

	@require_once("requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	$iAdminId = IO::intValue('aid');
	$sEmail   = IO::strValue('email');
	$sCode    = IO::strValue('code');


	$sSQL = "SELECT site_title, theme, website_mode FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$_SESSION["SiteTitle"]   = $objDb->getField(0, "site_title");
	$_SESSION["CmsTheme"]    = $objDb->getField(0, "theme");
	$_SESSION["WebsiteMode"] = $objDb->getField(0, "website_mode");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/password.js"></script>
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
    <div id="Page">
      <img id="Indicator" src="images/indicator.gif" alt="" title="" />
    </div>


    <div id="Tabs">
	  <ul>
	    <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Reset Password</b></a></li>
	    <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Admin Login</a></li>
	  </ul>

	  <div id="tabs-1" class="tab" style="background:url('images/themes/<?= $_SESSION['CmsTheme'] ?>/change.jpg') 10px 28px no-repeat;">
<?
	$sSQL = "SELECT name FROM tbl_admins WHERE id='$iAdminId' AND email='$sEmail' AND RIGHT(password, 10)='$sCode'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 1)
		$sName = $objDb->getField(0, 0);
?>
	    <form name="frmPassword" id="frmPassword" onsubmit="return false;">
	    <input type="hidden" name="AdminId" value="<?= $iAdminId ?>" />
	    <input type="hidden" name="Email" value="<?= $sEmail ?>" />
	    <input type="hidden" name="Code" value="<?= $sCode ?>" />

	      <div id="PasswordMsg" class="info">Please enter a new password for your account.</div>

	      <label><b>Name</b></label>
	      <div><input type="text" value="<?= $sName ?>" disabled class="textbox" /></div>

	      <div class="br10"></div>

	      <label><b>Email Address</b></label>
	      <div><input type="text" value="<?= $sEmail ?>" disabled class="textbox" /></div>

	      <div class="br10"></div>

	      <label for="txtNewPassword">New Password</label>
	      <div><input type="password" name="txtNewPassword" id="txtNewPassword" value="" maxlength="30" class="textbox" /></div>

	      <div class="br10"></div>

	      <label for="txtConfirmPassword">Confirm Password</label>
	      <div><input type="password" name="txtConfirmPassword" id="txtConfirmPassword" value="" maxlength="30" class="textbox" /></div>

	      <div class="br10"></div>

	      <div align="right">
	        <button id="BtnPassword">Change Password</button>
	      </div>
  	    </form>
<?
	if ($objDb->getCount( ) == 0)
	{
?>
		<script type="text/javascript">
		<!--
			$(document).ready(function( )
			{
				showMessage("#PasswordMsg", "error", "Invalid password reset request.");

				$("#frmPassword :input").attr('disabled', true);
			});
		-->
		</script>
<?
	}
?>
	  </div>


	  <div id="tabs-2" class="tab" style="background:url('images/themes/<?= $_SESSION['CmsTheme'] ?>/login.jpg') 10px 28px no-repeat;">
	    <form name="frmLogin" id="frmLogin" onsubmit="return false;">
	      <div id="LoginMsg" class="hidden"></div>

	      <label for="txtEmail">Email Address</label>
	      <div><input type="text" name="txtEmail" id="txtEmail" value="<?= $sEmail ?>" maxlength="100" class="textbox" /></div>

	      <div class="br10"></div>

	      <label for="txtPassword">Password</label>
	      <div><input type="password" name="txtPassword" id="txtPassword" value="" maxlength="30" class="textbox" /></div>

	      <div class="br10"></div>

	      <div align="right">
	        <button id="BtnLogin">Login</button>
	      </div>
  	    </form>
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