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
		@include("update-stage.php");


	$sSQL = "SELECT * FROM tbl_stages WHERE id='$iStageId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sName      = $objDb->getField(0, "name");
	$iParent    = $objDb->getField(0, "parent_id");
	$sUnit      = $objDb->getField(0, "unit");
	$fWeightage = $objDb->getField(0, "weightage");
	$iDays      = $objDb->getField(0, "days");
	$sRerasons  = $objDb->getField(0, "failure_reasons");
	$sSkip      = $objDb->getField(0, "skip");
	$sType      = $objDb->getField(0, "type");
	$sWork      = $objDb->getField(0, "work");
	$iPosition  = $objDb->getField(0, "position");
	$sStatus    = $objDb->getField(0, "status");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-stage.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="StageId" id="StageId" value="<?= $iStageId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateStage" id="DuplicateStage" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<label for="ddType">School Type</label>

	<div>
	  <select name="ddType" id="ddType">
		<option value=""></option>
		<option value="S"<?= ($sType == 'S' ? ' selected' : '') ?>>Single Storey</option>
		<option value="D"<?= ($sType == 'D' ? ' selected' : '') ?>>Double Storey</option>
		<option value="T"<?= ($sType == 'T' ? ' selected' : '') ?>>Triple Storey</option>
		<option value="B"<?= ($sType == 'B' ? ' selected' : '') ?>>Bespoke</option>
	  </select>
	</div>
	
    <div class="br10"></div>

    <label for="ddWorkType">Work Type</label>

    <div>
	  <select name="ddWorkType" id="ddWorkType">
	    <option value="B"<?= (($sWorkType == 'B') ? ' selected' : '') ?>>New Construction & Rehabilitation</option>
	    <option value="N"<?= (($sWorkType == 'N') ? ' selected' : '') ?>>New Construction</option>
	    <option value="R"<?= (($sWorkType == 'R') ? ' selected' : '') ?>>Rehabilitation Only</option>
	  </select>
    </div>

    <div class="br10"></div>
	
	<label for="ddNature">Stage Nature</label>

	<div>
	  <select name="ddNature" id="ddNature">
		<option value="P"<?= (($iParent == 0) ? ' selected' : '') ?>>Parent Stage</option>
		<option value="S"<?= (($iParent > 0) ? ' selected' : '') ?>>Sub Stage</option>
	  </select>
	</div>

	<div id="Parent"<?= (($iParent > 0) ? '' : ' class="hidden"') ?>>
      <div class="br10"></div>

      <label for="ddParent">Parent Stage</label>

      <div>
	    <select name="ddParent" id="ddParent" style="width:91%; max-width:91%;">
	      <option value=""></option>
<?
	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='$sType' AND id!='$iStageId' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParentId = $objDb->getField($i, "id");
		$sParent   = $objDb->getField($i, "name");
?>
			  <option value="<?= $iParentId ?>"<?= (($iParent == $iParentId) ? ' selected' : '') ?>><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParentId' AND id!='$iStageId' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");
			$sStage = $objDb2->getField($j, "name");
?>
	    	  <option value="<?= $iStage ?>"<?= (($iParent == $iStage) ? ' selected' : '') ?>><?= ($sParent." &raquo; ".$sStage) ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' AND id!='$iStageId' ORDER BY position";
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
    <div><input type="text" name="txtName" id="txtName" value="<?= formValue($sName) ?>" maxlength="100" size="35" class="textbox" style="width:90%;" /></div>

	<div class="br10"></div>

	<label for="ddUnit">Measurement Unit <span>(Optional)</span></label>

	<div>
	  <select name="ddUnit" id="ddUnit">
		<option value=""<?= (($sUnit == '') ? ' selected' : '') ?>></option>
		<option value="cft"<?= (($sUnit == 'cft') ? ' selected' : '') ?>>cft</option>
		<option value="sft"<?= (($sUnit == 'sft') ? ' selected' : '') ?>>sft</option>
		<option value="Kg"<?= (($sUnit == 'Kg') ? ' selected' : '') ?>>Kg</option>
		<option value="No"<?= (($sUnit == 'No') ? ' selected' : '') ?>>No</option>
	  </select>
	</div>
	
	<div class="br10"></div>

    <label for="txtWeightage">Weightage <span>(Optional)</span></label>
    <div><input type="text" name="txtWeightage" id="txtWeightage" value="<?= $fWeightage ?>" maxlength="10" size="10" class="textbox" /></div>

	<div class="br10"></div>

	<label for="txtDays">Activity Duration <span>(Days)</span></label>
	<div><input type="text" name="txtDays" id="txtDays" value="<?= $iDays ?>" maxlength="10" size="10" class="textbox" /></div>

	<div class="br10"></div>

	<label for="">Failure Reasons <span>(Optional)</span></label>

	<div class="multiSelect" style="width:350px; height:200px;">
	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
	$iReasons     = @explode(",", $sRerasons);
	$sReasonsList = getList("tbl_failure_reasons", "id", "reason");

	foreach ($sReasonsList as $iReason => $sReason)
	{
?>
		<tr valign="top">
		  <td width="25"><input type="checkbox" class="reason" name="cbReasons[]" id="cbReason<?= $iReason ?>" value="<?= $iReason ?>" <?= ((@in_array($iReason, $iReasons)) ? 'checked' : '') ?> /></td>
		  <td><label for="cbReason<?= $iReason ?>"><?= $sReason ?></label></td>
		</tr>
<?
	}
?>
	  </table>
	</div>
	
	<div class="br10"></div>
	
	<label for="cbSkip" class="noPadding"><input type="checkbox" name="cbSkip" id="cbSkip" value="Y" <?= (($sSkip == 'Y') ? 'checked' : '') ?> /> Skip in Progress Calculations</label>			
	
	<div class="br10"></div>

	<label for="txtPosition">Position</label>
	
	<div>
	  <input type="text" name="txtPosition" id="txtPosition" value="<?= $iPosition ?>" maxlength="10" size="10" class="textbox" />
	  
	  <label for="cbAdjust" class="noPadding" style="display:inline-block; font-size:11px;"><input type="checkbox" name="cbAdjust" id="cbAdjust" value="Y" checked /> (Adjust Stages Position)</label>			
	</div>

	<div class="br10"></div>

    <label for="ddStatus">Status</label>

    <div>
	  <select name="ddStatus" id="ddStatus">
	    <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
  	    <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
    </div>

    <br />
    <button id="BtnSave">Save Stage</button>
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