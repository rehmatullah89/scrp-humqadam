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

        $iIndex      = IO::strValue("Index");
        $iDocument   = IO::strValue("DocumentId");
        $sDate       = IO::strValue("txtDate");
	$sCode       = IO::strValue("txtCode");
	$iDocType    = IO::intValue("ddDocType");
	$sComments   = IO::strValue("txtComments");
	$iFiles      = IO::intValue("Files_count");

	$sFiles        = array( );
	$bError        = true;


	if ($sCode == "" || $iDocType == 0)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
        
        if ($_SESSION["Flag"] == "")
	{
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
		
		if ($iSchool == 0)
			$_SESSION["Flag"] = "INVALID_EMIS_CODE";
	}
        
  
	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");

                $iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
                
		$sSQL = "UPDATE tbl_documents SET        district_id     = '$iDistrict',
		                                         school_id       = '$iSchool',
		                                         type_id         = '$iDocType',
		                                         comments        = '$sComments',
                                                         `date`          = '$sDate',   
		                                    	 modified_by     = '{$_SESSION['AdminId']}',
		                                         modified_at     = NOW( )
                                                         Where id = '$iDocument'";
		$bFlag = $objDb->execute($sSQL);
                
                if ($bFlag == true && $_FILES['filePicture']['name'] != "")
		{
                    
                        $sPicture = ($iDocument."-".IO::getFileName($_FILES['filePicture']['name']));
                        
                        if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.DOCUMENTS_DIR.$sPicture))){
                            
                            $iFile = getNextId("tbl_document_files");
                            
                            $sSQL = "INSERT INTO tbl_document_files SET id      = '$iFile',
                                                                                document_id = '$iDocument',
                                                                                admin_id    = '{$_SESSION['AdminId']}',    
                                                                                file        = '$sPicture',
                                                                                date_time   = NOW( )";
                            $bFlag = $objDb->execute($sSQL);
                        
                        }
         
		}

		if ($bFlag == true && $_FILES['fileDocument']['name'] != "")
		{
			$sDocument = ($iDocument."-".IO::getFileName($_FILES['fileDocument']['name']));

			if (@move_uploaded_file($_FILES['fileDocument']['tmp_name'], ($sRootDir.DOCUMENTS_DIR.$sDocument))){
                            
                            $iFile = getNextId("tbl_document_files");
                            
                            $sSQL = "INSERT INTO tbl_document_files SET id      = '$iFile',
                                                                                document_id = '$iDocument',
                                                                                admin_id    = '{$_SESSION['AdminId']}',    
                                                                                file        = '$sDocument',
                                                                                date_time   = NOW( )";
                            $bFlag = $objDb->execute($sSQL);
                        
                        }		
		}
		
		if ($bFlag == true)
		{
			for ($i = 0; $i < $iFiles; $i ++)
			{
				$sUploadName   = IO::strValue("Files_{$i}_name");
				$sUploadStatus = IO::strValue("Files_{$i}_status");


				if ($sUploadStatus == "done" && $sUploadName != "")
				{
					$iPosition  = @strrpos($sUploadName, '.');
					$sExtension = @substr($sUploadName, $iPosition);

                                        $iFile = getNextId("tbl_document_files");
                                        
                                        $DateTime = strtotime(date("Y-m-d h:i:s"));
					$sFile = ("{$iDocument}-{$iFile}-{$iDocType}-{$DateTime}".$sExtension);
                                        
					if (@in_array($sExtension, array(".jpg", ".jpeg", ".png", ".gif", ".pdf", ".doc", ".docx", ".xls", ".xlsx")))
						copy(($sRootDir.TEMP_DIR.$sUploadName), ($sRootDir.DOCUMENTS_DIR.$sFile));

					$sSQL = "INSERT INTO tbl_document_files SET id      = '$iFile',
                                                                                document_id = '$iDocument',
                                                                                admin_id    = '{$_SESSION['AdminId']}',    
                                                                                file        = '$sFile',
                                                                                date_time   = NOW( )";
					$bFlag = $objDb->execute($sSQL);

					if ($bFlag == false)
						break;


					$sFiles[] = $sFile;
				}


				@unlink($sRootDir.TEMP_DIR.IO::strValue("Files_{$i}_name"));
			}
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
                        
                        $sDistrictsList = getList("tbl_districts", "id", "name");
                        
                        $sSQL = "SELECT d.id, d.created_at, d.modified_at,
                        s.code, s.name, s.district_id,
                                                (SELECT title FROM tbl_document_types WHERE id=d.type_id) AS _DocType,
						(SELECT name FROM tbl_admins WHERE id=d.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=d.modified_by) AS _ModifiedBy
		         FROM tbl_documents d, tbl_schools s
		         WHERE d.school_id=s.id AND d.id='$iDocument'
                         ORDER BY d.id";
                       
			$objDb->query($sSQL);

			$sDocType    = $objDb->getField(0, "_DocType");
			$sSchool     = $objDb->getField(0, "name");
			$sCode       = $objDb->getField(0, "code");
			$iDistrict   = $objDb->getField(0, "district_id");
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

		sFields[0] = "<?= @htmlentities($sSchool) ?>";
		sFields[1] = "<?= @htmlentities($sCode) ?>";
		sFields[2] = "<?= @htmlentities($sDistrictsList[$iDistrict]) ?>";
		sFields[3] = "<?= @htmlentities($sDocType) ?>";
		sFields[4] = "<?= @htmlentities($sCreatedBy) ?>";
                sFields[5] = "<?= @htmlentities(formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")) ?>";
		sFields[6] = '<img class="icon details" id="<?= $iStudentId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnEdit" id="<?= $iDocument ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnDelete" id="<?= $iDocument ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sFields[6] = (sFields[6] + '<img class="icnView" id="<?= $iDocument ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iDocument ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Document Record has been Updated successfully.");
	-->
	</script>    

                                
<?                        
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";

                        for ($i = 0; $i < count($sFiles); $i ++)
			{
				@unlink($sRootDir.TEMP_DIR.$sFiles[$i]);
			}
		}
	}
?>
