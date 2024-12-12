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

	$iInspector = IO::intValue("Inspector");
	$sStatus    = IO::strValue("Status");
	$iProvince  = IO::intValue("Province");
	$iDistrict  = IO::intValue("District");
	$sFromDate  = IO::strValue("FromDate");
	$sToDate    = IO::strValue("ToDate");	
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
  <h3><?= getDbValue("name", "tbl_admins", "id='$iInspector'") ?></h3>
  <b><?= (($sStatus == "P") ? "Passed Inspections" : (($sStatus == "R") ? "Re-Inspections" : "Failed Inspections")) ?></b><br />
  <br />

  <div class="grid">
	<table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	  <tr class="header" valign="top">
		<td width="50">#</td>
		<td>Stage</td>
		<td width="300">School</td>
		<td width="100">Code</td>
		<td width="85">Date</td>
	  </tr>
<?
	$sConditions = " WHERE s.id=i.school_id AND i.admin_id='$iInspector' AND i.status='$sStatus' AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND FIND_IN_SET(s.id, '{$_SESSION['AdminSchools']}') ";

	if ($iProvince > 0)
		$sConditions .= " AND s.province_id='$iProvince' ";

	if ($iDistrict > 0)
		$sConditions .= " AND s.district_id='$iDistrict' ";
	
	if ($sFromDate != "" && $sToDate != "")
		$sConditions .= "  AND (i.date BETWEEN '$sFromDate' AND '$sToDate') ";
	
	else
		$sConditions .= "  AND DATEDIFF(NOW( ), i.date) <= '7' ";
	
	
	$sSQL = "SELECT i.id, i.date,
	                s.code, s.name,
	                (SELECT name FROM tbl_stages WHERE id=i.stage_id) AS _Stage
	         FROM tbl_inspections i, tbl_schools s
	         $sConditions
	         ORDER BY i.date DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId     = $objDb->getField($i, "id");
		$sStage  = $objDb->getField($i, "_Stage");
		$sDate   = $objDb->getField($i, "date");
		$sCode   = $objDb->getField($i, "code");
		$sSchool = $objDb->getField($i, "name");
?>

		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td><a href="inspection-details.php?Id=<?= $iId ?>" class="inspection"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></a></td>
		  <td><?= $sStage ?></td>
		  <td><?= $sSchool ?></td>
		  <td><?= $sCode ?></td>
		  <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
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