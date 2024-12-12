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

	$sTitle  = IO::strValue("txtTitle");
	$sUnit   = IO::strValue("ddUnit");
	$sStatus = IO::strValue("ddStatus");
	$bError  = true;


	if ($sTitle == "" || $sUnit == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_boqs WHERE title LIKE '$sTitle'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "BOQ_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iBoq = getNextId("tbl_boqs");

		$sSQL = "INSERT INTO tbl_boqs SET id       = '$iBoq',
										  title    = '$sTitle',
										  unit     = '$sUnit',
										  position = '$iBoq',
										  status   = '$sStatus'";

		if ($objDb->execute($sSQL) == true)
			redirect("boqs.php", "BOQ_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>