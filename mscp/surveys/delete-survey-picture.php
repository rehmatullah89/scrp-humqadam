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

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iSurveyId   = IO::intValue("SurveyId");
	$iSectionId  = IO::intValue("SectionId");
	$iQuestionId = IO::intValue("QuestionId");
	$iPictureId  = IO::intValue("PictureId");
	$sPicture    = IO::strValue("Picture");


	$sSQL = "DELETE FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='$iSectionId' AND question_id='$iQuestionId' AND id='$iPictureId'";

	if ($objDb->execute($sSQL) == true)
	{
		@unlink($sRootDir.SURVEYS_DOC_DIR.$sPicture);
		
		
		if ($iSectionId == 15)
		{
			$iInCompletedSections = (int)getDbValue("COUNT(1)", "tbl_survey_details", "status!='C' AND survey_id='$iSurveyId'");
			$sStatus              = getDbValue("status", "tbl_survey_details", "survey_id='$iSurveyId' AND section_id='$iSectionId'");
			$sCompleted           = getDbValue("completed", "tbl_surveys", "id='$iSurveyId'");
			

			$sSQL = "SELECT site_plan_pdf, structure_pdf, drawing, structure FROM tbl_survey_checklist WHERE survey_id='$iSurveyId'";
			$objDb->query($sSQL);
			
			$sDrawingPdf   = $objDb->getField(0, "site_plan_pdf");
			$sDrawingDwg   = $objDb->getField(0, "drawing");
			$sStructurePdf = $objDb->getField(0, "structure_pdf");
			$sStructureDwg = $objDb->getField(0, "structure");

			
			if ($sDrawingPdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingPdf) ||
				$sDrawingDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingDwg) ||
				$sStructurePdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf) ||
				$sStructureDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructureDwg) ||
				$iInCompletedSections > 0)
				$sCompleted = "N";
				
			if ($sDrawingPdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingPdf) ||
				$sDrawingDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sDrawingDwg) ||
				$sStructurePdf == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructurePdf) ||
				$sStructureDwg == "" || !@file_exists($sRootDir.SURVEYS_DOC_DIR.$sStructureDwg) )
				$sStatus = "I";

				
			$sSQL = "UPDATE tbl_surveys SET completed   = '$sCompleted'
											modified_by = '{$_SESSION['AdminId']}',
											modified_at = NOW( )
					 WHERE id='$iSurveyId'";
			$bFlag = $objDb->execute($sSQL);
			
			if ($bFlag == true)
			{
				$sSQL = "UPDATE tbl_survey_details SET status      = '$sStatus',
													   modified_by = '{$_SESSION['AdminId']}',
													   modified_at = NOW( )
						 WHERE survey_id='$iSurveyId' AND section_id='$iSectionId'";
				$bFlag = $objDb->execute($sSQL);
			}
		}
		

		redirect($_SERVER['HTTP_REFERER'], "SURVEY_PICTURE_DELETED");
	}


	redirect($_SERVER['HTTP_REFERER'], "DB_ERROR");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>