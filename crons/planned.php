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

	@ini_set("max_execution_time", 0);
	@ini_set("mysql.connect_timeout", -1);

	@ini_set('display_errors', 0);
	//@error_reporting(E_ALL);

	$sBaseDir = "C:/wamp/www/scrp/";
	
	@require_once("{$sBaseDir}requires/configs.php");
	@require_once("{$sBaseDir}requires/db.class.php");
	@require_once("{$sBaseDir}requires/db-functions.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	
	$objDb->execute("BEGIN", false);
	

	$sSQL = "SELECT id, storey_type, design_type FROM tbl_schools WHERE status='A' AND dropped!='Y' AND adopted='Y' ORDER BY id";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iSchool     = $objDb->getField($i, "id");
		$sStoreyType = $objDb->getField($i, "storey_type");
		$sDesignType = $objDb->getField($i, "design_type");		
		
		
		$sSchoolType      = (($sDesignType == "B") ? "B" : $sStoreyType);
		$iPlannedStage    = getDbValue("csd.stage_id", "tbl_contract_schedules cs, tbl_contract_schedule_details csd", "cs.id=csd.schedule_id AND cs.school_id='$iSchool' AND csd.end_date<=CURDATE( )", "csd.end_date DESC");
		$iStagePosition   = getDbValue("position", "tbl_stages", "id='$iPlannedStage'");
		$fPlannedProgress = @round(getDbValue("COALESCE(SUM(weightage), '0')", "tbl_stages", "position<='$iStagePosition' AND `type`='$sSchoolType'"), 2);


		$sSQL  = "UPDATE tbl_schools SET planned='$fPlannedProgress' WHERE id='$iSchool'";
		$bFlag = $objDb2->execute($sSQL, false);
		
		if ($bFlag == false)
			break;
	}


	if ($bFlag == true)
	{
		print "Planned Progress Updated";

		$objDb->execute("COMMIT", false);
	}

	else
	{
		print "<b>ERROR:</b><br />".mysql_error( );
		
		$objDb->execute("ROLLBACK", false);
	}


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>