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
	$sOldLogo    = IO::strValue("Logo");
	$sOldPicture = IO::strValue("Picture");
	$sLogo       = "";
	$sPicture    = "";
	$sLogoSql    = "";
	$sPictureSql = "";


	if ($sCompany == "" || $sCity == "" || $sTitle == "" || $sFirstName == "" || $sLastName == "" || $sPhone == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_contractors WHERE company LIKE '$sCompany' AND id!='$iContractorId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "CONTRACTOR_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['fileLogo']['name'] != "")
		{
			$sLogo = ($iContractorId."-".IO::getFileName($_FILES['fileLogo']['name']));

			if (@move_uploaded_file($_FILES['fileLogo']['tmp_name'], ($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo)))
				$sLogoSql = ", logo='$sLogo'";
		}

		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iContractorId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture)))
				$sPictureSql = ", picture='$sPicture'";
		}


		$sSQL = "UPDATE tbl_contractors SET company     = '$sCompany',
											address     = '$sAddress',
											city        = '$sCity',
											individual  = '$sIndividual',
											tax_filer   = '$sTaxFiler',
											invoicing   = '$iInvoicing',
											title       = '$sTitle',
											first_name  = '$sFirstName',
											middle_name = '$sMiddleName',
											last_name   = '$sLastName',
											phone       = '$sPhone',
											mobile      = '$sMobile',
											email       = '$sEmail',
											status      = '$sStatus'
										    $sLogoSql
										    $sPictureSql
		         WHERE id='$iContractorId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sCompany) ?>";
		sFields[1] = "<?= addslashes($sCity) ?>";
		sFields[2] = "<?= addslashes($sPhone) ?>";
		sFields[3] = "<?= addslashes($sMobile) ?>";
		sFields[4] = "<?= addslashes($sEmail) ?>";
		sFields[5] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[6] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnToggle" id="<?= $iContractorId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[6] = (sFields[6] + '<img class="icnEdit" id="<?= $iContractorId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
		sFields[6] = (sFields[6] + '<img class="icon icnBoqs" id="<?= $iContractorId ?>" src="images/icons/boqs.png" alt="BOQs Setup" title="BOQs Setup" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnDelete" id="<?= $iContractorId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldLogo != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sOldLogo))
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnLogo" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."logos/".$sOldLogo) ?>" src="images/icons/logo.png" alt="Logo" title="Logo" /> ');
<?
			}

			else if ($sLogo != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo))
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnLogo" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."logos/".$sLogo) ?>" src="images/icons/logo.png" alt="Logo" title="Logo" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sOldPicture))
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnPicture" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."persons/".$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture))
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnPicture" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."persons/".$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}
?>
		sFields[6] = (sFields[6] + '<img class="icnView" id="<?= $iContractorId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iContractorId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Contractor Account has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sLogo != "" && $sOldLogo != $sLogo)
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo);

			if ($sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture);
		}
	}
?>