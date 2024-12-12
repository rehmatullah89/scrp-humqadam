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
	$objDb2      = new Database( );


	$sUser   = IO::strValue("User");
	$iSchool = IO::intValue("School");
	$iBlock  = IO::intValue("Block");



	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0 || $iBlock == 0)
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");

			$iProvinces = @explode(",", $sProvinces);
			$iDistricts = @explode(",", $sDistricts);
			$iSchools   = @explode(",", $sSchools);



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$sSQL = "SELECT storey_type, design_type, work_type FROM tbl_school_blocks WHERE school_id='$iSchool' AND block='$iBlock'";
				$objDb->query($sSQL);

				$sStoreyType = $objDb->getField(0, "storey_type");
				$sDesignType = $objDb->getField(0, "design_type");
				$sWorkType   = $objDb->getField(0, "work_type");

			
				$sBlockType = (($sWorkType == "R") ? "R" : (($sDesignType == "B") ? "B" : $sStoreyType));
				$iMainStage = 0;
				
				if ($sBlockType != "R")
					$iMainStage = getDbValue("id", "tbl_stages", "status='A' AND `type`='$sBlockType' AND parent_id='0'", "position DESC");

				
				$sStagesList     = getList("tbl_stages", "id", "name", "parent_id='$iMainStage' AND status='A' AND `type`='$sBlockType'", "position");
				$sStageUnitsList = getList("tbl_stages", "id", "unit", "status='A' AND `type`='$sBlockType'");
				$sSubStages      = array( );
			
				
				foreach ($sStagesList as $iParent => $sParent)
				{
					$sSubStages[$iParent] = "";


					$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' ORDER BY name";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );

					if ($iCount == 0)
						$sSubStages[$iParent] = $iParent;


					for ($i = 0; $i < $iCount; $i ++)
					{
						$iStage = $objDb->getField($i, "id");


						$sChildStages = getDbValue("GROUP_CONCAT(id SEPARATOR ',')", "tbl_stages", "parent_id='$iStage'");

						if ($sChildStages == "")
						{
							$sSubStages[$iStage]   = $iStage;
							$sSubStages[$iParent] .= ((($sSubStages[$iParent] != "") ? "," : "").$iStage);
						}

						else if ($sChildStages != "")
						{
							$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' ORDER BY name";
							$objDb2->query($sSQL);

							$iCount2 = $objDb2->getCount( );

							for ($j = 0; $j < $iCount2; $j ++)
							{
								$iSubStage = $objDb2->getField($j, "id");


								$sSubStages[$iSubStage] = $iSubStage;
								$sSubStages[$iStage]   .= ((($sSubStages[$iStage] != "") ? "," : "").$iSubStage);
								$sSubStages[$iParent]  .= ((($sSubStages[$iParent] != "") ? "," : "").$iSubStage);
							}
						}
					}
				}



				$sList     = array( );
				$iPosition = 1;

				foreach ($sStagesList as $iParent => $sParent)
				{
					$iRequiredStages  = @count(explode(",", $sSubStages[$iParent]));
					$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iParent]}')");
					$iCompletedStages = 0;

					if ($sDocumentStages != "")
						$iCompletedStages = @count(explode(",", $sDocumentStages));

					$iStatus = (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 1 : (($iCompletedStages == $iRequiredStages) ? 2 : 0));



					$sSubStagesList = getList("tbl_stages", "id", "name", "parent_id='$iParent' AND status='A' AND `type`='$sBlockType'", "position");
					$sList[]        = array("id" => $iParent, "name" => $sParent, "unit" => $sStageUnitsList[$iParent], "status" => $iStatus, "parent" => 0, "childs" => count($sSubStagesList), "position" => $iPosition ++);

					foreach ($sSubStagesList as $iStage => $sStage)
					{
						$iRequiredStages  = @count(explode(",", $sSubStages[$iStage]));
						$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iStage]}')");
						$iCompletedStages = 0;

						if ($sDocumentStages != "")
							$iCompletedStages = @count(explode(",", $sDocumentStages));

						$iStatus = (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 1 : (($iCompletedStages == $iRequiredStages) ? 2 : 0));


						$sThirdLevelStagesList = getList("tbl_stages", "id", "name", "parent_id='$iStage' AND status='A' AND `type`='$sBlockType'", "position");
						$sList[]               = array("id" => $iStage, "name" => $sStage, "unit" => $sStageUnitsList[$iStage], "status" => $iStatus, "parent" => $iParent, "childs" => count($sThirdLevelStagesList), "position" => $iPosition ++);


						foreach ($sThirdLevelStagesList as $iSubStage => $sSubStage)
						{
							$iRequiredStages  = @count(explode(",", $sSubStages[$iSubStage]));
							$sDocumentStages  = getDbValue("GROUP_CONCAT(DISTINCT(stage_id) SEPARATOR ',')", "tbl_inspections", "school_id='$iSchool' AND FIND_IN_SET(stage_id, '{$sSubStages[$iSubStage]}')");
							$iCompletedStages = 0;

							if ($sDocumentStages != "")
								$iCompletedStages = @count(explode(",", $sDocumentStages));

							$iStatus = (($iCompletedStages > 0 && $iCompletedStages < $iRequiredStages) ? 1 : (($iCompletedStages == $iRequiredStages) ? 2 : 0));
							$sList[] = array("id" => $iSubStage, "name" => $sSubStage, "unit" => $sStageUnitsList[$iSubStage], "status" => $iStatus, "parent" => $iStage, "childs" => 0, "position" => $iPosition ++);
						}
					}
				}

				
				$aResponse['Status'] = "OK";
				$aResponse['Stages'] = $sList;
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>