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

	$sTitle       = IO::strValue("txtTitle");
	$sDescription = IO::strValue("txtDescription");
	$sKeywords    = IO::strValue("txtKeywords");

	$sTitle       = strip_tags($sTitle);
	$sDescription = strip_tags($sDescription);
	$sKeywords    = strip_tags($sKeywords);


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE {$sTable} SET title_tag='$sTitle', description_tag='$sDescription', keywords_tag='$sKeywords' WHERE id='$iId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		parent.update<?= $sFunction ?>Title(<?= $iIndex ?>, "<?= addslashes($sTitle) ?>");
		parent.$.colorbox.close( );
		parent.showMessage("#<?= $sGrid ?>GridMsg", "success", "The selected <?= $sSection ?> Meta Tags have been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>