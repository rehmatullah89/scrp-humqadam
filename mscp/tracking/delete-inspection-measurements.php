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


	$iInspectionId  = IO::intValue("InspectionId");
	$iMeasurementId = IO::intValue("MeasurementId");


	$objDb->execute("BEGIN");
	
	$sSQL  = "DELETE FROM tbl_inspection_measurements WHERE inspection_id='$iInspectionId' AND id='$iMeasurementId'";
	$bFlag = $objDb->execute($sSQL);
	
	if ($bFlag == true)
	{
		$iMeasurements = getDbValue("COUNT(1)", "tbl_inspection_measurements", "inspection_id='$iInspectionId'");
		
		if ($iMeasurements == 0)
		{
			$sSQL  = "UPDATE tbl_inspections SET measurements='N' WHERE id='$iInspectionId'";
			$bFlag = $objDb->execute($sSQL);
		}
	}

	if ($bFlag == true)
	{
		$objDb->execute("COMMIT");
		
		redirect($_SERVER['HTTP_REFERER'], "INSPECTION_MEASUREMENT_DELETED");
	}

	else
	{
		$objDb->execute("ROLLBACK");
		
		redirect($_SERVER['HTTP_REFERER'], "DB_ERROR");
	}
	

	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>