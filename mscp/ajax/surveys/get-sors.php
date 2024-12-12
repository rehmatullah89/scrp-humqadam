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

	$iPageId       = IO::intValue("iDisplayStart");
	$iPageSize     = IO::intValue("iDisplayLength");
	$sKeywords     = IO::strValue("sSearch");
	$iDistrict     = IO::intValue("District");
	$sSorStatus    = IO::strValue("SorStatus");
	$sSyncStatus   = IO::strValue("SyncStatus");
	
	$sConditions   = " WHERE bs.school_id=s.id AND FIND_IN_SET(bs.district_id, '{$_SESSION['AdminDistricts']}') ";
	$sOrderBy      = " ORDER BY bs.id ASC ";
	$sSortOrder    = "ASC";
	$sColumns      = array('bs.id', 's.name', 's.code', 's.district_id', 'bs.admin_id', 'bs.date', 'bs.status', 'bs.completed');
	$iPageId       = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);

	$sDistrictsList = getList("tbl_districts", "id", "name");


	if (IO::strValue("iSortCol_0") != "")
	{
		$sOrderBy = "ORDER BY  ";

		for ($i = 0 ; $i < IO::intValue("iSortingCols"); $i ++)
		{
			if (IO::strValue("bSortable_".IO::intValue("iSortCol_{$i}")) == "true")
			{
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "district_id")
				{
					$sFields = getList("tbl_districts", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(district_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}
                                
                                elseif($sColumns[IO::intValue("iSortCol_{$i}")] == "admin_id")
				{
					$sFields = getList("tbl_admins", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(admin_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}

				else
					$sOrderBy .= ($sColumns[IO::intValue("iSortCol_{$i}")]." ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");

				$sSortOrder = strtoupper(IO::strValue("sSortDir_{$i}"));
			}
		}


		$sOrderBy = substr_replace($sOrderBy, "", -2);

		if ($sOrderBy == "ORDER BY")
			$sOrderBy = " ORDER BY bs.id ASC ";
	}


	if ($sKeywords != "")
	{
		$iSurvey = intval($sKeywords);

		
		$sConditions .= " AND (bs.id='$iSurvey' OR
                                        		   s.name LIKE '%{$sKeywords}%' OR
							   s.code LIKE '%{$sKeywords}%' )";
	}

	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";

	if ($sSorStatus != "")
		$sConditions .= " AND bs.completed='$sSorStatus' ";

	if ($sSyncStatus != "")
	{
		if ($sSyncStatus == 'C')
			$sConditions .= " AND bs.status='C' ";
	
		else
			$sConditions .= " AND bs.app='Y' AND bs.status='I' ";
	}                
		
	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(bs.school_id, '{$_SESSION['AdminSchools']}') ";	


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_sors bs, tbl_schools s", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT bs.id, bs.completed, bs.engineer_id, bs.date, bs.status, bs.created_at, bs.modified_at, bs.app,
					s.code, s.name, s.district_id,
                                        (SELECT name FROM tbl_admins WHERE id=bs.admin_id) AS _CDC,
					(SELECT name FROM tbl_admins WHERE id=bs.created_by) AS _CreatedBy,
					(SELECT name FROM tbl_admins WHERE id=bs.modified_by) AS _ModifiedBy
	         FROM tbl_sors bs, tbl_schools s
	         $sConditions
	         $sOrderBy
	         LIMIT $iStart, $iPageSize";
        $objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sSorSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSorSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_surveys", $sSorSQL),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId                = $objDb->getField($i, "id");
		$sCDC               = $objDb->getField($i, "_CDC");
		$sDate              = $objDb->getField($i, "date");
		$sStatus            = $objDb->getField($i, "status");
		$sSchool            = $objDb->getField($i, "name");
		$sCode              = $objDb->getField($i, "code");
		$iDistrict          = $objDb->getField($i, "district_id");
		$sCreatedAt         = $objDb->getField($i, "created_at");
		$sCreatedBy         = $objDb->getField($i, "_CreatedBy");
		$sModifiedAt        = $objDb->getField($i, "modified_at");
		$sModifiedBy        = $objDb->getField($i, "_ModifiedBy");
		$sApp               = $objDb->getField($i, "app");
		$sCompleted         = $objDb->getField($i, "completed");

		
		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");


		$sOptions = (' <img class="icon details" id="'.$iId.'" src="images/icons/info.png" alt="" title="'.$sInfo.'" />');

		if ($sUserRights["Edit"] == "Y" && ($sStatus == "C" || $sApp != "Y")){
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
                        $sOptions .= (' <img class="icnSor icon" id="'.$iId.'" src="images/icons/stats.gif" alt="Sor Details" title="Sor Details" />');
                        
                }
		if ($sUserRights["Delete"] == "Y" && ($sStatus == "C" || $sApp != "Y" || $_SESSION['AdminId'] == 1))
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');
		
		//if ($sStatus == "C"){
			$sOptions .= (' <a href="'.$sCurDir.'/export-sor-form.php?SorId='.$iId.'"><img class="icnPdf" src="images/icons/pdf.png" alt="Export SOR Form" title="Export SOR Form" /></a>');
                        
                //}
		$sOutput['aaData'][] = array( str_pad($iId, 3, '0', STR_PAD_LEFT),
		                              @utf8_encode($sSchool),
									  @utf8_encode($sCode),
		                              @utf8_encode($sDistrictsList[$iDistrict]),
									  @utf8_encode($sCDC),
									  formatDate($sDate, $_SESSION['DateFormat']),
                                      @utf8_encode((($sApp == 'Y' && $sStatus == 'I') ? "Syncing" : "Synced")),  
		                              @utf8_encode((($sCompleted == "Y") ? "Completed" : "In-Complete")),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>