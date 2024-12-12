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


	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");


	
	$sConditions = "status='A' AND dropped!='Y'
				    AND province_id IN ({$_SESSION['AdminProvinces']})
				    AND district_id IN ({$_SESSION['AdminDistricts']})";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND id IN ({$_SESSION['AdminSchools']}) ";


	if ($sKeywords != "")
	{
		$sKeywords    = str_replace(" ", "%", $sKeywords);

		$sConditions .= " AND (name LIKE '%{$sKeywords}%' OR code LIKE '{$sKeywords}' OR address LIKE '%{$sKeywords}%') ";
	}

	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}
	

	$iAdoptedSchools   = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND adopted='Y'");
	$iQualifiedSchools = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND qualified='Y'");
	$iCompletedSchools = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND qualified='Y' AND adopted='Y' AND completed='Y'");



	$iMilestoneStageS = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='S'", "position DESC");
	$iMilestoneStageD = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='D'", "position DESC");
	$iMilestoneStageT = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='T'", "position DESC");
	$iMilestoneStageB = getDbValue("position", "tbl_stages", "status='A' AND parent_id='0' AND `type`='B'", "position DESC");
	$iMilestoneStages = array( );
	
	
	$sSQL = "SELECT id FROM tbl_stages WHERE status='A' AND (`type`='R' OR (`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iMilestoneStages[] = $objDb->getField($i, 0);
		
	$sMilestoneStages = @implode(",", $iMilestoneStages);


	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions = " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";

	$iActiveSchools = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND qualified='Y' AND adopted='Y' AND completed!='Y' AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions)");


	if ($sStatus == "Active")
		$sConditions .= " AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";

	else if ($sStatus == "InActive")
		$sConditions .= " AND id NOT IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions) ";

	else if ($sStatus == "Delayed" || $sStatus == "OnTime")
	{
		$sSQL = "SELECT cs.school_id, csd.stage_id, MAX(csd.end_date) AS _EndDate
				 FROM tbl_contract_schedules cs, tbl_contract_schedule_details csd
				 WHERE cs.id=csd.schedule_id AND csd.end_date < CURDATE( ) AND csd.end_date!='0000-00-00'
				 GROUP BY cs.school_id";
		$objDb->query($sSQL);

		$iCount          = $objDb->getCount( );
		$iDelayedSchools = array( );
		$iOnTimeSchools  = array( );
		$iUserSchools    = array( );

		if ($_SESSION["AdminSchools"] != "")
			$iUserSchools = @explode(",", $_SESSION["AdminSchools"]);

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchool  = $objDb->getField($i, "school_id");
			$iStage   = $objDb->getField($i, "stage_id");
			$iEndDate = $objDb->getField($i, "_EndDate");

			if ($_SESSION["AdminSchools"] != "" && !@in_array($iSchool, $iUserSchools))
				continue;


			$iInspections = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iStage' AND status='P' AND stage_completed='Y'");

			if ($iInspections == 0)
				$iDelayedSchools[] = $iSchool;

			else
				$iOnTimeSchools[] = $iSchool;
		}


		if ($sStatus == "Delayed")
		{
			$sDelayedSchools = @implode(",", $iDelayedSchools);
			$sConditions    .= " AND FIND_IN_SET(id, '$sDelayedSchools') ";
		}

		if ($sStatus == "OnTime")
		{
			$sOnTimeSchools = @implode(",", $iOnTimeSchools);
			$sConditions   .= " AND FIND_IN_SET(id, '$sOnTimeSchools') ";
		}
	}



	$sSQL = "SELECT id, name, code, address, latitude, longitude, adopted, qualified, completed FROM tbl_schools WHERE $sConditions ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>
		for (var i = 0; i < objMarkers.length; i ++)
		{
			objPopups[i].close( );
			objMarkers[i].setMap(null);
		}

		objMarkers = [];
		objPopups  = [];

		
		$("#Map #Buttons #Counts #QualifiedCount").html("<span>Qualified</span><?= formatNumber($iQualifiedSchools, false) ?>");
		$("#Map #Buttons #Counts #AdoptedCount").html("<span>Adopted</span><?= formatNumber($iAdoptedSchools, false) ?>");
		$("#Map #Buttons #Counts #ActiveCount").html("<span>Active</span><?= formatNumber($iActiveSchools, false) ?>");
		$("#Map #Buttons #Counts #CompletedCount").html("<span>Completed</span><?= formatNumber($iCompletedSchools, false) ?>");
<?
	if ($sStatus == "Active")
	{
?>
		$("#Map #Buttons #Count").css("background", "#49a52d");
<?
	}

	else if ($sStatus == "InActive")
	{
?>
		$("#Map #Buttons #Count").css("background", "#cc0707");
<?
	}

	else if ($sStatus == "Delayed")
	{
?>
		$("#Map #Buttons #Count").css("background", "#cc0707");
<?
	}

	else if ($sStatus == "OnTime")
	{
?>
		$("#Map #Buttons #Count").css("background", "#49a52d");
<?
	}

	else
	{
?>
		$("#Map #Buttons #Count").css("background", "#1d70a3");
<?
	}



	for ($i = 0; $i < $iCount; $i ++)
	{
		$iSchool    = $objDb->getField($i, "id");
		$sSchool    = $objDb->getField($i, "name");
		$sCode      = $objDb->getField($i, "code");
		$sAddress   = $objDb->getField($i, "address");
		$sLatitude  = $objDb->getField($i, "latitude");
		$sLongitude = $objDb->getField($i, "longitude");

		if ($sLatitude == "" || $sLongitude == "")
			continue;

		if (!@is_numeric($sLatitude))
			$sLatitude = "0";
		
		if (!@is_numeric($sLongitude))
			$sLongitude = "0";
		

		$sInfo = ("<div><b style='font-weight:bold;'>{$sSchool}</b><br /><span style='color:#999999;'>EMIS Code: {$sCode}</span><br /><br />".utf8_encode(str_replace(array("\r\n", "\n"), "<br />", htmlentities($sAddress)))."</div>");
?>
			var objLatLong<?= $i ?> = new google.maps.LatLng(<?= $sLatitude ?>, <?= $sLongitude ?>);
			var objMarker<?= $i ?>  = new google.maps.Marker({ position:objLatLong<?= $i ?>, map:objMap, icon:'images/map/<?= (($sStatus == "InActive" || $sStatus == "Delayed") ? "red" : "green") ?>-school.png', title:'<?= $sSchool ?>', animation:google.maps.Animation.DROP });
			var objInfoWin<?= $i ?> = new google.maps.InfoWindow({ content:"<?= $sInfo ?>" });

			google.maps.event.addListener(objMarker<?= $i ?>, 'click', function( )
			{
				for (var i = 0; i < objPopups.length; i ++)
					objPopups[i].close( );

				objInfoWin<?= $i ?>.open(objMap, objMarker<?= $i ?>);

				showSchoolStatus('<?= $iSchool ?>');
				hideTimeline( );
			});

			objPopups.push(objInfoWin<?= $i ?>);
			objMarkers.push(objMarker<?= $i ?>);
<?
	}
?>
			if (objCluster)
				objCluster.clearMarkers( );

			objCluster = new MarkerClusterer(objMap, objMarkers, { maxZoom:15, gridSize:70, styles:objStyles} )
<?
	if ($iCount == 1 && $sLatitude != "" && $sLongitude != "")
	{
?>
			objInfoWin<?= ($i - 1) ?>.open(objMap, objMarker<?= ($i - 1) ?>);

			objMap.setCenter(objLatLong<?= ($i - 1) ?>);
			objMap.setZoom(12);

			showSchoolStatus('<?= $iSchool ?>');
<?
	}

	else if ($iPackage > 0 || $iProvince > 0 || $iDistrict > 0)
	{
		if ($iDistrict > 0)
		{
			$sSQL = "SELECT latitude, longitude FROM tbl_districts WHERE id='$iDistrict' AND latitude!='' AND longitude!=''";
			$objDb->query($sSQL);

			if ($objDb->getCount( ) == 1)
			{
				$sLatitude  = $objDb->getField(0, "latitude");
				$sLongitude = $objDb->getField(0, "longitude");
			}
		}

		else if ($iProvince > 0)
		{
			$sSQL = "SELECT latitude, longitude FROM tbl_provinces WHERE id='$iProvince'";
			$objDb->query($sSQL);

			$sLatitude  = $objDb->getField(0, "latitude");
			$sLongitude = $objDb->getField(0, "longitude");
		}


		if ($sLatitude != "" && $sLongitude != "")
		{
?>
			var objLatLong = new google.maps.LatLng(<?= $sLatitude ?>, <?= $sLongitude ?>);

			objMap.setCenter(objLatLong);
			objMap.setZoom(8);
<?
		}
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>