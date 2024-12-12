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


	$sInspectionsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sInspectionsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	$iDistrictId  = IO::intValue("District");
	$sType        = IO::strValue("Type");
	$iInspections = getDbValue("COUNT(1)", "tbl_inspections", $sInspectionsSQL);


	if ($iInspections > 50)
	{
		$sProvincesList = getList("tbl_provinces", "id", "name");

		if (count($sProvincesList) > 1)
		{
			print '<select id="District">';
			print '<option value="" rel="0">All Districts</option>';

			foreach ($sProvincesList as $iProvince => $sProvince)
			{
				print @utf8_encode('<optgroup label="'.$sProvince.'">');


				$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince' AND id IN (SELECT DISTINCT(district_id) FROM tbl_inspections WHERE $sInspectionsSQL)");

				foreach ($sDistrictsList as $iDistrict => $sDistrict)
				{
					print @utf8_encode('<option value="'.$iDistrict.'" rel="'.$iDistrict.'">'.$sDistrict.'</option>');
				}

				print '</optgroup>';
			}

			print '</select>';
		}



		print '<select id="Type">';
		print '<option value="">All Types</option>';
		print @utf8_encode('<option value="S"'.(($sType == "S") ? " selected" : "").'>Single Storey</option>');
		print @utf8_encode('<option value="D"'.(($sType == "D") ? " selected" : "").'>Double Storey</option>');
		print @utf8_encode('<option value="T"'.(($sType == "T") ? " selected" : "").'>Triple Storey</option>');
		print @utf8_encode('<option value="B"'.(($sType == "B") ? " selected" : "").'>Bespoke</option>');
		print '</select>';



		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='$sType' ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );


		print '<select id="Stage">';
		print '<option value="">All Stages</option>';

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iParent = $objDb->getField($i, "id");
			$sParent = $objDb->getField($i, "name");


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );

			if ($iCount2 == 0)
				print @utf8_encode('<option value="'.(($iInspections > 50) ? $iParent : $sParent).'">'.$sParent.'</option>');


			for ($j = 0; $j < $iCount2; $j ++)
			{
				$iStage = $objDb2->getField($j, "id");
				$sStage = $objDb2->getField($j, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
				$objDb3->query($sSQL);

				$iCount3 = $objDb3->getCount( );

				if ($iCount3 == 0)
					print @utf8_encode('<option value="'.(($iInspections > 50) ? $iStage : ($sParent.' &raquo; '.$sStage)).'">'.($sParent.' &raquo; '.$sStage).'</option>');


				for ($k = 0; $k < $iCount3; $k ++)
				{
					$iSubStage = $objDb3->getField($k, "id");
					$sSubStage = $objDb3->getField($k, "name");


					$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iSubStage' ORDER BY position";
					$objDb4->query($sSQL);

					$iCount4 = $objDb4->getCount( );

					if ($iCount4 == 0)
						print @utf8_encode('<option value="'.(($iInspections > 50) ? $iSubStage : ($sParent.' &raquo; '.$sStage.' &raquo; '.$sSubStage)).'">'.($sParent.' &raquo; '.$sStage.' &raquo; '.$sSubStage).'</option>');


					for ($l = 0; $l < $iCount4; $l ++)
					{
						$iFourthStage = $objDb4->getField($l, "id");
						$sFourthStage = $objDb4->getField($l, "name");

						print @utf8_encode('<option value="'.(($iInspections > 50) ? $iFourthStage : ($sParent.' &raquo; '.$sStage.' &raquo; '.$sSubStage.' &raquo; '.$sFourthStage)).'">'.($sParent.' &raquo; '.$sStage.' &raquo; '.$sSubStage.' &raquo; '.$sFourthStage).'</option>');
					}
				}
			}
		}

		print '</select>';
	}


	print '<select id="Status">';
	print '<option value="">Inspection Status</option>';
	print @utf8_encode('<option value="'.(($iInspections > 50) ? 'P' : 'Passed').'">Pass</option>');
	print @utf8_encode('<option value="'.(($iInspections > 50) ? 'F' : 'Fail').'">Fail</option>');
	print @utf8_encode('<option value="'.(($iInspections > 50) ? 'R' : 'Re-Inspection').'">Re-Inspection</option>');
	print '</select>';


	if ($iInspections > 50)
	{
		print '<select id="Completed">';
		print '<option value="">Completed ?</option>';
		print @utf8_encode('<option value="Y">Yes</option>');
		print @utf8_encode('<option value="N">No</option>');
		print '</select>';
	}


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>