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
	$sUnit   = IO::strValue("ddUnit");
	$sStatus = IO::strValue("ddStatus");



	if ($sTitle == "" || $sUnit == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_boqs WHERE title LIKE '$sTitle' AND id!='$iBoqId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "BOQ_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_boqs SET title  = '$sTitle',
									 unit   = '$sUnit',
								     status = '$sStatus'
		         WHERE id='$iBoqId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sTitle) ?>";
		sFields[1] = "<?= addslashes($sUnit) ?>";
		sFields[2] = "<?= (($sStatus == "A") ? "Active" : "In-Active") ?>";
		sFields[3] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnToggle" id="<?= $iBoqId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[3] = (sFields[3] + '<img class="icnEdit" id="<?= $iBoqId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[3] = (sFields[3] + '<img class="icnDelete" id="<?= $iBoqId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>

		parent.updateRecord(<?= $iBoqId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected BOQ has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>