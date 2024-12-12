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

	$sStartDate  = IO::strValue("txtStartDate");
	$sEndDate    = IO::strValue("txtEndDate");

	if ($sStartDate == "" || $sEndDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT start_date, end_date FROM tbl_contracts WHERE id='$iContractId'";
		$objDb->query($sSQL);

		$sContractStart = $objDb->getField(0, "start_date");
		$sContractEnd   = $objDb->getField(0, "end_date");


		if (strtotime($sStartDate) < strtotime($sContractStart) || strtotime($sStartDate) > strtotime($sContractEnd) ||
			strtotime($sEndDate) < strtotime($sContractStart) || strtotime($sEndDate) > strtotime($sContractEnd) )
			$_SESSION["Flag"] = "INVALID_SCHEDULE_DATES";
	}

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_contract_schedules SET start_date = '$sStartDate',
										  		   end_date   = '$sEndDate'
		         WHERE id='$iScheduleId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
<script type="text/javascript">
<!--
		var sFields = new Array( );

		sFields[0] = "<?= formatDate($sStartDate, $_SESSION["DateFormat"]) ?>";
		sFields[1] = "<?= formatDate($sEndDate, $_SESSION["DateFormat"]) ?>";

		parent.updateRecord(<?= $iScheduleId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Construction Schedule Entry has been Updated successfully.");
-->
</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>