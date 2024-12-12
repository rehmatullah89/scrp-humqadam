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
	$iProvince    = IO::intValue("ddProvince");
	$sSefUrl      = IO::strValue("Url");
	$sCoordinates = IO::strValue("txtCoordinates");
	$sDescription = IO::strValue("txtDescription");
	$sLatitude    = IO::strValue("txtLatitude");
	$sLongitude   = IO::strValue("txtLongitude");
	$sStatus      = IO::strValue("ddStatus");
	$sOldPicture  = IO::strValue("Picture");
	$sPicture     = "";
	$sPictureSql  = "";


	if ($sName == "" || $iProvince == 0 || $sSefUrl == "" || $sLatitude == "" || $sLongitude == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_districts WHERE sef_url LIKE '$sSefUrl' AND id!='$iDistrictId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "DISTRICT_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iDistrictId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.DISTRICTS_IMG_DIR.$sPicture)))
				$sPictureSql = ", picture='$sPicture'";
		}



		$sSQL = "UPDATE tbl_districts SET province_id = '$iProvince',
		                                  name        = '$sName',
		                                  sef_url     = '$sSefUrl',
										  latitude    = '$sLatitude',
										  longitude   = '$sLongitude',
		                                  coordinates = '$sCoordinates',
		                                  description = '$sDescription',
		                                  status      = '$sStatus'
		                                  $sPictureSql
		         WHERE id='$iDistrictId'";

		if ($objDb->execute($sSQL) == true)
		{
			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.DISTRICTS_IMG_DIR.$sOldPicture);


			$sProvince = getDbValue("name", "tbl_provinces", "id='$iProvince'");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sName) ?>";
		sFields[1] = "<?= $sSefUrl ?>";
		sFields[2] = "<?= addslashes($sProvince) ?>";
		sFields[3] = "<?= (($sStatus == 'A') ? 'Active' : 'In-Active') ?>";
		sFields[4] = "";
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnToggle" id="<?= $iDistrictId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[4] = (sFields[4] + '<img class="icnEdit" id="<?= $iDistrictId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnDelete" id="<?= $iDistrictId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.DISTRICTS_IMG_DIR.$sOldPicture))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnPicture" id="<?= (SITE_URL.DISTRICTS_IMG_DIR.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.DISTRICTS_IMG_DIR.$sPicture))
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnPicture" id="<?= (SITE_URL.DISTRICTS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
<?
			}
?>
		sFields[4] = (sFields[4] + '<img class="icnView" id="<?= $iDistrictId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iDistrictId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected District has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$_SESSION["Flag"] = "DB_ERROR";

			if ($sPicture != "" && $sOldPicture != $sPicture)
				@unlink($sRootDir.DISTRICTS_IMG_DIR.$sPicture);
		}
	}
?>