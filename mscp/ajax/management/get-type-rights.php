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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	$iTypeId = IO::intValue("Type");


	$sSQL = "SELECT ap.id, ap.module, ap.section, ar.`add`, ar.`edit`, ar.`delete`
			 FROM tbl_admin_pages ap, tbl_admin_rights ar
			 WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}'
			 ORDER BY ap.id";
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
				    <td width="10%" align="center">ALL</td>
			      </tr>
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$sModule  = $objDb->getField($i, "module");
		$sSection = $objDb->getField($i, "section");
		$sAdd     = $objDb->getField($i, "add");
		$sEdit    = $objDb->getField($i, "edit");
		$sDelete  = $objDb->getField($i, "delete");


		$sSQL = "SELECT `view`, `add`, `edit`, `delete` FROM tbl_admin_type_rights WHERE type_id='$iTypeId' AND page_id='$iId'";
		$objDb2->query($sSQL);

		$sViewRights   = $objDb2->getField(0, 'view');
		$sAddRights    = $objDb2->getField(0, 'add');
		$sEditRights   = $objDb2->getField(0, 'edit');
		$sDeleteRights = $objDb2->getField(0, 'delete');

		$sAllRights    = (($sViewRights == "Y" && $sAddRights == "Y" && $sEditRights == "Y" && $sDeleteRights == "Y") ? "Y" : "");
?>

			      <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
				    <td><input type="hidden" name="PageId<?= $i ?>" value="<?= $iId ?>" /><?= $sModule ?></td>
				    <td><?= $sSection ?></td>
				    <td align="center"><input type="checkbox" name="cbView<?= $i ?>" id="cbView<?= $i ?>" value="Y" <?= (($sViewRights == "Y") ? "checked" : "") ?> /></td>
				    <td align="center"><input type="checkbox" name="cbAdd<?= $i ?>" id="cbAdd<?= $i ?>" value="Y" <?= (($sAddRights == "Y") ? "checked" : "") ?> <?= (($sAdd == "Y") ? "" : "disabled") ?> /></td>
				    <td align="center"><input type="checkbox" name="cbEdit<?= $i ?>" id="cbEdit<?= $i ?>" value="Y" <?= (($sEditRights == "Y") ? "checked" : "") ?> <?= (($sEdit == "Y") ? "" : "disabled") ?> /></td>
				    <td align="center"><input type="checkbox" name="cbDelete<?= $i ?>" id="cbDelete<?= $i ?>" value="Y" <?= (($sDeleteRights == "Y") ? "checked" : "") ?> <?= (($sDelete == "Y") ? "" : "disabled") ?> /></td>
				    <td align="center"><input type="checkbox" name="cbAll<?= $i ?>" id="cbAll<?= $i ?>" value="Y" <?= (($sAllRights == "Y") ? "checked" : "") ?> /></td>
			      </tr>
<?
	}
?>
			    </table>
			  </div>
		    </div>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>