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
		@include("save-package.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/packages.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Packages</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Package</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Package?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Package?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Packages?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Packages?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="48%">Title</th>
                              <th width="6%">Lots</th>
			      <th width="7%">Schools</th>
			      <th width="7%">Classes</th>
			      <th width="6%">Toilets</th>
			      <th width="7%">Status</th>
			      <th width="14%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT id, title, schools, status, lots,
	                (SELECT SUM(class_rooms) FROM tbl_schools WHERE FIND_IN_SET(id, tbl_packages.schools)) AS _Classes,
	                (SELECT SUM(student_toilets) FROM tbl_schools WHERE FIND_IN_SET(id, tbl_packages.schools)) AS _Toilets
	         FROM tbl_packages
	         ORDER BY id DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId      = $objDb->getField($i, "id");
		$sTitle   = $objDb->getField($i, "title");
                $iLots    = $objDb->getField($i, "lots");
		$sSchools = $objDb->getField($i, "schools");
		$iClasses = $objDb->getField($i, "_Classes");
		$iToilets = $objDb->getField($i, "_Toilets");
		$sStatus  = $objDb->getField($i, "status");


		$iSchools = @explode(",", $sSchools);
?>
		        <tr id="<?= $iId ?>" valign="top">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sTitle ?></td>
                          <td><?= $iLots ?></td>
		          <td><?= count($iSchools) ?></td>
		          <td><?= formatNumber($iClasses, false) ?></td>
		          <td><?= formatNumber($iToilets, false) ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
		if ($sUserRights["Edit"] == "Y")
		{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
                    <img title="Lots" alt="Lots" src="images/icons/stats.gif" id="<?= $iId ?>" lots="<?=$iLots?>" class="icon icnLots" />
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
?>
	          </tbody>
            </table>
		  </div>

		  <div id="SelectButtons"<?= (($iCount > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
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
		    <input type="hidden" name="DuplicatePackage" id="DuplicatePackage" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
				<td width="500">
				  <label for="txtTitle">Title</label>
				  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="200" size="44" class="textbox" /></div>

				  <div class="br10"></div>
                                  
                                  <label for="txtLots">No. of Lots</label>
				  <div><input type="text" name="txtLots" id="txtLots" value="<?= IO::strValue('txtLots', true) ?>" maxlength="200" size="8" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtDetails">Details <span>(Optional)</span></label>
				  <div><textarea name="txtDetails" id="txtDetails" rows="4" cols="42"><?= IO::strValue('txtDetails') ?></textarea></div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save Package</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <h4 style="width:400px;">Schools</h4>
				  <input type="text" name="txtSchools" id="txtSchools" value="" />
		  		  <input type="hidden" id="Schools" value="<?= @json_encode(IO::strValue("txtSchools")) ?>" />
				</td>
			  </tr>
			</table>
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