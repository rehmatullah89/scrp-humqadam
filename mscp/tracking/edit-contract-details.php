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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iContractId = IO::intValue("ContractId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		@include("update-contract-details.php");


	$sSQL = "SELECT * FROM tbl_contracts WHERE id='$iContractId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle      = $objDb->getField(0, "title");
	$iContractor = $objDb->getField(0, "contractor_id");
	$sStartDate  = $objDb->getField(0, "start_date");
	$sEndDate    = $objDb->getField(0, "end_date");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-contract-details.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="ContractId" id="ContractId" value="<?= $iContractId ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width:1000px;">
	  <tr valign="top">
		<td width="30%">
		  <label><b>Title</b></label>
		  <div><?= $sTitle ?></div>
		</td>

		<td width="30%">
		  <label><b>Contractor</b></label>
		  <div><?= getDbValue("company", "tbl_contractors", "id='$iContractor'") ?></div>
		</td>

		<td width="20%">
		  <label><b>Start Date</b></label>
		  <div><?= formatDate($sStartDate, $_SESSION["DateFormat"]) ?></div>
		</td>

		<td width="20%">
		  <label><b>End Date</b></label>
		  <div><?= formatDate($sEndDate, $_SESSION["DateFormat"]) ?></div>
		</td>
	  </tr>
	</table>

	<hr />

	<div style="max-width:98%; padding-right:20px; overflow-x:scroll;">
	  <div class="grid" style="min-width:2500px;">
	    <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
		  <tr class="header" valign="top">
		    <td width="50">#</td>
		    <td width="250">School</td>
		    <td width="100" align="center">EIMC Code</td>
		    <td width="100" align="center">Package<br />Cost<br />(Millions PKR)</td>
		    <td width="100" align="center">Classrooms</td>
		    <td width="100" align="center">Student<br />Toilets</td>
		    <td width="100" align="center">Staff<br />Rooms</td>
		    <td width="100" align="center">Staff<br />Toilets</td>
		    <td width="100" align="center">Science Labs</td>
		    <td width="100" align="center">IT Labs</td>
		    <td width="100" align="center">Exam Halls</td>
		    <td width="100" align="center">Library</td>
		    <td width="100" align="center">Clerk Offices</td>
		    <td width="100" align="center">Principal<br />Office</td>
		    <td width="100" align="center">Parking /<br />Cycle Stand</td>
		    <td width="100" align="center">Chowkidar Hut</td>
		    <td width="100" align="center">Soakage Pit</td>
		    <td width="100" align="center">Water Supply</td>
		    <td width="150" align="center">Contract<br />Type</td>
		    <td width="150" align="center">LoA Signed /<br />Accepted date</td>
		    <td width="200" align="center">Performance Bond<br />(Insurance) Received<br />(10% of Contract Value)</td>
		    <td width="150" align="center">Construction<br />Schedule<br />Received</td>
		    <td width="150" align="center">Contract<br />Agreement<br />Received</td>
		    <td width="150" align="center">Mob Advance<br />(1st invoice) issued<br />(20% of Contract Value)</td>
		    <td width="150" align="center">Mobilization<br />at Site</td>
		    <td width="150" align="center">1st Stage Payment<br />2nd Invoice (IPC)</td>
		    <td width="150" align="center">1st Stage Payment<br />2nd Invoice (IPC)<br />(Amount PKR)</td>
		  </tr>
<?
	$sSQL = "SELECT *,
	                (SELECT name FROM tbl_schools WHERE id=tbl_contract_details.school_id) AS _School,
	                (SELECT code FROM tbl_schools WHERE id=tbl_contract_details.school_id) AS _Code
	         FROM tbl_contract_details
	         WHERE contract_id='$iContractId'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iSchool                       = $objDb->getField($i, "school_id");
		$sSchool                       = $objDb->getField($i, "_School");
		$sCode                         = $objDb->getField($i, "_Code");
		$fPrice                        = $objDb->getField($i, "price");
		$iClassRooms                   = $objDb->getField($i, "class_rooms");
		$iStudentToilets               = $objDb->getField($i, "student_toilets");
		$iStaffRooms                   = $objDb->getField($i, "staff_rooms");
		$iStaffToilets                 = $objDb->getField($i, "staff_toilets");
		$iScienceLabs                  = $objDb->getField($i, "science_labs");
		$iItLabs                       = $objDb->getField($i, "it_labs");
		$iExamHalls                    = $objDb->getField($i, "exam_halls");
		$iLibrary                      = $objDb->getField($i, "library");
		$iClerkOffices                 = $objDb->getField($i, "clerk_offices");
		$iPrincipalOffice              = $objDb->getField($i, "principal_office");
		$iParkingStand                 = $objDb->getField($i, "parking_stand");
		$iChowkidarHut                 = $objDb->getField($i, "chowkidar_hut");
		$iSoakagePit                   = $objDb->getField($i, "soakage_pit");
		$iWaterSupply                  = $objDb->getField($i, "water_supply");
		$sType                         = $objDb->getField($i, "type");
		$sLoaSigned                    = $objDb->getField($i, "loa_signed");
		$sPerformanceBondReceived      = $objDb->getField($i, "performance_bond_received");
		$sConstructionScheduleReceived = $objDb->getField($i, "construction_schedule_received");
		$sContractAgreementReceived    = $objDb->getField($i, "contract_agreement_received");
		$sMobilizationAdvanceIssued    = $objDb->getField($i, "mobilization_advance_issued");
		$sMobilizationAtSite           = $objDb->getField($i, "mobilization_at_site");
		$sFirstStageInvoice            = $objDb->getField($i, "first_stage_invoice");
		$iFirstStagePayment            = $objDb->getField($i, "first_stage_payment");


		$sLoaSigned                    = (($sLoaSigned == "0000-00-00") ? "" : $sLoaSigned);
		$sPerformanceBondReceived      = (($sPerformanceBondReceived == "0000-00-00") ? "" : $sPerformanceBondReceived);
		$sConstructionScheduleReceived = (($sConstructionScheduleReceived == "0000-00-00") ? "" : $sConstructionScheduleReceived);
		$sContractAgreementReceived    = (($sContractAgreementReceived == "0000-00-00") ? "" : $sContractAgreementReceived);
		$sMobilizationAdvanceIssued    = (($sMobilizationAdvanceIssued == "0000-00-00") ? "" : $sMobilizationAdvanceIssued);
		$sMobilizationAtSite           = (($sMobilizationAtSite == "0000-00-00") ? "" : $sMobilizationAtSite);
		$sFirstStageInvoice            = (($sFirstStageInvoice == "0000-00-00") ? "" : $sFirstStageInvoice);

		$fPrice                        = (($fPrice == 0) ? "" : $fPrice);
		$iClassRooms                   = (($iClassRooms == 0) ? "" : $iClassRooms);
		$iStudentToilets               = (($iStudentToilets == 0) ? "" : $iStudentToilets);
		$iStaffRooms                   = (($iStaffRooms == 0) ? "" : $iStaffRooms);
		$iStaffToilets                 = (($iStaffToilets == 0) ? "" : $iStaffToilets);
		$iScienceLabs                  = (($iScienceLabs == 0) ? "" : $iScienceLabs);
		$iItLabs                       = (($iItLabs == 0) ? "" : $iItLabs);
		$iExamHalls                    = (($iExamHalls == 0) ? "" : $iExamHalls);
		$iLibrary                      = (($iLibrary == 0) ? "" : $iLibrary);
		$iClerkOffices                 = (($iClerkOffices == 0) ? "" : $iClerkOffices);
		$iPrincipalOffice              = (($iPrincipalOffice == 0) ? "" : $iPrincipalOffice);
		$iParkingStand                 = (($iParkingStand == 0) ? "" : $iParkingStand);
		$iChowkidarHut                 = (($iChowkidarHut == 0) ? "" : $iChowkidarHut);
		$iSoakagePit                   = (($iSoakagePit == 0) ? "" : $iSoakagePit);
		$iWaterSupply                  = (($iWaterSupply == 0) ? "" : $iWaterSupply);
		$iFirstStagePayment            = (($iFirstStagePayment == 0) ? "" : $iFirstStagePayment);
?>

		  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		    <td><?= ($i + 1) ?></td>
		    <td><?= $sSchool ?></td>
		    <td align="center"><?= $sCode ?></td>
		    <td align="center"><input type="text" name="txtPrice<?= $iSchool ?>" id="txtPrice<?= $iSchool ?>" value="<?= $fPrice ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtClassRooms<?= $iSchool ?>" id="txtClassRooms<?= $iSchool ?>" value="<?= $iClassRooms ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtStudentToilets<?= $iSchool ?>" id="txtStudentToilets<?= $iSchool ?>" value="<?= $iStudentToilets ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtStaffRooms<?= $iSchool ?>" id="txtStaffRooms<?= $iSchool ?>" value="<?= $iStaffRooms ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtStaffToilets<?= $iSchool ?>" id="txtStaffToilets<?= $iSchool ?>" value="<?= $iStaffToilets ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtScienceLabs<?= $iSchool ?>" id="txtScienceLabs<?= $iSchool ?>" value="<?= $iScienceLabs ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtItLabs<?= $iSchool ?>" id="txtItLabs<?= $iSchool ?>" value="<?= $iItLabs ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtExamHalls<?= $iSchool ?>" id="txtExamHalls<?= $iSchool ?>" value="<?= $iExamHalls ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtLibrary<?= $iSchool ?>" id="txtLibrary<?= $iSchool ?>" value="<?= $iLibrary ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtClerkOffices<?= $iSchool ?>" id="txtClerkOffices<?= $iSchool ?>" value="<?= $iClerkOffices ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtPrincipalOffice<?= $iSchool ?>" id="txtPrincipalOffice<?= $iSchool ?>" value="<?= $iPrincipalOffice ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtParkingStand<?= $iSchool ?>" id="txtParkingStand<?= $iSchool ?>" value="<?= $iParkingStand ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtChowkidarHut<?= $iSchool ?>" id="txtChowkidarHut<?= $iSchool ?>" value="<?= $iChowkidarHut ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtSoakagePit<?= $iSchool ?>" id="txtSoakagePit<?= $iSchool ?>" value="<?= $iSoakagePit ?>" maxlength="10" size="10" class="textbox" /></td>
		    <td align="center"><input type="text" name="txtWaterSupply<?= $iSchool ?>" id="txtWaterSupply<?= $iSchool ?>" value="<?= $iWaterSupply ?>" maxlength="10" size="10" class="textbox" /></td>

		    <td align="center">
			  <select name="ddType<?= $iSchool ?>" id="ddType<?= $iSchool ?>" style="width:90%;">
			    <option value="I"<?= (($sType == 'I') ? ' selected' : '') ?>>Immediate</option>
			    <option value="P"<?= (($sType == 'P') ? ' selected' : '') ?>>Provisional</option>
			  </select>
		    </td>

		    <td align="center"><div class="date"><input type="text" name="txtLoaSigned<?= $iSchool ?>" id="txtLoaSigned<?= $iSchool ?>" value="<?= $sLoaSigned ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtPerformanceBondReceived<?= $iSchool ?>" id="txtPerformanceBondReceived<?= $iSchool ?>" value="<?= $sPerformanceBondReceived ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtConstructionScheduleReceived<?= $iSchool ?>" id="txtConstructionScheduleReceived<?= $iSchool ?>" value="<?= $sConstructionScheduleReceived ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtContractAgreementReceived<?= $iSchool ?>" id="txtContractAgreementReceived<?= $iSchool ?>" value="<?= $sContractAgreementReceived ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtMobilizationAdvanceIssued<?= $iSchool ?>" id="txtMobilizationAdvanceIssued<?= $iSchool ?>" value="<?= $sMobilizationAdvanceIssued ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtMobilizationAtSite<?= $iSchool ?>" id="txtMobilizationAtSite<?= $iSchool ?>" value="<?= $sMobilizationAtSite ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtFirstStageInvoice<?= $iSchool ?>" id="txtFirstStageInvoice<?= $iSchool ?>" value="<?= $sFirstStageInvoice ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><input type="text" name="txtFirstStagePayment<?= $iSchool ?>" id="txtFirstStagePayment<?= $iSchool ?>" value="<?= $iFirstStagePayment ?>" maxlength="10" size="10" class="textbox" /></td>
		  </tr>
<?
	}
?>
	    </table>
	  </div>
	</div>

    <br />
    <br />
    <button id="BtnSave">Save Contract Details</button>
    <button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>