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

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iPackageId = IO::intValue("PackageId");
	$iIndex     = IO::intValue("Index");

	if ($_POST)
		@include("update-package.php");


	$sSQL = "SELECT * FROM tbl_packages WHERE id='$iPackageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle   = $objDb->getField(0, "title");
        $iLots    = $objDb->getField(0, "lots");
	$sDetails = $objDb->getField(0, "details");
	$iSchools = $objDb->getField(0, "schools");
	$sStatus  = $objDb->getField(0, "status");


	$sSchools = array( );

	$sSQL = "SELECT id, name, code FROM tbl_schools WHERE FIND_IN_SET(id, '$iSchools')";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId   = $objDb->getField($i, "id");
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");

		$sSchools[] = array("id" => $iId, "name" => "{$sName} ({$sCode})");
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-package.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="PackageId" id="PackageId" value="<?= $iPackageId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicatePackage" id="DuplicatePackage" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="450">
		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="200" size="44" class="textbox" /></div>

		  <div class="br10"></div>

                   <label for="txtLots">No. of Lots</label>
		   <div><input type="text" name="txtLots" id="txtLots" value="<?= $iLots ?>" maxlength="200" size="8" class="textbox" /></div>

		    <div class="br10"></div>
                                  
		  <label for="txtDetails">Details <span>(Optional)</span></label>
		  <div><textarea name="txtDetails" id="txtDetails" rows="4" cols="42"><?= $sDetails ?></textarea></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>

		  <br />
		  <button id="BtnSave">Save Package</button>
		  <button id="BtnCancel">Cancel</button>
		</td>

		<td>
		  <h4 style="width:400px;">Schools</h4>
		  <input type="text" name="txtSchools" id="txtSchools" value="" />
		  <div class="hidden" id="Schools"><?= @json_encode($sSchools) ?></div>
		</td>
	  </tr>
	</table>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>