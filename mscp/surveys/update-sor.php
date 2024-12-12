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

        $sCode              = IO::strValue("txtCode");
	$sPrincipal         = IO::strValue("txtPrincipal");
        $sDistrictEngineer  = IO::intValue("ddEngineer");
        $sPtcRepresentative = IO::strValue("txtPtc");
        $sContactDetails    = IO::strValue("txtContact");        
        $sCcsiRepresentative= IO::strValue("txtCcsi");
	$sDate              = IO::strValue("txtDate");
        $iCountRows         = IO::intValue("CountRows");
	$bError             = true;
	
	if ($sCode == "" || $sPrincipal == "" || $sDistrictEngineer == "" || $sPtcRepresentative == "" || $sCcsiRepresentative == "" || $sDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
	
	if ($_SESSION["Flag"] == "")
	{
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
		
		if ($iSchool == 0)
			$_SESSION["Flag"] = "INVALID_EMIS_CODE";
	}
	
	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_sors WHERE school_id='$iSchool' AND id!='$iSorId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SOR_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
            
                $objDb->execute("BEGIN");
            
		$iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
			
			
		$sSQL = "UPDATE tbl_sors SET school_id   = '$iSchool',
                                            district_id  = '$iDistrict',
                                            admin_id     = '{$_SESSION['AdminId']}',
                                            engineer_id  = '$sDistrictEngineer',
                                            principal    = '$sPrincipal',
                                            ccsi         = '$sCcsiRepresentative',
                                            ptc          = '$sPtcRepresentative',
                                            contact_no   = '$sContactDetails',
                                            `date`       = '$sDate',											 
                                            modified_by = '{$_SESSION['AdminId']}',
                                            modified_at = NOW( )
                                        WHERE id='$iSorId'";
                
                $bFlag = $objDb->execute($sSQL);
                
                if($bFlag == true)
                {
                    $sSQL = "DELETE FROM tbl_sor_participants WHERE sor_id ='$iSorId'";
                    $bFlag = $objDb->execute($sSQL);
                            
                    for($i=1; $i<=$iCountRows ; $i++){
                        $sPName   = IO::strValue("pname_".$i);
                        $sPDesign = IO::strValue("pdesignation_".$i);
                        
                        if($sPName != "" && $sPDesign != "")
                        {
                            $iSorParticipantId = getNextId("tbl_sor_participants");

                            $sSQL = "INSERT INTO tbl_sor_participants SET id = '$iSorParticipantId',
                                                    sor_id    = '$iSorId',
                                                    name     = '$sPName',
                                                    designation  = '$sPDesign'";

                            $bFlag = $objDb->execute($sSQL);
                        }
                        if($bFlag == false)
                            break;
                    }
                }                            
		
		if($bFlag == true)
		{
                        
                        $objDb->execute("COMMIT");    
    
                        $sSchool     = getDbValue("name", "tbl_schools", "id='$iSchool'");
			$sDistrict   = getDbValue("name", "tbl_districts", "id='$iDistrict'");
			
			$sSQL = "SELECT created_at, modified_at,app,status,completed,
                                                        (SELECT name FROM tbl_admins WHERE id=tbl_sors.admin_id) AS _CDC,
							(SELECT name FROM tbl_admins WHERE id=tbl_sors.created_by) AS _CreatedBy,
							(SELECT name FROM tbl_admins WHERE id=tbl_sors.modified_by) AS _ModifiedBy
					 FROM tbl_sors
					 WHERE id='$iSorId'";
			$objDb->query($sSQL);

                        $sApp        = $objDb->getField(0, "app");
                        $sStatus     = $objDb->getField(0, "status");
                        $sCompleted  = $objDb->getField(0, "completed");      
			$sCreatedAt  = $objDb->getField(0, "created_at");
			$sCreatedBy  = $objDb->getField(0, "_CreatedBy");
			$sModifiedAt = $objDb->getField(0, "modified_at");
			$sModifiedBy = $objDb->getField(0, "_ModifiedBy");
                        $sCDC        = $objDb->getField(0, "_CDC");

			
			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

                sFields[0] = "<?= @htmlentities($iSorId) ?>";
		sFields[1] = "<?= @htmlentities($sSchool) ?>";
		sFields[2] = "<?= @htmlentities($sCode) ?>";
		sFields[3] = "<?= @htmlentities($sDistrict) ?>";
                sFields[4] = "<?= @htmlentities($sCDC) ?>";
                sFields[5] = "<?= formatDate($sDate, $_SESSION['DateFormat']) ?>";
                sFields[6] = "<?= @htmlentities((($sApp == 'Y' && $sStatus == 'I') ? "Syncing" : "Synced")) ?>";
                sFields[7] = "<?= @htmlentities((($sCompleted == "Y") ? "Completed" : "In-Complete")) ?>";
		sFields[8] = '<img class="icon details" id="<?= $iSorId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
                            sFields[8] = (sFields[8] + '<img class="icnEdit" id="<?= $iSorId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
                            sFields[8] = (sFields[8] + '<img class="icnSor icon" rel="<?= $sSchool ?>" id="<?= $iSorId ?>" src="images/icons/stats.gif" alt="Sor Details" title="Sor Details" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
                        sFields[8] = (sFields[8] + '<img class="icnDelete" id="<?= $iSorId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sFields[8] = (sFields[8] + '<img class="icnView" id="<?= $iSorId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');
                sFields[8] = (sFields[8] + '<a href="<?= $sCurDir ?>/export-sor-form.php?SorId=<?= $iSorId ?>"><img class="icnPdf" src="images/icons/pdf.png" alt="Export PDF" title="Export PDF" /> </a>');

                parent.updateRecord(<?= $iSorId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected SOR Record has been Updated successfully.");
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