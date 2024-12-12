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
	$bError           = true;
	$sStatus          = "C";
	$sCompleted       = "Y";
	$sNewPictures     = array( );


	$objDb->execute("BEGIN");
	
	
	$sSQL = "UPDATE tbl_sor_details SET status      = '$sStatus',
										   modified_by = '{$_SESSION['AdminId']}',
										   modified_at = NOW( )
			 WHERE sor_id='$iSorId' AND section_id='$iSectionId'";
	$bFlag = $objDb->execute($sSQL);

	if ($bFlag == true)
	{
                if ($iSectionId == 1)
                        @include("save-sor-section-a.php");

                else if ($iSectionId == 2)
                        @include("save-sor-section-b.php");

                else if ($iSectionId == 3)
                        @include("save-sor-section-c.php");

                else if ($iSectionId == 4)
                        @include("save-sor-section-d.php");
	}
		
	
	if ($bFlag == true)				 
	{
		$objDb->execute("COMMIT");
		
		$sSQL = "SELECT created_at, modified_at,
						(SELECT name FROM tbl_admins WHERE id=tbl_sor_details.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=tbl_sor_details.modified_by) AS _ModifiedBy
				 FROM tbl_sor_details
				 WHERE sor_id='$iSorId' AND section_id='$iSectionId'";
		$objDb->query($sSQL);

		$sCreatedAt  = $objDb->getField(0, "created_at");
		$sCreatedBy  = $objDb->getField(0, "_CreatedBy");
		$sModifiedAt = $objDb->getField(0, "modified_at");
		$sModifiedBy = $objDb->getField(0, "_ModifiedBy");


		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= (($sStatus == "C") ? "Completed" : "In-Complete") ?>";
		sFields[1] = '<img class="icon details" sor="<?= $iSorId ?>" section="<?= $iSectionId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[1] = (sFields[1] + '<img class="icnEdit" sor="<?= $iSorId ?>" section="<?= $iSectionId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}
?>
		sFields[1] = (sFields[1] + '<img class="icnView" sor="<?= $iSorId ?>" section="<?= $iSectionId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateSectionRecord(<?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#PageMsg", "success", "The selected Section Record has been Updated successfully.");
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
?>