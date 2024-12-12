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


	$sSQL = "SELECT * FROM tbl_settings WHERE id='1'";
	$objDb->query($sSQL);

	$sSiteTitle    = $objDb->getField(0, "site_title");
	$sCopyright    = $objDb->getField(0, "copyright");
	$sDateFormat   = $objDb->getField(0, "date_format");
	$sTimeFormat   = $objDb->getField(0, "time_format");
	$sSefMode      = $objDb->getField(0, "sef_mode");
	$sImageResize  = $objDb->getField(0, "image_resize");
	$sTheme        = $objDb->getField(0, "theme");
	$sWebsiteMode  = $objDb->getField(0, "website_mode");
	$sGeneralName  = $objDb->getField(0, "general_name");
	$sGeneralEmail = $objDb->getField(0, "general_email");
	$sHeader       = $objDb->getField(0, "header");
	$sFooter       = $objDb->getField(0, "footer");


	if ($_POST)
		@include("save-settings.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/settings.js"></script>
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
	        <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Website Settings</b></a></li>
	        <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Email Settings</a></li>
	        <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-3">Header/Footer</a></li>
	      </ul>

	      <div id="tabs-1">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
			    <td width="420">
				  <label for="txtSiteTitle">Site Title</label>
				  <div><input type="text" name="txtSiteTitle" id="txtSiteTitle" value="<?= formValue($sSiteTitle) ?>" maxlength="100" size="32" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtCopyright">Copyright</label>
				  <div><input type="text" name="txtCopyright" id="txtCopyright" value="<?= formValue($sCopyright) ?>" maxlength="100" size="32" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddDateFormat">Date Format</label>

				  <div>
				    <select name="ddDateFormat" id="ddDateFormat">
					  <option value="d-M-Y"<?= (($sDateFormat == 'd-M-Y') ? ' selected' : '') ?>>d-M-Y (<?= date("d-M-Y") ?>)</option>
					  <option value="m/d/Y"<?= (($sDateFormat == 'm/d/Y') ? ' selected' : '') ?>>m/d/Y (<?= date("m/d/Y") ?>)</option>
					  <option value="d/m/Y"<?= (($sDateFormat == 'd/m/Y') ? ' selected' : '') ?>>d/m/Y (<?= date("d/m/Y") ?>)</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddTimeFormat">Time Format</label>

				  <div>
				    <select name="ddTimeFormat" id="ddTimeFormat">
					  <option value="h:i A"<?= (($sTimeFormat == 'h:i A') ? ' selected' : '') ?>>h:i A (<?= date("h:i A") ?>)</option>
					  <option value="H:i:s"<?= (($sTimeFormat == 'H:i:s') ? ' selected' : '') ?>>H:i:s (<?= date("H:i:s") ?>)</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddImageResize">Auto Image Resizing</label>

				  <div>
				    <select name="ddImageResize" id="ddImageResize">
					  <option value="C"<?= (($sImageResize == 'C') ? ' selected' : '') ?>>Center & Crop</option>
					  <option value="F"<?= (($sImageResize == 'F') ? ' selected' : '') ?>>Fit to Size</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddTheme">Default CMS Theme</label>

				  <div>
				    <select name="ddTheme" id="ddTheme">
					  <option value="smoothness"<?= (($sTheme == "smoothness") ? ' selected' : '') ?>>Black</option>
					  <option value="redmond"<?= (($sTheme == "redmond") ? ' selected' : '') ?>>Blue</option>
					  <option value="blitzer"<?= (($sTheme == "blitzer") ? ' selected' : '') ?>>Red</option>
				    </select>
				  </div>
				</td>

				<td>
<?
	if ($_SESSION["AdminId"] == 1)
	{
?>
				  <label for="ddSefMode">SEF URLs</label>

				  <div>
				    <select name="ddSefMode" id="ddSefMode">
					  <option value="N"<?= (($sSefMode == 'N') ? ' selected' : '') ?>>Disabled</option>
					  <option value="Y"<?= (($sSefMode == 'Y') ? ' selected' : '') ?>>Enabled</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddWebsiteMode">Website Mode</label>

				  <div>
				    <input type="hidden" name="WebsiteMode" value="<?= $sWebsiteMode ?>" />

				    <select name="ddWebsiteMode" id="ddWebsiteMode">
					  <option value="D"<?= (($sWebsiteMode == "D") ? ' selected' : '') ?>>Development</option>
					  <option value="L"<?= (($sWebsiteMode == "L") ? ' selected' : '') ?>>Live</option>
				    </select>
				  </div>
<?
	}

	else
	{
?>
				  <input type="hidden" name="ddSefMode" value="<?= $sSefMode ?>" />
				  <input type="hidden" name="WebsiteMode" value="<?= $sWebsiteMode ?>" />
				  <input type="hidden" name="ddWebsiteMode" value="<?= $sWebsiteMode ?>" />
<?
	}
?>
	            </td>
	          </tr>
	        </table>
	      </div>


	      <div id="tabs-2">
		    <h4 style="width:255px;">General Email Settings</h4>

		    <label for="txtGeneralName">Sender Name</label>
		    <div><input type="text" name="txtGeneralName" id="txtGeneralName" value="<?= formValue($sGeneralName) ?>" maxlength="100" size="38" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="txtGeneralEmail">Sender Email</label>
		    <div><input type="text" name="txtGeneralEmail" id="txtGeneralEmail" value="<?= $sGeneralEmail ?>" maxlength="100" size="38" class="textbox" /></div>
	      </div>


	      <div id="tabs-3">
		    <label for="txtHeader">Page Header <span>(e.g; Google Site Verification Code)</span></label>
		    <div><textarea name="txtHeader" id="txtHeader" rows="8" style="width:99%;"><?= stripslashes($sHeader) ?></textarea></div>

		    <br />

		    <label for="txtFooter">Page Footer <span>(e.g; Google Analytics Code)</span></label>
		    <div><textarea name="txtFooter" id="txtFooter" rows="8" style="width:99%;"><?= stripslashes($sFooter) ?></textarea></div>
	      </div>
	    </div>

<?
	if ($sUserRights['Add'] == "Y" && $sUserRights['Edit'] == "Y")
	{
?>
	    <br />
		<button id="BtnSave">Save Settings</button>
<?
	}
?>
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