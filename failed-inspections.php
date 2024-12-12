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

	@require_once("requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	if ($_SESSION['AdminId'] == "")
		exitPopup("info", "Please login into your account to access the requested section.");


	@include("includes/meta-tags.php");
?>
</head>

<body class="popupBg">

<div id="PopupDiv">
  <h3>Failed Inspections ( Last 30 Days )</h3>
  <br />

  <div class="grid">
	<table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	  <tr class="header" valign="top">
		<td width="55">#</td>
		<td width="100">School</td>
		<td width="300">Inspector</td>
		<td width="90">Date</td>
		<td>Stage</td>
	  </tr>
<?
	$sSQL = "SELECT id, `date`,
	                (SELECT name FROM tbl_admins WHERE id=tbl_inspections.admin_id) AS _Inspector,
	                (SELECT `code` FROM tbl_schools WHERE id=tbl_inspections.school_id) AS _Code,
	                (SELECT name FROM tbl_stages WHERE id=tbl_inspections.stage_id) AS _Stage
	         FROM tbl_inspections
	         WHERE status='F' AND DATEDIFF(NOW( ), `date`) <= '30'
	         ORDER BY `date` DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId        = $objDb->getField($i, "id");
		$sDate      = $objDb->getField($i, "date");
		$sStage     = $objDb->getField($i, "_Stage");
		$sCode      = $objDb->getField($i, "_Code");
		$sInspector = $objDb->getField($i, "_Inspector");
?>

		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td><a href="inspection-details.php?Id=<?= $iId ?>" class="inspection"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></a></td>
		  <td><?= $sCode ?></td>
		  <td><?= $sInspector  ?></td>
		  <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
		  <td><?= $sStage ?></td>
		</tr>
<?
	}
?>
	</table>
  </div>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>