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


	$sSefMode = getDbValue("sef_mode", "tbl_settings", "id='1'");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/page-contents.js"></script>
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

	  <div id="GridMsg" class="hidden"></div>

	  <div class="dataGrid ex_highlight_row">
		<input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
		  <thead>
			<tr>
			  <th width="5%">#</th>
			  <th width="42%">Page Title</th>
			  <th width="30%"><?= (($sSefMode == "Y") ? "SEF" : "PHP") ?> URL</th>
			  <th width="13%">Status</th>
			  <th width="10%">Options</th>
			</tr>
		  </thead>

		  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_web_pages ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sTitle  = $objDb->getField($i, "title");
		$sSefUrl = $objDb->getField($i, "sef_url");
		$sPhpUrl = $objDb->getField($i, "php_url");
		$sStatus = $objDb->getField($i, "status");
?>
			<tr>
			  <td><?= ($i + 1) ?></td>
			  <td><?= $sTitle ?></td>
			  <td><?= (($sSefMode == "Y") ? $sSefUrl : (($sPhpUrl == "" && $iId != 1) ? "index.php?PageId={$iId}" : $sPhpUrl)) ?></td>
			  <td><?= (($sStatus == "P") ? "Published" : "Draft") ?></td>

			  <td>
<?
		if ($sUserRights["Edit"] == "Y")
		{
?>
				<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}
?>
			    <img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
			  </td>
			</tr>
<?
	}
?>
		  </tbody>
		</table>
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