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


	$sSchools = IO::strValue("Schools");

	if ($sSchools != "")
	{
		$iSchools  = @explode(",", $sSchools);
		$sPictures = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iSchools); $i ++)
		{
			$sSQL = "SELECT picture FROM tbl_schools WHERE id='{$iSchools[$i]}' AND picture!=''";
			$objDb->query($sSQL);

			if ($objDb->getCount( ) == 1)
				$sPictures[] = $objDb->getField(0, 0);


			$sSQL  = "DELETE FROM tbl_schools WHERE id='{$iSchools[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_school_blocks WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_contract_details WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}			

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_contract_schedule_details WHERE schedule_id IN (SELECT id FROM tbl_contract_schedules WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_contract_schedules WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_invoices WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_inspection_measurements WHERE inspection_id IN (SELECT id FROM tbl_inspections WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_inspections WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}		

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_details WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_answers WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_checklist WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_blocks WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_block_details WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_teacher_numbers WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_students_enrollment WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_student_attendance_numbers WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_differently_abled_student_numbers WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_school_facilities WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	
			
			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_declaration WHERE survey_id IN (SELECT id FROM tbl_surveys WHERE school_id='{$iSchools[$i]}')";
				$bFlag = $objDb->execute($sSQL);
			}	

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_surveys WHERE school_id='{$iSchools[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}		

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iSchools) > 1)
				print "success|-|The selected Schools have been Deleted successfully.";

			else
				print "success|-|The selected School has been Deleted successfully.";


			for ($i = 0; $i < count($sPictures); $i ++)
				@unlink($sRootDir.SCHOOLS_IMG_DIR.$sPictures[$i]);
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid School Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>