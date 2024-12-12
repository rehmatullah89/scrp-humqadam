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
		@include("save-contractor.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/contractors.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Contractors</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Contractor</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
		  <div id="GridMsg" class="hidden"></div>

		  <div id="ConfirmDelete" title="Delete Contractor?" class="hidden dlgConfirm">
			<span class="ui-icon ui-icon-trash"></span>
			Are you sure, you want to Delete this Contractor?<br />
		  </div>

		  <div id="ConfirmMultiDelete" title="Delete Contractors?" class="hidden dlgConfirm">
			<span class="ui-icon ui-icon-trash"></span>
			Are you sure, you want to Delete the selected Contractors?<br />
		  </div>


		  <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_contractors') ?>" />
		  <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
				<tr>
				  <th width="5%">#</th>
				  <th width="18%">Contractor</th>
				  <th width="14%">City</th>
				  <th width="10%">Phone</th>
				  <th width="10%">Mobile</th>
				  <th width="18%">Email</th>
				  <th width="10%">Status</th>
				  <th width="14%">Options</th>
				</tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 100)
	{
		$sSQL = "SELECT id, company, city, phone, mobile, email, status, logo, picture FROM tbl_contractors ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId      = $objDb->getField($i, "id");
			$sCompany = $objDb->getField($i, "company");
			$sCity    = $objDb->getField($i, "city");
			$sPhone   = $objDb->getField($i, "phone");
			$sMobile  = $objDb->getField($i, "mobile");
			$sEmail   = $objDb->getField($i, "email");
			$sStatus  = $objDb->getField($i, "status");
			$sLogo    = $objDb->getField($i, "logo");
			$sPicture = $objDb->getField($i, "picture");
?>
				<tr id="<?= $iId ?>">
				  <td class="position"><?= ($i + 1) ?></td>
				  <td><?= $sCompany ?></td>
				  <td><?= $sCity ?></td>
				  <td><?= $sPhone ?></td>
				  <td><?= $sMobile ?></td>
				  <td><?= $sEmail ?></td>
				  <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

				  <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
					<img class="icon icnBoqs" id="<?= $iId ?>" src="images/icons/boqs.png" alt="BOQs Setup" title="BOQs Setup" />
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}

			if ($sLogo != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogo))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."logos/".$sLogo) ?>" src="images/icons/logo.png" alt="Logo" title="Logo" />
<?
			}

			if ($sPicture != "" && @file_exists($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPicture))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.CONTRACTORS_IMG_DIR."persons/".$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
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
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		    <input type="hidden" name="DuplicateContractor" id="DuplicateContractor" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr valign="top">
			    <td width="450">
				  <label for="txtCompany">Company</label>
				  <div><input type="text" name="txtCompany" id="txtCompany" value="<?= IO::strValue('txtCompany', true) ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtAddress">Address <span>(Optional)</span></label>
				  <div><textarea name="txtAddress" id="txtAddress" rows="3" cols="42"><?= IO::strValue('txtAddress') ?></textarea></div>

				  <div class="br10"></div>

				  <label for="txtCity">City</label>
				  <div><input type="text" name="txtCity" id="txtCity" value="<?= IO::strValue('txtCity', true) ?>" maxlength="50" size="25" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="fileLogo">Logo <span>(optional)</span></label>
				  <div><input type="file" name="fileLogo" id="fileLogo" value="<?= IO::strValue('fileLogo') ?>" size="40" class="textbox" /></div>
				  
				  <div class="br10"></div>
				  
				  <label for="ddIndividual">Individual</label>

				  <div>
				    <select name="ddIndividual" id="ddIndividual">
					  <option value="N"<?= ((IO::strValue('ddIndividual') == 'N') ? ' selected' : '') ?>>No</option>
					  <option value="Y"<?= ((IO::strValue('ddIndividual') == 'Y') ? ' selected' : '') ?>>Yes</option>
				    </select>
				  </div>
				  
				  <div class="br10"></div>

				  <label for="ddTaxFiler">Tax Filer</label>

				  <div>
				    <select name="ddTaxFiler" id="ddTaxFiler">
					  <option value="Y"<?= ((IO::strValue('ddTaxFiler') == 'Y') ? ' selected' : '') ?>>Yes</option>
					  <option value="N"<?= ((IO::strValue('ddTaxFiler') == 'N') ? ' selected' : '') ?>>No</option>
				    </select>
				  </div>
				  
				  <div class="br10"></div>

				  <label for="ddInvoicing">Invoicing Scenario</label>

				  <div>
				    <select name="ddInvoicing" id="ddInvoicing">
					  <option value="1"<?= ((IO::intValue('ddInvoicing') == 1) ? ' selected' : '') ?>>Scenario 1</option>
					  <option value="2"<?= ((IO::intValue('ddInvoicing') == 2) ? ' selected' : '') ?>>Scenario 2</option>
					  <option value="3"<?= ((IO::intValue('ddInvoicing') == 3) ? ' selected' : '') ?>>Scenario 3</option>
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
				  <button id="BtnSave">Save Contractor</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <h3 style="width:350px; margin:0px 0px 15px 0px;">Contact person</h3>

				  <label for="ddTitle">Title</label>

				  <div>
				    <select name="ddTitle" id="ddTitle">
					  <option value="Mr"<?= ((IO::strValue('ddTitle') == 'Mr') ? ' selected' : '') ?>>Mr</option>
					  <option value="Ms"<?= ((IO::strValue('ddTitle') == 'Ms') ? ' selected' : '') ?>>Ms</option>
					  <option value="Mrs"<?= ((IO::strValue('ddTitle') == 'Mrs') ? ' selected' : '') ?>>Mrs</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="txtFirstName">First Name</label>
				  <div><input type="text" name="txtFirstName" id="txtFirstName" value="<?= IO::strValue('txtFirstName', true) ?>" maxlength="50" size="35" class="textbox" /></div>

				  <div class="br10"></div>
				  
				  <label for="txtMiddleName">Middle Name</label>
				  <div><input type="text" name="txtMiddleName" id="txtMiddleName" value="<?= IO::strValue('txtMiddleName', true) ?>" maxlength="50" size="35" class="textbox" /></div>

				  <div class="br10"></div>				  

				  <label for="txtLastName">Last Name</label>
				  <div><input type="text" name="txtLastName" id="txtLastName" value="<?= IO::strValue('txtLastName', true) ?>" maxlength="50" size="35" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtPhone">Phone</label>
				  <div><input type="text" name="txtPhone" id="txtPhone" value="<?= IO::strValue('txtPhone') ?>" maxlength="25" size="25" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtMobile">Mobile <span>(Optional)</span></label>
				  <div><input type="text" name="txtMobile" id="txtMobile" value="<?= IO::strValue('txtMobile') ?>" maxlength="25" size="25" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtEmail">Email <span>(Optional)</span></label>
				  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= IO::strValue('txtEmail') ?>" maxlength="100" size="35" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="filePicture">Picture <span>(optional)</span></label>
				  <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="40" class="textbox" /></div>
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