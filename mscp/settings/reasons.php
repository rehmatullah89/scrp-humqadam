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

	if ($_POST)
		@include("save-reason.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/reasons.js"></script>
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
      <input type="hidden" id="OpenTab" value="<?= (($_POST && $bError == true) ? 1 : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1">Failure Reasons</a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">New Reason</a></li>
<?
	}
?>
	    </ul>


		<div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Reason?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Reason?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Reasons?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Reasons?<br />
	      </div>

		  <div id="TblReasons" class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_failure_reasons">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="65%">Reason</th>
			      <th width="15%">Status</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_failure_reasons ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sReason = $objDb->getField($i, "reason");
		$sStatus = $objDb->getField($i, "status");


		$iInspections = getDbValue("COUNT(1)", "tbl_inspections", "failure_reason_id='$iId'");
?>
		        <tr id="<?= $iId ?>" valign="top">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sReason ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
		if ($sUserRights["Edit"] == "Y" && $iId != 5)
		{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}

		if ($sUserRights["Delete"] == "Y" && $iInspections == 0 && $iId != 5)
		{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
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
<?
	if ($iCount > 5 && $sUserRights["Delete"] == "Y")
	{
?>

	      <div class="br10"></div>

	      <div align="right" id="SelectButtons">
		    <button id="BtnSelectAll">Select All</button>
		    <button id="BtnSelectNone">Clear Selection</button>
	      </div>
<?
	}
?>
		</div>

<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="DuplicateReason" id="DuplicateReason" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="txtReason">Reason</label>
		    <div><input type="text" name="txtReason" id="txtReason" value="<?= IO::strValue('txtReason', true) ?>" maxlength="200" size="40" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
			    <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
			  </select>
		    </div>

		    <br />
		    <button id="BtnSave">Save Reason</button>
		    <button id="BtnReset">Clear</button>
		  </form>
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