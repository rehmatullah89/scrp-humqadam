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


	if ($_POST)
		@include("save-boq.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/boqs.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>BOQs Setup</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New BOQ Item</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete BOQ Item?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this BOQ Item?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete BOQ Items?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected BOQ Items?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_boqs') ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_boqs">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="55%">Title</th>
			      <th width="15%">Unit</th>
			      <th width="15%">Status</th>
			      <th width="10%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 100)
	{
		$sSQL = "SELECT id, title, unit, status FROM tbl_boqs ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId     = $objDb->getField($i, "id");
			$sTitle  = $objDb->getField($i, "title");
			$sUnit   = $objDb->getField($i, "unit");
			$sStatus = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sTitle ?></td>
		          <td><?= $sUnit ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}

			if ($sUserRights["Delete"] == "Y" && $iDocuments == 0)
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
		    <input type="hidden" name="DuplicateBoq" id="DuplicateBoq" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="txtTitle">BOQ Title</label>
		    <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="1000" size="37" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="ddUnit">Measurement Unit</label>

		    <div>
			  <select name="ddUnit" id="ddUnit">
			    <option value=""<?= ((IO::strValue('ddUnit') == '') ? ' selected' : '') ?>></option>
			    <option value="cft"<?= ((IO::strValue('ddUnit') == 'cft') ? ' selected' : '') ?>>cft</option>
			    <option value="sft"<?= ((IO::strValue('ddUnit') == 'sft') ? ' selected' : '') ?>>sft</option>
			    <option value="kg"<?= ((IO::strValue('ddUnit') == 'kg') ? ' selected' : '') ?>>kg</option>
			    <option value="rft"<?= ((IO::strValue('ddUnit') == 'rft') ? ' selected' : '') ?>>rft</option>
			    <option value="tons"<?= ((IO::strValue('ddUnit') == 'tons') ? ' selected' : '') ?>>tons</option>
			    <option value="job"<?= ((IO::strValue('ddUnit') == 'job') ? ' selected' : '') ?>>job</option>
			    <option value="no."<?= ((IO::strValue('ddUnit') == 'no.') ? ' selected' : '') ?>>no.</option>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
			    <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
			  </select>
		    </div>

		    <br />
		    <button id="BtnSave">Save Boq</button>
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
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>