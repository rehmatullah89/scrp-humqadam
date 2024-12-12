<?
	$sSQL  = "DELETE FROM tbl_sor_section_b WHERE sor_id='$iSorId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		$sSQL = "INSERT INTO tbl_sor_section_b SET sor_id='$iSorId', ";
                $sComments          = IO::strValue("comments");
		
		$sSQL .= ("comments      = '".$sComments."'");
		
		$bFlag = $objDb->execute($sSQL);
	}
        
        if ($bFlag == true)
	{
		$sSQL  = "DELETE FROM tbl_sor_section_b_details WHERE sor_id='$iSorId'";
                $bFlag = $objDb->execute($sSQL);
	}
        
        if ($bFlag == true)
	{
            $sFacilitiesList = getList("tbl_sor_facilities", "id", "name", "status='A' AND position>0", "position");
            
            foreach($sFacilitiesList as $iFacilityId => $sFacility){
            
               $iSorSecBId      = getNextId("tbl_sor_section_b_details");
               $sNumbers        = IO::intValue("Numbers_".$iFacilityId);
               $sSpaceAvailable = IO::strValue("facility_".$iFacilityId);
               $sComments       = IO::strValue("comments_".$iFacilityId);
                        
               $sSQL = "INSERT INTO tbl_sor_section_b_details SET id='$iSorSecBId', sor_id='$iSorId', ";
               
		$sSQL .= ("facility_id          = '".$iFacilityId."',
                                numbers         = '".$sNumbers."',
                                space_available = '".$sSpaceAvailable."',    
                                comments      = '".$sComments."'");
		
		$bFlag = $objDb->execute($sSQL);
                
                if($bFlag == false)
                    break;
                
            }
		
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