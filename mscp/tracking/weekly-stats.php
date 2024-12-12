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
        $_SESSION["Flag"] = "";
        
        $objDbGlobal = new Database( );
	$objDb       = new Database( );
        
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



	$sSQL = "SELECT DISTINCT(id), completed, last_milestone_id 
			 FROM tbl_schools
			 WHERE status='A' AND dropped!='Y' AND qualified='Y'
			       AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages))
			 ORDER BY id";
        
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
        
        if($iCount > 0){
        
            $sSQL = "INSERT INTO tbl_weekly_stats (school_id, date, milestone_id, mobilized, completed) values ";

            $valuesArr = array();

            for($i=0; $i < $iCount; $i++){
                
                $iSchools    = $objDb->getField($i, "id");
                $sCompleted  = $objDb->getField($i, "completed");
                $iMilestonId = $objDb->getField($i, "last_milestone_id");
                $sDate       = date('Y-m-d');
                
                $valuesArr[] = "('$iSchools', '$sDate', '$iMilestonId', 'Y', '$sCompleted')";
                
            }
            
                $sSQL .= implode(',', $valuesArr);
            
                if ($objDb->execute($sSQL) == false)
                    $_SESSION["Flag"] = "DB_ERROR";
        }

	
?>