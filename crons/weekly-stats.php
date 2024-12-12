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
    
    
    $iMilestoneStageS = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='S'", "position DESC");
	$iMilestoneStageD = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='D'", "position DESC");
	$iMilestoneStageB = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='B'", "position DESC");
	$iMilestoneStages = array( );

	
	$sSQL = "SELECT id FROM tbl_stages WHERE ((`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
			$iMilestoneStages[] = $objDb->getField($i, 0);

	$sMilestoneStages = @implode(",", $iMilestoneStages);



	$objDb->execute("BEGIN", false);
	
	
	$sSQL = "SELECT id, completed, last_milestone_id 
			 FROM tbl_schools
			 WHERE status='A' AND dropped!='Y' AND qualified='Y'
			       AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages))
			 ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
        
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iSchool    = $objDb->getField($i, "id");
		$sCompleted = $objDb->getField($i, "completed");
		$iMilestone = $objDb->getField($i, "last_milestone_id");

		$sSQL  = "INSERT INTO tbl_weekly_stats (school_id, `date`, milestone_id, mobilized, completed) VALUES ('$iSchool', CURDATE( ), '$iMilestone', 'Y', '$sCompleted')";		
		$bFlag = $objDb2->execute($sSQL, false);
		
		if ($bFlag == false)
			break;
	}


	if ($bFlag == true)
	{
		print "Weekly Stats Added";

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