<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.3-tree.com/imc/                                                               **
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

	$sOffline = IO::strValue("ddOffline");
	$sMessage = IO::strValue("txtMessage");


	if ($sOffline == "Y" && ($sMessage == "" || $sMessage == "<br />"))
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_maintenance SET offline='$sOffline', message='$sMessage', date_time=NOW( ) WHERE id='1'";

		if ($objDb->execute($sSQL) == true)
			redirect("maintenance.php", "MAINTENANCE_UPDATED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>