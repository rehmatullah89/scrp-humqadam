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
	$iProvince   = IO::intValue("Province");
	$iDistrict   = IO::intValue("District");
	$iType       = IO::intValue("Type");
	$sConditions = " WHERE id>'1' ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'name', 'email', 'mobile', 'type_id', 'status');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);



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
	{
		$sStatus = ((strtolower($sKeywords) == "active") ? "A" : ((strtolower($sKeywords) == "in-active") ? "I" : ""));

		$sConditions .= " AND (name LIKE '%{$sKeywords}%' OR
		                       email LIKE '%{$sKeywords}%' OR
		                       mobile LIKE '%{$sKeywords}%' OR
		                       status='$sStatus') ";
	}


	if ($_SESSION["AdminLevel"] == 0)
		$sConditions .= " AND level='0' ";

	if ($iProvince > 0)
		$sConditions .= " AND FIND_IN_SET('$iProvince', provinces) ";

	if ($iDistrict > 0)
		$sConditions .= " AND FIND_IN_SET('$iDistrict', districts) ";

	if ($iType > 0)
		$sConditions .= " AND type_id='$iType' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_admins", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, type_id, name, email, mobile, status, level, picture FROM tbl_admins $sConditions $sOrderBy LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sTypesList    = getList("tbl_admin_types", "id", "title");
	$sTypesList[0] = "N/A";

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_admins"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );


	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$iType    = $objDb->getField($i, "type_id");
		$sName    = $objDb->getField($i, "name");
		$sMobile  = $objDb->getField($i, "mobile");
		$sEmail   = $objDb->getField($i, "email");
		$iLevel   = $objDb->getField($i, "level");
		$sPicture = $objDb->getField($i, "picture");
		$sStatus  = $objDb->getField($i, "status");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
		{
			$sOptions .= (' <img class="icnToggle" id="'.$iId.'" src="images/icons/'.(($sStatus == 'A') ? 'success' : 'error').'.png" alt="Toggle Status" title="Toggle Status" />');
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
		}

		if ($sUserRights["Delete"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		if ($sPicture != "" && @file_exists($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture))
		{
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.ADMINS_IMG_DIR.'originals/'.$sPicture).'" src="images/icons/picture.png" alt="Picture" title="Picture" />');
			$sOptions .= (' <img class="icnThumb" id="'.$iId.'" rel="Admin" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" />');
		}

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sName),
		                              @utf8_encode($sEmail),
		                              @utf8_encode($sMobile),
		                              @utf8_encode($sTypesList[$iType]),
		                              (($sStatus == "A") ? "Active" : "In-Active"),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>