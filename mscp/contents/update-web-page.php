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

	$sTitle      = IO::strValue("txtTitle");
	$sSefUrl     = IO::strValue("txtSefUrl");
	$sPlacements = @implode(",", IO::getArray("cbPlacements"));
	$sStatus     = IO::strValue("ddStatus");


	if ($sTitle == "" || ($iPageId != 1 && $sSefUrl == "") || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_web_pages WHERE sef_url LIKE '$sSefUrl' AND id!='$iPageId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "WEB_PAGE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_web_pages SET sef_url='$sSefUrl', title='$sTitle', placements='$sPlacements', status='$sStatus' WHERE id='$iPageId'";

		if ($objDb->execute($sSQL) == true)
		{
			$sPlacements = str_replace("H", "Header", $sPlacements);
			$sPlacements = str_replace("F", "Footer", $sPlacements);
			$sPlacements = str_replace("L", "Left Panel", $sPlacements);
			$sPlacements = str_replace("R", "Right Panel", $sPlacements);
			$sPlacements = str_replace(",", ", ", $sPlacements);
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sTitle) ?>";
		sFields[1] = "<?= $sSefUrl ?>";
		sFields[2] = "<?= $sPlacements ?>";
		sFields[3] = "<?= (($sStatus == 'P') ? 'Published' : 'Draft') ?>";
		sFields[4] = "images/icons/<?= (($sStatus == 'P') ? 'success' : 'error') ?>.png";

		parent.updateRecord(<?= $iPageId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Web Page has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>