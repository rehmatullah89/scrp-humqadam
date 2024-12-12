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


	$iPageId     = IO::intValue("iDisplayStart");
	$iPageSize   = IO::intValue("iDisplayLength");
	$sKeywords   = IO::strValue("sSearch");
	$iStage      = IO::intValue("Stage");
	$sConditions = " WHERE id>'0' ";
	$sOrderBy    = " ORDER BY position ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'name', 'parent_id', 'unit', 'baseline_qty');
	$iPageId     = (($iPageId > 0) ? (($iPageId / $iPageSize) + 1) : 1);


	$sStages = array( );

	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParentId = $objDb->getField($i, "id");
		$sParent   = $objDb->getField($i, "name");

		$sStages[$iParentId] = $sParent;


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParentId' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStageId = $objDb2->getField($j, "id");
			$sStage   = $objDb2->getField($j, "name");

			$sStages[$iStageId] = ($sParent." &raquo; ".$sStage);


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStageId' ORDER BY name";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStageId = $objDb3->getField($k, "id");
				$sSubStage   = $objDb3->getField($k, "name");

				$sStages[$iSubStageId] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);
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
				if ($sColumns[IO::intValue("iSortCol_{$i}")] == "parent_id")
				{
					$sFields = getList("tbl_stages", "id", "id", "", "name");
					$sOrder  = @implode(",", $sFields);

					$sOrderBy .= ("FIELD(parent_id, {$sOrder}) ".strtoupper(IO::strValue("sSortDir_{$i}")).", ");
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


	if ($iStage > 0)
		$sConditions .= " AND parent_id='$iStage' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_stages", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, parent_id, name, unit, baseline_qty FROM tbl_stages $sConditions $sOrderBy LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_stages"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId       = $objDb->getField($i, "id");
		$iParent   = $objDb->getField($i, "parent_id");
		$sName     = $objDb->getField($i, "name");
		$sUnit     = $objDb->getField($i, "unit");
		$fQuantity = $objDb->getField($i, "baseline_qty");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y" && $sUnit != "")
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sName),
		                              @utf8_encode($sStages[$iParent]),
		                              @utf8_encode($sUnit),
		                              formatNumber($fQuantity),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>