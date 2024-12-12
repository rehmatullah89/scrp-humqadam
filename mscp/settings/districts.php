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
		@include("save-district.php");


	$sProvinces = array( );


	$sSQL = "SELECT id, name, sef_url FROM tbl_provinces ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iProvince = $objDb->getField($i, "id");
		$sProvince = $objDb->getField($i, "name");
		$sSefUrl   = $objDb->getField($i, "sef_url");

		$sProvinces[$iProvince] = array('Name' => $sProvince, 'SefUrl' => $sSefUrl);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="plugins/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="plugins/ckeditor/adapters/jquery.js"></script>
  <script type="text/javascript" src="plugins/ckfinder/ckfinder.js"></script>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/districts.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Districts</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New District</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete District?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this District?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Districts?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Districts?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_districts') ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_districts">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="20%">District</th>
			      <th width="25%">SEF URL</th>
			      <th width="20%">Province</th>
			      <th width="15%">Status</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 100)
	{
		$sSQL = "SELECT id, province_id, name, sef_url, picture, status FROM tbl_districts ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId       = $objDb->getField($i, "id");
			$iProvince = $objDb->getField($i, "province_id");
			$sName     = $objDb->getField($i, "name");
			$sSefUrl   = $objDb->getField($i, "sef_url");
			$sPicture  = $objDb->getField($i, "picture");
			$sStatus   = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sName ?></td>
		          <td><?= $sSefUrl ?></td>
		          <td><?= $sProvinces[$iProvince]['Name'] ?></td>
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

			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}

			if ($sPicture != "" && @file_exists($sRootDir.DISTRICTS_IMG_DIR.$sPicture))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.DISTRICTS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
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
		    <input type="hidden" name="DuplicateDistrict" id="DuplicateDistrict" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr valign="top">
				<td width="450">
				  <label for="txtName">District Name</label>
				  <div><input type="text" name="txtName" id="txtName" value="<?= IO::strValue('txtName', true) ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddProvince">Province</label>

				  <div>
				    <select name="ddProvince" id="ddProvince">
					  <option value=""></option>
<?
		foreach ($sProvinces as $iProvince => $sProvince)
		{
?>
			          <option value="<?= $iProvince ?>" sefUrl="<?= $sProvince['SefUrl'] ?>"<?= ((IO::intValue('ddProvince') == $iProvince) ? ' selected' : '') ?>><?= $sProvince['Name'] ?></option>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="txtSefUrl">SEF URL <span id="SefUrl"><?= ((IO::strValue('Url') != "") ? ("/".IO::strValue('Url')) : "") ?></span></label>

				  <div>
				    <input type="hidden" name="Url" id="Url" value="<?= IO::strValue('Url') ?>" />
				    <input type="text" name="txtSefUrl" id="txtSefUrl" value="<?= IO::strValue('txtSefUrl') ?>" maxlength="100" size="44" class="textbox" />
				  </div>

				  <div class="br10"></div>

				  <label for="txtLatitude">Map Coordinates <span>(Latitude, Longitude)</span></label>

				  <div>
					<input type="text" name="txtLatitude" id="txtLatitude" value="<?= IO::strValue("txtLatitude") ?>" maxlength="30" size="10" class="textbox" />
					-
					<input type="text" name="txtLongitude" id="txtLongitude" value="<?= IO::strValue("txt$sLongitude") ?>" maxlength="30" size="10" class="textbox" />
				  </div>

				  <div class="br10"></div>

				  <label for="txtCoordinates">Area Coordinates <span>(optional)</span></label>
				  <div><textarea name="txtCoordinates" id="txtCoordinates" style="width:320px; height:180px;"><?= IO::strValue('txtCoordinates') ?></textarea></div>

				  <div class="br10"></div>

				  <label for="filePicture">Picture <span>(optional)</span></label>
				  <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="40" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save District</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <label for="txtDescription">Description <span>(optional)</span></label>
				  <div><textarea name="txtDescription" id="txtDescription" style="width:100%; height:300px;"><?= IO::strValue('txtDescription') ?></textarea></div>
				</td>
			  </tr>
			</table>
			</div>
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