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

	$sCompany    = IO::strValue("txtCompany");
	$sAddress    = IO::strValue("txtAddress");
	$sCity       = IO::strValue("txtCity");
	$sIndividual = IO::strValue("ddIndividual");
	$sTaxFiler   = IO::strValue("ddTaxFiler");
	$iInvoicing  = IO::intValue("ddInvoicing");
	$sTitle      = IO::strValue("ddTitle");
	$sFirstName  = IO::strValue("txtFirstName");
	$sMiddleName = IO::strValue("txtMiddleName");
	$sLastName   = IO::strValue("txtLastName");
	$sPhone      = IO::strValue("txtPhone");
	$sMobile     = IO::strValue("txtMobile");
	$sEmail      = IO::strValue("txtEmail");
	$sStatus     = IO::strValue("ddStatus");
	$sLogo       = "";
	$sPicture    = "";
	$bError      = true;


	if ($sCompany == "" || $sCity == "" || $sTitle || $sFirstName == "" || $sLastName == "" || $sPhone == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_contractors WHERE company LIKE '$sCompany'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "CONTRACTOR_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iContractor = getNextId("tbl_contractors");

		if ($_FILES['fileLogo']['name'] != "")
		{
			$sLogo = ($iContractor."-".IO::getFileName($_FILES['fileLogo']['name']));

			if (!@move_uploaded_file($_FILES['fileLogo']['tmp_name'], ($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo)))
				$sLogo = "";
		}

		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iContractor."-".IO::getFileName($_FILES['filePicture']['name']));

			if (!@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture)))
				$sPicture = "";
		}


		$sSQL = "INSERT INTO tbl_contractors SET id          = '$iContractor',
											     company     = '$sCompany',
											     address     = '$sAddress',
											     city        = '$sCity',
												 individual  = '$sIndividual',
												 tax_filer   = '$sTaxFiler',
												 invoicing   = '$iInvoicing',
										    	 logo        = '$sLogo',
												 title       = '$sTitle',
											     first_name  = '$sFirstName',
												 middle_name = '$sMiddleName',
											     last_name   = '$sLastName',
											     phone       = '$sPhone',
											     mobile      = '$sMobile',
											     email       = '$sEmail',
										    	 picture     = '$sPicture',
											     status      = '$sStatus',
											     date_time   = NOW( )";

		if ($objDb->execute($sSQL) == true)
			redirect("contractors.php", "CONTRACTOR_ADDED");

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sLogo != "")
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo);

			if ($sPicture != "")
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture);
		}
	}
?>