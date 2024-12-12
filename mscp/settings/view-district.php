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
	$objDb2      = new Database( );

	$iDistrictId = IO::intValue("DistrictId");

	$sSQL = "SELECT * FROM tbl_districts WHERE id='$iDistrictId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sName        = $objDb->getField(0, "name");
	$iProvince    = $objDb->getField(0, "province_id");
	$sSefUrl      = $objDb->getField(0, "sef_url");
	$sLatitude    = $objDb->getField(0, "latitude");
	$sLongitude   = $objDb->getField(0, "longitude");
	$sCoordinates = $objDb->getField(0, "coordinates");
	$sPicture     = $objDb->getField(0, "picture");
	$sStatus      = $objDb->getField(0, "status");
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
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
		<td width="400">
		  <label for="txtName">District Name</label>
		  <div><input type="text" name="txtName" id="txtName" value="<?= formValue($sName) ?>" maxlength="100" size="40" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddProvince">Province</label>

		  <div>
		    <select name="ddProvince" id="ddProvince">
			  <option value=""></option>
<?
	$sSQL = "SELECT id, name, sef_url FROM tbl_provinces ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iProvinceId  = $objDb->getField($i, "id");
		$sProvince    = $objDb->getField($i, "name");
		$sProvinceUrl = $objDb->getField($i, "sef_url");
?>
			  <option value="<?= $iProvinceId ?>" sefUrl="<?= $sProvinceUrl ?>"<?= (($iProvince == $iProvinceId) ? ' selected' : '') ?>><?= $sProvince ?></option>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtSefUrl">SEF URL</label>
		  <div><input type="text" name="txtSefUrl" id="txtSefUrl" value="<?= $sSefUrl ?>" maxlength="100" size="40" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtLatitude">Map Coordinates <span>(Latitude, Longitude)</span></label>

		  <div>
			<input type="text" name="txtLatitude" id="txtLatitude" value="<?= $sLatitude ?>" maxlength="30" size="10" class="textbox" />
			-
			<input type="text" name="txtLongitude" id="txtLongitude" value="<?= $sLongitude ?>" maxlength="30" size="10" class="textbox" />
		  </div>

		  <div class="br10"></div>

		  <label for="txtCoordinates">Area Coordinates <span>(optional)</span></label>
		  <div><textarea name="txtCoordinates" id="txtCoordinates" style="width:320px; height:180px;"><?= $sCoordinates ?></textarea></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>
<?
	if ($sPicture != "")
	{
?>
		  <div style="width:304px; margin-top:15px;">
		    <div style="border:solid 1px #888888; padding:1px;"><img src="<?= (SITE_URL.DISTRICTS_IMG_DIR.$sPicture) ?>" width="300" alt="" title="" /></div>
		  </div>
<?
	}
?>
        </td>

        <td>
		  <label for="txtDescription">Description <span>(optional)</span></label>
		  <iframe id="Description" frameborder="1" width="100%" height="450" src="editor-contents.php?Table=tbl_districts&Field=description&Id=<?= $iDistrictId ?>"></iframe>
        </td>
      </tr>
    </table>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>