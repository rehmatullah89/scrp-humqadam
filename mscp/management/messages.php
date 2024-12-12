<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.3-tree.com/imc/                                                               **
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/messages.js"></script>
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

	  <div id="ConfirmDelete" title="Delete Message?" class="hidden dlgConfirm">
		<span class="ui-icon ui-icon-trash"></span>
		Are you sure, you want to Delete this Message?<br />
	  </div>

	  <div id="ConfirmMultiDelete" title="Delete Messages?" class="hidden dlgConfirm">
		<span class="ui-icon ui-icon-trash"></span>
		Are you sure, you want to Delete the selected Messages?<br />
	  </div>


	  <div class="dataGrid ex_highlight_row">
		<input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_web_messages') ?>" />
		<input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
		  <thead>
			<tr>
			  <th width="5%">#</th>
			  <th width="20%">Name</th>
			  <th width="20%">Email</th>
			  <th width="12%">Phone</th>
			  <th width="21%">Subject</th>
			  <th width="14%">Date/Time</th>
			  <th width="8%">Options</th>
			</tr>
		  </thead>

		  <tbody>
<?
	if ($iTotalRecords <= 100)
	{
		$sSQL = "SELECT * FROM tbl_web_messages ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId       = $objDb->getField($i, "id");
			$sName     = $objDb->getField($i, "name");
			$sEmail    = $objDb->getField($i, "email");
			$sPhone    = $objDb->getField($i, "phone");
			$sSubject  = $objDb->getField($i, "subject");
			$sDateTime = $objDb->getField($i, "date_time");
?>
			<tr id="<?= $iId ?>">
			  <td class="position"><?= ($i + 1) ?></td>
			  <td><?= $sName ?></td>
			  <td><?= $sEmail ?></td>
			  <td><?= $sPhone ?></td>
			  <td><?= $sSubject ?></td>
			  <td><?= formatDate($sDateTime, "{$_SESSION["DateFormat"]} {$_SESSION["TimeFormat"]}") ?></td>

			  <td>
<?
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