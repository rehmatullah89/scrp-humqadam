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

	$sType   = IO::strValue("txtType");
	$sStatus = IO::strValue("ddStatus");
	$bError  = true;


	if ($sType == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_school_types WHERE `type` LIKE '$sType'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SCHOOL_TYPE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iType = getNextId("tbl_school_types");

		$sSQL  = "INSERT INTO tbl_school_types SET id     = '$iType',
											       `type` = '$sType',
											       status = '$sStatus'";

		if ($objDb->execute($sSQL) == true)
			redirect("school-types.php", "SCHOOL_TYPE_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>