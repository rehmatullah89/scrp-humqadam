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
		@include("save-sor.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/sors.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>SORs</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New SOR</a></li>
<?
	}
	
	$sSorSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSorSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete SOR?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this SOR Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete SORs?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected SOR Records?<br />
	      </div>
		  
		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_school_sors', $sSorSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="4%">#</th>
				  <th width="20%">School</th>
				  <th width="8%">Code</th>
				  <th width="11%">District</th>
				  <th width="15%">CDC</th>
				  <th width="8%">Date</th>
                                  <th width="10%">Sync Status</th>
                                  <th width="9%">Sor Status</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sDistrictsList = getList("tbl_districts", "id", "name");
		
		
		$sSorSQL = " AND FIND_IN_SET(ss.district_id, '{$_SESSION['AdminDistricts']}')";

		if ($_SESSION["AdminSchools"] != "")
			$sSorSQL .= " AND FIND_IN_SET(ss.school_id, '{$_SESSION['AdminSchools']}') ";

		
		$sSQL = "SELECT ss.id, ss.date, ss.created_at, ss.modified_at,ss.completed,
                        s.code, s.name, s.district_id,
                            (SELECT name FROM tbl_admins WHERE id=ss.admin_id) AS _CDC,
                            (SELECT name FROM tbl_admins WHERE id=ss.created_by) AS _CreatedBy,
                            (SELECT name FROM tbl_admins WHERE id=ss.modified_by) AS _ModifiedBy
		         FROM tbl_sors ss, tbl_schools s
		         WHERE ss.school_id=s.id $sSorSQL
				 ORDER BY ss.id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId            = $objDb->getField($i, "id");
			$sDate          = $objDb->getField($i, "date");
			$sSchool        = $objDb->getField($i, "name");
			$sCode          = $objDb->getField($i, "code");
			$iDistrict      = $objDb->getField($i, "district_id");
			$sCDC           = $objDb->getField($i, "_CDC");
                        $sCreatedAt     = $objDb->getField($i, "created_at");
			$sCreatedBy     = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt    = $objDb->getField($i, "modified_at");
			$sModifiedBy    = $objDb->getField($i, "_ModifiedBy");
                        $sStatus        = $objDb->getField($i, "status");
                        $sApp           = $objDb->getField($i, "app");
			$sCompleted     = $objDb->getField($i, "completed");


			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSchool ?></td>
                          <td><?= $sCode ?></td>
		          <td><?= $sDistrictsList[$iDistrict] ?></td>
                          <td><?= $sCDC ?></td>
                          <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
                          <td><?= (($sApp == 'Y' && $sStatus == 'I') ? "Syncing" : "Synced") ?></td>
		          <td><?= (($sCompleted == "Y") ? "Completed" : "In-Complete") ?></td>
		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
                            <img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
                            <img class="icnSor icon" id="<?= $iId ?>" src="images/icons/stats.gif" alt="Sor Details" title="Sor Details" rel="<?= $sSchool ?>" />
<?			}
?>
                                        
<?
                        if ($sUserRights["Delete"] == "Y")
			{
?>
                            <img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
                            <img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
                            <a href="<?= $sCurDir ?>/export-sor-form.php?SorId=<?= $iId ?>"><img class="icnPdf" src="images/icons/pdf.png" alt="Export PDF" title="Export PDF" /></a>                                        
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
                $iDistrictEngineersList = getList("tbl_admins", "id", "name", "type_id='6' "); //AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}'
                
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicateSor" id="DuplicateSor" value="0" />
			<div id="RecordMsg" class="hidden"></div>
                        
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr valign="top">
				<td width="600">
                                    <label for="txtCode">EMIS Code</label>
                                    <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="20" class="textbox" /></div>
                                    <div class="br10"></div>
                                    
                                    <label for="ddEngineer">District Engineer</label>
                                    <div>
                                    <select name="ddEngineer" id="ddEngineer">
					  <option value="">Select District Engineer</option>
<?
		foreach ($iDistrictEngineersList as $iDistrictEngId => $sDistrictEng)
		{
?>
					  <option value="<?= $iDistrictEngId ?>"<?= ((IO::intValue('ddEngineer') == $iDistrictEngId) ? ' selected' : '') ?>><?= $sDistrictEng ?></option>
<?
		}
?>
				    </select>
                                    </div>
                                    <div class="br10"></div>

                                    <label for="txtPrincipal">Head Teacher/ Principal</label>
                                    <div><input type="text" name="txtPrincipal" id="txtPrincipal" value="<?= IO::strValue("txtPrincipal") ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtCcsi">CCSI Representative</label>
                                    <div><input type="text" name="txtCcsi" id="txtCcsi" value="<?= IO::strValue("txtCcsi") ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtPtc">PTC/SC Representative</label>
                                    <div><input type="text" name="txtPtc" id="txtPtc" value="<?= IO::strValue("txtPtc") ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtContact">Contact No.</label>
                                    <div><input type="text" name="txtContact" id="txtContact" value="<?= IO::strValue("txtContact") ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtDate">Date</label>
                                    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= ((IO::strValue('txtDate') == "") ? date("Y-m-d") : IO::strValue('txtDate')) ?>" maxlength="10" size="10" class="textbox" readonly /></div>
                                    <div class="br10"></div>
                               </td>
                              <td>
                                  <h3>Add Participants</h3><br/>
                                  <table id="ParticipantsTable" border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align:left;">
                                       <thead>
                                        <tr>
                                          <th width="5%">#</th>
                                           <th width="50%">Name</th>
                                           <th width="45%">Designation</th>
                                        </tr>
                                      </thead>

                                      <tbody>   
                                          <tr id="first_p">
                                              <td>1</td><td><input type="text" class="textbox" name="pname_1" id="pname_1" value="<?=IO::strValue('pname_1')?>" style="width:95%;"/></td><td><input type="text" name="pdesignation_1" id="pdesignation_1" class="textbox" value="<?=IO::strValue('pdesignation_1')?>" style="width:95%;"/></td>
                                          </tr>
                                      </tbody>
                                  </table> 
                                  <br/>
                                  <input type="hidden" name="CountRows" id="CountRows" value="1">
                                  <a id="BtnAddRow" onclick="AddNewRow()">Add New Participant [+]</a>
                                  <a id="BtnDelRow" onclick="DeleteRow()">Remove Last Participant [-]</a>
                              </td> 
                          </tr>
                        </table>
                    <div class="br10"></div>
		    <button id="BtnSave">Save SOR</button>
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
<script type="text/javascript">
	<!--

        var i=2;
        function AddNewRow() {
            var table = document.getElementById("ParticipantsTable");
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            
            var pName = "pname_"+i;
            var pDesig = "pdesignation_"+i;
            cell1.innerHTML = i;
            cell2.innerHTML = "<input type='text' class='textbox' name="+pName+" id="+pName+" value='<?=IO::strValue('pName')?>'  style='width:95%;'/>";
            cell3.innerHTML = "<input type='text' class='textbox' name="+pDesig+" id="+pDesig+" value='<?=IO::strValue('pDesig')?>' style='width:95%;'/>";
            i++;
            document.getElementById("CountRows").value = i;
        }

        function DeleteRow() {
            var table = document.getElementById("ParticipantsTable");
            var rowCount = table.rows.length;
            table.deleteRow(rowCount-1);
            i--;
            document.getElementById("CountRows").value = i;
        }
        -->
</script>                
</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>