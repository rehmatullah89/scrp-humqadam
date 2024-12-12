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
		require_once("../requires/common.php");
		require_once("{$sRootDir}requires/fpdf/fpdf.php");
		require_once("{$sRootDir}requires/fpdi/fpdi.php");

		$objDbGlobal = new Database( );
		$objDb       = new Database( );
		$objDb2      = new Database( );
                $objDb3      = new Database( );

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
	$objPdf->setSourceFile("{$sRootDir}templates/interim_payment_certificate.pdf");
	$iTemplateId = $objPdf->importPage(1, '/MediaBox');

	$objPdf->addPage( );
	$objPdf->useTemplate($iTemplateId, 0, 0);
	
	$objPdf->SetTextColor(0, 0, 0);
	$objPdf->SetFont('Arial', '', 8);

	$objPdf->Text(110, 80.0, $sCompany);
	$objPdf->Text(110, 100.0, $sCode);
	$objPdf->Text(110, 122.0, $sSchool);
	$objPdf->Text(110, 142.0, $sDistrict);
	
	$objPdf->Text(400, 80.0, $sInvoiceNo);
	$objPdf->Text(400, 100.0, formatDate($sDate, $_SESSION['DateFormat']));
	$objPdf->Text(400, 122.0, $sContract);
	$objPdf->Text(400, 142.0, $sProvince);
	
	
	$objPdf->Text(200, 172.0, formatNumber($fCost, false));
	$objPdf->Text(200, 192.0, "-");
	$objPdf->Text(200, 212.0, "-");
	
	$objPdf->Text(490, 172.0, formatDate($sContractDate, $_SESSION['DateFormat']));
	$objPdf->Text(490, 192.0, formatDate($sCommencementLetter, $_SESSION['DateFormat']));
	$objPdf->Text(490, 212.0, "-");
	
	
	$objPdf->Text(135, 258.0, "-");
	$objPdf->Text(315, 258.0, "-");
	$objPdf->Text(498, 258.0, "-");
	
	
	$iInvoicedAmount     = getDbValue("SUM(amount)", "tbl_invoices", "contract_id='$iContract' AND school_id='$iSchool' AND `date`<'$sDate' AND status='P'");
	$iThisInvoice        = getDbValue("SUM(im.amount)", "tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb", "b.id=im.boq_id AND cb.boq_id=b.id AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'");
	$iRetentionMoney     = @round(($iThisInvoice / 100) * 10);
	$iMobAdvanceRecovery = 0;
	$iWithholdingTax     = @round(($iThisInvoice / 100) * $fWithholdingTaxRate);
	$iNetAmount          = ($iThisInvoice - ($iRetentionMoney + $iMobAdvanceRecovery + $iWithholdingTax));
	
	$objPdf->Text(450, 310.0, formatNumber($iInvoicedAmount, false));
	$objPdf->Text(450, 332.0, formatNumber($iThisInvoice, false));

	
	$objPdf->Text(315, 450.0, formatNumber($iRetentionMoney, false));
	$objPdf->Text(315, 475.0, formatNumber($iMobAdvanceRecovery, false));
	$objPdf->Text(315, 500.0, formatNumber($iWithholdingTax, false));
	
	$objPdf->Text(380, 525.0, formatNumber(($iRetentionMoney + $iMobAdvanceRecovery + $iWithholdingTax), false));
	$objPdf->Text(380, 550.0, formatNumber($iNetAmount, false));
	
	$objPdf->Text(105, 575.0, numberToWord($iNetAmount));
	
	
	

	// Summary sheet for previuos invoices
	$sSQL = "SELECT id, `date`, invoice_no, amount, status FROM tbl_invoices WHERE contract_id='$iContract' AND school_id='$iSchool' AND `date`<'$sDate' ORDER BY `date`";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
		$objPdf->setSourceFile("{$sRootDir}templates/interim_payment_certificate.pdf");
		$iTemplateId = $objPdf->importPage(2, '/MediaBox');

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


		$iInvoiced = 0;

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iInvoice   = $objDb->getField($i, "id");
			$sInvoiceNo = $objDb->getField($i, "invoice_no");
			$sDate      = $objDb->getField($i, "date");
			$iAmount    = $objDb->getField($i, "amount");
			$sStatus    = $objDb->getField($i, "status");


			$objPdf->Text(40, (273 + ($i * 18)),  formatDate($sDate, $_SESSION['DateFormat']));
			$objPdf->Text(114, (273 + ($i * 18)), $sInvoiceNo);
			$objPdf->Text(195, (273 + ($i * 18)), getDbValue("s.name", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC"));
			$objPdf->Text(425, (273 + ($i * 18)), (formatNumber($iAmount, false)." PKR"));
			$objPdf->Text(510, (273 + ($i * 18)), (($sStatus == "P") ? "Paid" : "Un-Paid"));

			$iInvoiced += $iAmount;
		}


		$objPdf->SetFont('Arial', 'B', 10);
		$objPdf->Text(440, 765, (formatNumber($iInvoiced, false)." PKR"));
	}
		
	
	
    // Page 3 - Summary Sheet for Current Invoice
	$sSQL = "SELECT b.title, b.unit, im.id as _ImId,im.inspection_id,
	                SUM(im.measurements) AS _Quantity, SUM(im.amount) AS _Amount,
	                cb.rate
	         FROM tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb
	         WHERE b.id=im.boq_id AND cb.boq_id=b.id AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'
	         GROUP BY im.boq_id 
             ORDER BY b.title";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	
	if ($iCount > 0)
	{
		$objPdf->setSourceFile("{$sRootDir}templates/interim_payment_certificate.pdf");
		$iTemplateId = $objPdf->importPage(3, '/MediaBox');

		$objPdf->addPage( );
		$objPdf->useTemplate($iTemplateId, 0, 0);


		$objPdf->SetTextColor(0, 0, 0);

		$iLastStage      = getDbValue("s.parent_id", "tbl_inspections i, tbl_stages s", "i.stage_id=s.id AND i.invoice_id='$iInvoiceId'", "s.position DESC");
		$InspectionAdmin = getDbValue("a.name", "tbl_admins a, tbl_inspections i", "i.admin_id=a.id AND i.invoice_id!='0' AND i.invoice_id='$iInvoiceId'");
			
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

		$objPdf->Text(155, 134, getDbValue("name", "tbl_stages", "id='$iLastStage'"));
		$objPdf->Text(200, 148, $InspectionAdmin);


		$objPdf->SetFont('Arial', '', 9);


		$iTotal  = 0;

		for ($j = 0, $iIndex = 0; $j < $iCount; $j ++, $iIndex ++)
		{	
			if ($j > 0 && ($j % 25) == 0)
			{
				$objPdf->addPage( );
				$objPdf->useTemplate($iTemplateId, 0, 0);			
				
				$objPdf->Text(155, 134, getDbValue("name", "tbl_stages", "id='$iLastStage'"));
				$objPdf->Text(200, 148, $InspectionAdmin);
		
				$objPdf->SetFont('Arial', '', 9);	
				$iIndex = 0;
			}

			$iImId             = $objDb->getField($j, "_ImId");
			$iInspectionId     = $objDb->getField($j, "inspection_id");
			$sBoqItem          = iconv('utf-8', 'cp1252', $objDb->getField($j, "title"));
			$sUnit             = $objDb->getField($j, "unit");
			$fNegativeQuantity =  getDbValue("SUM(measurements)", "tbl_inspection_measurements", "parent_id='$iImId' AND inspection_id='$iInspectionId'");
			$fQuantity         = $objDb->getField($j, "_Quantity");
			$fOrigQuantity     = $fQuantity - $fNegativeQuantity;
			$iAmount           = $objDb->getField($j, "_Amount");
			$fRate             = $objDb->getField($j, "rate");

			$objPdf->Text(40, (210 + ($iIndex * 18.3)), ($j + 1));
			$objPdf->Text(71, (210 + ($iIndex * 18.3)), $sBoqItem);
			$objPdf->Text(305, (210 + ($iIndex * 18.3)), formatNumber($fOrigQuantity));
			$objPdf->Text(370, (210 + ($iIndex * 18.3)), strtoupper($sUnit));
			$objPdf->Text(423, (210 + ($iIndex * 18.3)), formatNumber($fRate));
			$objPdf->Text(477, (210 + ($iIndex * 18.3)), formatNumber($iAmount, false));


			$iTotal += $iAmount;
		}

		$objPdf->SetFont('Arial', 'B', 10);
		$objPdf->Text(475, 665, (formatNumber($iTotal, false). " PKR"));
	}
	
	
	


	// Page 4 - Invoice Details
	$sSQL = "SELECT b.id, b.title, b.unit,
	                SUM(im.measurements) AS _Quantity, SUM(im.amount) AS _Amount,
	                cb.rate
	         FROM tbl_inspection_measurements im, tbl_boqs b, tbl_contract_boqs cb
	         WHERE b.id=im.boq_id AND cb.boq_id=b.id AND im.parent_id='0' AND FIND_IN_SET(im.inspection_id, '$sInspections') AND cb.contract_id='$iContract'
            	 GROUP BY im.boq_id
                 ORDER BY b.title";
    $objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	
	if ($iCount > 0)
	{
		$objPdf->setSourceFile("{$sRootDir}templates/interim_payment_certificate.pdf");
		$iTemplateId = $objPdf->importPage(4, '/MediaBox');

		$objPdf->addPage( );
		$objPdf->useTemplate($iTemplateId, 0, 0);


		$objPdf->SetTextColor(0, 0, 0);

		$iIndex = 0;

		for ($i = 0; $i < $iCount; $i ++)
		{
			//$iImId             = $objDb->getField($i, "_ImId");
			$iBoqItem          = $objDb->getField($i, "id");
			$sBoqItem          = iconv('utf-8', 'cp1252', $objDb->getField($i, "title"));
			$sUnit             = $objDb->getField($i, "unit");
			$fQuantity         = $objDb->getField($i, "_Quantity");
			$iAmount           = $objDb->getField($i, "_Amount");
			$fRate             = $objDb->getField($i, "rate");


			if ($iIndex == 31)
			{
				$iIndex = 0;

				$objPdf->addPage( );
				$objPdf->useTemplate($iTemplateId, 0, 0);
			}


			$objPdf->SetFont('Arial', 'B', 9);

			$objPdf->Text(40, (168.0 + ($iIndex * 18.2)), ($i + 1));
			$objPdf->Text(71, (168.0 + ($iIndex * 18.2)), $sBoqItem);
			//$objPdf->Text(347, (168.0 + ($iIndex * 18.3)), formatNumber($fQuantity));
			//$objPdf->Text(395, (168.0 + ($iIndex * 18.3)), strtoupper($sUnit));
			//$objPdf->Text(442, (168.0 + ($iIndex * 18.3)), formatNumber($iAmount, false));


			$sSQL = "SELECT id, title, measurements, multiplier, length, width, height FROM tbl_inspection_measurements WHERE school_id='$iSchool' AND FIND_IN_SET(inspection_id, '$sInspections') AND boq_id='$iBoqItem' AND parent_id='0' ORDER BY title";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );
			$iIndex ++;

			for ($j = 0; $j < $iCount2; $j ++, $iIndex ++)
			{
				$fTotalQuantity = $fQuantity;
				$iId         = $objDb2->getField($j, "id");
				$sTitle      = iconv('utf-8', 'cp1252', $objDb2->getField($j, "title"));
				$iQuantity   = $objDb2->getField($j, "measurements");
				$fMultiplier = $objDb2->getField($j, "multiplier");
				$fLength     = $objDb2->getField($j, "length");
				$fWidth      = $objDb2->getField($j, "width");
				$fHeight     = $objDb2->getField($j, "height");


				$sMeasurements = ((($fMultiplier > 1) ? "(" : "").formatNumber($fLength).(($sUnit == "cft" || $sUnit == "sft") ? (" x ".formatNumber($fWidth)) : "").(($sUnit == "cft") ? (" x ".formatNumber($fHeight)) : "").(($fMultiplier > 1) ? ") x {$fMultiplier}" : ""));


				$objPdf->SetFont('Arial', '', 9);
				$objPdf->SetFillColor(255, 255, 255);

				$objPdf->Text(80, (167.0 + ($iIndex * 18.2)), '[+]  '.$sTitle);
				$objPdf->Text(340, (167.0 + ($iIndex * 18.2)), '+'.formatNumber($iQuantity));
				$objPdf->Text(400, (167.0 + ($iIndex * 18.2)), strtoupper($sUnit));
				
				$objPdf->SetXY(425, (155 + ($iIndex * 18.2)));
				$objPdf->Cell(150, 17.2, $sMeasurements);
							
							
				$sSQL3 = "SELECT title, measurements, multiplier, length, width, height FROM tbl_inspection_measurements WHERE parent_id='$iId' ORDER BY title";
				$objDb3->query($sSQL3);
				$iCount3 = $objDb3->getCount( );
				
				for ($k = 0; $k < $iCount3; $k ++, $iIndex ++)
				{
					$sTitle         = $objDb3->getField($k, "title");
					$iQuantity      = $objDb3->getField($k, "measurements");
					$fMultiplier    = $objDb3->getField($k, "multiplier");
					$fLength        = $objDb3->getField($k, "length");
					$fWidth         = $objDb3->getField($k, "width");
					$fHeight        = $objDb3->getField($k, "height");
					$fTotalQuantity = $fTotalQuantity - $iQuantity;

					$sMeasurements = ((($fMultiplier > 1) ? "(" : "").formatNumber($fLength).(($sUnit == "cft" || $sUnit == "sft") ? (" x ".formatNumber($fWidth)) : "").(($sUnit == "cft") ? (" x ".formatNumber($fHeight)) : "").(($fMultiplier > 1) ? ") x {$fMultiplier}" : ""));

					$objPdf->SetFont('Arial', '', 9);
					$objPdf->SetFillColor(255, 255, 255);

					$objPdf->Text(80, (185.0 + ($iIndex * 18.2)), '[-]  '.$sTitle);
					$objPdf->Text(340, (185.0 + ($iIndex * 18.2)), '-'.formatNumber($iQuantity));
					$objPdf->Text(400, (185.0 + ($iIndex * 18.2)), strtoupper($sUnit));

					$objPdf->SetXY(425, (172 + ($iIndex * 18.2)));
					$objPdf->Cell(150, 17.2, $sMeasurements);

					if ($iIndex == 31)
					{
						$iIndex = (($k < $iCount3) ? -1 : 0);
						$objPdf->addPage( );
						$objPdf->useTemplate($iTemplateId, 0, 0);
					}
				}
                        
                if ($iIndex == 31)
				{
					$iIndex = (($j < $iCount2) ? -1 : 0);

					$objPdf->addPage( );
					$objPdf->useTemplate($iTemplateId, 0, 0);
				}
			}
			
			$objPdf->SetFont('Arial', 'B', 9);
			$objPdf->Text(300, (167.0 + ($iIndex * 18.2)), 'Total');
			$objPdf->Text(345, (167.0 + ($iIndex * 18.2)), formatNumber($fTotalQuantity));
			$objPdf->Text(400, (167.0 + ($iIndex * 18.2)), strtoupper($sUnit));
			$iIndex++;
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