<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.3-tree.com/imc/                                                               **
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/backups.js"></script>
</head>

<body>

<div id="MainDiv">

<!--  Header Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/header.php");
?>
<!--  Header Section Ends Here  -->


<!--  Navigation Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/navigation.php");
?>
<!--  Navigation Section Ends Here  -->


<!--  Body Section Starts Here  -->
  <div id="Body">
<?
	@include("{$sAdminDir}includes/breadcrumb.php");
?>

    <div id="Contents">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
	  <div id="ConfirmDelete" title="Delete File?" class="hidden dlgConfirm">
		<span class="ui-icon ui-icon-trash"></span>
		Are you sure, you want to Delete this Backup File?<br />
	  </div>

	  <div id="ConfirmMultiDelete" title="Delete Files?" class="hidden dlgConfirm">
		<span class="ui-icon ui-icon-trash"></span>
		Are you sure, you want to Delete the selected Backup Files?<br />
	  </div>

	  <div id="ConfirmRestore" title="Restore Backup?" class="hidden dlgConfirm">
		<span class="ui-icon ui-icon-refresh"></span>
		Are you sure, you want to Restore the System from selected Backup File?<br />
	  </div>


      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Database</b></a></li>
<?
	if ($_SESSION["AdminId"] == 1)
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Website</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <div align="right"><button id="BtnDbBackup" onclick="document.location='<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/backup-database.php';">Take Database Backup</button></div>
	      <br />
<?
	}
?>

		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DbGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="40%">File Name</th>
			      <th width="20%">File Size (KB)</th>
			      <th width="20%">Date/Time</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
    $sFiles = @glob($sRootDir.BACKUPS_DIR."db/*.zip");
	$iCount = @count($sFiles);

	for ($i = 0; $i < $iCount; $i ++)
	{
		$fFileSize = @(filesize($sFiles[$i]) / 1024);
		$sDateTime = date("{$_SESSION["DateFormat"]} {$_SESSION["TimeFormat"]}", @filemtime($sFiles[$i]));
?>
		        <tr id="<?= @basename($sFiles[$i]) ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= @basename($sFiles[$i]) ?></td>
		          <td><?= formatNumber($fFileSize) ?></td>
		          <td><?= $sDateTime ?></td>

		          <td>
					<a href="management/download-backup.php?Type=Database&File=<?= @basename($sFiles[$i]) ?>"><img class="icnDownload" src="images/icons/download.gif" alt="Download" title="Download" /></a>
<?
		if ($sUserRights["Add"] == "Y" && $sUserRights["Edit"] == "Y" && $sUserRights["Delete"] == "Y")
		{
?>
					<img class="icnRestore" id="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/restore-database.php?File=<?= @basename($sFiles[$i]) ?>" src="images/icons/restore.gif" alt="Restore" title="Restore" />
<?
		}

		if ($sUserRights["Delete"] == "Y")
		{
?>
					<img class="icnDelete" id="<?= @basename($sFiles[$i]) ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
		}
?>
		          </td>
		        </tr>
<?
	}
?>
	          </tbody>
            </table>
		  </div>


		  <div id="SelectDbButtons"<?= (($iCount > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<div align="right">
			  <button id="BtnDbSelectAll">Select All</button>
			  <button id="BtnDbSelectNone">Clear Selection</button>
			</div>
		  </div>
		</div>


<?
	if ($_SESSION["AdminId"] == 1)
	{
?>
		<div id="tabs-2">
<?
		if ($sUserRights["Add"] == "Y")
		{
?>
	      <div align="right"><button id="BtnWebBackup" onclick="document.location='<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/backup-website.php';">Take Website Backup</button></div>
	      <br />
<?
		}
?>

		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="WebGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="40%">File Name</th>
			      <th width="20%">File Size (KB)</th>
			      <th width="20%">Date/Time</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
		$sFiles = @glob($sRootDir.BACKUPS_DIR."www/*.zip");
		$iCount = @count($sFiles);

		for ($i = 0; $i < $iCount; $i ++)
		{
			$fFileSize = @(filesize($sFiles[$i]) / 1024);
			$sDateTime = date("{$_SESSION["DateFormat"]} {$_SESSION["TimeFormat"]}", @filemtime($sFiles[$i]));
?>
		        <tr id="<?= @basename($sFiles[$i]) ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= @basename($sFiles[$i]) ?></td>
		          <td><?= formatNumber($fFileSize) ?></td>
		          <td><?= $sDateTime ?></td>

		          <td>
					<a href="management/download-backup.php?Type=Website&File=<?= @basename($sFiles[$i]) ?>"><img class="icnDownload" src="images/icons/download.gif" alt="Download" title="Download" /></a>
<?
			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= @basename($sFiles[$i]) ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
		          </td>
		        </tr>
<?
		}
?>
	          </tbody>
            </table>
		  </div>


		  <div id="SelectWebButtons"<?= (($iCount > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<div align="right">
			  <button id="BtnWebSelectAll">Select All</button>
			  <button id="BtnWebSelectNone">Clear Selection</button>
			</div>
		  </div>
	    </div>
<?
	}
?>
	  </div>

    </div>
  </div>
<!--  Body Section Ends Here  -->


<!--  Footer Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/footer.php");
?>
<!--  Footer Section Ends Here  -->

</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>