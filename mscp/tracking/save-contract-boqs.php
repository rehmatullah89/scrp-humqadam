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

	$bFlag = $objDb->execute("BEGIN");


	$sBoqsList = getList("tbl_boqs", "id", "title");

	foreach ($sBoqsList as $iBoq => $sTitle)
	{
		$fRate = IO::floatValue("txtRate{$iBoq}");

		$sSQL = "UPDATE tbl_contract_boqs SET `rate`='$fRate' WHERE contract_id='$iContractId' AND boq_id='$iBoq'";
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
	parent.showMessage("#GridMsg", "success", "The selected Contract BOQ Details have been Updated successfully.");
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