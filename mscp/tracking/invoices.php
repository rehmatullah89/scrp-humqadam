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
		@include("save-invoice.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/invoices.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Invoices</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Invoice</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
		  <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Invoice?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Invoice Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Invoices?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Invoice Records?<br />
	      </div>

		  <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_invoices') ?>" />
		  <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
				<tr>
				  <th width="5%">#</th>
				  <th width="10%">Invoice #</th>
				  <th width="18%">Title</th>
				  <th width="15%">School</th>
				  <th width="14%">Contract</th>
				  <th width="10%">Date</th>
				  <th width="8%">Amount</th>
				  <th width="7%">Status</th>
				  <th width="13%">Options</th>
				</tr>
			  </thead>

			  <tbody>
<?
	$sContractsList = getList("tbl_contracts", "id", "title");


	if ($iTotalRecords <= 100)
	{
		$sSQL = "SELECT id, contract_id, date, invoice_no, title, amount, status,
		                (SELECT name FROM tbl_schools WHERE id=tbl_invoices.school_id) AS _School
		         FROM tbl_invoices
		         ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId        = $objDb->getField($i, "id");
			$iContract  = $objDb->getField($i, "contract_id");
			$sSchool    = $objDb->getField($i, "_School");
			$sDate      = $objDb->getField($i, "date");
			$sInvoiceNo = $objDb->getField($i, "invoice_no");
			$sTitle     = $objDb->getField($i, "title");
			$sAmount    = $objDb->getField($i, "amount");
			$sStatus    = $objDb->getField($i, "status");
			$sReleased  = $objDb->getField($i, "released");
?>
				<tr id="<?= $iId ?>">
				  <td class="position"><?= ($i + 1) ?></td>
				  <td><?= $sInvoiceNo ?></td>
				  <td><?= $sTitle ?></td>
				  <td><?= $sSchool ?></td>
				  <td><?= $sContractsList[$iContract] ?></td>
		          <td><?= formatDate($sDate, $_SESSION["DateFormat"]) ?></td>
		          <td><?= formatNumber($sAmount, false) ?></td>
		          <td><?= (($sStatus == "P") ? "Paid" : "Un-Paid") ?></td>

				  <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
					<img class="icon icnReleased" id="<?= $iId ?>" src="images/icons/<?= (($sReleased == 'Y') ? 'green' : 'blue') ?>.png" alt="Toggle Released Status" title="Toggle Released Status" />
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
					<a href="<?= $sCurDir ?>/export-invoice.php?Id=<?= $iId ?>"><img class="icnPdf" src="images/icons/pdf.png" alt="Invoice" title="Invoice" /></a>
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
		    <input type="hidden" name="DuplicateInvoice" id="DuplicateInvoice" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr valign="top">
			    <td width="500">
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

				  <label for="txtInvoiceNo">Invoice #</label>
				  <div><input type="text" name="txtInvoiceNo" id="txtInvoiceNo" value="<?= ((IO::strValue('txtInvoiceNo') == '') ? date('YmdHis') : IO::strValue('txtInvoiceNo')) ?>" maxlength="20" size="20" class="textbox" /></div>

				  <div class="br10"></div>
				  
				  <label for="txtTitle">Invoice Title</label>
				  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="200" size="48" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtDetails" >Details <span>(Optional)</span></label>
				  <div><textarea name="txtDetails" id="txtDetails" style="width:350px; height:120px;"><?= IO::strValue('txtDetails') ?></textarea></div>

				  <div class="br10"></div>

				  <label for="txtDate">Date</label>
				  <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= ((IO::strValue('txtDate') == "") ? date("Y-m-d") : IO::strValue('txtDate')) ?>" maxlength="10" size="10" class="textbox" readonly /></div>

				  <br />
				  <button id="BtnSave">Save Invoice</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <label>Inspections</label>

				  <div id="Inspections" class="multiSelect" style="width:500px; height:280px;">
				    <table border="0" cellpadding="0" cellspacing="1" width="100%">
<?
		$sSQL = "SELECT id, title, `date` FROM tbl_inspections WHERE status='A' AND invoice_id='0' AND school_id='$iSchool' ORDER BY id DESC";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iInspection = $objDb->getField($i, "id");
			$sTitle      = $objDb->getField($i, "title");
			$sDate       = $objDb->getField($i, "date");
?>
					  <tr>
						<td width="25"><input type="checkbox" class="inspection" name="cbInspections[]" id="cbInspection<?= $i ?>" value="<?= $iInspection ?>" <?= ((@in_array($iInspection, IO::getArray('cbInspections'))) ? 'checked' : '') ?> /></td>
						<td><label for="cbInspection<?= $i ?>"><?= $sTitle ?> <span><?= formatDate($sDate, $_SESSION["DateFormat"]) ?></span></label></td>
					  </tr>
<?
		}
?>
			        </table>
				  </div>
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
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>