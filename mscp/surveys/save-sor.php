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
		$sSQL = "SELECT * FROM tbl_sors WHERE school_id='$iSchool'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SOR_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
            
                $objDb->execute("BEGIN");

                $iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");

                $iSor = getNextId("tbl_sors");
		
		$sSQL = "INSERT INTO tbl_sors SET id         = '$iSor',
                                                school_id    = '$iSchool',
                                                district_id  = '$iDistrict',
                                                admin_id     = '{$_SESSION['AdminId']}',
                                                engineer_id  = '$sDistrictEngineer',
                                                principal    = '$sPrincipal',
                                                ccsi         = '$sCcsiRepresentative',
                                                ptc          = '$sPtcRepresentative',
                                                contact_no   = '$sContactDetails',
                                                `date`       = '$sDate',											 
                                                created_by   = '{$_SESSION['AdminId']}',
                                                created_at   = NOW( ),
                                                modified_by  = '{$_SESSION['AdminId']}',
                                                modified_at  = NOW( )";
                                                
		
               $bFlag = $objDb->execute($sSQL);
                    
                if ($bFlag == true)
		{
			$sSQL  = "INSERT INTO tbl_sor_details (sor_id, section_id, `status`, created_by, created_at, modified_by, modified_at)
			                                  (SELECT '$iSor', id, 'I', '{$_SESSION['AdminId']}', NOW( ), '{$_SESSION['AdminId']}', NOW( ) FROM tbl_sor_sections)";
			$bFlag = $objDb->execute($sSQL);	
		} 
                
                if($bFlag == true)
                {
                    for($i=1; $i<=$iCountRows ; $i++){
                        $sPName   = IO::strValue("pname_".$i);
                        $sPDesign = IO::strValue("pdesignation_".$i);
                        
                        if($sPName != "" && $sPDesign != "")
                        {
                            $iSorParticipantId = getNextId("tbl_sor_participants");

                            $sSQL = "INSERT INTO tbl_sor_participants SET id = '$iSorParticipantId',
                                                    sor_id    = '$iSor',
                                                    name     = '$sPName',
                                                    designation  = '$sPDesign'";

                            $bFlag = $objDb->execute($sSQL);
                        }
                        if($bFlag == false)
                            break;
                    }
                }
                if ($bFlag == true)
		{
			$objDb->execute("COMMIT");    
                    
			redirect("sors.php", "SOR_ADDED");
                }
		else{
                        $objDb->execute("ROLLBACK");
                    
			$_SESSION["Flag"] = "DB_ERROR";
                }
	}
?>