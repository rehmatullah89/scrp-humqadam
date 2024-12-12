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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sSurveys = IO::strValue("Surveys");

	if ($sSurveys != "")
	{
		$iSurveys  = @explode(",", $sSurveys);
		$sPictures = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iSurveys); $i ++)
		{
			$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='{$iSurveys[$i]}'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );
			
			for ($j = 0; $j < $iCount; $j ++)
				$sPictures[] = $objDb->getField($j, 0);


			$sSQL  = "UPDATE tbl_survey_schedules SET status='P' WHERE school_id=(SELECT school_id FROM tbl_surveys WHERE id='{$iSurveys[$i]}')";
			$bFlag = $objDb->execute($sSQL);						
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_surveys WHERE id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_details WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_answers WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_pictures WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}			

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_declaration WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_differently_abled_student_numbers WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_block_details WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_blocks WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}			
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_facilities WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}	

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_student_attendance_numbers WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_students_enrollment WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_teacher_numbers WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_other_blocks WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_checklist WHERE survey_id='{$iSurveys[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
				

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iSurveys) > 1)
				print "success|-|The selected Survey Records have been Deleted successfully.";

			else
				print "success|-|The selected Survey Record has been Deleted successfully.";
			
			
			for ($i = 0; $i < count($sPictures); $i ++)
				@unlink($sRootDir.SURVEYS_DOC_DIR.$sPictures[$i]);			
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid Survey Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>