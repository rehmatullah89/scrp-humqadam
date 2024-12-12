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

	$iTypeId = IO::intValue("TypeId");

	$sSQL = "SELECT * FROM tbl_admin_types WHERE id='$iTypeId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle  = $objDb->getField(0, "title");
	$sStatus = $objDb->getField(0, "status");
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
		<td width="380">
		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="100" size="44" class="textbox" /></div>

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
		  <div class="grid" style="max-height:420px; overflow:auto;">
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
	$sSQL = "SELECT * FROM tbl_admin_pages ORDER BY module, position";
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

		$sSQL = "SELECT `view`, `add`, `edit`, `delete` FROM tbl_admin_type_rights WHERE type_id='$iTypeId' AND page_id='$iId'";
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