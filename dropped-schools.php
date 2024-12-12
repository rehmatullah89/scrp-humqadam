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
  <h3>Dropped Schools</h3>

  <div class="grid">
    <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	  <tr class="header" valign="top">
	    <td width="40">#</td>
	    <td width="100">EMIS Code</td>
	    <td width="150">District</td>
	    <td>School</td>
	  </tr>
<?
	$sDistrictsList = getList("tbl_districts", "id", "name");


	$sSQL = "SELECT district_id, name, code FROM tbl_schools WHERE dropped='Y' ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iDistrict = $objDb->getField($i, "district_id");
		$sName     = $objDb->getField($i, "name");
		$sCode     = $objDb->getField($i, "code");
?>

	  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		<td><?= ($i + 1) ?></td>
		<td><?= $sCode ?></td>
		<td><?= $sDistrictsList[$iDistrict] ?></td>
		<td><?= $sName ?></td>
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