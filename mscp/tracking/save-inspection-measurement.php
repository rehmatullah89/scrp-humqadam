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

	$iBoqItem      = IO::intValue("ddBoqItem");
	$sTitle        = IO::strValue("txtTitle");
	$fLengthFeet   = IO::floatValue("txtLengthFeet");
	$iLengthInches = IO::intValue("txtLengthInches");
	$fWidthFeet    = IO::floatValue("txtWidthFeet");
	$iWidthInches  = IO::intValue("txtWidthInches");
	$fHeightFeet   = IO::floatValue("txtHeightFeet");
	$iHeightInches = IO::intValue("txtHeightInches");
	$fMultiplier   = IO::floatValue("txtMultiplier");

	if ($iBoqItem == 0 || $sTitle == "" || $fLengthFeet <= 0 || $fMultiplier <= 0)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$fLength = $fLengthFeet;
		$fWidth  = $fWidthFeet;
		$fHeight = $fHeightFeet;
		$sUnit   = getDbValue("unit", "tbl_boqs", "id='$iBoqItem'");

		if ($iLengthInches > 0 && ($sUnit == "cft" || $sUnit == "sft" || $sUnit == "rft"))
			$fLength = @round(($fLengthFeet + ($iLengthInches / 12)), 3);

		if ($iWidthInches > 0 && ($sUnit == "cft" || $sUnit == "sft"))
			$fWidth = @round(($fWidthFeet + ($iWidthInches / 12)), 3);

		if ($iHeightInches > 0 && $sUnit == "cft")
			$fHeight = @round(($fHeightFeet + ($iHeightInches / 12)), 3);
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT school_id, stage_id, `date` FROM tbl_inspections WHERE id='$iInspectionId'";
		$objDb->query($sSQL);

		$iSchool = $objDb->getField(0, "school_id");
		$iStage  = $objDb->getField(0, "stage_id");
		$sDate   = $objDb->getField(0, "date");


		$iContract     = getDbValue("id", "tbl_contracts", "FIND_IN_SET('$iSchool', schools) AND ('$sDate' BETWEEN start_date AND end_date)", "id DESC");
		$fRate         = getDbValue("rate", "tbl_contract_boqs", "contract_id='$iContract' AND boq_id='$iBoqItem'");
		$fMeasurements = 0;
		$fAmount       = 0;

		if ($sUnit == "cft")
			$fMeasurements = ($fLength * $fWidth * $fHeight);

		else if ($sUnit == "sft")
			$fMeasurements = ($fLength * $fWidth);

		else
			$fMeasurements = $fLength;

		$fMeasurements *= $fMultiplier;
		$fAmount        = ($fMeasurements * $fRate);
		
		if ($iParentId > 0 || ($iMeasurementId > 0 && getDbValue("parent_id", "tbl_inspection_measurements", "id='$iMeasurementId'") > 0))
			$fAmount *= -1;


		$objDb->execute("BEGIN");
		
		
		if ($iMeasurementId > 0)
		{
			$sSQL = "UPDATE tbl_inspection_measurements SET boq_id       = '$iBoqItem',
													        title        = '$sTitle',
													        multiplier   = '$fMultiplier',
													        length       = '$fLength',
													        width        = '$fWidth',
													        height       = '$fHeight',
													        measurements = '$fMeasurements',
													        amount       = '$fAmount',
															modified_by  = '{$_SESSION['AdminId']}',
															modified_at  = NOW( )
					 WHERE inspection_id='$iInspectionId' AND id='$iMeasurementId'";
			$bFlag = $objDb->execute($sSQL);
		}
			
        else
		{
			$iMeasurement = getNextId("tbl_inspection_measurements");

			$sSQL = "INSERT INTO tbl_inspection_measurements SET id            = '$iMeasurement',
																 inspection_id = '$iInspectionId',
                                                                 parent_id     = '$iParentId',
																 school_id     = '$iSchool',
																 stage_id      = '$iStage',
																 boq_id        = '$iBoqItem',
																 title         = '$sTitle',
																 multiplier    = '$fMultiplier',
																 length        = '$fLength',
																 width         = '$fWidth',
																 height        = '$fHeight',
																 measurements  = '$fMeasurements',
																 amount        = '$fAmount',
																 created_by    = '{$_SESSION['AdminId']}',
																 created_at    = NOW( ),
																 modified_by   = '{$_SESSION['AdminId']}',
																 modified_at   = NOW( )";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_inspections SET measurements='Y' WHERE id='$iInspectionId'";							  
			$bFlag = $objDb->execute($sSQL);
		}		

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
			
			redirect("edit-inspection-measurements.php?InspectionId={$iInspectionId}", (($iMeasurementId > 0) ? "INSPECTION_MEASUREMENT_UPDATED" : "INSPECTION_MEASUREMENT_SAVED"));
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>