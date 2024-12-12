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
	$iProvince   = IO::intValue("Province");
	$sConditions = " WHERE id>'0' ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'name', 'sef_url', 'province_id', 'status');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);

	$sProvinces = getList("tbl_provinces", "id", "name");


	if (IO::strValue("iSortCol_0") != "")
	{
		$sOrderBy = "ORDER BY  ";

		for ($i = 0 ; $i < IO::intValue("iSortingCols"); $i ++)
		{
			if (IO::strValue("bSortable_".IO::intValue("iSortCol_{$i}")) == "true")
			{
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "province_id")
				{
					$sFields = getList("tbl_provinces", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(province_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
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
	{
		$sStatus = ((strtolower($sKeywords) == "active") ? "A" : ((strtolower($sKeywords) == "in-active") ? "I" : ""));

		$sConditions .= " AND ( name LIKE '%{$sKeywords}%' ";

		if ($sStatus != "")
			$sConditions .= " OR status='$sStatus' ";

		$sConditions .= " ) ";
	}


	if ($iProvince > 0)
		$sConditions .= " AND province_id='$iProvince' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_districts", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, province_id, name, sef_url, picture, status FROM tbl_districts $sConditions $sOrderBy LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_districts"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId       = $objDb->getField($i, "id");
		$iProvince = $objDb->getField($i, "province_id");
		$sName     = $objDb->getField($i, "name");
		$sSefUrl   = $objDb->getField($i, "sef_url");
		$sPicture  = $objDb->getField($i, "picture");
		$sStatus   = $objDb->getField($i, "status");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnToggle" id="'.$iId.'" src="images/icons/'.(($sStatus == 'A') ? 'success' : 'error').'.png" alt="Toggle Status" title="Toggle Status" />');
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
		}

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		if ($sPicture != "" && @file_exists($sRootDir.DISTRICTS_IMG_DIR.'originals/'.$sPicture))
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.DISTRICTS_IMG_DIR.'originals/'.$sPicture).'" src="images/icons/picture.png" alt="Picture" title="Picture" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sName),
		                              @utf8_encode($sSefUrl),
		                              @utf8_encode($sDistricts[$iProvince]),
		                              (($sStatus == "A") ? "Active" : "In-Active"),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>