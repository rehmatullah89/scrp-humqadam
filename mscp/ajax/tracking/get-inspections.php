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

	header("Expires: Tue, 01 Jan 2010 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
	$objDb3      = new Database( );
	$objDb4      = new Database( );


	$iPageId     = IO::intValue("iDisplayStart");
	$iPageSize   = IO::intValue("iDisplayLength");
	$sKeywords   = IO::strValue("sSearch");
	$iDistrict   = IO::intValue("District");
	$sType       = IO::strValue("Type");
	$iStage      = IO::intValue("Stage");
	$sStatus     = IO::strValue("Status");
	$sCompleted  = IO::strValue("Completed");
	$sConditions = " WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'title', 'date', 'stage_id', 'school_id', 'status', 'stage_completed');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);


	$sSchoolsList = getList("tbl_schools", "id", "name", "id IN (SELECT DISTINCT(school_id) FROM tbl_inspections)");
	$sStagesList  = array( );


	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");
		$sParent = $objDb->getField($i, "name");


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		if ($iCount2 == 0)
			$sStagesList[$iParent] = $sParent;


		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStageId = $objDb2->getField($j, "id");
			$sStage   = $objDb2->getField($j, "name");


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStageId' ORDER BY name";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			if ($iCount3 == 0)
				$sStagesList[$iStageId] = ($sParent." &raquo; ".$sStage);


			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iSubStage' ORDER BY name";
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


	if (IO::strValue("iSortCol_0") != "")
	{
		$sOrderBy = "ORDER BY  ";

		for ($i = 0 ; $i < IO::intValue("iSortingCols"); $i ++)
		{
			if (IO::strValue("bSortable_".IO::intValue("iSortCol_{$i}")) == "true")
			{
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "stage_id")
				{
					$sFields = getList("tbl_stages", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(stage_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}

				else if ($sColumns[IO::intValue("iSortCol_{$i}")] == "school_id")
				{
					$sFields = getList("tbl_schools", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(school_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}

				else
					$sOrderBy .= ($sColumns[IO::intValue("iSortCol_{$i}")]." ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");

				$sSortOrder = strtoupper(IO::strValue("sSortDir_{$i}"));
			}
		}


		$sOrderBy = substr_replace($sOrderBy, "", -2);

		if ($sOrderBy == "ORDER BY")
			$sOrderBy = " ORDER BY id ASC ";
	}


	if ($sKeywords != "")
	{
		$iInspection = intval($sKeywords);

		
		$sConditions .= " AND (id='$iInspection' OR
		                       title LIKE '%{$sKeywords}%' OR
							   admin_id IN (SELECT id FROM tbl_admins WHERE email LIKE '{$sKeywords}' OR mobile LIKE '%{$sKeywords}%' OR name LIKE '%{$sKeywords}%') OR
							   school_id IN (SELECT id FROM tbl_schools WHERE `code`='{$sKeywords}' OR name LIKE '%{$sKeywords}%') )";
	}

	if ($iDistrict > 0)
		$sConditions .= " AND district_id='$iDistrict' ";

	if ($sType != "")
		$sConditions .= " AND school_id IN (SELECT id FROM tbl_schools WHERE status='A' AND dropped!='Y' AND (design_type='$sType' OR (design_type='R' AND storey_type='$sType'))) ";

	if ($iStage > 0)
		$sConditions .= " AND stage_id='$iStage' ";

	if ($sStatus != "")
		$sConditions .= " AND status='$sStatus' ";

	if ($sCompleted != "")
	{
		if ($sCompleted == "Y")
			$sConditions .= " AND stage_completed='Y' ";

		else
			$sConditions .= " AND stage_completed!='Y' ";
	}

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_inspections", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, school_id, stage_id, title, `date`, picture, file, status, stage_completed, created_at, modified_at,
					IF (stage_completed='Y', (SELECT weightage FROM tbl_stages WHERE id=tbl_inspections.stage_id), '0') AS _Weightage,
					(SELECT name FROM tbl_admins WHERE id=tbl_inspections.created_by) AS _CreatedBy,
					(SELECT name FROM tbl_admins WHERE id=tbl_inspections.modified_by) AS _ModifiedBy
	         FROM tbl_inspections
	         $sConditions
	         $sOrderBy
	         LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );



	$sInspectionsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sInspectionsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";


	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_inspections", $sInspectionsSQL),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId         = $objDb->getField($i, "id");
		$iSchool     = $objDb->getField($i, "school_id");
		$iStage      = $objDb->getField($i, "stage_id");
		$sTitle      = $objDb->getField($i, "title");
		$sDate       = $objDb->getField($i, "date");
		$sPicture    = $objDb->getField($i, "picture");
		$sDocument   = $objDb->getField($i, "file");
		$sStatus     = $objDb->getField($i, "status");
		$sCompleted  = $objDb->getField($i, "stage_completed");
		$fWeightage  = $objDb->getField($i, "_Weightage");
		$sCreatedAt  = $objDb->getField($i, "created_at");
		$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
		$sModifiedAt = $objDb->getField($i, "modified_at");
		$sModifiedBy = $objDb->getField($i, "_ModifiedBy");

		switch ($sStatus)
		{
			case "P" : $sStatus = "Pass"; break;
			case "R" : $sStatus = "Re-Inspection"; break;
			case "F" : $sStatus = "Fail"; break;
			default  : $sStatus = "N/A"; break;
		}


		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");


		$sOptions = (' <img class="icon details" id="'.$iId.'" src="images/icons/info.png" alt="" title="'.$sInfo.'" />');

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
			$sOptions .= (' <img class="icon icnMeasurements" id="'.$iId.'" src="images/icons/boqs.png" alt="Measurements" title="Measurements" />');
		}

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		if ($sPicture != "" && @file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sPicture))
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.INSPECTIONS_IMG_DIR.$sPicture).'" src="images/icons/picture.png" alt="Picture" title="Picture" />');

		if ($sDocument != "" && @file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sDocument))
			$sOptions .= (' <a href="'.$sCurDir.'/download-inspection-document.php?Id='.$iId.'&File='.$sDocument.'"><img class="icnDownload" id="'.(SITE_URL.INSPECTIONS_DOC_DIR.$sDocument).'" src="images/icons/download.gif" alt="Download" title="Download" /></a>');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


		$sOutput['aaData'][] = array( str_pad($iId, 5, '0', STR_PAD_LEFT),
		                              @utf8_encode($sTitle),
		                              @utf8_encode(formatDate($sDate, $_SESSION['DateFormat'])),
		                              @utf8_encode($sStagesList[$iStage]),
		                              @utf8_encode($sSchoolsList[$iSchool]),
		                              @utf8_encode($sStatus),
		                              (formatNumber($fWeightage)."%"),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>