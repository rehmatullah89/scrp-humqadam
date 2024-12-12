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

	$_SESSION["Flag"] = "";

	$sTitle  = IO::strValue("txtTitle");
	$sStatus = IO::strValue("ddStatus");
	$sReset  = IO::strValue("cbReset");


	if ($sTitle == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_admin_types WHERE title LIKE '$sTitle' AND id!='$iTypeId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "USER_TYPE_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$sSQL = "UPDATE tbl_admin_types SET title  = '$sTitle',
		                                    status = '$sStatus'
		         WHERE id='$iTypeId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL  = "DELETE FROM tbl_admin_type_rights WHERE type_id='$iTypeId'";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$iPageCount = getDbValue("COUNT(1)", "tbl_admin_pages");

			for ($i = 0; $i < $iPageCount; $i ++)
			{
				$iPageId = IO::intValue("PageId{$i}");
				$sView   = IO::strValue("cbView{$i}");
				$sAdd    = IO::strValue("cbAdd{$i}");
				$sEdit   = IO::strValue("cbEdit{$i}");
				$sDelete = IO::strValue("cbDelete{$i}");

				if ($sView != "" || $sAdd != "" || $sEdit != "" || $sDelete != "")
				{
					$sSQL = "INSERT INTO tbl_admin_type_rights SET type_id  = '$iTypeId',
																   page_id  = '$iPageId',
																   `view`   = '$sView',
																   `add`    = '$sAdd',
																   `edit`   = '$sEdit',
																   `delete` = '$sDelete'";
					$bFlag = $objDb->execute($sSQL);

					if ($bFlag == false)
						break;
				}
			}
		}

		if ($bFlag == true && $sReset == "Y")
		{
			$sSQL  = "DELETE FROM tbl_admin_rights WHERE admin_id IN (SELECT id FROM tbl_admins WHERE type_id='$iTypeId')";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL = "SELECT p.id, r.view, r.add, r.edit, r.delete FROM tbl_admin_type_rights r, tbl_admin_pages p WHERE r.page_id=p.id AND r.type_id='$iTypeId' ORDER BY p.id";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );

				for ($i = 0; $i < $iCount; $i ++)
				{
					$iPage   = $objDb->getField($i, "id");
					$sView	 = $objDb->getField($i, 'view');
					$sAdd	 = $objDb->getField($i, 'add');
					$sEdit	 = $objDb->getField($i, 'edit');
					$sDelete = $objDb->getField($i, 'delete');

					if ($sView != "" || $sAdd != "" || $sEdit != "" || $sDelete != "")
					{
						$sSQL  = "INSERT INTO tbl_admin_rights (admin_id, page_id, `view`, `add`, `edit`, `delete`) (SELECT id, '$iPage', '$sView', '$sAdd', '$sEdit', '$sDelete' FROM tbl_admins WHERE type_id='$iTypeId')";
						$bFlag = $objDb2->execute($sSQL);

						if ($bFlag == false)
							break;
					}
				}
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sTitle) ?>";
		sFields[1] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[2] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateType(<?= $iTypeId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#TypeGridMsg", "success", "The selected Admin User Type has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>