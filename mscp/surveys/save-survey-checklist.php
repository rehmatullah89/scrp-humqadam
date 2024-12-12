<?
	$sSQL = "DELETE FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
	$bFlag = $objDb->execute($sSQL);
          
	if ($bFlag == true)
	{
		$sOldSitePlan     = IO::strValue("SitePlan");
		$sSitePlan        = "";
        $sOldSitePlanPdf  = IO::strValue("SitePlanPdf");
		$sSitePlanPdf     = "";
		$sOldDrawing      = IO::strValue("Drawing");
		$sDrawing         = "";
		$sOldStructure    = IO::strValue("Structure");
		$sStructure       = "";
		$sOldStructurePdf = IO::strValue("StructurePdf");
		$sStructurePdf    = "";
		
		
		if ($_FILES["fileSitePlan"]['name'] != "")
		{
			$sSitePlan = ($iSurveyId."-".$iSectionId."-sp-".IO::getFileName($_FILES["fileSitePlan"]['name']));

			if (!@move_uploaded_file($_FILES["fileSitePlan"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sSitePlan)))
				$sSitePlan = "";
		}
		
		else if ($sSitePlan == "" && $sOldSitePlan != "")
			$sSitePlan = $sOldSitePlan;


		if ($_FILES["fileSitePlanPdf"]['name'] != "")
		{
			$sSitePlanPdf = ($iSurveyId."-".$iSectionId."-pdf-".IO::getFileName($_FILES["fileSitePlanPdf"]['name']));

			if (!@move_uploaded_file($_FILES["fileSitePlanPdf"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sSitePlanPdf)))
				$sSitePlanPdf = "";
		}
		
		else if ($sSitePlanPdf == "" && $sOldSitePlanPdf != "")
			$sSitePlanPdf = $sOldSitePlanPdf;

		
		if ($_FILES["fileStructurePdf"]['name'] != "")
		{
			$sStructurePdf = ($iSurveyId."-".$iSectionId."-spdf-".IO::getFileName($_FILES["fileStructurePdf"]['name']));

			if (!@move_uploaded_file($_FILES["fileStructurePdf"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf)))
				$sStructurePdf = "";
		}
		
		else if ($sStructurePdf == "" && $sOldStructurePdf != "")
			$sStructurePdf = $sOldStructurePdf;
		
		
		if ($_FILES["fileDrawing"]['name'] != "")
		{
			$sDrawing = ($iSurveyId."-".$iSectionId."-dn-".IO::getFileName($_FILES["fileDrawing"]['name']));

			if (!@move_uploaded_file($_FILES["fileDrawing"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sDrawing)))
				$sDrawing = "";
		}
		
		else if ($sDrawing == "" && $sOldDrawing != "")
			$sDrawing = $sOldDrawing;
		
		
		if ($_FILES["fileStructure"]['name'] != "")
		{
			$sStructure = ($iSurveyId."-".$iSectionId."-ps-".IO::getFileName($_FILES["fileStructure"]['name']));

			if (!@move_uploaded_file($_FILES["fileStructure"]['tmp_name'], ($sRootDir.SURVEYS_DOC_DIR.$sStructure)))
				$sStructure = "";
		}
		
		else if ($sStructure == "" && $sOldStructure != "")
			$sStructure = $sOldStructure;
			
		
		$fTotalArea         = IO::floatValue("txtTotalArea");
		$iStudents          = IO::intValue("txtStudents");
		$sNorthArrow        = IO::strValue("cbNorthArrow");
		$sClassRooms        = IO::strValue("cbClassRooms");
		$sClassRoomSize     = IO::strValue("cbClassRoomSize");
		$sToilets           = IO::strValue("cbToilets");
		$sPlaygrounds       = IO::strValue("cbPlaygrounds");
		$sWaterTanks        = IO::strValue("cbWaterTanks");
		$sSepticTanks       = IO::strValue("cbSepticTanks");
		$sBoundaryWall      = IO::strValue("cbBoundaryWall");
		$sDrainage          = IO::strValue("cbDrainage");
		$sNewDevelopment    = IO::strValue("cbNewDevelopment");
		$sMeasurements      = IO::strValue("cbMeasurements");
		$sSlopes            = IO::strValue("cbSlopes");
		$sTreesSmaller      = IO::strValue("cbTreesSmaller");
		$sTreesLarger       = IO::strValue("cbTreesLarger");
		$sElectricity1Phase = IO::strValue("cbElectricity1Phase");
		$sElectricity3Phase = IO::strValue("cbElectricity3Phase");

		
		$sSQL = "INSERT INTO tbl_survey_checklist SET survey_id           = '$iSurveyId',
													  site_plan           = '$sSitePlan',
													  site_plan_pdf       = '$sSitePlanPdf',  
													  structure_pdf       = '$sStructurePdf',  
													  drawing             = '$sDrawing',
													  structure           = '$sStructure',
													  total_area          = '$fTotalArea',
													  students            = '$iStudents',
													  north_arrow         = '$sNorthArrow',
													  class_rooms         = '$sClassRooms',
													  class_room_size     = '$sClassRoomSize',
													  toilets             = '$sToilets',
													  playgrounds         = '$sPlaygrounds',
													  water_tanks         = '$sWaterTanks',
													  spetic_tanks        = '$sSepticTanks',
													  boundary_wall       = '$sBoundaryWall',
													  drainage            = '$sDrainage',
													  new_development     = '$sNewDevelopment',
													  measurements        = '$sMeasurements',
													  slopes              = '$sSlopes',
													  trees_smaller       = '$sTreesSmaller',											 
													  trees_larger        = '$sTreesLarger',
													  electricity_1_phase = '$sElectricity1Phase',
													  electricity_3_phase = '$sElectricity3Phase'";
		$bFlag = $objDb->execute($sSQL);
		
		if ($bFlag == true)
		{
			if ($sOldSitePlan != "" && $sSitePlan != $sOldSitePlan)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sOldSitePlan);
			
			if ($sOldSitePlanPdf != "" && $sSitePlanPdf != $sOldSitePlanPdf)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sOldSitePlanPdf);
			
			if ($sOldDrawing != "" && $sDrawing != $sOldDrawing)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sOldDrawing);
			
			if ($sOldStructure != "" && $sStructure != $sOldStructure)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sOldStructure);
			
			if ($sOldStructurePdf != "" && $sStructurePdf != $sOldStructurePdf)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sOldStructurePdf);
		}
		
		else
		{
			if ($sSitePlan != "" && $sSitePlan != $sOldSitePlan)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sSitePlan);

			if ($sSitePlanPdf != "" && $sSitePlanPdf != $sOldSitePlanPdf)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sSitePlanPdf);

			if ($sDrawing != "" && $sDrawing != $sOldDrawing)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sDrawing);
			
			if ($sStructure != "" && $sStructure != $sOldStructure)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sStructure);
			
			if ($sStructurePdf != "" && $sStructurePdf != $sOldStructurePdf)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf);
		}
	}
?>