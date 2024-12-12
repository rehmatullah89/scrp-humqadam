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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$iSchool = IO::intValue("School");
	$iStage  = IO::intValue("Stage");
	
	
	$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	


	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$sStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='$sSchoolType'", "position");
	$sSubStages  = array( );

	foreach ($sStagesList as $iStage => $sStage)
	{
		$sSubStages[$iStage] = $iStage;


		$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "status='A' AND parent_id='$iStage'");

		if ($sChildStages != "")
			$sSubStages[$iStage] = $sChildStages;
	}
?>
            <div id="Accordion">
<?
	foreach ($sStagesList as $iStage => $sStage)
	{
		$sSubStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='$sSchoolType'", "position");


		if (count($sSubStagesList) > 0)
		{
			$iRequiredStages  = @count(explode(",", $sSubStages[$iStage]));
			$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iStage]}')");
			$iCompletedStages = 0;

			if ($sDocumentStages != "")
				$iCompletedStages = @count(explode(",", $sDocumentStages));
?>
			  <div class="accordian" id="Stage<?= $iStage ?>" rel="<?= $iStage ?>">
			    <h3 rel="<?= $iStage ?>" class="<?= (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 'started' : (($iCompletedStages == $iRequiredStages) ? 'Completed' : 'notStarted')) ?>"><?= $sStage ?></h3>

			    <div class="stages">
				  <div>
				    <ul>
<?
			foreach ($sSubStagesList as $iSubStage => $sSubStage)
			{
				$iDocuments = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iSubStage'");
?>
                	  <li><span class="<?= (($iDocuments == 0) ? 'cross' : 'tick') ?>"></span><?= $sSubStage ?></li>
<?
			}
?>
				    </ul>
				  </div>
				</div>
			  </div>
<?
		}

		else
		{
			$iDocuments = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iStage'");
?>
			  <h3 class="li"><span class="<?= (($iDocuments == 0) ? 'cross' : 'tick') ?>"></span><?= $sStage ?></h3>
<?
		}
	}
?>
          </div>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>