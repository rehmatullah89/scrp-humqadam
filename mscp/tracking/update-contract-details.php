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

	$sSchools = getDbValue("schools", "tbl_contracts", "id='$iContractId'");


	$objDb->execute("BEGIN");


	$iSchools = @explode(",", $sSchools);

	foreach ($iSchools as $iSchool)
	{
		$iPrice                        = IO::floatValue("txtPrice{$iSchool}");
		$iClassRooms                   = IO::intValue("txtClassRooms{$iSchool}");
		$iStudentToilets               = IO::intValue("txtStudentToilets{$iSchool}");
		$iStaffRooms                   = IO::intValue("txtStaffRooms{$iSchool}");
		$iStaffToilets                 = IO::intValue("txtStaffToilets{$iSchool}");
		$iScienceLabs                  = IO::intValue("txtScienceLabs{$iSchool}");
		$iItLabs                       = IO::intValue("txtItLabs{$iSchool}");
		$iExamHalls                    = IO::intValue("txtExamHalls{$iSchool}");
		$iLibrary                      = IO::intValue("txtLibrary{$iSchool}");
		$iClerkOffices                 = IO::intValue("txtClerkOffices{$iSchool}");
		$iPrincipalOffice              = IO::intValue("txtPrincipalOffice{$iSchool}");
		$iParkingStand                 = IO::intValue("txtParkingStand{$iSchool}");
		$iChowkidarHut                 = IO::intValue("txtChowkidarHut{$iSchool}");
		$iSoakagePit                   = IO::intValue("txtSoakagePit{$iSchool}");
		$iWaterSupply                  = IO::intValue("txtWaterSupply{$iSchool}");
		$sType                         = IO::strValue("ddType{$iSchool}");
		$sLoaSigned                    = IO::strValue("txtLoaSigned{$iSchool}");
		$sPerformanceBondReceived      = IO::strValue("txtPerformanceBondReceived{$iSchool}");
		$sConstructionScheduleReceived = IO::strValue("txtConstructionScheduleReceived{$iSchool}");
		$sContractAgreementReceived    = IO::strValue("txtContractAgreementReceived{$iSchool}");
		$sMobilizationAdvanceIssued    = IO::strValue("txtMobilizationAdvanceIssued{$iSchool}");
		$sMobilizationAtSite           = IO::strValue("txtMobilizationAtSite{$iSchool}");
		$sFirstStageInvoice            = IO::strValue("txtFirstStageInvoice{$iSchool}");
		$iFirstStagePayment            = IO::intValue("txtFirstStagePayment{$iSchool}");

		$sLoaSigned                    = (($sLoaSigned == "") ? "0000-00-00" : date("Y-m-d", strtotime($sLoaSigned)));
		$sPerformanceBondReceived      = (($sPerformanceBondReceived == "") ? "0000-00-00" : date("Y-m-d", strtotime($sPerformanceBondReceived)));
		$sConstructionScheduleReceived = (($sConstructionScheduleReceived == "") ? "0000-00-00" : date("Y-m-d", strtotime($sConstructionScheduleReceived)));
		$sContractAgreementReceived    = (($sContractAgreementReceived == "") ? "0000-00-00" : date("Y-m-d", strtotime($sContractAgreementReceived)));
		$sMobilizationAdvanceIssued    = (($sMobilizationAdvanceIssued == "") ? "0000-00-00" : date("Y-m-d", strtotime($sMobilizationAdvanceIssued)));
		$sMobilizationAtSite           = (($sMobilizationAtSite == "") ? "0000-00-00" : date("Y-m-d", strtotime($sMobilizationAtSite)));
		$sFirstStageInvoice            = (($sFirstStageInvoice == "") ? "0000-00-00" : date("Y-m-d", strtotime($sFirstStageInvoice)));


		$sSQL = "UPDATE tbl_contract_details SET price                          = '$iPrice',
												 class_rooms                    = '$iClassRooms',
												 student_toilets                = '$iStudentToilets',
												 staff_rooms                    = '$iStaffRooms',
												 staff_toilets                  = '$iStaffToilets',
												 science_labs                   = '$iScienceLabs',
												 it_labs                        = '$iItLabs',
												 exam_halls                     = '$iExamHalls',
												 library                        = '$iLibrary',
												 clerk_offices                  = '$iClerkOffices',
												 principal_office               = '$iPrincipalOffice',
												 parking_stand                  = '$iParkingStand',
												 chowkidar_hut                  = '$iChowkidarHut',
												 soakage_pit                    = '$iSoakagePit',
												 water_supply                   = '$iWaterSupply',
												 type                           = '$sType',
												 loa_signed                     = '$sLoaSigned',
												 performance_bond_received      = '$sPerformanceBondReceived',
												 construction_schedule_received = '$sConstructionScheduleReceived',
												 contract_agreement_received    = '$sContractAgreementReceived',
												 mobilization_advance_issued    = '$sMobilizationAdvanceIssued',
												 mobilization_at_site           = '$sMobilizationAtSite',
												 first_stage_invoice            = '$sFirstStageInvoice',
												 first_stage_payment            = '$iFirstStagePayment'
				 WHERE contract_id='$iContractId' AND school_id='$iSchool'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == false)
			break;
	}

	if ($bFlag == true)
	{
		$objDb->execute("COMMIT");
?>
<script type="text/javascript">
<!--
	parent.$.colorbox.close( );
	parent.showMessage("#GridMsg", "success", "The selected Contract Details have been Updated successfully.");
-->
</script>
<?
		exit( );
	}

	else
	{
		$objDb->execute("ROLLBACK");

		$_SESSION["Flag"] = "DB_ERROR";
	}
?>