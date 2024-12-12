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

	if ($bExport != true)
	{
		@require_once("../requires/common.php");
		@require_once("{$sRootDir}requires/fpdf/fpdf.php");
		@require_once("{$sRootDir}requires/fpdi/fpdi.php");

		$objDbGlobal = new Database( );
		$objDb       = new Database( );
		$objDb2      = new Database( );

		$iInvoiceId = IO::intValue("Id");
	}


	$sSQL = "SELECT * FROM tbl_invoices WHERE id='$iInvoiceId'";
	$objDb->query($sSQL);

	$iContract    = $objDb->getField(0, "contract_id");
	$iSchool      = $objDb->getField(0, "school_id");
	$sInvoiceNo   = $objDb->getField(0, "invoice_no");
	$sDate        = $objDb->getField(0, "date");
	$sDetails     = $objDb->getField(0, "details");
	$sInspections = $objDb->getField(0, "inspections");
	$sChequeNo    = $objDb->getField(0, "cheque_no");
	$sStatus      = $objDb->getField(0, "status");


	$sSQL = "SELECT title, contractor_id FROM tbl_contracts WHERE id='$iContract'";
	$objDb->query($sSQL);

	$sContract   = $objDb->getField(0, "title");	
	$iContractor = $objDb->getField(0, "contractor_id");
	
	
	$sSQL = "SELECT company, individual, tax_filer FROM tbl_contractors WHERE id='$iContractor'";
	$objDb->query($sSQL);

	$sCompany    = $objDb->getField(0, "company");	
	$sIndividual = $objDb->getField(0, "individual");
	$sTaxFiler   = $objDb->getField(0, "tax_filer");	


	$sSQL = "SELECT * FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool         = $objDb->getField(0, "name");
	$sCode           = $objDb->getField(0, "code");
	$iType           = $objDb->getField(0, "type_id");
	$iClassRooms     = $objDb->getField(0, "class_rooms");
	$iStudentToilets = $objDb->getField(0, "student_toilets");
	$iStaffRooms     = $objDb->getField(0, "staff_rooms");
	$iStaffToilets   = $objDb->getField(0, "staff_toilets");
	$fCost           = $objDb->getField(0, "cost");
	$iDistrict       = $objDb->getField(0, "district_id");
	$sStoreyType     = $objDb->getField(0, "storey_type");
	$sDesignType     = $objDb->getField(0, "design_type");


	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$iProvince   = getDbValue("province_id", "tbl_districts", "id='$iDistrict'");
	$sDistrict   = getDbValue("name", "tbl_districts", "id='$iDistrict'");
	$sProvince   = getDbValue("name", "tbl_provinces", "id='$iProvince'");
	
	
	$fWithholdingTaxRate = (($sIndividual == "Y") ? (($sTaxFiler == "Y") ? 7.50 : 10.0) : (($sTaxFiler == "Y") ? 7.0 : 10.0));
	$iContractDate       = 10;
	$iCommencementLetter = 5;
	$iMileStones         = 3;
	
	if ($sSchoolType == "D")
	{
		$iContractDate       = 125;
		$iCommencementLetter = 120;
		$iMileStones         = 118;
	}
	
	else if ($sSchoolType == "B")
	{
		$iContractDate       = 240;
		$iCommencementLetter = 235;
		$iMileStones         = 233;
	}
	
	$sContractDate       = getDbValue("`date`", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iContractDate'", "`date` DESC");
	$sCommencementLetter = getDbValue("`date`", "tbl_inspections", "school_id='$iSchool' AND stage_id='$iCommencementLetter'", "`date` DESC");



	$objPdf = new FPDI("P", "pt", "A4");
	
	
	// Cover Page
	$objPdf->setSourceFile("{$sRootDir}templates/invoice-cover-page.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);
	
	$objPdf->SetTextColor(0, 0, 0);
	$objPdf->SetFont('Arial', '', 8);

	$objPdf->Text(165, 55.0, $sCompany);
	$objPdf->Text(165, 76.0, $sCode);
	$objPdf->Text(165, 97.0, $sSchool);
	$objPdf->Text(165, 118.0, $sDistrict);
	
	$objPdf->Text(438, 55.0, $sInvoiceNo);
	$objPdf->Text(438, 76.0, formatDate($sDate, $_SESSION['DateFormat']));
	$objPdf->Text(438, 97.0, $sContract);
	$objPdf->Text(438, 118.0, $sProvince);
	
	
	$objPdf->Text(233, 143.0, formatNumber($fCost, false));
	$objPdf->Text(233, 166.0, "-");
	$objPdf->Text(233, 193.0, "-");
	
	$objPdf->Text(497, 143.0, formatDate($sContractDate, $_SESSION['DateFormat']));
	$objPdf->Text(497, 168.0, formatDate($sCommencementLetter, $_SESSION['DateFormat']));
	$objPdf->Text(497, 193.0, "");
	
	
	$objPdf->Text(142, 231.0, "-");
	$objPdf->Text(315, 231.0, "-");
	$objPdf->Text(497, 231.0, "-");
	
	
	$iInvoicedAmount     = getDbValue("SUM(amount)", "tbl_invoices", "contract_id='$iContract' AND school_id='$iSchool' AND `date`<'$sDate' AND status='P'");
	$iThisInvoice        = getDbValue("SUM(im.amount)", "tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb", "b.id=im.boq_id AND cb.boq_id=b.id AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'");
	$iRetentionMoney     = @round(($iThisInvoice / 100) * 10);
	$iMobAdvanceRecovery = 0;
	$iWithholdingTax     = @round(($iThisInvoice / 100) * $fWithholdingTaxRate);
	$iNetAmount          = ($iThisInvoice - ($iRetentionMoney + $iMobAdvanceRecovery + $iWithholdingTax));
	
	$objPdf->Text(315, 285.0, formatNumber($iInvoicedAmount, false));
	$objPdf->Text(450, 308.0, formatNumber($iThisInvoice, false));

	
	$objPdf->Text(315, 432.0, formatNumber($iRetentionMoney, false));
	$objPdf->Text(315, 457.0, formatNumber($iMobAdvanceRecovery, false));
	$objPdf->Text(315, 482.0, formatNumber($iWithholdingTax, false));
	
	$objPdf->Text(450, 505.0, formatNumber(($iRetentionMoney + $iMobAdvanceRecovery + $iWithholdingTax), false));
	$objPdf->Text(450, 530.0, formatNumber($iNetAmount, false));
	
	$objPdf->Text(95, 554.0, numberToWord($iNetAmount));
	
	
	
	
	// Summary Page
	$objPdf->setSourceFile("{$sRootDir}templates/invoice-summary.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);


	$objPdf->SetTextColor(0, 0, 0);
	$objPdf->SetFont('Arial', '', 8);

	$objPdf->Text(120, 117, $sSchool);
	$objPdf->Text(120, 127.6, $sCode);
	$objPdf->Text(120, 138.2, getDbValue("`type`", "tbl_school_types", "id='$iType'"));
	$objPdf->Text(120, 148.8, getDbValue("title", "tbl_packages", "FIND_IN_SET('$iSchool', schools)"));
	$objPdf->Text(120, 159.4, $sProvince);
	$objPdf->Text(120, 170, $sDistrict);
	$objPdf->Text(120, 180.6, getDbValue("s.name", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC"));
	$objPdf->Text(120, 191.2, $sCompany);
	$objPdf->Text(120, 201.8, $sContract);

	$objPdf->Text(470, 116, formatNumber($iClassRooms, false));
	$objPdf->Text(470, 127, formatNumber($iStudentToilets, false));
	$objPdf->Text(470, 138, formatNumber($iStaffRooms, false));
	$objPdf->Text(470, 149, formatNumber($iStaffToilets, false));
	$objPdf->Text(470, 160, "0");
	$objPdf->Text(470, 171, "0");


	$objPdf->SetFont('Arial', 'B', 10);
	$objPdf->Text(450, 202.5, (formatNumber($fCost, false)." PKR"));


	$objPdf->SetFont('Arial', '', 8);

	$sSQL = "SELECT id, `date`, invoice_no, amount, status FROM tbl_invoices WHERE contract_id='$iContract' AND school_id='$iSchool' AND `date`<'$sDate' ORDER BY `date`";
	$objDb->query($sSQL);

	$iCount    = $objDb->getCount( );
	$iInvoiced = 0;

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iInvoice   = $objDb->getField($i, "id");
		$sInvoiceNo = $objDb->getField($i, "invoice_no");
		$sDate      = $objDb->getField($i, "date");
		$iAmount    = $objDb->getField($i, "amount");
		$sStatus    = $objDb->getField($i, "status");


		$objPdf->Text(40, (273 + ($i * 18)), formatDate($sDate, $_SESSION['DateFormat']));
		$objPdf->Text(114, (273 + ($i * 18)), $sInvoiceNo);
		$objPdf->Text(195, (273 + ($i * 18)), getDbValue("s.name", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC"));
		$objPdf->Text(425, (273 + ($i * 18)), (formatNumber($iAmount, false)." PKR"));
		$objPdf->Text(510, (273 + ($i * 18)), (($sStatus == "P") ? "Paid" : "Un-Paid"));

		$iInvoiced += $iAmount;
	}


	$objPdf->SetFont('Arial', 'B', 10);
	$objPdf->Text(448, 378.5, (formatNumber($iInvoiced, false)." PKR"));


	$iLastStage = getDbValue("s.parent_id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC");

	if ($iLastStage > $iMileStones)
	{
		$iParentStage = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");

		if ($iParentStage > $iMileStones)
		{
			$iLastStage   = $iParentStage;
			$iParentStage = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");
		}

		if ($iParentStage > $iMileStones)
		{
			$iLastStage   = $iParentStage;
			$iParentStage = getDbValue("parent_id", "tbl_stages", "id='$iLastStage'");
		}
	}


	$objPdf->SetFont('Arial', '', 8);

	$objPdf->Text(135, 421.5, getDbValue("name", "tbl_stages", "id='$iLastStage'"));
	$objPdf->Text(135, 432.5, "-----");
	$objPdf->Text(135, 443.5, "-----");



	$objPdf->SetFont('Arial', '', 9);

	$sSQL = "SELECT b.title, b.unit,
	                SUM(im.measurements) AS _Quantity, SUM(im.amount) AS _Amount,
	                cb.rate
	         FROM tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb
	         WHERE b.id=im.boq_id AND cb.boq_id=b.id AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'
	         GROUP BY im.boq_id
	         ORDER BY b.title";
	$objDb2->query($sSQL);

	$iCount2 = $objDb2->getCount( );
	$iTotal  = 0;

	for ($j = 0, $iIndex = 0; $j < $iCount2; $j ++, $iIndex ++)
	{	
		if ($j > 0 && ($j % 11) == 0)
		{
			$objPdf->addPage( );
			$objPdf->useTemplate($iTemplateId, 0, 0);			
			
			
			$objPdf->SetFont('Arial', '', 8);

			$objPdf->Text(120, 117, $sSchool);
			$objPdf->Text(120, 127.6, $sCode);
			$objPdf->Text(120, 138.2, getDbValue("`type`", "tbl_school_types", "id='$iType'"));
			$objPdf->Text(120, 148.8, getDbValue("title", "tbl_packages", "FIND_IN_SET('$iSchool', schools)"));
			$objPdf->Text(120, 159.4, $sProvince);
			$objPdf->Text(120, 170, $sDistrict);
			$objPdf->Text(120, 180.6, getDbValue("s.name", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC"));
			$objPdf->Text(120, 191.2, $sContractor);
			$objPdf->Text(120, 201.8, $sContract);

			$objPdf->Text(470, 116, formatNumber($iClassRooms, false));
			$objPdf->Text(470, 127, formatNumber($iStudentToilets, false));
			$objPdf->Text(470, 138, formatNumber($iStaffRooms, false));
			$objPdf->Text(470, 149, formatNumber($iStaffToilets, false));
			$objPdf->Text(470, 160, "0");
			$objPdf->Text(470, 171, "0");


			$objPdf->SetFont('Arial', 'B', 10);
			$objPdf->Text(450, 202.5, (formatNumber($fCost, false)." PKR"));


			$objPdf->SetFont('Arial', '', 8);

			$iInvoiced = 0;

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iInvoice   = $objDb->getField($i, "id");
				$sInvoiceNo = $objDb->getField($i, "invoice_no");
				$sDate      = $objDb->getField($i, "date");
				$iAmount    = $objDb->getField($i, "amount");
				$sStatus    = $objDb->getField($i, "status");


				$objPdf->Text(40, (273 + ($i * 18)), formatDate($sDate, $_SESSION['DateFormat']));
				$objPdf->Text(114, (273 + ($i * 18)), $sInvoiceNo);
				$objPdf->Text(195, (273 + ($i * 18)), getDbValue("s.name", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC"));
				$objPdf->Text(425, (273 + ($i * 18)), (formatNumber($iAmount, false)." PKR"));
				$objPdf->Text(510, (273 + ($i * 18)), (($sStatus == "P") ? "Paid" : "Un-Paid"));

				$iInvoiced += $iAmount;
			}


			$objPdf->SetFont('Arial', 'B', 10);
			$objPdf->Text(448, 378.5, (formatNumber($iInvoiced, false)." PKR"));


			$objPdf->SetFont('Arial', '', 8);

			$objPdf->Text(135, 421.5, getDbValue("name", "tbl_stages", "id='$iLastStage'"));
			$objPdf->Text(135, 432.5, "");
			$objPdf->Text(135, 443.5, "");


			$objPdf->SetFont('Arial', '', 9);	
					
			$iIndex = 0;
		}

		
		$sBoqItem  = $objDb2->getField($j, "title");
		$sUnit     = $objDb2->getField($j, "unit");
		$fQuantity = $objDb2->getField($j, "_Quantity");
		$iAmount   = $objDb2->getField($j, "_Amount");
		$fRate     = $objDb2->getField($j, "rate");

		$objPdf->Text(40, (501 + ($iIndex * 18.5)), ($j + 1));
		$objPdf->Text(71, (501 + ($iIndex * 18.5)), $sBoqItem);
		$objPdf->Text(305, (501 + ($iIndex * 18.5)), formatNumber($fQuantity));
		$objPdf->Text(370, (501 + ($iIndex * 18.5)), strtoupper($sUnit));
		$objPdf->Text(423, (501 + ($iIndex * 18.5)), formatNumber($fRate));
		$objPdf->Text(477, (501 + ($iIndex * 18.5)), formatNumber($iAmount, false));


		$iTotal += $iAmount;
	}



	$objPdf->SetFont('Arial', 'B', 10);
	$objPdf->Text(476, 703.5, (formatNumber($iTotal, false). " PKR"));



	// Page 2 - Invoice Details
	$objPdf->setSourceFile("{$sRootDir}templates/invoice-details.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);


	$objPdf->SetTextColor(0, 0, 0);


	$sSQL = "SELECT b.id, b.title, b.unit,
	                SUM(im.measurements) AS _Quantity, SUM(im.amount) AS _Amount,
	                cb.rate
	         FROM tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb
	         WHERE b.id=im.boq_id AND cb.boq_id=b.id AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'
	         GROUP BY im.boq_id
	         ORDER BY b.title";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	$iIndex = 0;

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iBoqItem  = $objDb->getField($i, "id");
		$sBoqItem  = $objDb->getField($i, "title");
		$sUnit     = $objDb->getField($i, "unit");
		$fQuantity = $objDb->getField($i, "_Quantity");
		$iAmount   = $objDb->getField($i, "_Amount");
		$fRate     = $objDb->getField($i, "rate");


		if ($iIndex == 31)
		{
			$iIndex = 0;

			$objPdf->addPage( );
			$objPdf->useTemplate($iTemplateId, 0, 0);
		}


		$objPdf->SetFont('Arial', 'B', 9);

		$objPdf->Text(40, (168.0 + ($iIndex * 18.3)), ($i + 1));
		$objPdf->Text(71, (168.0 + ($iIndex * 18.3)), $sBoqItem);
		$objPdf->Text(282, (168.0 + ($iIndex * 18.3)), formatNumber($fQuantity));
		$objPdf->Text(347, (168.0 + ($iIndex * 18.3)), strtoupper($sUnit));
		$objPdf->Text(395, (168.0 + ($iIndex * 18.3)), formatNumber($fRate));
		$objPdf->Text(442, (168.0 + ($iIndex * 18.3)), formatNumber($iAmount, false));


		$sSQL = "SELECT title, measurements, multiplier, length, width, height FROM tbl_inspection_measurements WHERE school_id='$iSchool' AND FIND_IN_SET(inspection_id, '$sInspections') AND boq_id='$iBoqItem' ORDER BY title";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );
		$iIndex ++;

		for ($j = 0; $j < $iCount2; $j ++, $iIndex ++)
		{
			$sTitle      = $objDb2->getField($j, "title");
			$fMultiplier = $objDb2->getField($j, "multiplier");
			$fLength     = $objDb2->getField($j, "length");
			$fWidth      = $objDb2->getField($j, "width");
			$fHeight     = $objDb2->getField($j, "height");


			$sMeasurements = ((($fMultiplier > 1) ? "(" : "").formatNumber($fLength).(($sUnit == "cft" || $sUnit == "sft") ? (" x ".formatNumber($fWidth)) : "").(($sUnit == "cft") ? (" x ".formatNumber($fHeight)) : "").(($fMultiplier > 1) ? ") x {$fMultiplier}" : ""));


			$objPdf->SetFont('Arial', '', 9);
			$objPdf->SetFillColor(255, 255, 255);

			$objPdf->Text(71, (168.0 + ($iIndex * 18.3)), $sTitle);

			$objPdf->SetXY(277, (156.1 + ($iIndex * 18.3)));
			$objPdf->Cell(150, 17.2, $sMeasurements, 0, 0, "L", true);


			if ($iIndex == 31)
			{
				$iIndex = (($j < $iCount2) ? -1 : 0);

				$objPdf->addPage( );
				$objPdf->useTemplate($iTemplateId, 0, 0);
			}
		}
	}



	if ($bExport != true)
	{
		$objPdf->Output("{$sInvoiceNo}.pdf", "D");


		$objDb->close( );
		$objDb2->close( );
		$objDbGlobal->close( );

		@ob_end_flush( );
	}
	
	else
		$objPdf->Output($sRootDir.TEMP_DIR."{$sInvoiceNo}.pdf", "F");
?>