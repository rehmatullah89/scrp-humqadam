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


	$sAction = IO::strValue("Action");

	if ($_POST)
		@include($sAction);
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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/faqs.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>FAQs</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New FAQ</a></li>
<?
	}
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-3">Categories</a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-4">Add New Category</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="FaqsGridMsg" class="hidden"></div>

	      <div id="ConfirmFaqDelete" title="Delete FAQ?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this FAQ?<br />
	      </div>

	      <div id="ConfirmMultiFaqDelete" title="Delete FAQs?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected FAQs?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="FaqsGrid" rel="tbl_faqs">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="45%">Question</th>
			      <th width="20%">Category</th>
			      <th width="15%">Status</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sCategories = getList("tbl_faq_categories", "id", "name");


	$sSQL = "SELECT * FROM tbl_faqs ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId       = $objDb->getField($i, "id");
		$iCategory = $objDb->getField($i, "category_id");
		$sQuestion = $objDb->getField($i, "question");
		$sStatus   = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>" valign="top">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= nl2br($sQuestion) ?></td>
		          <td><?= $sCategories[$iCategory] ?></td>
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


		  <div id="SelectFaqButtons"<?= (($iCount > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<div align="right">
			  <button id="BtnFaqSelectAll">Select All</button>
			  <button id="BtnFaqSelectNone">Clear Selection</button>
			</div>
		  </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmFaq" id="frmFaq" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="Action" value="save-faq.php" />
		    <input type="hidden" name="OpenTab" value="1" />
			<div id="FaqMsg" class="hidden"></div>

		    <label for="txtQuestion">Question</label>
		    <div><textarea name="txtQuestion" id="txtQuestion" rows="3" style="width:99.2%;"><?= IO::strValue('txtQuestion') ?></textarea></div>

			<br />
			<label for="txtAnswer">Answer</label>
			<div><textarea name="txtAnswer" id="txtAnswer" style="width:100%; height:300px;"><?= IO::strValue('txtAnswer') ?></textarea></div>

		    <div class="br10"></div>

			<label for="ddCategory">Category <span>(Optional)</span></label>

			<div>
			  <select name="ddCategory" id="ddCategory">
				<option value=""></option>
<?
		foreach ($sCategories as $iCategory => $sCategory)
		{
?>
				<option value="<?= $iCategory ?>"<?= ((IO::intValue('ddCategory') == $iCategory) ? ' selected' : '') ?>><?= $sCategory ?></option>
<?
		}
?>
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
		    <button id="BtnSave">Save FAQ</button>
		    <button id="BtnReset">Clear</button>
		  </form>
	    </div>
<?
	}
?>


	    <div id="tabs-3">
	      <div id="CategoriesGridMsg" class="hidden"></div>

	      <div id="ConfirmCategoryDelete" title="Delete Category?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Category?<br />
	      </div>

	      <div id="ConfirmMultiCategoryDelete" title="Delete Categories?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Categories?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="CategoriesGrid" rel="tbl_faq_categories">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="65%">Name</th>
			      <th width="15%">Status</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_faq_categories ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sName   = $objDb->getField($i, "name");
		$sStatus = $objDb->getField($i, "status");
?>
		        <tr id="<?= $iId ?>" valign="top">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sName ?></td>
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


		  <div id="SelectCategoryButtons"<?= (($iTotalRecords > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
			<div class="br10"></div>

			<div align="right">
			  <button id="BtnCategorySelectAll">Select All</button>
			  <button id="BtnCategorySelectNone">Clear Selection</button>
			</div>
		  </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-4">
		  <form name="frmCategory" id="frmCategory" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="Action" value="save-faq-category.php" />
		    <input type="hidden" name="OpenTab" value="3" />
		    <input type="hidden" name="DuplicateCategory" id="DuplicateCategory" value="0" />
			<div id="CategoryMsg" class="hidden"></div>

		    <label for="txtName">Name</label>
		    <div><input type="text" name="txtName" id="txtName" value="<?= IO::strValue('txtName', true) ?>" maxlength="100" size="53" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="txtDescription">Description</label>
		    <div><textarea name="txtDescription" id="txtDescription" rows="8" cols="50"><?= IO::strValue('txtDescription') ?></textarea></div>

			<div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
			    <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
			  </select>
		    </div>

		    <br />
		    <button id="BtnSave">Save Category</button>
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