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
	$iContract   = IO::intValue("Contract");
	$sStatus     = IO::strValue("Status");
	$sConditions = " WHERE id>'0' ";
	$sOrderBy    = " ORDER BY id ASC ";
	$sSortOrder  = "ASC";
	$sColumns    = array('id', 'invoice_id', 'title', '_School', 'contract_id', 'date', 'amount', 'status');
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
		$sConditions .= " AND (invoice_no LIKE '$sKeywords' OR title LIKE '%{$sKeywords}%') ";

	if ($iContract > 0)
		$sConditions .= " AND contract_id='$iContract' ";

	if ($sStatus != "")
		$sConditions .= " AND status='$sStatus' ";


	@list($iTotalRecords, $iPageCount, $iStart) = getPagingInfo("tbl_invoices", $sConditions, $iPageSize, $iPageId);


	$sSQL = "SELECT id, contract_id, date, invoice_no, title, amount, status,
					(SELECT name FROM tbl_schools WHERE id=tbl_invoices.school_id) AS _School
			 FROM tbl_invoices
			 $sConditions
			 $sOrderBy
			 LIMIT $iStart, $iPageSize";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	$sContractsList = getList("tbl_contracts", "id", "title");

	$sOutput = array("sEcho"                => IO::intValue("sEcho"),
	                 "iTotalRecords"        => getDbValue("COUNT(1)", "tbl_invoices"),
	                 "iTotalDisplayRecords" => $iTotalRecords,
	                 "aaData"               => array( ) );


	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId        = $objDb->getField($i, "id");
		$iContract  = $objDb->getField($i, "contract_id");
		$sSchool    = $objDb->getField($i, "_School");
		$sDate      = $objDb->getField($i, "date");
		$sInvoiceNo = $objDb->getField($i, "invoice_no");
		$sTitle     = $objDb->getField($i, "title");
		$sAmount    = $objDb->getField($i, "amount");
		$sStatus    = $objDb->getField($i, "status");
		$sReleased  = $objDb->getField($i, "released");


		$sOptions = "";

		if ($sUserRights["Edit"] == "Y")
		{
			$sOptions .= (' <img class="icnEdit" id="'.$iId.'" src="images/icons/edit.gif" alt="Edit" title="Edit" />');
			$sOptions .= (' <img class="icon icnReleased" id="'.$iId.'" src="images/icons/'.(($sReleased == 'Y') ? 'green' : 'blue').'.png" alt="Toggle Released Status" title="Toggle Released Status" />');
		}

		if ($sUserRights["Delete"] == "Y")
			$sOptions .= (' <img class="icnDelete" id="'.$iId.'" src="images/icons/delete.gif" alt="Delete" title="Delete" />');

		$sOptions .= (' <img class="icnView" id="'.$iId.'" src="images/icons/view.gif" alt="View" title="View" />');
		$sOptions .= (' <a href="'.$sCurDir.'/export-invoice.php?Id='.$iId.'"><img class="icnPdf" src="images/icons/pdf.png" alt="Invoice" title="Invoice" /></a>');


		$sOutput['aaData'][] = array( (($sSortOrder == "ASC") ? ($iStart + $i + 1) : ($iTotalRecords - $i - $iStart)),
		                              @utf8_encode($sInvoiceNo),
									  @utf8_encode($sTitle),
									  @utf8_encode($sSchool),
		                              @utf8_encode($sContractsList[$iContract]),
		                              @utf8_encode(formatDate($sDate, $_SESSION["DateFormat"])),
		                              @utf8_encode(formatNumber($sAmount, false)),
		                              @utf8_encode((($sStatus == "P") ? "Paid" : "Un-Paid")),
		                              $sOptions );
	}

	print @json_encode($sOutput);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>