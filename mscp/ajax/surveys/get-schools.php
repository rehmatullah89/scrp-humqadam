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


	$iPageId     = IO::intValue("iDisplayStart");
	$iPageSize   = IO::intValue("iDisplayLength");
	$sKeywords   = IO::strValue("sSearch");
	$iDistrict   = IO::intValue("District");
	$iType       = IO::intValue("Type");
	$sWorkType   = IO::strValue("WorkType");
    $sStatus     = IO::strValue("Status");
	$sDesignType = IO::strValue("DesignType");
	$sStoreyType = IO::strValue("StoreyType");
	$sConditions = " WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'name', 'code', 'type_id', 'storey_type', 'design_type', 'students', 'revised_cost', 'district_id');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);

	$sTypesList     = getList("tbl_school_types", "id", "`type`");
	$sDistrictsList = getList("tbl_districts", "id", "name");


	if (IO::strValue("iSortCol_0") != "")
	{
		$sOrderBy = "ORDER BY  ";

		for ($i = 0 ; $i < IO::intValue("iSortingCols"); $i ++)
		{
			if (IO::strValue("bSortable_".IO::intValue("iSortCol_{$i}")) == "true")
			{
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "type_id")
				{
					$sFields = getList("tbl_school_type", "id", "id", "", "`type`");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(type_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}
				
				else if ($sColumns[IO::intValue("iSortCol_{$i}")] == "district_id")
				{
					$sFields = getList("tbl_districts", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(district_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}

				else if ($sColumns[IO::intValue("iSortCol_{$i}")] == "id")
					$sOrderBy .= ("position ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");

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
		$sConditions .= " AND ( name LIKE '%{$sKeywords}%' OR
		                        code LIKE '%{$sKeywords}%' OR
		                        address LIKE '%{$sKeywords}%') ";

	if ($iDistrict > 0)
		$sConditions .= " AND district_id='$iDistrict' ";

	if ($iType > 0)
		$sConditions .= " AND type_id='$iType' ";

	if ($sWorkType != "")
		$sConditions .= " AND work_type='$sWorkType' ";
	
	if ($sDesignType != "")
		$sConditions .= " AND design_type='$sDesignType' ";
	
	if ($sStoreyType != "")
		$sConditions .= " AND storey_type='$sStoreyType' ";
        
	if ($sStatus != "")
	{
		if ($sStatus == "active")
		{
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

			
			$sConditions .= " AND status='A' AND dropped!='Y' AND qualified='Y' AND completed!='Y' AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE stage_id IN ($sMilestoneStages) ";
			
			if ($iDistrict > 0)
				$sConditions .= " AND district_id='$iDistrict' ";
			
			else
				$sConditions .= " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";
			
			if ($_SESSION["AdminSchools"] != "")
				$sConditions .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
			
			$sConditions .= ")";
		}
		
		else if ($sStatus == "completed")
			$sConditions .= " AND status='A' AND dropped!='Y' AND qualified='Y' AND completed='Y' ";
		
		else if ($sStatus == "qualified")
			$sConditions .= " AND status='A' AND dropped!='Y' AND qualified='Y' ";
		
		else
			$sConditions .= " AND status='A' AND {$sStatus}='Y' ";
	}
	
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_schools", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, district_id, type_id, name, code, students, storey_type, design_type, revised_cost, picture, status,
	                (SELECT COUNT(1) FROM tbl_inspections WHERE school_id=tbl_schools.id) AS _Inspections
	         FROM tbl_schools
	         $sConditions
	         $sOrderBy
	         LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sSchoolsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_schools", $sSchoolsSQL),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId          = $objDb->getField($i, "id");
		$iDistrict    = $objDb->getField($i, "district_id");
		$sName        = $objDb->getField($i, "name");
		$iType        = $objDb->getField($i, "type_id");
		$sCode        = $objDb->getField($i, "code");
		$sStoreyType  = $objDb->getField($i, "storey_type");
		$sDesignType  = $objDb->getField($i, "design_type");
		$iStudents    = $objDb->getField($i, "students");
		$fRevisedCost = $objDb->getField($i, "revised_cost");
		$sPicture     = $objDb->getField($i, "picture");
		$sStatus      = $objDb->getField($i, "status");
		$iInspections = $objDb->getField($i, "_Inspections");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnToggle" id="'.$iId.'" src="images/icons/'.(($sStatus == 'A') ? 'success' : 'error').'.png" alt="Toggle Status" title="Toggle Status" />');
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
		}

		if ($sUserRights["Delete"] == "Y" && $iInspections == 0)
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		if ($sPicture != "" && @file_exists($sRootDir.SCHOOLS_IMG_DIR.$sPicture))
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.SCHOOLS_IMG_DIR.$sPicture).'" src="images/icons/picture.png" alt="Picture" title="Picture" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');

                $sOptions .= (' <img class="icon icnMembers" id="'.$iId.'" src="images/icons/members.png" alt="Members" title="Members" />');

		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sName),
		                              @utf8_encode($sCode),
		                              @utf8_encode($sTypesList[$iType]),
		                              @utf8_encode((($sStoreyType == "S") ? "Single" : (($sStoreyType == "D") ? "Double" : "Triple"))),
		                              @utf8_encode((($sDesignType == "R") ? "Regular" : "Bespoke")),
		                              @utf8_encode(formatNumber($iStudents, false)),
		                              @utf8_encode(formatNumber($fRevisedCost)),
		                              @utf8_encode($sDistrictsList[$iDistrict]),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>