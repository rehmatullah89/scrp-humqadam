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


	$iStages     = getDbValue("COUNT(1)", "tbl_stages");
	$sStagesList = getList("tbl_stages", "id", "name", "parent_id='0'", "position");

	if (count($sStagesList) > 1)
	{
		print '<select id="Stage">';
		print '<option value="">All Stages</option>';

		foreach ($sStagesList as $iParent => $sParent)
		{
			print @utf8_encode('<option value="'.(($iStages > 100) ? $iParent : $sParent).'">'.$sParent.'</option>');


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iStage = $objDb->getField($i, "id");
				$sStage = $objDb->getField($i, "name");

				print @utf8_encode('<option value="'.(($iStages > 100) ? $iStage : ($sParent." &raquo; ".$sStage)).'">'.($sParent." &raquo; ".$sStage).'</option>');


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iSubStage = $objDb2->getField($j, "id");
					$sSubStage = $objDb2->getField($j, "name");

					print @utf8_encode('<option value="'.(($iStages > 100) ? $iSubStage : ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage)).'">'.($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage).'</option>');
				}
			}
		}

		print '</select>';
	}


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>