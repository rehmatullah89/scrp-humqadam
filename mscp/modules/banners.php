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
		@include("save-banner.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/banners.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Banners</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Banner</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Banner?" class="dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Banner?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Banners?" class="dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Banners?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION['PageRecords'] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_banners">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="20%">Title</th>
			      <th width="30%">URL</th>
			      <th width="8.5%">Size</th>
			      <th width="8%">Views</th>
			      <th width="8%">Clicks</th>
			      <th width="8.5%">Status</th>
			      <th width="12%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_banners ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sTitle  = $objDb->getField($i, "title");
		$sType   = $objDb->getField($i, "type");
		$sLink   = $objDb->getField($i, "link");
		$sBanner = $objDb->getField($i, "banner");
		$iWidth  = $objDb->getField($i, "width");
		$iHeight = $objDb->getField($i, "height");
		$iViews  = $objDb->getField($i, "views");
		$iClicks = $objDb->getField($i, "clicks");
		$sStatus = $objDb->getField($i, "status");
		$sUrl    = "";

		if (@in_array($sType, array("W", "C", "P")))
		{
			if ($sType == "W")
				$sUrl = getDbValue("sef_url", "tbl_web_pages", "id='$sLink'");

			else if ($sType == "C")
				$sUrl = getDbValue("sef_url", "tbl_blog_categories", "id='$sLink'");

			else if ($sType == "P")
				$sUrl = getDbValue("sef_url", "tbl_blog_posts", "id='$sLink'");

			$sUrl = (SITE_URL.$sUrl);
		}

		else if ($sType == "U")
			$sUrl = $sLink;

		else if ($sType == "I")
			$sUrl = "Image Banner";

		else if ($sType == "F")
			$sUrl = "Flash Banner";

		else if ($sType == "S")
			$sUrl = "Script Banner";
?>
		        <tr id="<?= $iId ?>" valign="top">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sTitle ?></td>
		          <td><?= $sUrl ?></td>
		          <td><?= "{$iWidth} x {$iHeight}" ?></td>
		          <td><?= formatNumber($iViews, false) ?></td>
		          <td><?= formatNumber($iClicks, false) ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
		if ($sUserRights['Edit'] == "Y")
		{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}

		if ($sUserRights['Delete'] == "Y")
		{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
		}


		if (@in_array($sType, array("W", "C", "P", "U", "I")) && $sBanner != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sBanner))
		{
?>
					<img class="icnPicture" id="<?= (SITE_URL.BANNERS_IMG_DIR.$sBanner) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
<?
		}

		else if ($sType == "F" && $sBanner != "" && @file_exists($sRootDir.BANNERS_IMG_DIR.$sBanner))
		{
?>
					<img class="icnFlash" id="<?= $iId ?>" rel="<?= $iWidth ?>|<?= $iHeight ?>" src="images/icons/flash.gif" alt="Flash" title="Flash" />
<?
		}

		else if ($sType == "S")
		{
?>
					<img class="icnScript" id="<?= $iId ?>" rel="<?= $iWidth ?>|<?= $iHeight ?>" src="images/icons/script.png" alt="Script" title="Script" />
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

	      <div align="right" id="SelectButtons">
		    <button id="BtnSelectAll">Select All</button>
		    <button id="BtnSelectNone">Clear Selection</button>
	      </div>
<?
	}
?>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
				<td width="450">
				  <label for="txtTitle">Title</label>
				  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle') ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddLinkType">Link Type</label>

				  <div>
				    <select name="ddLinkType" id="ddLinkType">
					  <option value=""></option>

					  <optgroup label="Picture">
					    <option value="W"<?= ((IO::strValue('ddLinkType') == 'W') ? ' selected' : '') ?>>Web Page</option>
					    <option value="C"<?= ((IO::strValue('ddLinkType') == 'C') ? ' selected' : '') ?>>Blog Category</option>
					    <option value="P"<?= ((IO::strValue('ddLinkType') == 'P') ? ' selected' : '') ?>>Blog Post</option>
					    <option value="U"<?= ((IO::strValue('ddLinkType') == 'U') ? ' selected' : '') ?>>URL</option>
					  </optgroup>

					  <optgroup label="Others">
					    <option value="I"<?= ((IO::strValue('ddLinkType') == 'I') ? ' selected' : '') ?>>Image</option>
					    <option value="F"<?= ((IO::strValue('ddLinkType') == 'F') ? ' selected' : '') ?>>Flash</option>
					    <option value="S"<?= ((IO::strValue('ddLinkType') == 'S') ? ' selected' : '') ?>>Script</option>
					  </optgroup>
				    </select>
				  </div>

				  <div id="LinkPage"<?= ((IO::strValue('ddLinkType') == 'W') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="ddLinkType">Web Page</label>

				    <div>
					  <select name="ddLinkPage" id="ddLinkPage">
					    <option value=""></option>
<?
		$sPagesList = getList("tbl_web_pages w", "w.id", "CONCAT(COALESCE((SELECT CONCAT(p.title, ' &raquo; ') FROM tbl_web_pages p WHERE p.id>'0' AND p.id=w.parent_id), ''), w.title)", "w.id='1' OR (w.id>'0' AND w.sef_url LIKE '%.html')");

		foreach ($sPagesList as $iPageId => $sPage)
		{
?>
		                <option value="<?= $iPageId ?>"<?= (($iPageId == IO::intValue('ddLinkPage')) ? ' selected' : '') ?>><?= $sPage ?></option>
<?
		}
?>
			          </select>
			        </div>
			      </div>


			      <div id="LinkCategory"<?= ((IO::strValue('ddLinkType') == 'C') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="ddLinkCategory">Blog Category</label>

				    <div>
					  <select name="ddLinkCategory" id="ddLinkCategory">
					    <option value=""></option>
<?
		$sCategoriesList = getList("tbl_blog_categories", "id", "name", "parent_id='0'");

		foreach ($sCategoriesList as $iParentId => $sParent)
		{
?>
			    	    <option value="<?= $iParentId ?>"<?= ((IO::intValue('ddLinkCategory') == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iCategoryId = $objDb->getField($i, "id");
				$sCategory   = $objDb->getField($i, "name");
?>
			    	    <option value="<?= $iCategoryId ?>"<?= ((IO::intValue('ddLinkCategory') == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
			}
		}
?>
			          </select>
			        </div>
			      </div>


				  <div id="LinkPost"<?= ((IO::strValue('ddLinkType') == 'P') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="ddLinkPost">Blog Post</label>

				    <div>
			          <select name="ddLinkPostCategory" id="ddLinkPostCategory">
			            <option value="">Select Blog Category</option>
<?
		foreach ($sCategoriesList as $iParentId => $sParent)
		{
?>
			    	    <option value="<?= $iParentId ?>"<?= ((IO::intValue('ddLinkPostCategory') == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iCategoryId = $objDb->getField($i, "id");
				$sCategory   = $objDb->getField($i, "name");
?>
			    	    <option value="<?= $iCategoryId ?>"<?= ((IO::intValue('ddLinkPostCategory') == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
			}
		}
?>
			          </select>

			          <div class="br5"></div>

					  <select name="ddLinkPost" id="ddLinkPost">
					    <option value="">Select Blog Post</option>
<?
		$sPostsList = getList("tbl_blog_posts", "id", "title", ("category_id='".IO::intValue('ddLinkPostCategory')."'"));

		foreach ($sPostsList as $iPostId => $sPost)
		{
?>
		                <option value="<?= $iPostId ?>"<?= (($iPostId == IO::intValue('ddLinkPost')) ? ' selected' : '') ?>><?= $sPost ?></option>
<?
		}
?>
			          </select>
			        </div>
			      </div>


				  <div id="LinkUrl"<?= ((IO::strValue('ddLinkType') == 'U') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="txtUrl">URL</label>
				    <div><input type="text" name="txtUrl" id="txtUrl" value="<?= IO::strValue('txtUrl') ?>" maxlength="250" size="44" class="textbox" /></div>
				  </div>


				  <div id="LinkFlash"<?= ((IO::strValue('ddLinkType') == 'F') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="fileFlash">Flash <span>(banner swf file)</span></label>
				    <div><input type="file" name="fileFlash" id="fileFlash" value="<?= IO::strValue('fileFlash') ?>" size="40" class="textbox" /></div>
				  </div>


				  <div id="LinkScript"<?= ((IO::strValue('ddLinkType') == 'S') ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="txtScript">Script <span>(banner code)</span></label>
				    <div><textarea name="txtScript" id="txtScript" rows="5" style="width:280px;"><?= IO::strValue('txtScript') ?></textarea></div>
				  </div>


				  <div id="Picture"<?= ((@in_array(IO::strValue('ddLinkType'), array('', 'F', 'S'))) ? ' class="hidden"' : '') ?>>
				    <div class="br10"></div>

				    <label for="filePicture">Picture</label>
				    <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="40" class="textbox" /></div>
				  </div>


				  <div class="br10"></div>

				  <label for="txtWidth">Width</label>
				  <div><input type="text" name="txtWidth" id="txtWidth" value="<?= IO::strValue('txtWidth') ?>" maxlength="4" size="10" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtHeight">Height</label>
				  <div><input type="text" name="txtHeight" id="txtHeight" value="<?= IO::strValue('txtHeight') ?>" maxlength="4" size="10" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save Banner</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <label>Placement <span>(<a href="#" rel="Check">Check All</a> | <a href="#" rel="Clear">Clear</a>)</span></label>

				  <div class="multiSelect" style="height:auto;">
				    <table border="0" cellpadding="0" cellspacing="0" width="100%">
					  <tr>
					    <td width="25"><input type="checkbox" name="cbPlacements[]" class="placement" id="cbHeader" value="H" <?= ((@in_array("H", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbHeader">Header</label></td>
					  </tr>

					  <tr>
					    <td><input type="checkbox" name="cbPlacements[]" class="placement" id="cbFooter" value="F" <?= ((@in_array("F", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbFooter">Footer</label></td>
					  </tr>

					  <tr>
					    <td><input type="checkbox" name="cbPlacements[]" class="placement" id="cbLeftPanel" value="L" <?= ((@in_array("L", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbLeftPanel">Left Panel</label></td>
					  </tr>

					  <tr>
					    <td><input type="checkbox" name="cbPlacements[]" class="placement" id="cbRightPanel" value="R" <?= ((@in_array("R", IO::getArray('cbPlacements'))) ? 'checked' : '') ?> /></td>
					    <td><label for="cbRightPanel">Right Panel</label></td>
					  </tr>
				    </table>
				  </div>

				  <div class="br10"></div>

				  <label for="ddPage">Web Page</label>

				  <div>
					<select name="ddPage" id="ddPage">
					  <option value="0"<?= ((IO::intValue('Page') == 0) ? ' selected' : '') ?>>All Pages</option>
					  <option value="-1"<?= ((IO::intValue('Page') == -1) ? ' selected' : '') ?>>None</option>
					  <option value="" disabled>----------------------------------------</option>
<?
		$sPagesList = getList("tbl_web_pages w", "w.id", "CONCAT(COALESCE((SELECT CONCAT(p.title, ' &raquo; ') FROM tbl_web_pages p WHERE p.id>'0' AND p.id=w.parent_id), ''), w.title)", "w.id>'0'");

		foreach ($sPagesList as $iPageId => $sPage)
		{
?>
		              <option value="<?= $iPageId ?>"<?= (($iPageId == IO::intValue('Page')) ? ' selected' : '') ?>><?= $sPage ?></option>
<?
		}
?>
			        </select>
			      </div>

				  <div class="br10"></div>

				  <label for="ddCategory">Blog Category</label>

				  <div>
					<select name="ddCategory" id="ddCategory">
					  <option value="0"<?= ((IO::intValue('ddCategory') == 0) ? ' selected' : '') ?>>All Blog Categories</option>
					  <option value="-1"<?= ((IO::intValue('ddCategory') == -1) ? ' selected' : '') ?>>None</option>
					  <option value="" disabled>----------------------------------------</option>
<?
		foreach ($sCategoriesList as $iParentId => $sParent)
		{
?>
			    	  <option value="<?= $iParentId ?>"<?= ((IO::intValue('ddCategory') == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iCategoryId = $objDb->getField($i, "id");
				$sCategory   = $objDb->getField($i, "name");
?>
			    	  <option value="<?= $iCategoryId ?>"<?= ((IO::intValue('ddCategory') == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
			}
		}
?>
			        </select>
			      </div>

				  <div class="br10"></div>

				  <label for="ddPost">Blog Post</label>

				  <div>
					<select name="ddPost" id="ddPost">
					  <option value="0"<?= ((IO::intValue('ddPost') == 0) ? ' selected' : '') ?>>All Blog Posts</option>
					  <option value="-1"<?= ((IO::intValue('ddPost') == -1) ? ' selected' : '') ?>>None</option>
		              <option value="1"<?= ((IO::intValue('ddPost') == 1) ? ' selected' : '') ?>>Select</option>
			        </select>

			        <div id="Post" style="display:<?= ((IO::intValue('ddPost') == 1) ? 'block' : 'none') ?>; margin-top:10px; padding-top:10px; border-top:dotted 1px #bbbbbb;">
			          <select name="ddSelectedCategory" id="ddSelectedCategory">
			            <option value="">Select Blog Category</option>
<?
		foreach ($sCategoriesList as $iParentId => $sParent)
		{
?>
			    	    <option value="<?= $iParentId ?>"<?= ((IO::intValue('ddSelectedCategory') == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iCategoryId = $objDb->getField($i, "id");
				$sCategory   = $objDb->getField($i, "name");
?>
			    	    <option value="<?= $iCategoryId ?>"<?= ((IO::intValue('ddSelectedCategory') == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
			}
		}
?>
			          </select>

			          <div class="br5"></div>

					  <select name="ddSelectedPost" id="ddSelectedPost">
					    <option value="">Select Blog Post</option>
<?
		$sPostsList = getList("tbl_blog_posts", "id", "title", ("category_id='".IO::intValue('ddSelectedCategory')."' AND status='A'"));

		foreach ($sPostsList as $iPostId => $sPost)
		{
?>
		                <option value="<?= $iPostId ?>"<?= (($iPostId == IO::intValue('ddSelectedPost')) ? ' selected' : '') ?>><?= $sPost ?></option>
<?
		}
?>
			          </select>
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