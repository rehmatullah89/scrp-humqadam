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
	$iDistrict   = IO::intValue("District");
        $iDocType    = IO::intValue("DocType");

        $sConditions = " WHERE d.school_id=s.id ";
	$sOrderBy    = " ORDER BY d.id ASC ";
	$sSortOrder  = "ASC";
        
	$sColumns    = array('d.id', 's.name', 's.code', 's.district_id', 'd.type_id', '_CreatedBy', 'd.created_at');
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
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "district_id")
				{
					$sFields = getList("tbl_districts", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(district_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
				}

				else
					$sOrderBy .= ($sColumns[IO::intValue("iSortCol_{$i}")]." ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");

				$sSortOrder = strtoupper(IO::strValue("sSortDir_{$i}"));
			}
		}


		$sOrderBy = substr_replace($sOrderBy, "", -2);

		if ($sOrderBy == "ORDER BY")
			$sOrderBy = " ORDER BY d.id ASC ";
	}


	if ($sKeywords != "")
		$sConditions .= " AND (s.code='{$sKeywords}' OR s.name LIKE '%{$sKeywords}%') ";

	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
	
	if ($iDocType > 0)
		$sConditions .= " AND d.type_id='$iDocType' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_documents d, tbl_schools s", $sConditions, $iPageSize, $iPageId);

        
        $sSQL = "SELECT d.id, d.created_at, d.modified_at, d.type_id,
        s.code, s.name, s.district_id,
                                (SELECT type FROM tbl_document_types WHERE id=d.type_id) AS _DocType,
                                (SELECT name FROM tbl_admins WHERE id=d.created_by) AS _CreatedBy,
                                (SELECT name FROM tbl_admins WHERE id=d.modified_by) AS _ModifiedBy
         FROM tbl_documents d, tbl_schools s
         WHERE d.school_id=s.id $sConditions
            $sOrderBy
            LIMIT $iStart, $iPageSize";

	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sContractsList = getList("tbl_contracts", "id", "title");
	$sDocumentsSQL    = "FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sDocumentsSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
	

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_documents d, tbl_schools s", "d.school_id=s.id AND {$sDocumentsSQL}"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

   	$sDistrictsList = getList("tbl_districts", "id", "name");

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId         = $objDb->getField($i, "id");
                $sDocType    = $objDb->getField($i, "_DocType");
                $sSchool     = $objDb->getField($i, "name");
                $sCode       = $objDb->getField($i, "code");
                $iDistrict   = $objDb->getField($i, "district_id");
                $sCreatedAt  = $objDb->getField($i, "created_at");
                $sCreatedBy  = $objDb->getField($i, "_CreatedBy");
                $sModifiedAt = $objDb->getField($i, "modified_at");
                $sModifiedBy = $objDb->getField($i, "_ModifiedBy");


		$sOptions = "";

		
		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");


		$sOptions = (' <img class="icon details" id="'.$iId.'" src="images/icons/info.png" alt="" title="'.$sInfo.'" />');

		if ($sUserRights["Edit"] == "Y")
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


                $sOutput['aaData'][] = array( str_pad($iId, 3, '0', STR_PAD_LEFT),
                                      @utf8_encode($sSchool),
                                      @utf8_encode($sCode),
                                      @utf8_encode($sDistrictsList[$iDistrict]),
                                      @utf8_encode($sDocType),
                                      @utf8_encode($sCreatedBy),
                                      formatDate($sCreatedAt, $_SESSION['DateFormat']),
                                      $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>