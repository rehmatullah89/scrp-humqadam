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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iSchoolId = IO::intValue("SchoolId");
	$iIndex    = IO::intValue("Index");


	$sSQL = "SELECT status, picture FROM tbl_schools WHERE id='$iSchoolId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) == 1)
	{
		$sStatus  = $objDb->getField(0, "status");
		$sPicture = $objDb->getField(0, "picture");


		$sSQL = "UPDATE tbl_schools SET picture='' WHERE id='$iSchoolId'";

		if ($objDb->execute($sSQL) == true)
		{
			@unlink($sRootDir.DISTRIICTS_IMG_DIR.$sPicture);
?>
	<script type="text/javascript">
	<!--
		var sOptions = "";

<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sOptions = (sOptions + '<img class="icnToggle" id="<?= $iSchoolId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sOptions = (sOptions + '<img class="icnEdit" id="<?= $iSchoolId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sOptions = (sOptions + '<img class="icnDelete" id="<?= $iSchoolId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sOptions = (sOptions + '<img class="icnView" id="<?= $iSchoolId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateOptions(<?= $iIndex ?>, sOptions);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected School Picture has been Deleted successfully.");
	-->
	</script>
<?
			exit( );
		}
	}


	redirect($_SERVER['HTTP_REFERER'], "DB_ERROR");


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>