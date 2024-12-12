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

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iTypeId = IO::intValue("TypeId");
	$iIndex  = IO::intValue("Index");

	if ($_POST)
		@include("update-user-type.php");


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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-user-type.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="TypeId" id="TypeId" value="<?= $iTypeId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateType" id="DuplicateType" value="0" />
	<div id="RecordMsg" class="hidden"></div>

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

		  <div class="br10"></div>

		  <label for="cbReset" class="noPadding"><input type="checkbox" name="cbReset" id="cbReset" value="Y" /> Reset this User Group Rights</label>

		  <br />
		  <button id="BtnSave">Save Type</button>
		  <button id="BtnCancel">Cancel</button>
		</td>

		<td>
<?
	$sSQL = "SELECT * FROM tbl_admin_pages ORDER BY module, position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>
		  <input type="hidden" name="PageCount" id="PageCount" value="<?= $iCount ?>" />

		  <div class="grid" style="max-height:420px; overflow:auto;">
		    <div class="grid">
 			  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
			    <tr class="header">
				  <td width="25%">Section</td>
				  <td width="25%">Page</td>
				  <td width="10%" align="center"><a href="#" id="View">View</a></td>
				  <td width="10%" align="center"><a href="#" id="Add">Add</a></td>
				  <td width="10%" align="center"><a href="#" id="Edit">Edit</a></td>
				  <td width="10%" align="center"><a href="#" id="Delete">Delete</a></td>
				  <td width="10%" align="center"><a href="#" id="All">ALL</a></td>
			    </tr>
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$sModule  = $objDb->getField($i, "module");
		$sSection = $objDb->getField($i, "section");


		$sViewRights   = "";
		$sAddRights    = "";
		$sEditRights   = "";
		$sDeleteRights = "";

		$sSQL = "SELECT `view`, `add`, `edit`, `delete` FROM tbl_admin_type_rights WHERE type_id='$iTypeId' AND page_id='$iId'";
		$objDb2->query($sSQL);

		if ($objDb2->getCount( ) == 1)
		{
			$sViewRights   = $objDb2->getField(0, 'view');
			$sAddRights    = $objDb2->getField(0, 'add');
			$sEditRights   = $objDb2->getField(0, 'edit');
			$sDeleteRights = $objDb2->getField(0, 'delete');
		}

		$sAllRights = (($sViewRights == "Y" && $sAddRights == "Y" && $sEditRights == "Y" && $sDeleteRights == "Y") ? "Y" : "");
?>

			    <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
				  <td>
				    <input type="hidden" name="PageId<?= $i ?>" value="<?= $iId ?>" />
				    <?= $sModule ?>
				  </td>

				  <td><?= $sSection ?></td>
				  <td align="center"><input type="checkbox" name="cbView<?= $i ?>" id="cbView<?= $i ?>" value="Y" <?= (($sViewRights == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbAdd<?= $i ?>" id="cbAdd<?= $i ?>" value="Y" <?= (($sAddRights == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbEdit<?= $i ?>" id="cbEdit<?= $i ?>" value="Y" <?= (($sEditRights == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbDelete<?= $i ?>" id="cbDelete<?= $i ?>" value="Y" <?= (($sDeleteRights == "Y") ? "checked" : "") ?> /></td>
				  <td align="center"><input type="checkbox" name="cbAll<?= $i ?>" id="cbAll<?= $i ?>" value="Y" <?= (($sAllRights == "Y") ? "checked" : "") ?> /></td>
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