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

	$iBannerId = IO::intValue("BannerId");

	$sSQL = "SELECT * FROM tbl_banners WHERE id='$iBannerId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle        = $objDb->getField(0, "title");
	$sLinkType     = $objDb->getField(0, "type");
	$sLink         = $objDb->getField(0, "link");
	$sBanner       = $objDb->getField(0, "banner");
	$iWidth        = $objDb->getField(0, "width");
	$iHeight       = $objDb->getField(0, "height");
	$sPlacements   = $objDb->getField(0, "placements");
	$iPage         = $objDb->getField(0, "page_id");
	$iCategory     = $objDb->getField(0, "category_id");
	$iSelectedPost = $objDb->getField(0, "post_id");
	$sStatus       = $objDb->getField(0, "status");

	$sPlacements       = @explode(",", $sPlacements);
	$iLinkPage         = 0;
	$iLinkCategory     = 0;
	$iLinkPost         = 0;
	$iPost             = "";
	$iSelectedCategory = 0;
	$sUrl              = "";
	$sPicture          = "";
	$sFlash            = "";
	$sScript           = "";

	if (@in_array($sLinkType, array("W", "C", "P")))
	{
		if ($sLinkType == "W")
			$iLinkPage = $sLink;

		else if ($sLinkType == "C")
			$iLinkCategory = $sLink;

		else if ($sLinkType == "P")
			$iLinkPost = $sLink;

		$sPicture = $sBanner;
	}

	else if ($sLinkType == "U")
	{
		$sUrl     = $sLink;
		$sPicture = $sBanner;
	}

	else if ($sLinkType == "I")
		$sPicture = $sBanner;

	else if ($sLinkType == "F")
		$sFlash = $sBanner;

	else if ($sLinkType == "S")
		$sScript = $sLink;


	$iPost = (($iSelectedPost >= 1) ? 1 : $iSelectedPost);

	if ($iLinkPost > 0)
		$iLinkPostCategory = getDbValue("category_id", "tbl_blog_posts", "id='$iLinkPost'");

	if ($iSelectedPost > 0)
		$iSelectedCategory = getDbValue("category_id", "tbl_blog_posts", "id='$iSelectedPost'");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="450">
		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= $sTitle ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddLinkType">Link Type</label>

		  <div>
			<select name="ddLinkType" id="ddLinkType">
			  <option value=""></option>

			  <optgroup label="Picture">
			   <option value="W"<?= (($sLinkType == 'W') ? ' selected' : '') ?>>Web Page</option>
			   <option value="C"<?= (($sLinkType == 'C') ? ' selected' : '') ?>>Blog Category</option>
			   <option value="P"<?= (($sLinkType == 'P') ? ' selected' : '') ?>>Blog Post</option>
			   <option value="U"<?= (($sLinkType == 'U') ? ' selected' : '') ?>>URL</option>
			  </optgroup>

			  <optgroup label="Others">
			    <option value="I"<?= (($sLinkType == 'I') ? ' selected' : '') ?>>Image</option>
			    <option value="F"<?= (($sLinkType == 'F') ? ' selected' : '') ?>>Flash</option>
			    <option value="S"<?= (($sLinkType == 'S') ? ' selected' : '') ?>>Script</option>
			  </optgroup>
			</select>
		  </div>

		  <div id="LinkPage"<?= (($sLinkType == 'W') ? '' : ' class="hidden"') ?>>
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
				<option value="<?= $iPageId ?>"<?= (($iPageId == $iLinkPage) ? ' selected' : '') ?>><?= $sPage ?></option>
<?
	}
?>
			  </select>
			</div>
		  </div>


		  <div id="LinkCategory"<?= (($sLinkType == 'C') ? '' : ' class="hidden"') ?>>
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
				<option value="<?= $iParentId ?>"<?= (($iLinkCategory == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iCategoryId = $objDb->getField($i, "id");
			$sCategory   = $objDb->getField($i, "name");
?>
				<option value="<?= $iCategoryId ?>"<?= (($iLinkCategory == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
		}
	}
?>
			  </select>
			</div>
		  </div>


		  <div id="LinkPost"<?= (($sLinkType == 'P') ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<label for="ddLinkPost">Blog Post</label>

			<div>
			  <select name="ddLinkPostCategory" id="ddLinkPostCategory">
				<option value="">Select Blog Category</option>
<?
	foreach ($sCategoriesList as $iParentId => $sParent)
	{
?>
				<option value="<?= $iParentId ?>"<?= (($iLinkPostCategory == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iCategoryId = $objDb->getField($i, "id");
			$sCategory   = $objDb->getField($i, "name");
?>
				<option value="<?= $iCategoryId ?>"<?= (($iLinkPostCategory == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
		}
	}
?>
			  </select>

			  <div class="br5"></div>

			  <select name="ddLinkPost" id="ddLinkPost">
				<option value="">Select Blog Post</option>
<?
	$sPostsList = getList("tbl_blog_posts", "id", "title", "category_id='$iLinkPostCategory'");

	foreach ($sPostsList as $iPostId => $sPost)
	{
?>
				<option value="<?= $iPostId ?>"<?= (($iPostId == $iLinkPost) ? ' selected' : '') ?>><?= $sPost ?></option>
<?
	}
?>
			  </select>
			</div>
		  </div>


		  <div id="LinkUrl"<?= (($sLinkType == 'U') ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<label for="txtUrl">URL</label>
			<div><input type="text" name="txtUrl" id="txtUrl" value="<?= $sUrl ?>" maxlength="250" size="44" class="textbox" /></div>
		  </div>


		  <div id="LinkFlash"<?= (($sLinkType == 'F') ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<label for="fileFlash">Flash</label>
			<div><?= $sFlash ?></div>
		  </div>


		  <div id="LinkScript"<?= (($sLinkType == 'S') ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<label for="txtScript">Script <span>(banner code)</span></label>
			<div><textarea name="txtScript" id="txtScript" rows="5" style="width:280px;"><?= $sScript ?></textarea></div>
		  </div>

		  <div class="br10"></div>

		  <label for="txtWidth">Width</label>
		  <div><input type="text" name="txtWidth" id="txtWidth" value="<?= $iWidth ?>" maxlength="4" size="10" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtHeight">Height</label>
		  <div><input type="text" name="txtHeight" id="txtHeight" value="<?= $iHeight ?>" maxlength="4" size="10" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
			<select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
			</select>
		  </div>
		</td>

		<td>
		  <label>Placement</label>

		  <div class="multiSelect" style="height:auto;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr>
				<td width="25"><input type="checkbox" name="cbPlacements[]" id="cbHeader" value="H" <?= ((@in_array("H", $sPlacements)) ? 'checked' : '') ?> /></td>
				<td><label for="cbHeader">Header</label></td>
			  </tr>

			  <tr>
				<td><input type="checkbox" name="cbPlacements[]" id="cbFooter" value="F" <?= ((@in_array("F", $sPlacements)) ? 'checked' : '') ?> /></td>
				<td><label for="cbFooter">Footer</label></td>
			  </tr>

			  <tr>
				<td><input type="checkbox" name="cbPlacements[]" id="cbLeftPanel" value="L" <?= ((@in_array("L", $sPlacements)) ? 'checked' : '') ?> /></td>
				<td><label for="cbLeftPanel">Left Panel</label></td>
			  </tr>

			  <tr>
				<td><input type="checkbox" name="cbPlacements[]" id="cbRightPanel" value="R" <?= ((@in_array("R", $sPlacements)) ? 'checked' : '') ?> /></td>
				<td><label for="cbRightPanel">Right Panel</label></td>
			  </tr>
			</table>
		  </div>

		  <div class="br10"></div>

		  <label for="ddPage">Web Page</label>

		  <div>
			<select name="ddPage" id="ddPage">
			  <option value="0"<?= (($iPage == 0) ? ' selected' : '') ?>>All Pages</option>
			  <option value="-1"<?= (($iPage == -1) ? ' selected' : '') ?>>None</option>
			  <option value="" disabled>----------------------------------------</option>
<?
	$sPagesList = getList("tbl_web_pages w", "w.id", "CONCAT(COALESCE((SELECT CONCAT(p.title, ' &raquo; ') FROM tbl_web_pages p WHERE p.id>'0' AND p.id=w.parent_id), ''), w.title)", "w.id>'0'");

	foreach ($sPagesList as $iPageId => $sPage)
	{
?>
			  <option value="<?= $iPageId ?>"<?= (($iPageId == $iPage) ? ' selected' : '') ?>><?= $sPage ?></option>
<?
	}
?>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddCategory">Blog Category</label>

		  <div>
			<select name="ddCategory" id="ddCategory">
			  <option value="0"<?= (($iCategory == 0) ? ' selected' : '') ?>>All Blog Categories</option>
			  <option value="-1"<?= (($iCategory == -1) ? ' selected' : '') ?>>None</option>
			  <option value="" disabled>----------------------------------------</option>
<?
	foreach ($sCategoriesList as $iParentId => $sParent)
	{
?>
			  <option value="<?= $iParentId ?>"<?= (($iCategory == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iCategoryId = $objDb->getField($i, "id");
			$sCategory   = $objDb->getField($i, "name");
?>
			  <option value="<?= $iCategoryId ?>"<?= (($iCategory == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
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
			  <option value="0"<?= (($iPost == 0) ? ' selected' : '') ?>>All Blog Posts</option>
			  <option value="-1"<?= (($iPost == -1) ? ' selected' : '') ?>>None</option>
			  <option value="1"<?= (($iPost == 1) ? ' selected' : '') ?>>Select</option>
			</select>

			<div id="Post" style="display:<?= (($iPost == 1) ? 'block' : 'none') ?>; margin-top:10px; padding-top:10px; border-top:dotted 1px #bbbbbb;">
			  <select name="ddSelectedCategory" id="ddSelectedCategory">
				<option value="">Select Blog Category</option>
<?
	foreach ($sCategoriesList as $iParentId => $sParent)
	{
?>
				<option value="<?= $iParentId ?>"<?= (($iSelectedCategory == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_blog_categories WHERE parent_id='$iParentId' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iCategoryId = $objDb->getField($i, "id");
			$sCategory   = $objDb->getField($i, "name");
?>
				<option value="<?= $iCategoryId ?>"<?= (($iSelectedCategory == $iCategoryId) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sCategory) ?></option>
<?
		}
	}
?>
			  </select>

			  <div class="br5"></div>

			  <select name="ddSelectedPost" id="ddSelectedPost">
				<option value="">Select Blog Post</option>
<?
	$sPostsList = getList("tbl_blog_posts", "id", "title", "category_id='$iSelectedCategory'");

	foreach ($sPostsList as $iPostId => $sPost)
	{
?>
				<option value="<?= $iPostId ?>"<?= (($iPostId == $iSelectedPost) ? ' selected' : '') ?>><?= $sPost ?></option>
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

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>