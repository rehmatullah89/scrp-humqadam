<?
	$sSQL  = "DELETE FROM tbl_sor_section_a WHERE sor_id='$iSorId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		$sSQL = "INSERT INTO tbl_sor_section_a SET sor_id='$iSorId', ";

		$sAttendance        = IO::strValue("attendance");
                $sBlocks            = IO::strValue("blocks");
                $sGrades            = IO::strValue("grades");
                $sClassRooms        = IO::strValue("class_rooms");
                $sNToilets          = IO::strValue("normal_toilets");
                $sDToilets          = IO::strValue("disable_toilets");
                $sClassRamps        = IO::strValue("classroom_ramps");
                $sToiletRamps       = IO::strValue("toilet_ramps");
                $sSorAScienceLab    = IO::strValue("science_lab");
                $sSorAITLab         = IO::strValue("it_lab");
                $sSorALibrary       = IO::strValue("library");
                $sSorAExamHall      = IO::strValue("exam_hall");
                $sSorAPrincOffice   = IO::strValue("principal_office");
                $sSorAClerkOffice   = IO::strValue("clerk_office");
                $sSorAStaffRooms    = IO::strValue("staff_room");
                $sSorAChowkidarHut  = IO::strValue("chowkidar_hut");
                $sSorACycleStand    = IO::strValue("cycle_stand");
                $sInfoCorrect       = IO::strValue("info_correct");
                $sDesignCorrect     = IO::strValue("design_correct");
                $sComments          = IO::strValue("comments");
		
		$sSQL .= ("attendance   = '".$sAttendance."',
                                blocks   = '".$sBlocks."',
                                grades   = '".$sGrades."',
                                class_rooms   = '".$sClassRooms."',
                                normal_toilets   = '".$sNToilets."',
                                disable_toilets   = '".$sDToilets."',
                                classroom_ramps   = '".$sClassRamps."',
                                toilet_ramps   = '".$sToiletRamps."',
                                science_lab   = '".$sSorAScienceLab."',
                                it_lab   = '".$sSorAITLab."',
                                library   = '".$sSorALibrary."',
                                exam_hall   = '".$sSorAExamHall."',
                                principal_office   = '".$sSorAPrincOffice."',
                                clerk_office   = '".$sSorAClerkOffice."',
                                staff_room   = '".$sSorAStaffRooms."',
                                chowkidar_hut   = '".$sSorAChowkidarHut."',
                                cycle_stand   = '".$sSorACycleStand."',
                                info_correct   = '".$sInfoCorrect."',
                                design_correct   = '".$sDesignCorrect."',    
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