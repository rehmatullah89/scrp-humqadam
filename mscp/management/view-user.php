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

	$iUserId = IO::intValue("UserId");

	$sSQL = "SELECT * FROM tbl_admins WHERE id='$iUserId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iType      = $objDb->getField(0, "type_id");
	$sName      = $objDb->getField(0, "name");
	$sEmail     = $objDb->getField(0, "email");
	$sMobile    = $objDb->getField(0, "mobile");
	$sPicture   = $objDb->getField(0, "picture");
	$sProvinces = $objDb->getField(0, "provinces");
	$sDistricts = $objDb->getField(0, "districts");
	$sSchools   = $objDb->getField(0, "schools");
	$iRecords   = $objDb->getField(0, "records");
	$sTheme     = $objDb->getField(0, "theme");
	$sStatus    = $objDb->getField(0, "status");


	$iProvinces = @explode(",", $sProvinces);
	$iDistricts = @explode(",", $sDistricts);
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
  <form name="frmRecord" id="frmRecord">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="400">
		  <label for="txtName">Name</label>
		  <div><input type="text" name="txtName" id="txtName" value="<?= formValue($sName) ?>" maxlength="50" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtMobile">Mobile</label>
		  <div><input type="text" name="txtMobile" id="txtMobile" value="<?= $sMobile ?>" maxlength="25" size="25" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtEmail">Email Address</label>
		  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= $sEmail ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddType">User Type</label>

		  <div>
			<select name="ddType" id="ddType">
			  <option value=""></option>
<?
	$sTypes = getList("tbl_admin_types", "id", "title", "status='A'");

	foreach ($sTypes as $iTypeId => $sType)
	{
?>
			  <option value="<?= $iTypeId ?>"<?= (($iTypeId == $iType) ? ' selected' : '') ?>><?= $sType ?></option>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="filePicture">Picture <span><?= (($sPicture == "") ? ('(optional)') : ('(<a href="'.(SITE_URL.ADMINS_IMG_DIR.'originals/'.$sPicture).'" class="colorbox">'.substr($sPicture, strlen("{$iUserId}-")).'</a>)')) ?></span></label>
		  <div><input type="file" name="filePicture" id="filePicture" value="" size="60" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddRecords">Records per page</label>

		  <div>
			<select name="ddRecords" id="ddRecords">
			  <option value="10"<?= (($iRecords == 10) ? ' selected' : '') ?>>10</option>
			  <option value="25"<?= (($iRecords == 25) ? ' selected' : '') ?>>25</option>
			  <option value="50"<?= (($iRecords == 50 || $iRecords == 0) ? ' selected' : '') ?>>50</option>
			  <option value="100"<?= (($iRecords == 100) ? ' selected' : '') ?>>100</option>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddTheme">CMS Theme</label>

		  <div>
		    <select name="ddTheme" id="ddTheme">
			  <option value="smoothness"<?= (($sTheme == "smoothness") ? ' selected' : '') ?>>Black</option>
			  <option value="redmond"<?= (($sTheme == "redmond") ? ' selected' : '') ?>>Blue</option>
			  <option value="blitzer"<?= (($sTheme == "blitzer") ? ' selected' : '') ?>>Red</option>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
			<select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="D"<?= (($sStatus == 'D') ? ' selected' : '') ?>>Disabled</option>
			</select>
		  </div>
		</td>

		<td>
		  <label for="ddProvinces">Provinces</label>

		  <div class="multiSelect" style="width:400px; height:auto;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
	$sProvincesList = getList("tbl_provinces", "id", "name");

	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
			  <tr valign="top">
				<td width="25"><input type="checkbox" class="province" name="cbProvinces[]" id="cbProvince<?= $iProvince ?>" value="<?= $iProvince ?>" <?= ((@in_array($iProvince, $iProvinces)) ? 'checked' : '') ?> /></td>
				<td><label for="cbProvince<?= $iProvince ?>"><?= $sProvince ?></label></td>
			  </tr>
<?
	}
?>
			</table>
		  </div>

		  <div class="br10"></div>

		  <label for="ddDistrict">District</label>

		  <div class="multiSelect" style="width:400px; height:200px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
	$sDistrictsList = getList("tbl_districts", "id", "CONCAT(name, ' (', (SELECT code FROM tbl_provinces WHERE id=tbl_districts.province_id), ')')");

	foreach ($sDistrictsList as $iDistrict => $sDistrict)
	{
?>
			  <tr valign="top">
				<td width="25"><input type="checkbox" class="district" name="cbDistricts[]" id="cbDistrict<?= $iDistrict ?>" value="<?= $iDistrict ?>" <?= ((@in_array($iDistrict, $iDistricts)) ? 'checked' : '') ?> /></td>
				<td><label for="cbDistrict<?= $iDistrict ?>"><?= $sDistrict ?></label></td>
			  </tr>
<?
	}
?>
			</table>
		  </div>

		  <div class="br10"></div>

		  <label for="txtSchools">School(s)</label>

		  <div style="border:solid 1px #999999; padding:5px 5px 5px 10px; line-height:20px; background:#f9f9f9; width:calc(100% - 15px); max-height:300px; overflow:auto;">
<?
	if ($sSchools == "")
		$sSQL = "SELECT name, code FROM tbl_schools WHERE FIND_IN_SET(province_id, '$sProvinces') AND FIND_IN_SET(district_id, '$sDistricts')";

	else
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

		  <div class="br10"></div>
		  <div class="br10"></div>

		  <div>
		    <div class="grid">
			  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
			    <tr class="header">
				  <td width="25%">Section</td>
				  <td width="25%">Page</td>
				  <td width="10%" align="center">View</td>
				  <td width="10%" align="center">Add</td>
				  <td width="10%" align="center">Edit</td>
				  <td width="10%" align="center">Delete</td>
				  <td width="10%" align="center">ALL</td>
			    </tr>
<?
	$sSQL = "SELECT id, module, section
	         FROM tbl_admin_pages
	         WHERE id IN (SELECT page_id FROM tbl_admin_rights WHERE `view`='Y' AND admin_id='{$_SESSION["AdminId"]}')
	         ORDER BY module, position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$sModule  = $objDb->getField($i, "module");
		$sSection = $objDb->getField($i, "section");


		$sView   = "";
		$sAdd    = "";
		$sEdit   = "";
		$sDelete = "";

		$sSQL = "SELECT `view`, `add`, `edit`, `delete` FROM tbl_admin_rights WHERE admin_id='$iUserId' AND page_id='$iId'";
		$objDb2->query($sSQL);

		if ($objDb2->getCount( ) == 1)
		{
			$sView   = $objDb2->getField(0, 'view');
			$sAdd    = $objDb2->getField(0, 'add');
			$sEdit   = $objDb2->getField(0, 'edit');
			$sDelete = $objDb2->getField(0, 'delete');
		}

		$sAll = (($sView == "Y" && $sAdd == "Y" && $sEdit == "Y" && $sDelete == "Y") ? "Y" : "");
?>

			    <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
				  <td><input type="hidden" name="PageId<?= $i ?>" value="<?= $iId ?>" /><?= $sModule ?></td>
				  <td><?= $sSection ?></td>
				  <td align="center"><input type="checkbox" name="cbView<?= $i ?>" id="cbView<?= $i ?>" value="Y" <?= (($sView == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbAdd<?= $i ?>" id="cbAdd<?= $i ?>" value="Y" <?= (($sAdd == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbEdit<?= $i ?>" id="cbEdit<?= $i ?>" value="Y" <?= (($sEdit == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbDelete<?= $i ?>" id="cbDelete<?= $i ?>" value="Y" <?= (($sDelete == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbAll<?= $i ?>" id="cbAll<?= $i ?>" value="Y" <?= (($sAll == "Y") ? "checked" : "") ?> /></td>
			    </tr>
<?
	}
?>
			  </table>
		    </div>
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
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>