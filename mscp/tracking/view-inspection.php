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
	$objDb4      = new Database( );

	$iInspectionId = IO::intValue("InspectionId");


	$sSQL = "SELECT * FROM tbl_inspections WHERE id='$iInspectionId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iUser         = $objDb->getField(0, "admin_id");
	$sLatitude     = $objDb->getField(0, "latitude");
	$sLongitude    = $objDb->getField(0, "longitude");
	$sLocation     = $objDb->getField(0, "location");
	$sDateTime     = $objDb->getField(0, "created_at");

	$iDistrict     = $objDb->getField(0, "district_id");
	$iSchool       = $objDb->getField(0, "school_id");
	$iStage        = $objDb->getField(0, "stage_id");
	$sTitle        = $objDb->getField(0, "title");
	$sDate         = $objDb->getField(0, "date");
	$sDetails      = $objDb->getField(0, "details");
	$sPicture      = $objDb->getField(0, "picture");
	$sDocument     = $objDb->getField(0, "file");
	$sStatus       = $objDb->getField(0, "status");
	$iReason       = $objDb->getField(0, "failure_reason_id");
	$sComments     = $objDb->getField(0, "failure_reason");
	$sCompleted    = $objDb->getField(0, "stage_completed");
	$sReInspection = $objDb->getField(0, "re_inspection");


	$sSQL = "SELECT storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	


	$sSchoolType  = (($sDesignType == "B") ? "B" : $sStoreyType);	
	$sSchoolsList = getList("tbl_schools", "id", "CONCAT(code, ' - ', name)", "district_id='$iDistrict'");
	$sStagesList  = array( );


	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='$sSchoolType' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");
		$sParent = $objDb->getField($i, "name");


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		if ($iCount2 == 0)
			$sStagesList[$iParent] = $sParent;


		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStageId = $objDb2->getField($j, "id");
			$sStage  = $objDb2->getField($j, "name");


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStageId' ORDER BY position";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			if ($iCount3 == 0)
				$sStagesList[$iStageId] = ($sParent." &raquo; ".$sStage);


			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iSubStage' ORDER BY position";
				$objDb4->query($sSQL);

				$iCount4 = $objDb4->getCount( );

				if ($iCount4 == 0)
					$sStagesList[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);


				for ($l = 0; $l < $iCount4; $l ++)
				{
					$iFourthStage = $objDb4->getField($l, "id");
					$sFourthStage = $objDb4->getField($l, "name");

					$sStagesList[$iFourthStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage." &raquo; ".$sFourthStage);
				}
			}
		}
	}
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
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr valign="top">
        <td width="60%">
		  <label for="ddUser">Inspector</label>

		  <div>
		    <select name="ddUser" id="ddUser">
			  <option value=""></option>
<?
	$sUsersList = getList("tbl_admins", "id", "name", "status='A'");

	foreach ($sUsersList as $iUserId => $sUser)
	{
?>
	    	  <option value="<?= $iUserId ?>"<?= (($iUserId == $iUser) ? ' selected' : '') ?>><?= $sUser ?></option>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddDistrict">District</label>

		  <div>
		    <select name="ddDistrict" id="ddDistrict">
			  <option value=""></option>
<?
	$sSQL = "SELECT id, name FROM tbl_provinces ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iProvince = $objDb->getField($i, "id");
		$sProvince = $objDb->getField($i, "name");
?>
	    	  <optgroup label="<?= $sProvince ?>">
<?
		$sSQL = "SELECT id, name FROM tbl_districts WHERE province_id='$iProvince' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iDistrictId = $objDb2->getField($j, "id");
			$sDistrict   = $objDb2->getField($j, "name");
?>
		  	    <option value="<?= $iDistrictId ?>"<?= (($iDistrict == $iDistrictId) ? ' selected' : '') ?>><?= $sDistrict ?></option>
<?
		}
?>
	    	  </optgroup>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddSchool">School</label>

		  <div>
		    <select name="ddSchool" id="ddSchool" style="width:506px; max-width:506px;">
			  <option value=""></option>
<?
	foreach ($sSchoolsList as $iSchoolId => $sSchool)
	{
?>
	    	  <option value="<?= $iSchoolId ?>"<?= (($iSchool == $iSchoolId) ? ' selected' : '') ?>><?= $sSchool ?></option>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddStage">Stage</label>

		  <div>
		    <select name="ddStage" id="ddStage" style="width:506px; max-width:506px;">
			  <option value=""></option>
<?
	foreach ($sStagesList as $iStageId => $sStage)
	{
?>
			  <option value="<?= $iStageId ?>"<?= (($iStageId == $iStage) ? ' selected' : '') ?>><?= $sStage ?></option>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="100" size="50" class="textbox" style="width:500px;" /></div>

		  <div class="br10"></div>

		  <label for="filePicture">Picture <span><?= (($sPicture == "") ? '' : ('(<a href="'.(SITE_URL.INSPECTIONS_IMG_DIR.$sPicture).'" class="colorbox">'.substr($sPicture, strlen("{$iInspectionId}-")).'</a>)')) ?></span></label>
		  <div><input type="file" name="filePicture" id="filePicture" value="" size="50" class="textbox" /></div>

		  <div class="br10"></div>

<?
	$iPosition  = @strrpos($sDocument, '.');
	$sExtension = @substr($sDocument, $iPosition);

	if (@in_array($sExtension, array(".jpg", ".png", ".jpeg", ".gif")))
	{
?>
		  <label for="fileDocument">Document <span><?= (($sDocument == "") ? '' : ('(<a href="'.(SITE_URL.INSPECTIONS_DOC_DIR.$sDocument).'" class="colorbox">'.substr($sDocument, strlen("{$iInspectionId}-")).'</a>)')) ?></span></label>
<?
	}

	else
	{
?>
		  <label for="fileDocument">Document <span><?= (($sDocument == "") ? '' : ('(<a href="'.$sCurDir.'/download-inspection-document.php?Id='.$iDocumentId.'&File='.$sDocument.'">'.substr($sDocument, strlen("{$iInspectionId}-")).'</a>)')) ?></span></label>
<?
	}
?>
		  <div><input type="file" name="fileDocument" id="fileDocument" value="" size="50" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtDate">Date</label>
		  <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		  <div class="br10"></div>

		  <label for="txtDetails">Details <span>(Optional)</span></label>
		  <div><textarea name="txtDetails" id="txtDetails" rows="10" style="width:500px;"><?= $sDetails ?></textarea></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value=""<?= (($sStatus == '') ? ' selected' : '') ?>></option>
			  <option value="P"<?= (($sStatus == 'P') ? ' selected' : '') ?>>Pass</option>
			  <option value="F"<?= (($sStatus == 'F') ? ' selected' : '') ?>>Fail</option>
			  <option value="R"<?= (($sStatus == 'R') ? ' selected' : '') ?>>Re-Inspection</option>
		    </select>
		  </div>

		  <div id="Failed"<?= (($sStatus == "F") ? '' : ' class="hidden"') ?>>
		    <div class="br10"></div>

		    <label for="ddReason">Failure Reason</label>

		    <div>
			  <select name="ddReason" id="ddReason">
			    <option value=""></option>
<?
	$sStageReasons = getDbValue("failure_reasons", "tbl_stages", "id='$iStage'");
	$sReasonsList  = getList("tbl_failure_reasons", "id", "reason", "FIND_IN_SET(id, '$sStageReasons')");

	foreach ($sReasonsList as $iReasonId => $sReason)
	{
?>
	      	    <option value="<?= $iReasonId ?>"<?= (($iReasonId == $iReason) ? ' selected' : '') ?>><?= $sReason ?></option>
<?
	}
?>
			  </select>
		    </div>

		    <div id="Comments"<?= (($sStatus == "F" && $iReason == 5) ? '' : ' class="hidden"') ?>>
			  <div class="br10"></div>

			  <label for="txtComments">Comments</label>
			  <div><textarea name="txtComments" id="txtComments" rows="3" style="width:500px;"><?= $sComments ?></textarea></div>
		    </div>
		  </div>

		  <div id="Passed"<?= (($sStatus == "P") ? '' : ' class="hidden"') ?>>
		    <div class="br10"></div>

		    <label for="ddCompleted">Stage Completed?</label>

		    <div>
			  <select name="ddCompleted" id="ddCompleted">
			    <option value="N"<?= (($sCompleted == "N") ? ' selected' : '') ?>>No</option>
			    <option value="Y"<?= (($sCompleted == "Y") ? ' selected' : '') ?>>Yes</option>
			  </select>
		    </div>
		  </div>

		  <div id="ReInspection"<?= (($sStatus == "R") ? '' : ' class="hidden"') ?>>
		    <div class="br10"></div>

		    <label for="txtReInspection">Re-Inspection Date</label>
		    <div class="date"><input type="text" name="txtReInspection" id="txtReInspection" value="<?= (($sReInspection == "0000-00-00") ? "" : $sReInspection) ?>" maxlength="10" size="10" class="textbox" readonly /></div>
		  </div>
		</td>

        <td width="5%"></td>

		<td width="35%">
		  <b>Inspection ID</b><br />
		  <?= str_pad($iInspectionId, 6, '0', STR_PAD_LEFT) ?><br />

		  <div class="br10"></div>

		  <b>Entry Date/Time</b><br />
		  <div><?= formatDate($sDateTime, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}") ?></div>

		  <div class="br10"></div>

<?
	if ($sLatitude != "" && $sLongitude != "")
	{
		if ($sLocation != "")
			$sGpsTag = rtrim(str_replace(array("\r\n", "\n"), ", ", $sLocation), ", ");

		else
			$sGpsTag = "Unknown";
?>
		  <b>GPS Tag</b><br />
		  <div><?= $sGpsTag ?></div>

		  <div class="br10"></div>
<?
	}
?>
		</td>
	  </tr>
	</table>

<?
	$sSQL = "SELECT * FROM tbl_inspection_documents WHERE inspection_id='$iInspectionId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
?>
    <br />
    <hr />

    <h3>Additional Documents</h3>

    <ul style="list-style:none; margin:0px; padding:0px;">
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iFile = $objDb->getField($i, "id");
			$sFile = $objDb->getField($i, "file");


			$iPosition  = @strrpos($sFile, '.');
			$sExtension = @substr($sFile, $iPosition);
			$sImages    = array(".jpg", ".jpeg", ".png", ".gif");
?>
	  <li>
<?
			if (@in_array($sExtension, $sImages))
			{
				if (@file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sFile))
				{
?>
	    <a href="<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sFile) ?>" class="colorbox"><?= substr($sFile, strlen("{$iInspectionId}-{$iFile}-")) ?></a>
<?
				}

				else
				{
?>
	    <a href="<?= (SITE_URL.INSPECTIONS_DOC_DIR.$sFile) ?>" class="colorbox"><?= substr($sFile, strlen("{$iInspectionId}-{$iFile}-")) ?></a>
<?
				}
			}

			else
			{
?>
	    <a href="<?= $sCurDir ?>/download-inspection-document.php?Id=<?= $iFile ?>&File=<?= $sFile ?>"><?= substr($sFile, strlen("{$iInspectionId}-{$iFile}-")) ?></a>
<?
			}
?>
	  </li>
<?
		}
?>
    </ul>

    <div class="br5"></div>
<?
	}




	$sSQL = "SELECT b.id, b.title, b.unit,
	                im.id, im.title, im.length, im.width, im.height, im.multiplier, im.measurements, im.amount
	         FROM tbl_inspection_measurements im, tbl_boqs b
	         WHERE b.id=im.boq_id AND im.inspection_id='$iInspectionId' AND im.parent_id='0'
	         ORDER BY b.position";
	$objDb->query($sSQL);

	$iCount     = $objDb->getCount( );
	$fNetAmount = 0;

	if ($iCount > 0)
	{
		$iContract = getDbValue("id", "tbl_contracts", "FIND_IN_SET('$iSchool', schools) AND ('$sDate' BETWEEN start_date AND end_date)", "id DESC");
?>
    <br />
    <hr />

    <h3>Measurements</h3>

    <div class="grid">
	  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	    <tr class="header" valign="top">
		  <td width="5%">#</td>
		  <td width="28%">BOQ Item</td>
		  <td width="22%">Title</td>
		  <td width="18%">Measurements</td>
		  <td width="9%">Calculated</td>
		  <td width="9%">Rate</td>
		  <td width="9%">Amount</td>
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
							im.title, im.multiplier, im.length, im.width, im.height, im.measurements, im.amount
					 FROM tbl_inspection_measurements im, tbl_boqs b
					 WHERE b.id=im.boq_id AND im.inspection_id='$iInspectionId' AND im.parent_id='$iId'
					 ORDER BY im.id";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );
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
		  </tr>
<?
			for ($j = 0; $j < $iCount2; $j ++)
			{
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
		  </tr>
<?
			}
			
			
			$fNetAmount += $fTotalAmount;
		}
?>

	    <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td colspan="6" align="right"><b>Net Total</b> &nbsp; </td>
		  <td><b><?= formatNumber($fNetAmount, false) ?></b></td>
	    </tr>
      </table>
    </div>

    <div class="br5"></div>
<?
	}
?>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>
