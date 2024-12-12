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


	$iPageId     = IO::intValue("iDisplayStart");
	$iPageSize   = IO::intValue("iDisplayLength");
	$sKeywords   = IO::strValue("sSearch");
	$sDesignType = IO::strValue("DesignType");
	$sStoreyType = IO::strValue("StoreyType");
	$iContract   = IO::intValue("Contract");
	$iProvince   = IO::intValue("Province");
	$iDistrict   = IO::intValue("District");
	$iPackage    = IO::intValue("Package");
	$sConditions = " WHERE cs.school_id=s.id ";
	$sOrderBy    = " ORDER BY cs.id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('cs.id', 's.code', 's.name', 'cs.contract_id', 'cs.start_date', 'cs.end_date');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);

	
	$sConditions .= " AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
	

	if (IO::strValue("iSortCol_0") != "")
	{
		$sOrderBy = "ORDER BY  ";

		for ($i = 0 ; $i < IO::intValue("iSortingCols"); $i ++)
		{
			if (IO::strValue("bSortable_".IO::intValue("iSortCol_{$i}")) == "true")
			{
				$sOrderBy .= ($sColumns[IO::intValue("iSortCol_{$i}")]." ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				$sSortOrder = strtoupper(IO::strValue("sSortDir_{$i}"));
			}
		}


		$sOrderBy = substr_replace($sOrderBy, "", -2);

		if ($sOrderBy == "ORDER BY")
			$sOrderBy = " ORDER BY id ASC ";
	}


	if ($sKeywords != "")
		$sConditions .= " AND (s.code='{$sKeywords}' OR s.name LIKE '%{$sKeywords}%') ";

	if ($iProvince > 0)
		$sConditions .= " AND s.province_id='$iProvince' ";
	
	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
	
	if ($iPackage > 0)
	{
		$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");
		
		$sConditions .= " AND s.id IN ($sSchools)";            
	}
	
	if ($sDesignType != "")
		$sConditions .= " AND s.design_type='{$sDesignType}'";
	
	if ($sStoreyType != "")
		$sConditions .= " AND s.storey_type='{$sStoreyType}'";
        
	if ($iContract > 0)
		$sConditions .= " AND cs.contract_id='$iContract' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_contract_schedules cs, tbl_schools s", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT cs.id, cs.contract_id, cs.start_date, cs.end_date,
					s.code, s.name
			 FROM tbl_contract_schedules cs, tbl_schools s
			 $sConditions
			 $sOrderBy
			 LIMIT $iStart, $iPageSize";
        
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sContractsList = getList("tbl_contracts", "id", "title");
	$sSchoolsSQL    = "FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
	

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_contract_schedules cs, tbl_schools s", "cs.school_id=s.id AND {$sSchoolsSQL}"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );


	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId        = $objDb->getField($i, "id");
		$iContract  = $objDb->getField($i, "contract_id");
		$sCode      = $objDb->getField($i, "code");
		$sSchool    = $objDb->getField($i, "name");
		$sStartDate = $objDb->getField($i, "start_date");
		$sEndDate   = $objDb->getField($i, "end_date");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
			$sOptions .= (' <img class="icon icnEditDetails" id="'.$iId.'" src="images/icons/stats.gif" alt="Edit Details" title="Edit Details" />');
		}

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sCode),
		                              @utf8_encode($sSchool),
		                              @utf8_encode($sContractsList[$iContract]),
		                              @utf8_encode(formatDate($sStartDate, $_SESSION["DateFormat"])),
		                              @utf8_encode(formatDate($sEndDate, $_SESSION["DateFormat"])),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>