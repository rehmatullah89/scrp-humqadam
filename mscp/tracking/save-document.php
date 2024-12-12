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
		$sSQL = "SELECT * FROM tbl_documents WHERE school_id='$iSchool' AND type_id='$iDocType'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "DOCUMENT_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");

                $iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
                
                $iDocument = getNextId("tbl_documents");
                
		$sSQL = "INSERT INTO tbl_documents SET  id               = '$iDocument', 
                                                         district_id     = '$iDistrict',
		                                         school_id       = '$iSchool',
                                                         type_id         = '$iDocType',
		                                         comments        = '$sComments',
                                                         `date`          = '$sDate',   
		                                    	 created_by      = '{$_SESSION['AdminId']}',
		                                         created_at      = NOW( ),
		                                         modified_by     = '{$_SESSION['AdminId']}',
		                                         modified_at     = NOW( )";
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
			redirect("documents.php", "DOCUMENT_ADDED");
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