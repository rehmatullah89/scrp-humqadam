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


	$sSQL = "SELECT site_title, theme, website_mode FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$_SESSION["SiteTitle"]   = $objDb->getField(0, "site_title");
	$_SESSION["CmsTheme"]    = $objDb->getField(0, "theme");
	$_SESSION["WebsiteMode"] = $objDb->getField(0, "website_mode");
	
	
	if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE && @strpos($_SERVER['HTTP_HOST'], "www") === FALSE)
		redirect(SITE_URL.ADMIN_CP_DIR."/");	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/index.js"></script>
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
	    <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Admin Login</b></a></li>
	    <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Forgot Password?</a></li>
	  </ul>

	  <div id="tabs-1" class="tab" style="background:url('images/themes/<?= $_SESSION['CmsTheme'] ?>/login.jpg') 10px 28px no-repeat;">
	    <form name="frmLogin" id="frmLogin" onsubmit="return false;">
	      <div id="LoginMsg" class="hidden"></div>

	      <label for="txtEmail">Email Address</label>
	      <div><input type="text" name="txtEmail" id="txtEmail" value="" maxlength="100" class="textbox" /></div>

	      <div class="br10"></div>

	      <label for="txtPassword">Password</label>
	      <div><input type="password" name="txtPassword" id="txtPassword" value="" maxlength="30" class="textbox" /></div>

	      <div class="br10"></div>

	      <div align="right">
	        <button id="BtnLogin">Login</button>
	      </div>
  	    </form>
	  </div>


	  <div id="tabs-2" class="tab" style="background:url('images/themes/<?= $_SESSION['CmsTheme'] ?>/password.jpg') 10px 28px no-repeat;">
	    <form name="frmPassword" id="frmPassword" onsubmit="return false;">
	      <div id="PasswordMsg" class="info noHide">Please provide your login email address to reset your account password.</div>

	      <label for="txtLoginEmail">Email Address</label>
	      <div><input type="text" name="txtLoginEmail" id="txtLoginEmail" value="" maxlength="100" class="textbox" /></div>

	      <div class="br10"></div>

	      <div align="right">
	        <button id="BtnPassword">Get Password</button>
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