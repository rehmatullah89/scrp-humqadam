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

	$sName       = IO::strValue("txtName");
	$sMobile     = IO::strValue("txtMobile");
	$sEmail      = IO::strValue("txtEmail");
	$sPassword   = IO::strValue("txtPassword");
	$sProvinces  = @implode(",", IO::getArray("cbProvinces", "int"));
	$sDistricts  = @implode(",", IO::getArray("cbDistricts", "int"));
	$sSchools    = IO::strValue("txtSchools");
	$iType       = IO::intValue("ddType");
	$iRecords    = IO::intValue("ddRecords");
	$sTheme      = IO::strValue("ddTheme");
	$sStatus     = IO::strValue("ddStatus");
	$sOldPicture = IO::strValue("Picture");
	$sPicture    = "";
	$sPictureSql = "";


	if ($sDistricts == "" && $sSchools != "")
		$sDistricts = getDbValue("GROUP_CONCAT(DISTINCT(district_id) SEPARATOR ',')", "tbl_schools", "FIND_IN_SET(id, '$sSchools')");


	if ($sName == "" || $sMobile == "" || $sEmail == "" || $iType == 0 || $iRecords == 0 || $sTheme == "" || $sStatus == "" || $sProvinces == "" || $sDistricts == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_admins WHERE email='$sEmail' AND id!='$iUserId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "USER_EMAIL_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		if ($_FILES['filePicture']['name'] != "")
		{
			$sPicture = ($iUserId."-".IO::getFileName($_FILES['filePicture']['name']));

			if (@move_uploaded_file($_FILES['filePicture']['tmp_name'], ($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture)))
			{
				createImage(($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture), ($sRootDir.ADMINS_IMG_DIR.'thumbs/'.$sPicture), ADMINS_IMG_WIDTH, ADMINS_IMG_HEIGHT);

				$sPictureSql = ", picture='$sPicture'";
			}
		}


		$objDb->execute("BEGIN");

		if ($sPassword != "")
			$sPasswordSql = ", password=PASSWORD('$sPassword') ";


		$sSQL = "UPDATE tbl_admins SET type_id   = '$iType',
		                               name      = '$sName',
		                               email     = '$sEmail',
		                               mobile    = '$sMobile',
		                               provinces = '$sProvinces',
		                               districts = '$sDistricts',
		                               schools   = '$sSchools',
		                               records   = '$iRecords',
		                               theme     = '$sTheme',
		                               status    = '$sStatus'
									   $sPictureSql
									   $sPasswordSql
		         WHERE id='$iUserId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$sSQL = "DELETE FROM tbl_admin_rights WHERE admin_id='$iUserId'";
			$bFlag = $objDb->execute($sSQL);
		}

		if ($bFlag == true)
		{
			$iPageCount = IO::intValue("PageCount");

			for ($i = 0; $i < $iPageCount; $i ++)
			{
				$iPageId = IO::intValue("PageId{$i}");
				$sView   = IO::strValue("cbView{$i}");
				$sAdd    = IO::strValue("cbAdd{$i}");
				$sEdit   = IO::strValue("cbEdit{$i}");
				$sDelete = IO::strValue("cbDelete{$i}");


				$sSQL = "INSERT INTO tbl_admin_rights SET admin_id = '$iUserId',
														  page_id  = '$iPageId',
														  `view`   = '$sView',
														  `add`    = '$sAdd',
														  `edit`   = '$sEdit',
														  `delete` = '$sDelete'";
				$bFlag = $objDb->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");


			if ($sOldPicture != "" && $sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.ADMINS_IMG_DIR.'thumbs/'.$sOldPicture);
				@unlink($sRootDir.ADMINS_IMG_DIR.'originals/'.$sOldPicture);
			}

			$sType = getDbValue("title", "tbl_admin_types", "id='$iType'");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= addslashes($sName) ?>";
		sFields[1] = "<?= $sEmail ?>";
		sFields[2] = "<?= $sMobile ?>";
		sFields[3] = "<?= addslashes($sType) ?>";
		sFields[4] = "<?= (($sStatus == 'A') ? 'Active' : 'Disabled') ?>";
		sFields[5] = "";
<?
			if ($sUserRights["Edit"] == "Y" && ($iType > 1 || $_SESSION["AdminLevel"] == 1))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnToggle" id="<?= $iUserId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[5] = (sFields[5] + '<img class="icnEdit" id="<?= $iUserId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y" && ($iType > 1 || $_SESSION["AdminLevel"] == 1))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnDelete" id="<?= $iUserId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}

			if ($sOldPicture != "" && @file_exists($sRootDir.ADMINS_IMG_DIR.'originals/'.$sOldPicture))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnPicture" id="<?= (SITE_URL.ADMINS_IMG_DIR.'originals/'.$sOldPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
		sFields[5] = (sFields[5] + '<img class="icnThumb" id="<?= $iUserId ?>" rel="Admin" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" /> ');
<?
			}

			else if ($sPicture != "" && @file_exists($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture))
			{
?>
		sFields[5] = (sFields[5] + '<img class="icnPicture" id="<?= (SITE_URL.ADMINS_IMG_DIR.'originals/'.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" /> ');
		sFields[5] = (sFields[5] + '<img class="icnThumb" id="<?= $iUserId ?>" rel="Admin" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" /> ');
<?
			}
?>
		sFields[5] = (sFields[5] + '<img class="icnView" id="<?= $iUserId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');


		parent.updateUser(<?= $iUserId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#UserGridMsg", "success", "The selected Admin User Account has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";


			if ($sPicture != "" && $sOldPicture != $sPicture)
			{
				@unlink($sRootDir.ADMINS_IMG_DIR.'thumbs/'.$sPicture);
				@unlink($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture);
			}
		}
	}
?>