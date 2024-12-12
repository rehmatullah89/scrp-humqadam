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
	$objDb2      = new Database( );
	$objDb3      = new Database( );
	$objDb4      = new Database( );


	$iSchool = IO::intValue("School");
	
	
	$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	


	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$sStagesList = array( );


	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='$sSchoolType' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");
		$sParent = $objDb->getField($i, "name");


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		if ($iCount2 == 0)
			$sStagesList[$iParent] = $sParent;


		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStageId = $objDb2->getField($j, "id");
			$sStage   = $objDb2->getField($j, "name");


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStageId' ORDER BY position";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			if ($iCount3 == 0)
				$sStagesList[$iStageId] = ($sParent." &raquo; ".$sStage);


			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iSubStage' ORDER BY position";
				$objDb4->query($sSQL);

				$iCount4 = $objDb4->getCount( );

				if ($iCount4 == 0)
					$sStagesList[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);


				for ($l = 0; $l < $iCount4; $l ++)
				{
					$iFourthStage = $objDb4->getField($l, "id");
					$sFourthStage = $objDb4->getField($l, "name");

					$sStagesList[$iFourthStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage." &raquo; ".$sFourthStage);
				}
			}
		}
	}	
?>
			  <option value=""></option>
<?
	foreach ($sStagesList as $iStageId => $sStage)
	{
?>
			  <option value="<?= $iStageId ?>"><?= $sStage ?></option>
<?
	}	


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>