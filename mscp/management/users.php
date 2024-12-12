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
		@include(IO::strValue("Action"));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/users.js"></script>
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
      <input type="hidden" id="OpenTab" value="<?= ((($_POST && $bError == true) || IO::intValue('OpenTab') > 0) ? IO::intValue('OpenTab') : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Users</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New User</a></li>
<?
	}
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-3"><b>User Types</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-4">Add New Type</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="UserGridMsg" class="hidden"></div>

	      <div id="ConfirmUserDelete" title="Delete User?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this User?<br />
	      </div>

	      <div id="ConfirmUserMultiDelete" title="Delete Users?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Users?<br />
	      </div>


		  <div id="TblUsers" class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_admins') ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="UserGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="20%">Name</th>
			      <th width="20%">Email</th>
			      <th width="12%">Mobile</th>
			      <th width="15%">User Type</th>
			      <th width="10%">Status</th>
			      <th width="18%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 100)
	{
		$sTypesList    = getList("tbl_admin_types", "id", "title");
		$sTypesList[0] = "N/A";


		$sConditions = " WHERE id>'1' ";

		if ($_SESSION["AdminLevel"] == 0)
			$sConditions .= " AND level='0' ";


		$sSQL = "SELECT id, type_id, name, email, mobile, status, level, picture FROM tbl_admins $sConditions ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId      = $objDb->getField($i, "id");
			$iType    = $objDb->getField($i, "type_id");
			$sName    = $objDb->getField($i, "name");
			$sMobile  = $objDb->getField($i, "mobile");
			$sEmail   = $objDb->getField($i, "email");
			$iLevel   = $objDb->getField($i, "level");
			$sPicture = $objDb->getField($i, "picture");
			$sStatus  = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sName ?></td>
		          <td><?= $sEmail ?></td>
		          <td><?= $sMobile ?></td>
		          <td><?= $sTypesList[$iType] ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "Disabled") ?></td>

		          <td>
<?
			if ($sUserRights["Edit"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}

			if ($sUserRights["Delete"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}

			if ($sPicture != "" && @file_exists($sRootDir.ADMINS_IMG_DIR.'originals/'.$sPicture))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.ADMINS_IMG_DIR.'originals/'.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
					<img class="icnThumb" id="<?= $iId ?>" rel="Admin" src="images/icons/thumb.png" alt="Create Thumb" title="Create Thumb" />
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


	      <div id="SelectUserButtons"<?= (($iTotalRecords > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
	        <div class="br10"></div>

	        <div align="right">
		      <button id="BtnUserSelectAll">Select All</button>
		      <button id="BtnUserSelectNone">Clear Selection</button>
		    </div>
	      </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmUser" id="frmUser" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="DuplicateEmail" id="DuplicateEmail" value="0" />
		    <input type="hidden" name="OpenTab" value="1" />
		    <input type="hidden" name="Action" value="save-user.php" />
			<div id="UserMsg" class="hidden"></div>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
			    <td width="450">
				  <label for="txtName">Name</label>
				  <div><input type="text" name="txtName" id="txtName" value="<?= IO::strValue('txtName', true) ?>" maxlength="50" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtMobile">Mobile</label>
				  <div><input type="text" name="txtMobile" id="txtMobile" value="<?= IO::strValue('txtMobile') ?>" maxlength="25" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtEmail">Email Address</label>
				  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= IO::strValue('txtEmail') ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtPassword">Password</label>
				  <div><input type="text" name="txtPassword" id="txtPassword" value="<?= IO::strValue('txtPassword') ?>" maxlength="30" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddType">User Type</label>

				  <div>
				    <select name="ddType" id="ddType">
					  <option value=""></option>
<?
		$sTypes = getList("tbl_admin_types", "id", "title", "status='A'");

		foreach ($sTypes as $iType => $sType)
		{
?>
			    	  <option value="<?= $iType ?>"<?= ((IO::intValue('ddTypes') == $iType) ? ' selected' : '') ?>><?= $sType ?></option>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="filePicture">Picture <span>(optional)</span></label>
				  <div><input type="file" name="filePicture" id="filePicture" value="" size="60" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddRecords">Records per page</label>

				  <div>
				    <select name="ddRecords" id="ddRecords">
					  <option value="10"<?= ((IO::intValue('ddRecords') == 10) ? ' selected' : '') ?>>10</option>
					  <option value="25"<?= ((IO::intValue('ddRecords') == 25) ? ' selected' : '') ?>>25</option>
					  <option value="50"<?= ((IO::intValue('ddRecords') == 50 || IO::intValue('ddRecords') == 0) ? ' selected' : '') ?>>50</option>
					  <option value="100"<?= ((IO::intValue('ddRecords') == 100) ? ' selected' : '') ?>>100</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddTheme">CMS Theme</label>

				  <div>
				    <select name="ddTheme" id="ddTheme">
					  <option value="smoothness"<?= ((IO::strValue('ddTheme') == "smoothness") ? ' selected' : '') ?>>Black</option>
					  <option value="redmond"<?= ((IO::strValue('ddTheme') == "redmond") ? ' selected' : '') ?>>Blue</option>
					  <option value="blitzer"<?= ((IO::strValue('ddTheme') == "blitzer") ? ' selected' : '') ?>>Red</option>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
				 	  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="D"<?= ((IO::strValue('ddStatus') == 'D') ? ' selected' : '') ?>>Disabled</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save User</button>
				  <button id="BtnReset">Clear</button>
			    </td>

			    <td>
				  <label for="ddProvinces">Provinces</label>

				  <div class="multiSelect" style="width:400px; height:auto;">
				    <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
		$sProvincesList = getList("tbl_provinces", "id", "name");

		foreach ($sProvincesList as $iProvince => $sProvince)
		{
?>
					  <tr valign="top">
					    <td width="25"><input type="checkbox" class="province" name="cbProvinces[]" id="cbProvince<?= $iProvince ?>" value="<?= $iProvince ?>" <?= ((@in_array($iProvince, IO::getArray('cbProvinces'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbProvince<?= $iProvince ?>"><?= $sProvince ?></label></td>
					  </tr>
<?
		}
?>
				    </table>
				  </div>

				  <div class="br10"></div>

				  <label for="ddDistrict">District(s) <span>(<a href="#" rel="Check|district">Check All</a> | <a href="#" rel="Clear|district">Clear</a>)</span></label>

				  <div class="multiSelect" style="width:400px; height:200px;">
				    <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
		$sSQL = "SELECT id, province_id, CONCAT(name, ' (', (SELECT code FROM tbl_provinces WHERE id=tbl_districts.province_id), ')') AS _Name
		         FROM tbl_districts
		         ORDER BY _Name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iDistrict = $objDb->getField($i, "id");
			$iProvince = $objDb->getField($i, "province_id");
			$sDistrict = $objDb->getField($i, "_Name");
?>
					  <tr valign="top">
					    <td width="25"><input type="checkbox" class="district province<?= $iProvince ?>" name="cbDistricts[]" id="cbDistrict<?= $iDistrict ?>" value="<?= $iDistrict ?>" <?= ((@in_array($iDistrict, IO::getArray('cbDistricts'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbDistrict<?= $iDistrict ?>"><?= $sDistrict ?></label></td>
					  </tr>
<?
		}
?>
				    </table>
				  </div>

				  <div class="br10"></div>

				  <label for="txtSchools">School(s)</label>
				  <input type="text" name="txtSchools" id="txtSchools" value="" />
<?
		$sSchools = IO::strValue("txtSchools");
?>
		  		  <input type="hidden" id="Schools" value="<?= @json_encode($sSchools) ?>" />
			    </td>
			  </tr>
			</table>
		  </form>
	    </div>
<?
	}
?>


	    <div id="tabs-3">
	      <div id="TypeGridMsg" class="hidden"></div>

	      <div id="ConfirmTypeDelete" title="Delete User Type?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this User Type?<br />
	      </div>

	      <div id="ConfirmTypeMultiDelete" title="Delete User Types?" class="dlgConfirm hidden">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected User Types?<br />
	      </div>


		  <div id="TblTypes" class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="TypeGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="50%">Title</th>
			      <th width="25%">Status</th>
			      <th width="20%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_admin_types ORDER BY title";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sTitle  = $objDb->getField($i, "title");
		$sStatus = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sTitle ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
		if ($sUserRights["Edit"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
		{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}

		if ($sUserRights["Delete"] == "Y" && ($iId > 1 || $_SESSION["AdminLevel"] == 1))
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
<?
	if ($iCount > 5 && $sUserRights["Delete"] == "Y")
	{
?>

	      <div class="br10"></div>

	      <div align="right" id="SelectTypeButtons">
		    <button id="BtnTypeSelectAll">Select All</button>
		    <button id="BtnTypeSelectNone">Clear Selection</button>
	      </div>
<?
	}
?>
	    </div>

<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-4">
		  <form name="frmType" id="frmType" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicateType" id="DuplicateType" value="0" />
		    <input type="hidden" name="OpenTab" value="3" />
		    <input type="hidden" name="Action" value="save-user-type.php" />
			<div id="TypeMsg" class="hidden"></div>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
			    <td width="400">
				  <label for="txtTitle">Title</label>
				  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save Type</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
<?
		$sSQL = "SELECT ap.id, ap.module, ap.section, ar.`add`, ar.`edit`, ar.`delete`
		         FROM tbl_admin_pages ap, tbl_admin_rights ar
		         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}'
		         ORDER BY ap.module, ap.position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );
?>
				  <input type="hidden" name="PageCount" id="PageCount" value="<?= $iCount ?>" />

				  <div class="grid" style="max-height:420px; overflow:auto;">
				    <div class="grid">
				      <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
					    <tr class="header">
					      <td width="25%">Section</td>
					      <td width="25%">Page</td>
					      <td width="10%" align="center"><a href="#" id="View">View</a></td>
					      <td width="10%" align="center"><a href="#" id="Add">Add</a></td>
					      <td width="10%" align="center"><a href="#" id="Edit">Edit</a></td>
					      <td width="10%" align="center"><a href="#" id="Delete">Delete</a></td>
					      <td width="10%" align="center"><a href="#" id="All">ALL</a></td>
					    </tr>
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId      = $objDb->getField($i, "id");
			$sModule  = $objDb->getField($i, "module");
			$sSection = $objDb->getField($i, "section");
			$sAdd     = $objDb->getField($i, "add");
			$sEdit    = $objDb->getField($i, "edit");
			$sDelete  = $objDb->getField($i, "delete");
?>

					    <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
					      <td><input type="hidden" name="PageId<?= $i ?>" value="<?= $iId ?>" /><?= $sModule ?></td>
					      <td><?= $sSection ?></td>
					      <td align="center"><input type="checkbox" name="cbView<?= $i ?>" id="cbView<?= $i ?>" value="Y" <?= ((IO::strValue('cbView'.$i) == "Y") ? "checked" : "") ?> /></td>
					      <td align="center"><input type="checkbox" name="cbAdd<?= $i ?>" id="cbAdd<?= $i ?>" value="Y" <?= ((IO::strValue('cbAdd'.$i) == "Y") ? "checked" : "") ?> <?= (($sAdd == "Y") ? "" : "disabled") ?> /></td>
					      <td align="center"><input type="checkbox" name="cbEdit<?= $i ?>" id="cbEdit<?= $i ?>" value="Y" <?= ((IO::strValue('cbEdit'.$i) == "Y") ? "checked" : "") ?> <?= (($sEdit == "Y") ? "" : "disabled") ?> /></td>
					      <td align="center"><input type="checkbox" name="cbDelete<?= $i ?>" id="cbDelete<?= $i ?>" value="Y" <?= ((IO::strValue('cbDelete'.$i) == "Y") ? "checked" : "") ?> <?= (($sDelete == "Y") ? "" : "disabled") ?> /></td>
					      <td align="center"><input type="checkbox" name="cbAll<?= $i ?>" id="cbAll<?= $i ?>" value="Y" <?= ((IO::strValue('cbAll'.$i) == "Y") ? "checked" : "") ?> /></td>
					    </tr>
<?
		}
?>
				      </table>
				    </div>
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
	$objDbGlobal->close( );

	@ob_end_flush( );
?>