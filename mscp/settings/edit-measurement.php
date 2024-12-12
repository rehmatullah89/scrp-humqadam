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
	$objDb3      = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iStageId = IO::intValue("StageId");
	$iIndex   = IO::intValue("Index");

	if ($_POST)
		@include("save-measurement.php");


	$sSQL = "SELECT * FROM tbl_stages WHERE id='$iStageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sName     = $objDb->getField(0, "name");
	$iParent   = $objDb->getField(0, "parent_id");
	$sUnit     = $objDb->getField(0, "unit");
	$fQuantity = $objDb->getField(0, "baseline_qty");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-measurement.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="StageId" id="StageId" value="<?= $iStageId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<div id="Parent"<?= (($iParent > 0) ? '' : ' class="hidden"') ?>>
      <label for="ddParent">Parent Stage</label>

      <div>
	    <select name="ddParent" id="ddParent" style="width:91%; max-width:91%;" disabled>
	      <option value=""></option>
<?
	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND id!='$iStageId' ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParentId = $objDb->getField($i, "id");
		$sParent   = $objDb->getField($i, "name");
?>
			  <option value="<?= $iParentId ?>"<?= (($iParent == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParentId' AND id!='$iStageId' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");
			$sStage = $objDb2->getField($j, "name");
?>
	    	  <option value="<?= $iStage ?>"<?= (($iParent == $iStage) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sStage) ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' AND id!='$iStageId' ORDER BY name";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");
?>
	    	  <option value="<?= $iSubStage ?>"<?= (($iParent == $iSubStage) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage) ?></option>
<?
			}
		}
	}
?>
	    </select>
      </div>
	</div>

    <div class="br10"></div>

    <label for="txtName">Stage Name</label>
    <div><input type="text" name="txtName" id="txtName" value="<?= formValue($sName) ?>" maxlength="100" size="35" class="textbox" style="width:90%;" disabled /></div>

	<div class="br10"></div>

	<label for="ddUnit">Measurement Unit</label>

	<div>
	  <select name="ddUnit" id="ddUnit" disabled>
		<option value=""<?= (($sUnit == '') ? ' selected' : '') ?>></option>
		<option value="cft"<?= (($sUnit == 'cft') ? ' selected' : '') ?>>cft</option>
		<option value="sft"<?= (($sUnit == 'sft') ? ' selected' : '') ?>>sft</option>
		<option value="Kg"<?= (($sUnit == 'Kg') ? ' selected' : '') ?>>Kg</option>
		<option value="No"<?= (($sUnit == 'No') ? ' selected' : '') ?>>No</option>
	  </select>
	</div>

    <div class="br10"></div>

    <label for="txtQuantity">Baseline Quantity</label>
    <div><input type="text" name="txtQuantity" id="txtQuantity" value="<?= $fQuantity ?>" maxlength="10" size="15" class="textbox" /></div>

    <br />
    <button id="BtnSave">Save Measurement</button>
    <button id="BtnCancel">Cancel</button>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>