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

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iInspectionId  = IO::intValue("InspectionId");
	$iMeasurementId = IO::intValue("MeasurementId");
	$iParentId      = IO::intValue("ParentId");

		
	if ($_POST)
		@include("save-inspection-measurement.php");
	

	$sSQL = "SELECT * FROM tbl_inspections WHERE id='$iInspectionId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool     = $objDb->getField(0, "school_id");
	$iStage      = $objDb->getField(0, "stage_id");
	$sInspection = $objDb->getField(0, "title");
	$sDate       = $objDb->getField(0, "date");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-inspection-measurements.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="InspectionId" id="InspectionId" value="<?= $iInspectionId ?>" />
	<input type="hidden" name="MeasurementId" id="MeasurementId" value="<?= $iMeasurementId ?>" />
    <input type="hidden" name="ParentId" id="ParentId" value="<?= $iParentId ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<b style="font-size:13px;">School</b>
	<div style="font-size:13px;"><?= getDbValue("name", "tbl_schools", "id='$iSchool'") ?></div>

	<div class="br10"></div>

	<b style="font-size:13px;">Stage</b>
	<div style="font-size:13px;"><?= getDbValue("name", "tbl_stages", "id='$iStage'") ?></div>

	<div class="br10"></div>

	<b style="font-size:13px;">Inspection Title</b>
	<div style="font-size:13px;"><?= $sInspection ?></div>


<?
	$iNegatives = 0;
	
	if ($iMeasurementId > 0)
	{
		$sSQL = "SELECT parent_id, boq_id, title, multiplier, length, width, height FROM tbl_inspection_measurements WHERE id='$iMeasurementId'";
		$objDb->query($sSQL);

        $iParent     = $objDb->getField(0, "parent_id");
		$iBoqItem    = $objDb->getField(0, "boq_id");
		$sTitle      = $objDb->getField(0, "title");
		$fMultiplier = $objDb->getField(0, "multiplier");
		$fLength     = $objDb->getField(0, "length");
		$fWidth      = $objDb->getField(0, "width");
		$fHeight     = $objDb->getField(0, "height");
		
		
		$iNegatives = getDbValue("COUNT(1)", "tbl_inspection_measurements", "parent_id='$iMeasurementId'");
	}
	
	else if ($iParentId > 0)
		$iBoqItem = getDbValue("boq_id", "tbl_inspection_measurements", "id='$iParentId'");
	
	
	
	if ($iMeasurementId > 0 || $sUserRights["Add"] == "Y")
	{
?>
	<div style="margin-top:20px; background:#fbfbfb; padding:25px; border:dotted 1px #dddddd;">
	  <h3 style="margin:0px 0px 20px 0px;"><?= (($iMeasurementId > 0) ? (($iParentId > 0) ? 'Edit -tive' : 'Edit') : (($iParentId > 0) ? 'Add -tive' : 'Add')) ?> Measurement<?= (($iParentId > 0) ? ' <small style="font-weight:normal; font-size:11px;">(These are measurements that need to be removed from the primary measurement)</small>' : '') ?></h3>

      <label for="ddBoqItem">BOQ Item</label>

      <div>
        <select style="max-width:330px;" <?= (($iParentId > 0 || $iNegatives > 0) ? 'disabled' : 'name="ddBoqItem" id="ddBoqItem"') ?>>
	      <option value=""></option>
<?
		$sSelectedUnit = "";


		$sSQL = "SELECT id, title, unit FROM tbl_boqs ORDER BY title";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iBoq  = $objDb->getField($i, "id");
			$sBoq  = $objDb->getField($i, "title");
			$sUnit = $objDb->getField($i, "unit");
?>
	      <option value="<?= $iBoq ?>" rel="<?= $sUnit ?>"<?= (($iBoq == $iBoqItem) ? ' selected' : '') ?>><?= $sBoq ?></option>
<?
			if ($iBoq == $iBoqItem)
				$sSelectedUnit = $sUnit;
		}
?>
	    </select>
<?
		if ($iParentId > 0 || $iNegatives > 0)
		{
?>
        <input type="hidden" name="ddBoqItem" id="ddBoqItem" rel="<?= $sSelectedUnit ?>" value="<?= $iBoqItem ?>" />
<?
		}
?>
      </div>

      <div class="br10"></div>

      <label for="txtTitle">Title</label>
      <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="200" size="44" class="textbox" /></div>

      <div class="br10"></div>
<?
		if ($iMeasurementId > 0)
		{
			if (@in_array($sSelectedUnit, array("cft", "sft", "rft", "")))
			{
				if (@in_array($sSelectedUnit, array("cft", "sft", "rft")))
				{
					if (@strpos($fLength, ".") !== FALSE)
					{
						$fLengthFeet   = substr($fLength, 0, strpos($fLength, "."));
						$iLengthInches = rtrim(substr($fLength, (strpos($fLength, ".") + 1)), "0");

						if (strlen($iLengthInches) > 3)
							$iLengthInches = substr($iLengthInches, 0, 2);

						if ($iLengthInches > 0)
						{
							$iLengthInches = @round(($iLengthInches * 0.1) * 12);
							$iLengthInches = rtrim($iLengthInches, "0");
						}
					}

					else
					{
						$fLengthFeet   = $fLength;
						$iLengthInches = 0;
					}
				}


				if (@in_array($sSelectedUnit, array("cft", "sft")))
				{
					if (@strpos($fWidth, ".") !== FALSE)
					{
						$fWidthFeet   = substr($fWidth, 0, strpos($fWidth, "."));
						$iWidthInches = rtrim(substr($fWidth, (strpos($fWidth, ".") + 1)), "0");

						if (strlen($iWidthInches) > 3)
							$iWidthInches = substr($iWidthInches, 0, 2);

						if ($iWidthInches > 0)
						{
							$iWidthInches = @round(($iWidthInches * 0.1) * 12);
							$iWidthInches = rtrim($iWidthInches, "0");
						}
					}

					else
					{
						$fWidthFeet   = $fWidth;
						$iWidthInches = 0;
					}
				}


				if ($sSelectedUnit == "cft")
				{
					if (@strpos($fHeight, ".") !== FALSE)
					{
						$fHeightFeet   = substr($fHeight, 0, strpos($fHeight, "."));
						$iHeightInches = rtrim(substr($fHeight, (strpos($fHeight, ".") + 1)), "0");

						if (strlen($iHeightInches) > 3)
							$iHeightInches = substr($iHeightInches, 0, 2);

						if ($iHeightInches > 0)
						{
							$iHeightInches = @round(($iHeightInches * 0.1) * 12);
							$iHeightInches = rtrim($iHeightInches, "0");
						}
					}

					else
					{
						$fHeightFeet   = $fHeight;
						$iHeightInches = 0;
					}
				}
			}

			else
				$fLengthFeet = $fLength;
		}
?>
      <label for="txtLengthFeet" id="lblLength"><?= ((!@in_array($sSelectedUnit, array("cft", "sft", "rft", ""))) ? strtoupper($sSelectedUnit) : 'Length') ?></label>

      <div>
        <input type="text" name="txtLengthFeet" id="txtLengthFeet" value="<?= $fLengthFeet ?>" maxlength="8" size="8" class="textbox" placeholder="<?= ((!@in_array($sSelectedUnit, array("cft", "sft", "rft", ""))) ? '' : 'Feet') ?>" />
        <input type="text" name="txtLengthInches" id="txtLengthInches" value="<?= $iLengthInches ?>" maxlength="6" size="5" class="textbox<?= ((!@in_array($sSelectedUnit, array("cft", "sft", "rft", ""))) ? ' hidden' : '') ?>" placeholder="Inches" />
      </div>

      <div id="Width"<?= (($sSelectedUnit != "cft" && $sSelectedUnit != "sft" && $sSelectedUnit != "") ? ' class="hidden"' : "") ?>>
        <div class="br10"></div>

        <label for="txtWidthFeet">Width</label>

        <div>
          <input type="text" name="txtWidthFeet" id="txtWidthFeet" value="<?= $fWidthFeet ?>" maxlength="8" size="8" class="textbox" placeholder="Feet" />
          <input type="text" name="txtWidthInches" id="txtWidthInches" value="<?= $iWidthInches ?>" maxlength="6" size="5" class="textbox" placeholder="Inches" />
        </div>
      </div>

      <div id="Height"<?= (($sSelectedUnit != "cft" && $sSelectedUnit != "") ? ' class="hidden"' : "") ?>>
        <div class="br10"></div>

        <label for="txtHeightFeet">Height</label>

        <div>
          <input type="text" name="txtHeightFeet" id="txtHeightFeet" value="<?= $fHeightFeet ?>" maxlength="8" size="8" class="textbox" placeholder="Feet" />
          <input type="text" name="txtHeightInches" id="txtHeightInches" value="<?= $iHeightInches ?>" maxlength="6" size="5" class="textbox" placeholder="Inches" />
        </div>
      </div>

	  <div class="br10"></div>

	  <label for="txtMultiplier">Multiplier</label>
	  <div><input type="text" name="txtMultiplier" id="txtMultiplier" value="<?= (($fMultiplier <= 0) ? 1 : $fMultiplier) ?>" maxlength="5" size="8" class="textbox" /></div>

	  <br />
	  <button id="BtnSave"><?= (($iMeasurementId > 0) ? 'Update' : 'Save') ?> Measurement</button>
<?
		if ($iMeasurementId > 0 || $iParentId > 0)
		{
?>
	  <button id="BtnBack" rel="<?= ($sCurDir."/edit-inspection-measurements.php?InspectionId=".$iInspectionId) ?>">Cancel</button>
<?
		}
		
		else
		{
?>
	  <button id="BtnCancel">Close</button>
<?
		}
?>
	</div>
  </form>

<?
	}
	
	
	
	
	$sSQL = "SELECT b.id, b.title, b.unit,
	                im.id, im.title, im.multiplier, im.length, im.width, im.height, im.measurements, im.amount
	         FROM tbl_inspection_measurements im, tbl_boqs b
	         WHERE b.id=im.boq_id AND im.inspection_id='$iInspectionId' AND im.parent_id='0'
	         ORDER BY im.id";
	$objDb->query($sSQL);

	$iCount     = $objDb->getCount( );
	$fNetAmount = 0;
                
	if ($iCount > 0)
	{
		$iContract = getDbValue("id", "tbl_contracts", "FIND_IN_SET('$iSchool', schools) AND ('$sDate' BETWEEN start_date AND end_date)", "id DESC");
?>
    <hr />
    <h3>Measurements</h3>

    <div class="grid">
	  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	    <tr class="header" valign="top">
		  <td width="5%">#</td>
		  <td width="22%">BOQ Item</td>
		  <td width="18%">Title</td>
		  <td width="18%">Measurements</td>
		  <td width="9%">Calculated</td>
		  <td width="9%">Rate</td>
		  <td width="9%">Amount</td>
		  <td width="10%">Options</td>
	    </tr>
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId           = $objDb->getField($i, "im.id");
			$iBoqItem      = $objDb->getField($i, "b.id");
			$sBoqItem      = $objDb->getField($i, "b.title");
			$sUnit         = $objDb->getField($i, "unit");
			$sTitle        = $objDb->getField($i, "im.title");
			$fMultiplier   = $objDb->getField($i, "multiplier");
			$fLength       = $objDb->getField($i, "length");
			$fWidth        = $objDb->getField($i, "width");
			$fHeight       = $objDb->getField($i, "height");
			$fMeasurements = $objDb->getField($i, "measurements");
			$fAmount       = $objDb->getField($i, "amount");
			
			

			$fTotalMeasurements = $fMeasurements;
			$fTotalAmount       = $fAmount;

			
			$sSQL = "SELECT b.title, b.unit,
							im.id, im.title, im.multiplier, im.length, im.width, im.height, im.measurements, im.amount
					 FROM tbl_inspection_measurements im, tbl_boqs b
					 WHERE b.id=im.boq_id AND im.inspection_id='$iInspectionId' AND im.parent_id='$iId'
					 ORDER BY im.id";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );
			$iParent = $iId;
			$fRate   = getDbValue("rate", "tbl_contract_boqs", "contract_id='$iContract' AND boq_id='$iBoqItem'");
?>

		  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		    <td><?= ($i + 1) ?></td>
		    <td><?= $sBoqItem ?></td>
		    <td><?= $sTitle ?></td>
		    <td><?= (($fMultiplier > 1) ? "{$fMultiplier} x (" : "") ?><?= formatNumber($fLength) ?><?= (($sUnit == "cft" || $sUnit == "sft") ? (" x ".formatNumber($fWidth)) : "") ?><?= (($sUnit == "cft") ? (" x ".formatNumber($fHeight)) : "") ?> <?= $sUnit ?><?= (($fMultiplier > 1) ? ")" : "") ?></td>
			<td><?= formatNumber($fMeasurements) ?></td>
			<td><?= formatNumber($fRate) ?></td>
			<td><?= formatNumber($fAmount, false) ?></td>

		    <td>
		      <a href="<?= $sCurDir ?>/edit-inspection-measurements.php?InspectionId=<?= $iInspectionId ?>&MeasurementId=<?= $iId ?>"><img src="images/icons/edit.gif" hspace="2" alt="Edit" title="Edit" /></a>
<?
			if ($sUserRights["Delete"] == "Y" && $iCount2 == 0)
			{
?>
		      <a href="<?= $sCurDir ?>/delete-inspection-measurements.php?InspectionId=<?= $iInspectionId ?>&MeasurementId=<?= $iId ?>"><img src="images/icons/delete.gif" hspace="2" alt="Delete" title="Delete" /></a>
<?
			}
			
			if ($sUserRights["Add"] == "Y")
			{
?>
              <a href="<?= $sCurDir ?>/edit-inspection-measurements.php?InspectionId=<?= $iInspectionId ?>&ParentId=<?= $iId ?>"><img src="images/icons/negative.png" hspace="2" alt="Add -tive Measurement" title="Add -tive Measurement" /></a>
<?                      
			}
?>
            </td>
		  </tr>
<?
			for ($j = 0; $j < $iCount2; $j ++)
			{
				$iId           = $objDb2->getField($j, "id");
				$sUnit         = $objDb2->getField($j, "unit");
				$sTitle        = $objDb2->getField($j, "im.title");
				$fMultiplier   = $objDb2->getField($j, "multiplier");
				$fLength       = $objDb2->getField($j, "length");
				$fWidth        = $objDb2->getField($j, "width");
				$fHeight       = $objDb2->getField($j, "height");
				$fMeasurements = $objDb2->getField($j, "measurements");
				$fAmount       = $objDb2->getField($j, "amount");
?>

		  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		    <td></td>
		    <td>-</td>
		    <td><?= $sTitle ?></td>
		    <td><?= (($fMultiplier > 1) ? "{$fMultiplier} x (" : "") ?><?= formatNumber($fLength) ?><?= (($sUnit == "cft" || $sUnit == "sft") ? (" x ".formatNumber($fWidth)) : "") ?><?= (($sUnit == "cft") ? (" x ".formatNumber($fHeight)) : "") ?> <?= $sUnit ?><?= (($fMultiplier > 1) ? ")" : "") ?></td>
			<td><?= formatNumber($fMeasurements) ?></td>
			<td><?= formatNumber($fRate) ?></td>
			<td><?= formatNumber($fAmount, false) ?></td>

		    <td>
		      <a href="<?= $sCurDir ?>/edit-inspection-measurements.php?InspectionId=<?= $iInspectionId ?>&MeasurementId=<?= $iId ?>&ParentId=<?= $iParent ?>"><img src="images/icons/edit.gif" hspace="2" alt="Edit" title="Edit" /></a>
<?
				if ($sUserRights["Delete"] == "Y")
				{
?>
		      <a href="<?= $sCurDir ?>/delete-inspection-measurements.php?InspectionId=<?= $iInspectionId ?>&MeasurementId=<?= $iId ?>&ParentId=<?= $iParent ?>"><img src="images/icons/delete.gif" hspace="2" alt="Delete" title="Delete" /></a>
<?
				}
?>
            </td>
		  </tr>
<?
				$fTotalMeasurements -= $fMeasurements;
				$fTotalAmount       += $fAmount;
			}
			
			
			if ($iCount2 > 0)
			{
?>

		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td><?= formatNumber($fTotalMeasurements) ?></td>
		  <td></td>
		  <td><?= formatNumber($fTotalAmount, false) ?></td>
		  <td></td>
		</tr>
<?
			}
			
			
			$fNetAmount += $fTotalAmount;			
		}
?>

		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td colspan="6" align="right"><b>Net Total</b> &nbsp; </td>
		  <td colspan="2"><b><?= formatNumber($fNetAmount, false) ?></b></td>
		</tr>
      </table>
    </div>

    <div class="br5"></div>
<?
	}
?>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>