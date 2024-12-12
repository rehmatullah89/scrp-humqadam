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

	$iPackageId = IO::intValue("PackageId");

	$sSQL = "SELECT * FROM tbl_packages WHERE id='$iPackageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle   = $objDb->getField(0, "title");
	$sDetails = $objDb->getField(0, "details");
	$sSchools = $objDb->getField(0, "schools");
	$sStatus  = $objDb->getField(0, "status");
        $iLots    = $objDb->getField(0, "lots");

	$iClasses = getDbValue("SUM(class_rooms)", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");
	$iToilets = getDbValue("SUM(student_toilets)", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");
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
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="450">
		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="200" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtDetails">Details <span>(Optional)</span></label>
		  <div><textarea name="txtDetails" id="txtDetails" rows="4" cols="42"><?= $sDetails ?></textarea></div>

		  <div class="br10"></div>
                  
                  <label for="txtLots">No. of Lots</label>
		  <div><input type="text" name="txtLots" id="txtLots" value="<?= $iLots ?>" maxlength="10" size="10" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtClasses">Classes</label>
		  <div><input type="text" name="txtClasses" id="txtClasses" value="<?= $iClasses ?>" maxlength="10" size="10" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtToilets">Toilets</label>
		  <div><input type="text" name="txtToilets" id="txtToilets" value="<?= $iToilets ?>" maxlength="10" size="10" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>
		</td>

		<td>
		  <h4 style="width:90%;">Schools</h4>

		  <div style="border:solid 1px #999999; padding:5px 5px 5px 10px; line-height:20px; background:#f9f9f9; width:calc(90% - 15px);">
<?
	$sSQL = "SELECT name, code FROM tbl_schools WHERE FIND_IN_SET(id, '$sSchools')";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");
?>
		    <?= ($i + 1) ?>. <?= "{$sName} ({$sCode})" ?><br />
<?
	}
?>
		  </div>
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