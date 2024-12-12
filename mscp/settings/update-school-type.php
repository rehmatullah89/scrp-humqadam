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


	if ($sType == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_school_types WHERE `type` LIKE '$sType' AND id!='$iTypeId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SCHOOL_TYPE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_school_types SET `type` = '$sType',
										     status = '$sStatus'
		         WHERE id='$iTypeId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sType) ?>";
		sFields[1] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[2] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateRecord(<?= $iTypeId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected School Type has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>