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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/meta-tags.js"></script>
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
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Web Pages</b></a></li>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Districts</a></li>
	    </ul>


	    <div id="tabs-1">
	      <div id="WebPagesGridMsg" class="hidden"></div>

		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="WebPagesGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="20%">Page</th>
			      <th width="55%">Title Tag</th>
			      <th width="10%">Status</th>
			      <th width="10%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT * FROM tbl_web_pages ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId       = $objDb->getField($i, "id");
		$sPage     = $objDb->getField($i, "title");
		$sTitleTag = $objDb->getField($i, "title_tag");
		$sStatus   = $objDb->getField($i, "status");
?>
		        <tr valign="top">
		          <td><?= ($i + 1) ?></td>
		          <td><?= $sPage ?></td>
		          <td><?= $sTitleTag ?></td>
		          <td><?= (($sStatus == "P") ? "Published" : "Draft") ?></td>

		          <td>
<?
		if ($sUserRights["Edit"] == "Y")
		{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
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
		</div>


		<div id="tabs-2">
		  <input type="hidden" id="DistrictRecords" value="<?= $iDistrictRecords = getDbValue('COUNT(1)', 'tbl_districts') ?>" />

	      <div id="DistrictsGridMsg" class="hidden"></div>

		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DistrictsGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="35%">District</th>
			      <th width="40%">Title Tag</th>
			      <th width="10%">Status</th>
			      <th width="10%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iDistrictRecords <= 100)
	{
		$sSQL = "SELECT id, name, title_tag, status FROM tbl_districts ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId       = $objDb->getField($i, "id");
			$sDistrict = $objDb->getField($i, "name");
			$sTitleTag = $objDb->getField($i, "title_tag");
			$sStatus   = $objDb->getField($i, "status");
?>
		        <tr valign="top">
		          <td><?= ($i + 1) ?></td>
		          <td><?= $sDistrict ?></td>
		          <td><?= $sTitleTag ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
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
		</div>
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