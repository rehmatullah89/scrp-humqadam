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

	@require_once("../requires/common.php");
	@require_once("{$sRootDir}requires/fpdf/fpdf.php");
	@require_once("{$sRootDir}requires/fpdi/fpdi.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	$iSorId = IO::intValue("Id");
        
        $sSQL = "SELECT ss.*, s.village, s.name, s.code, s.city, s.tehsil FROM tbl_sor_schools ss, tbl_schools s WHERE s.id = ss.school_id AND ss.id='$iSorId'";
        $objDb->query($sSQL);
            
        $sCode              = $objDb->getField(0, "code");
        $sSchool            = $objDb->getField(0, "name");
        $sCity              = $objDb->getField(0, "city");
        $sTehsil            = $objDb->getField(0, "tehsil");
        $sVillage           = $objDb->getField(0, "village");
        $iSchool            = $objDb->getField(0, "school_id");
        $iDistrict          = $objDb->getField(0, "district_id");
	$sDate              = $objDb->getField(0, "date");
	$sHeadTeacher       = $objDb->getField(0, "head_teacher");
        $sSiteEngineer      = $objDb->getField(0, "site_engineer");
        $sPtcRepresentative = $objDb->getField(0, "ptc_representative");
        $sPtcPosition       = $objDb->getField(0, "rep_position");
        $sContactDetails    = $objDb->getField(0, "rep_contact_details");        
        $iClassRooms        = $objDb->getField(0, "classrooms");
        $iScienceLabs       = $objDb->getField(0, "science_labs");
        $iLibraries         = $objDb->getField(0, "libraries");
        $iChowkidarHuts     = $objDb->getField(0, "chowkidar_huts");
        $iStudentToilets    = $objDb->getField(0, "student_toilets");
        $iITLabs            = $objDb->getField(0, "it_labs");
        $iClerkOffices      = $objDb->getField(0, "clerk_offices");
        $iSoakagePits       = $objDb->getField(0, "soakage_pits");    
        $iStaffRooms        = $objDb->getField(0, "staff_rooms");    
        $iExamHalls         = $objDb->getField(0, "exam_halls");
        $iPrincipalOffice   = $objDb->getField(0, "principal_office");
        $iWaterSupply       = $objDb->getField(0, "water_supply");
        $iStaffToilets      = $objDb->getField(0, "staff_toilets");
        $iStores            = $objDb->getField(0, "stores");
        $iParkingStands     = $objDb->getField(0, "parking_stands");
        $iElectricSupply    = $objDb->getField(0, "electric_supply"); 
        $sOtherRequirements = $objDb->getField(0, "other_requirements");

            /////////////////////////////////////////Page #1///////////////////////////// 
            $objPdf = new FPDI("P", "pt", "A4");
            $objPdf->setSourceFile("{$sRootDir}templates/sor.pdf");
            $iTemplateId = $objPdf->importPage(1, '/MediaBox');

            $objPdf->addPage( );
            $objPdf->useTemplate($iTemplateId, 0, 0);
            $objPdf->SetTextColor(50, 50, 50);
            $objPdf->SetFont('Arial', 'B', 10);

            @list($iYear, $iMonth, $iDay) = @explode("-", $sDate);
            
            $objPdf->Text(100, 160, implode('   ',str_split($iDay)));
            $objPdf->Text(175, 160, implode('   ',str_split($iMonth)));
            $objPdf->Text(240, 160, implode('    ',str_split($iYear)));
            
            $objPdf->Text(195, 192, $sCode); 
            $objPdf->Text(195, 220, $sSchool);

            $objPdf->Text(195, 248, $sCity);
            $objPdf->Text(195, 276, $sTehsil);
            $objPdf->Text(415, 248, getDbValue("name", "tbl_districts", "id='$iDistrict'"));
            $objPdf->Text(415, 276, $sVillage);
            
            $objPdf->Text(195, 302, $sHeadTeacher);
            $objPdf->Text(195, 330, $sSiteEngineer);
            $objPdf->Text(195, 358, $sPtcRepresentative);
            $objPdf->Text(195, 386, $sPtcPosition);
            $objPdf->Text(195, 414, $sContactDetails);
            
            $objPdf->SetFont('Arial', '', 8);
            
            
            $objPdf->Text(127, 485, implode('   ',str_split($iClassRooms)));
            $objPdf->Text(127, 509, implode('   ',str_split($iStudentToilets)));
            $objPdf->Text(127, 534, implode('   ',str_split($iStaffRooms)));
            $objPdf->Text(127, 560, implode('   ',str_split($iStaffToilets)));
            
            $objPdf->Text(265, 485, implode('   ',str_split($iScienceLabs)));
            $objPdf->Text(265, 509, implode('   ',str_split($iITLabs)));
            $objPdf->Text(265, 534, implode('   ',str_split($iExamHalls)));
            $objPdf->Text(265, 560, implode('   ',str_split($iStores)));
            
            $objPdf->Text(403, 485, implode('   ',str_split($iLibraries)));
            $objPdf->Text(403, 509, implode('   ',str_split($iClerkOffices)));
            $objPdf->Text(403, 534, implode('   ',str_split($iPrincipalOffice)));
            $objPdf->Text(403, 560, implode('   ',str_split($iParkingStands)));
            
            $objPdf->Text(541, 485, implode('   ',str_split($iChowkidarHuts)));
            $objPdf->Text(541, 509, implode('   ',str_split($iSoakagePits)));
            $objPdf->Text(541, 534, implode('   ',str_split($iWaterSupply)));
            $objPdf->Text(541, 560, implode('   ',str_split($iElectricSupply)));
                    
            $objPdf->SetXY(120, 575);
            $objPdf->MultiCell(420, 8, iconv('utf-8', 'cp1252', $sOtherRequirements));
            
            $objPdf->Text(180, 665, $sHeadTeacher);
            $objPdf->Text(180, 717, $sPtcRepresentative);
            
        ///////******** output PDF *********///////
        $objPdf->Output("SOR{$iId}.pdf", "D");



	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>