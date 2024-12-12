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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$iContract = IO::intValue("Contract");
	$sList     = IO::strValue("List");
	$sSchools  = getDbValue("schools", "tbl_contracts", "id='$iContract'");


	if ($sList == "Y")
		$sSQL = "SELECT id, name, code FROM tbl_schools WHERE FIND_IN_SET(id, '$sSchools') AND id IN (SELECT school_id FROM tbl_contract_schedules WHERE contract_id='$iContract') ORDER BY name";

	else
		$sSQL = "SELECT id, name, code FROM tbl_schools WHERE FIND_IN_SET(id, '$sSchools') ORDER BY name";

	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>
		<option value=""></option>
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId   = $objDb->getField($i, "id");
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");
?>
		<option value="<?= $iId ?>"><?= "{$sCode} - {$sName}" ?></option>
<?
	}


	if ($sList == "Y")
	{
		print "|-|";
?>
			  <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
		$sSQL = "SELECT id, CONCAT(code, ' - ', name) AS _Name
		         FROM tbl_schools
		         WHERE status='A' AND dropped!='Y' AND FIND_IN_SET(id, '$sSchools')
		               AND id NOT IN (SELECT school_id FROM tbl_contract_schedules WHERE contract_id='$iContract')
		         ORDER BY _Name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSchool = $objDb->getField($i, "id");
			$sSchool = $objDb->getField($i, "_Name");
?>
			    <tr valign="top">
				  <td width="25"><input type="checkbox" class="school" name="cbSchools[]" id="cbSchool<?= $iSchool ?>" value="<?= $iSchool ?>" <?= ((@in_array($iSchool, IO::getArray('cbSchools'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbSchool<?= $iSchool ?>"><?= $sSchool ?></label></td>
			    </tr>
<?
		}
?>
			  </table>
<?
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>