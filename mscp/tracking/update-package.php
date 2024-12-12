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
	$iLots    = IO::intValue("txtLots");
        $sDetails = IO::strValue("txtDetails");
	$sStatus  = IO::strValue("ddStatus");
	$sSchools = IO::strValue("txtSchools");
        


	if ($sTitle == "" || $sSchools == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_packages WHERE title LIKE '$sTitle' AND id!='$iPackageId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "PACKAGE_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_packages SET title   = '$sTitle',
                                                                                 lots    = '$iLots',   
										 details = '$sDetails',
										 schools = '$sSchools',
										 status  = '$sStatus'
		         WHERE id='$iPackageId'";

		if ($objDb->execute($sSQL) == true)
		{
                        $iClasses = getDbValue("SUM(class_rooms)", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");
			$iToilets = getDbValue("SUM(student_toilets)", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= str_replace("\r\n", "<br />", addslashes($sTitle)) ?>";
		sFields[1] = "<?= addslashes($iLots) ?>";
        sFields[2] = "<?= formatNumber($iClasses, false) ?>";
		sFields[3] = "<?= formatNumber($iToilets, false) ?>";
		sFields[4] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[5] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateRecord(<?= $iPackageId ?>, <?= $iIndex ?>,<?= $iLots ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Package has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>