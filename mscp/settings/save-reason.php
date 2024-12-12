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

	$sReason = IO::strValue("txtReason");
	$sStatus = IO::strValue("ddStatus");
	$bError  = true;


	if ($sReason == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_failure_reasons WHERE reason LIKE '$sReason'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "FAILURE_REASON_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iReason = getNextId("tbl_failure_reasons");

		$sSQL  = "INSERT INTO tbl_failure_reasons SET id       = '$iReason',
													  reason   = '$sReason',
													  position = '$iReason',
													  status   = '$sStatus'";

		if ($objDb->execute($sSQL) == true)
			redirect("reasons.php", "FAILURE_REASON_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>