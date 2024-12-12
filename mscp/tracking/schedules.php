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
	$objDb2      = new Database( );
	$objDb3      = new Database( );


	if ($_POST)
		@include(IO::strValue("Action"));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/schedules.js"></script>
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
      <input type="hidden" id="OpenTab" value="<?= (($_POST && $bError == true) ? ((IO::strValue("Action") == "copy-schedule.php") ? 2 : 1) : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Construction Schedules</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Schedule</a></li>
<?
	}

	if ($sUserRights["Add"] == "Y" && $sUserRights["Edit"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-3">Copy Schedule</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
		  <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Schedule?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Schedule?<br />
	      </div>
		  
		  <div id="ConfirmMultiDelete" title="Delete Schedules?" class="hidden dlgConfirm">
			<span class="ui-icon ui-icon-trash"></span>
			Are you sure, you want to Delete the selected Schedules?<br />
		  </div>
  
		  <div align="right">
		    <button id="BtnExportNonSchedules" title="Export Non-Scheduled Schools" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-no-schedule-schools.php">Export Non-Scheduled Schools</button>
		    <button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-schedules.php">Export Detailed Schedules</button>
		    <button id="BtnExportMileStone" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-milestone-schedules.php">Export MileStone Schedules</button>
		  </div>
		  
<?
	$sSchoolsSQL = "FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";
?>
		  <br />
		  <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_contract_schedules cs, tbl_schools s', "cs.school_id=s.id AND $sSchoolsSQL") ?>" />
		  <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
				<tr>
				  <th width="6%">#</th>
				  <th width="10%">Code</th>
				  <th width="28%">School</th>
				  <th width="20%">Contract</th>
				  <th width="12%">Start Date</th>
				  <th width="12%">End Date</th>
				  <th width="12%">Options</th>
				</tr>
			  </thead>

			  <tbody>
<?
	$sContractsList = getList("tbl_contracts", "id", "title");


	if ($iTotalRecords <= 50)
	{
		$sSQL = "SELECT cs.id, cs.contract_id, cs.start_date, cs.end_date,
		                s.code, s.name
		         FROM tbl_contract_schedules cs, tbl_schools s
		         WHERE cs.school_id=s.id AND $sSchoolsSQL
		         ORDER BY cs.id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId        = $objDb->getField($i, "id");
			$iContract  = $objDb->getField($i, "contract_id");
			$sCode      = $objDb->getField($i, "code");
			$sSchool    = $objDb->getField($i, "name");
			$sStartDate = $objDb->getField($i, "start_date");
			$sEndDate   = $objDb->getField($i, "end_date");
?>
				<tr id="<?= $iId ?>">
				  <td class="position"><?= ($i + 1) ?></td>
				  <td><?= $sCode ?></td>
				  <td><?= $sSchool ?></td>
				  <td><?= $sContractsList[$iContract] ?></td>
		          <td><?= formatDate($sStartDate, $_SESSION["DateFormat"]) ?></td>
		          <td><?= formatDate($sEndDate, $_SESSION["DateFormat"]) ?></td>

				  <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
					<img class="icon icnEditDetails" id="<?= $iId ?>" src="images/icons/stats.gif" alt="Edit Details" title="Edit Details" />
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
					<img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
				  </td>
				</tr>
<?
		}
	}
?>
			  </tbody>
			</table>
		  </div>

	      <div id="SelectButtons"<?= (($iTotalRecords > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
	        <div class="br10"></div>

	        <div align="right">
		      <button id="BtnSelectAll">Select All</button>
		      <button id="BtnSelectNone">Clear Selection</button>
		    </div>
	      </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="Action" value="save-schedule.php" />
		    <input type="hidden" name="DuplicateSchedule" id="DuplicateSchedule" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="ddContract">Contract</label>

		    <div>
			  <select name="ddContract" id="ddContract">
			    <option value=""></option>
<?
		foreach ($sContractsList as $iContract => $sContract)
		{
?>
			    <option value="<?= $iContract ?>"<?= ((IO::intValue('ddContract') == $iContract) ? ' selected' : '') ?>><?= $sContract ?></option>
<?
		}
?>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="ddSchool">School</label>

		    <div>
			  <select name="ddSchool" id="ddSchool" style="min-width:200px;">
			    <option value=""></option>
<?
		$sSchools     = getDbValue("schools", "tbl_contracts", ("id='".IO::intValue("ddContract")."'"));
		$sSchoolsList = getList("tbl_schools", "id", "name", "FIND_IN_SET(id, '$sSchools')");

		foreach ($sSchoolsList as $iSchool => $sSchool)
		{
?>
			    <option value="<?= $iSchool ?>"<?= ((IO::intValue('ddSchool') == $iSchool) ? ' selected' : '') ?>><?= $sSchool ?></option>
<?
		}
?>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="txtStartDate">Start Date</label>
		    <div class="date"><input type="text" name="txtStartDate" id="txtStartDate" value="<?= IO::strValue('txtStartDate') ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		    <div class="br10"></div>

		    <label for="txtEndDate">End Date</label>
		    <div class="date"><input type="text" name="txtEndDate" id="txtEndDate" value="<?= IO::strValue('txtEndDate') ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		    <br />
		    <button id="BtnSave">Save Schedule</button>
		    <button id="BtnReset">Clear</button>
		  </form>
	    </div>
<?
	}




	if ($sUserRights["Add"] == "Y" && $sUserRights["Edit"] == "Y")
	{
?>
		<div id="tabs-3">
		  <form name="frmCopy" id="frmCopy" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" name="Action" value="copy-schedule.php" />
			<div id="CopyMsg" class="hidden"></div>

		    <label for="ddContract">Contract</label>

		    <div>
			  <select name="ddContract" id="ddContract">
			    <option value=""></option>
<?
		foreach ($sContractsList as $iContract => $sContract)
		{
?>
			    <option value="<?= $iContract ?>"<?= ((IO::intValue('ddContract') == $iContract) ? ' selected' : '') ?>><?= $sContract ?></option>
<?
		}
?>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="ddSchool">From School</label>

		    <div>
			  <select name="ddSchool" id="ddSchool" style="min-width:400px;">
			    <option value=""></option>
<?
		$iContract    = IO::intValue("ddContract");
		$sSchools     = getDbValue("schools", "tbl_contracts", "id='$iContract'");
		$sSchoolsList = getList("tbl_schools", "id", "CONCAT(code, ' - ', name)", "FIND_IN_SET(id, '$sSchools') AND id IN (SELECT school_id FROM tbl_contract_schedules WHERE contract_id='$iContract')");

		foreach ($sSchoolsList as $iSchool => $sSchool)
		{
?>
			    <option value="<?= $iSchool ?>"<?= ((IO::intValue('ddSchool') == $iSchool) ? ' selected' : '') ?>><?= $sSchool ?></option>
<?
		}
?>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="">Copy To <span>(<a href="#" rel="Check|school">Check All</a> | <a href="#" rel="Clear|school">Clear</a>)</span></label>

		    <div id="Schools" class="multiSelect" style="width:400px; height:200px;">
			  <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
		$sSQL = "SELECT id, CONCAT(code, ' - ', name) AS _Name
		         FROM tbl_schools
		         WHERE status='A' AND dropped!='Y' AND FIND_IN_SET(id, '$sSchools')
		               AND id NOT IN (SELECT school_id FROM tbl_contract_schedules WHERE contract_id='$iContract')
		         ORDER BY _Name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchool = $objDb->getField($i, "id");
			$sSchool = $objDb->getField($i, "_Name");
?>
			    <tr valign="top">
				  <td width="25"><input type="checkbox" class="school" name="cbSchools[]" id="cbSchool<?= $iSchool ?>" value="<?= $iSchool ?>" <?= ((@in_array($iSchool, IO::getArray('cbSchools'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbSchool<?= $iSchool ?>"><?= $sSchool ?></label></td>
			    </tr>
<?
		}
?>
			  </table>
		    </div>

		    <br />
		    <button id="BtnCopy">Copy Schedule</button>
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
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>