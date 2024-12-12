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

	$sName        = IO::strValue("txtName");
	$sDescription = IO::strValue("txtDescription");
	$sStatus      = IO::strValue("ddStatus");


	if ($sName == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_faq_categories WHERE name LIKE '$sName' AND id!='$iCategoryId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "FAQ_CATEGORY_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "UPDATE tbl_faq_categories SET name        = '$sName',
											   description = '$sDescription',
											   status      = '$sStatus'
		         WHERE id='$iCategoryId'";

		if ($objDb->execute($sSQL) == true)
		{
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sName) ?>";
		sFields[1] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[2] = "images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png";

		parent.updateCategoryRecord(<?= $iCategoryId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#CategoriesGridMsg", "success", "The selected Category has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>