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
		@include("save-web-page.php");


	$sSefMode = getDbValue("sef_mode", "tbl_settings", "id='1'");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/web-pages.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Web Pages</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Page</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Page?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Web Page?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Pages?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Web Pages?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_web_pages">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="30%">Page Title</th>
			      <th width="28%"><?= (($sSefMode == "Y") ? "SEF" : "PHP") ?> URL</th>
			      <th width="12%">Placement</th>
			      <th width="13%">Status</th>
			      <th width="12%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_web_pages ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId         = $objDb->getField($i, "id");
		$sTitle      = $objDb->getField($i, "title");
		$sSefUrl     = $objDb->getField($i, "sef_url");
		$sPhpUrl     = $objDb->getField($i, "php_url");
		$sPlacements = $objDb->getField($i, "placements");
		$sToggle     = $objDb->getField($i, "toggle");
		$sRename     = $objDb->getField($i, "rename");
		$sStatus     = $objDb->getField($i, "status");

		$sPlacements = str_replace("H", "Header", $sPlacements);
		$sPlacements = str_replace("F", "Footer", $sPlacements);
		$sPlacements = str_replace("L", "Left Panel", $sPlacements);
		$sPlacements = str_replace("R", "Right Panel", $sPlacements);
		$sPlacements = str_replace(",", ", ", $sPlacements);
?>
		        <tr id="<?= $iId ?>"<?= (($sToggle == "Y" && $sRename == "Y") ? '' : ' class="noDelete"') ?>>
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sTitle ?></td>
		          <td><?= (($sSefMode == "Y") ? $sSefUrl : (($sPhpUrl == "" && $iId != 1) ? "index.php?PageId={$iId}" : $sPhpUrl)) ?></td>
		          <td><?= $sPlacements ?></td>
		          <td><?= (($sStatus == "P") ? "Published" : "Draft") ?></td>

		          <td>
<?
		if ($sUserRights["Edit"] == "Y")
		{
			if ($sToggle == "Y")
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'P') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
<?
			}
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}

		if ($sUserRights["Delete"] == "Y" && $sToggle == "Y" && $sRename == "Y")
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
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicatePage" id="DuplicatePage" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="txtTitle">Page Title</label>
		    <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="100" size="44" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="txtSefUrl">SEF URL</label>
		    <div><input type="text" name="txtSefUrl" id="txtSefUrl" value="<?= IO::strValue('txtSefUrl') ?>" maxlength="100" size="44" class="textbox" /></div>

		    <div class="br10"></div>

		    <label>Link Placement <span>(optional)</span></label>

		    <div class="multiSelect" style="height:auto;">
			  <table border="0" cellpadding="0" cellspacing="0" width="100%">
			    <tr>
				  <td width="25"><input type="checkbox" name="cbPlacements[]" id="cbHeader" value="H" <?= ((@in_array("H", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbHeader">Header</label></td>
			    </tr>

			    <tr>
				  <td><input type="checkbox" name="cbPlacements[]" id="cbFooter" value="F" <?= ((@in_array("F", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbFooter">Footer</label></td>
			    </tr>

			    <tr>
				  <td><input type="checkbox" name="cbPlacements[]" id="cbLeftPanel" value="L" <?= ((@in_array("L", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbLeftPanel">Left Panel</label></td>
			    </tr>

			    <tr>
				  <td><input type="checkbox" name="cbPlacements[]" id="cbRightPanel" value="R" <?= ((@in_array("R", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbRightPanel">Right Panel</label></td>
			    </tr>
			  </table>
		    </div>

			<div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="D"<?= ((IO::strValue('ddStatus') == 'D') ? ' selected' : '') ?>>Draft</option>
			    <option value="P"<?= ((IO::strValue('ddStatus') == 'P') ? ' selected' : '') ?>>Published</option>
			  </select>
		    </div>

		    <br />
		    <button id="BtnSave">Save Page</button>
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