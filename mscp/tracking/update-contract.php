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

	$_SESSION["Flag"] = "";

	$iContractor = IO::intValue("ddContractor");
	$sTitle      = IO::strValue("txtTitle");
	$sDetails    = IO::strValue("txtDetails");
	$sStartDate  = IO::strValue("txtStartDate");
	$sEndDate    = IO::strValue("txtEndDate");
	$sStatus     = IO::strValue("ddStatus");
	$sSchools    = IO::strValue("txtSchools");


	if ($iContractor == 0 || $sTitle == "" || $sStartDate == "" || $sEndDate == "" || $sSchools == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_contracts WHERE title LIKE '$sTitle' AND id!='$iContractId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "CONTRACT_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$sSQL = "UPDATE tbl_contracts SET contractor_id = '$iContractor',
										  title         = '$sTitle',
										  details       = '$sDetails',
										  start_date    = '$sStartDate',
										  end_date      = '$sEndDate',
										  schools       = '$sSchools',
										  status        = '$sStatus'
		         WHERE id='$iContractId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL  = "DELETE FROM tbl_contract_details WHERE contract_id='$iContractId' AND NOT FIND_IN_SET(school_id, '$sSchools')";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_contract_schedules SET contract_id='0' WHERE contract_id='$iContractId' AND NOT FIND_IN_SET(school_id, '$sSchools')";
			$bFlag = $objDb->execute($sSQL);
		}
		
		if ($bFlag == true)
		{
			$sSQL  = "UPDATE tbl_contract_schedules SET contract_id='$iContractId' WHERE contract_id!='$iContractId' AND FIND_IN_SET(school_id, '$sSchools')";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$iSchools = @explode(",", $sSchools);

			foreach ($iSchools as $iSchool)
			{
				if (getDbValue("COUNT(1)", "tbl_contract_details", "contract_id='$iContractId' AND school_id='$iSchool'") == 1)
					continue;


				$sSQL  = "INSERT INTO tbl_contract_details (contract_id, school_id) VALUES ('$iContractId', '$iSchool')";
				$bFlag = $objDb->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= str_replace("\r\n", "<br />", addslashes($sTitle)) ?>";
		sFields[1] = "<?= addslashes(getDbValue("company", "tbl_contractors", "id='$iContractor'")) ?>";
		sFields[2] = "<?= formatDate($sStartDate, $_SESSION["DateFormat"]) ?>";
		sFields[3] = "<?= formatDate($sEndDate, $_SESSION["DateFormat"]) ?>";
		sFields[4] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[5] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateRecord(<?= $iContractId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Contract has been Updated successfully.");
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
	}
?>