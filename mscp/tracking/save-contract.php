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
	$bError      = true;


	if ($iContractor == 0 || $sTitle == "" || $sStartDate == "" || $sEndDate == "" || $sSchools == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_contracts WHERE title LIKE '$sTitle'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "CONTRACT_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$iContract = getNextId("tbl_contracts");

		$sSQL = "INSERT INTO tbl_contracts SET id            = '$iContract',
											   contractor_id = '$iContractor',
											   title         = '$sTitle',
											   details       = '$sDetails',
											   start_date    = '$sStartDate',
											   end_date      = '$sEndDate',
											   schools       = '$sSchools',
											   status        = '$sStatus',
											   date_time     = NOW( )";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$iSchools = @explode(",", $sSchools);

			foreach ($iSchools as $iSchool)
			{
				$sSQL  = "INSERT INTO tbl_contract_details (contract_id, school_id) VALUES ('$iContract', '$iSchool')";
				$bFlag = $objDb->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}

		if ($bFlag == true)
		{
			$sSQL  = "INSERT INTO tbl_contract_boqs (contract_id, boq_id, `rate`) (SELECT '$iContract', b.id, cb.rate FROM tbl_boqs b, tbl_contractor_boqs cb WHERE b.id=cb.boq_id AND cb.contractor_id='$iContractor')";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("contracts.php", "CONTRACT_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>