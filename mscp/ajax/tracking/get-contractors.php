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
	$sCity       = IO::strValue("City");
	$sConditions = " WHERE id>'0' ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'company', 'city', 'phone', 'mobile', 'email', 'status');
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

		$sConditions .= " AND (company LIKE '%{$sKeywords}%' OR
		                       first_name LIKE '%{$sKeywords}%' OR
		                       last_name LIKE '%{$sKeywords}%' OR
		                       email LIKE '%{$sKeywords}%' OR
		                       city LIKE '%{$sKeywords}%' OR
		                       phone LIKE '%{$sKeywords}%' OR
		                       mobile LIKE '%{$sKeywords}%' OR
		                       status='$sStatus') ";
	}

	if ($sCity != "")
		$sConditions .= " AND city LIKE '$sCity' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_contractors", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, company, city, phone, mobile, email, status, logo, picture FROM tbl_contractors $sConditions $sOrderBy LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_contractors"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );


	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$sCompany = $objDb->getField($i, "company");
		$sCity    = $objDb->getField($i, "city");
		$sPhone   = $objDb->getField($i, "phone");
		$sMobile  = $objDb->getField($i, "mobile");
		$sEmail   = $objDb->getField($i, "email");
		$sStatus  = $objDb->getField($i, "status");
		$sLogo    = $objDb->getField($i, "logo");
		$sPicture = $objDb->getField($i, "picture");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnToggle" id="'.$iId.'" src="images/icons/'.(($sStatus == 'A') ? 'success' : 'error').'.png" alt="Toggle Status" title="Toggle Status" />');
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
			$sOptions .= (' <img class="icon icnBoqs" id="'.$iId.'" src="images/icons/boqs.png" alt="BOQs Setup" title="BOQs Setup" />');
		}

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		if ($sLogo != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo))
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.CONTRACTORS_IMG_DIR.'logos/'.$sLogo).'" src="images/icons/logo.png" alt="Logo" title="Logo" />');

		if ($sPicture != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture))
			$sOptions .= (' <img class="icnPicture" id="'.(SITE_URL.CONTRACTORS_IMG_DIR.'persons/'.$sPicture).'" src="images/icons/picture.png" alt="Picture" title="Picture" />');


		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sCompany),
		                              @utf8_encode($sCity),
		                              @utf8_encode($sPhone),
		                              @utf8_encode($sMobile),
		                              @utf8_encode($sEmail),
		                              (($sStatus == "A") ? "Active" : "In-Active"),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>