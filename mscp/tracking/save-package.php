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

	$sTitle   = IO::strValue("txtTitle");
	$sDetails = IO::strValue("txtDetails");
	$sStatus  = IO::strValue("ddStatus");
	$sSchools = IO::strValue("txtSchools");
        $iLots    = IO::intValue("txtLots");
        
	$bError   = true;


	if ($sTitle == "" || $sSchools == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_packages WHERE title LIKE '$sTitle'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "PACKAGE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iPackage = getNextId("tbl_packages");

		$sSQL = "INSERT INTO tbl_packages SET id        = '$iPackage',
											  title     = '$sTitle',
                                                                                          lots      = '$iLots',    
											  details   = '$sDetails',
											  schools   = '$sSchools',
											  status    = '$sStatus',
											  date_time = NOW( )";

		if ($objDb->execute($sSQL) == true)
			redirect("packages.php", "PACKAGE_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>