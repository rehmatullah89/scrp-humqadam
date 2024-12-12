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

	$sQuestion = IO::strValue("txtQuestion");
	$sAnswer   = IO::strValue("txtAnswer");
	$iCategory = IO::intValue("ddCategory");
	$sStatus   = IO::strValue("ddStatus");


	if ($sQuestion == "" || $sAnswer == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_faqs WHERE category_id='$iCategory' AND question LIKE '$sQuestion' AND id!='$iFaqId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "FAQ_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_faqs SET category_id = '$iCategory',
		                             question    = '$sQuestion',
		                             answer      = '$sAnswer',
		                             status      = '$sStatus'
		         WHERE id='$iFaqId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= str_replace("\r\n", "<br />", addslashes($sQuestion)) ?>";
		sFields[1] = "<?= (($iCategory > 0) ? addslashes(getDbValue("name", "tbl_faq_categories", "id='$iCategory'")) : '') ?>";
		sFields[2] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[3] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateFaqRecord(<?= $iFaqId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#FaqMsg", "success", "The selected FAQ has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>