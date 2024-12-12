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

	$iScheduleId = IO::intValue("ScheduleId");


	$sSQL = "SELECT * FROM tbl_survey_schedules WHERE id='$iScheduleId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool     = $objDb->getField(0, "school_id");
	$iEnumerator = $objDb->getField(0, "admin_id");
	$sDate       = $objDb->getField(0, "date");
	$sStatus     = $objDb->getField(0, "status");
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
  <form name="frmRecord" id="frmRecord">
	<label for="ddEnumerator">Enumerator</label>
	
	<div>
	  <select name="ddEnumerator" id="ddEnumerator">
		<option value=""></option>
<?
	$sEnumeratorsList = getList("tbl_admins", "id", "name", "(status='A' OR id='$iEnumerator') AND type_id='12'");
	
	foreach ($sEnumeratorsList as $iEnumeratorId => $sEnumerator)
	{
?>
		<option value="<?= $iEnumeratorId ?>"<?= (($iEnumeratorId == $iEnumerator) ? ' selected' : '') ?>><?= $sEnumerator ?></option>
<?
	}
?>    
	  </select>
	</div>

	<div class="br10"></div>

	<label for="txtCode">EMIS Code</label>
	<div><input type="text" name="txtCode" id="txtCode" value="<?= getDbValue("code", "tbl_schools", "id='$iSchool'") ?>" maxlength="10" size="20" class="textbox" /></div>

	<div class="br10"></div>
	
	<label for="txtDate">Date</label>
	<div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

	<div class="br10"></div>

    <label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="C"<?= (($sStatus == 'C') ? ' selected' : '') ?>>Completed</option>
		<option value="P"<?= (($sStatus == 'P') ? ' selected' : '') ?>>Pending</option>
	  </select>
	</div>
  </form>
</div>


</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>