<?
	$sSQL  = "DELETE FROM tbl_sor_section_d WHERE sor_id='$iSorId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		$sSQL = "INSERT INTO tbl_sor_section_d SET sor_id='$iSorId', ";

		$sFloorHeight        = IO::strValue("floor_height");
                $sOther            = IO::strValue("other");
                $sComments          = IO::strValue("comments");
		
		$sSQL .= ("floor_height   = '".$sFloorHeight."',
                                other   = '".$sOther."',
                                comments      = '".$sComments."'");
		
		$bFlag = $objDb->execute($sSQL);
	}
      
        if ($bFlag == true)
        {
            
            foreach($_FILES["fileFixedSection"]['name'] as $iFile => $sFileName)
            {        
                if ($sFileName != "")
                {
                        $time = strtotime(date('Y-m-d h:i:s'));
                        $exts = explode('.', $sFileName);
                        $extension = end($exts);

                        if(in_array(strtolower($extension), array('jpg','jpeg','tiff','gif','bmp','png','pdf')))
                        {
                            $sPicture = ($iSorId."-S".$iSectionId.'-'.$time.'.'.$extension);

                            if (@move_uploaded_file($_FILES["fileFixedSection"]['tmp_name'][$iFile], ($sRootDir.SORS_DOC_DIR.$sPicture)))
                            {

                                    $iPicture = getNextId("tbl_sor_documents");

                                    $sSQL  = "INSERT INTO tbl_sor_documents SET id          = '$iPicture',
                                                                                sor_id      = '$iSorId',
                                                                                section_id  = '$iSectionId',
                                                                                document    = '$sPicture'";
                                    $bFlag = $objDb->execute($sSQL);
                            }
                        }
                }
            
            }
        } 
?>