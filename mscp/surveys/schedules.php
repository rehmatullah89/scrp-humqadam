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
		@include("save-schedule.php");
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
      <input type="hidden" id="OpenTab" value="<?= (($_POST && $bError == true) ? 1 : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs" rel="<?= $_SERVER['REQUEST_URI'] ?>">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Surveys Schedules</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Schedule</a></li>
<?
	}
	
	
	$sSchedulesSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchedulesSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";	
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Schedule?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Schedule Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Schedules?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Schedule Records?<br />
	      </div>
		  
		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_survey_schedules', $sSchedulesSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
				  <th width="20%">School</th>
				  <th width="10%">Code</th>
				  <th width="20%">District</th>
				  <th width="15%">Enumerator</th>
				  <th width="10%">Date</th>
				  <th width="8%">Status</th>
			      <th width="12%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sDistrictsList = getList("tbl_districts", "id", "name");
		
		
		$sSchedulesSQL = " AND FIND_IN_SET(ss.district_id, '{$_SESSION['AdminDistricts']}')";

		if ($_SESSION["AdminSchools"] != "")
			$sSchedulesSQL .= " AND FIND_IN_SET(ss.school_id, '{$_SESSION['AdminSchools']}') ";

		
		$sSQL = "SELECT ss.id, ss.date, ss.status, ss.created_at, ss.modified_at,
                        s.code, s.name, s.district_id,
						(SELECT name FROM tbl_admins WHERE id=ss.admin_id) AS _Enumerator,
						(SELECT name FROM tbl_admins WHERE id=ss.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=ss.modified_by) AS _ModifiedBy
		         FROM tbl_survey_schedules ss, tbl_schools s
		         WHERE ss.school_id=s.id $sSchedulesSQL
				 ORDER BY ss.id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId         = $objDb->getField($i, "id");
			$sEnumerator = $objDb->getField($i, "_Enumerator");
			$sDate       = $objDb->getField($i, "date");
			$sStatus     = $objDb->getField($i, "status");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");
			$sCreatedAt  = $objDb->getField($i, "created_at");
			$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt = $objDb->getField($i, "modified_at");
			$sModifiedBy = $objDb->getField($i, "_ModifiedBy");


			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSchool ?></td>
                  <td><?= $sCode ?></td>
		          <td><?= $sDistrictsList[$iDistrict] ?></td>
                  <td><?= $sEnumerator ?></td>
                  <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
		          <td><?= (($sStatus == "C") ? "Completed" : "Pending") ?></td>

		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
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
		$iSchool          = getDbValue("id", "tbl_schools", ("code='".IO::strValue("txtCode")."'"));
        $sEnumeratorsList = getList("tbl_admins", "id", "name", "status='A' AND type_id='12'");
                
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicateSchedule" id="DuplicateSchedule" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="ddEnumerator">Enumerator</label>
		    
			<div>
			  <select name="ddEnumerator" id="ddEnumerator">
			    <option value=""></option>
<?
		foreach ($sEnumeratorsList as $iEnumerator => $sEnumerator)
		{
?>
			    <option value="<?= $iEnumerator ?>"<?= ((IO::strValue("ddEnumerator") == $iEnumerator) ? ' selected' : '') ?>><?= $sEnumerator ?></option>
<?
		}
?>    
			  </select>
            </div>

		    <div class="br10"></div>
			
		    <label for="txtCode">EMIS Code</label>
		    <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="20" class="textbox" /></div>

		    <div class="br10"></div>
			
		    <label for="txtDate">Date</label>
		    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= ((IO::strValue('txtDate') == "") ? date("Y-m-d") : IO::strValue('txtDate')) ?>" maxlength="10" size="10" class="textbox" readonly /></div>
                    
		    <br />
		    <button id="BtnSave">Save Schedule</button>
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