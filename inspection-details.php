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
	$objDb2      = new Database( );


	$iInspectionId = IO::intValue("Id");


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



	$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iStage'";
	$objDb->query($sSQL);

	$sStage  = $objDb->getField(0, "name");
	$iParent = $objDb->getField(0, "parent_id");

	if ($iParent > 0)
	{
		$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iParent'";
		$objDb->query($sSQL);

		$sParent = $objDb->getField(0, "name");
		$iParent = $objDb->getField(0, "parent_id");

		$sStage = ($sParent.'<br /> &nbsp; &nbsp; &raquo; '.$sStage);
	}

	if ($iParent > 0)
	{
		$sSQL = "SELECT name, parent_id FROM tbl_stages WHERE id='$iParent'";
		$objDb->query($sSQL);

		$sParent = $objDb->getField(0, "name");
		$iParent = $objDb->getField(0, "parent_id");

		$sStage = ($sParent.'<br /> &nbsp; &raquo; '.$sStage);
	}

	if ($iParent > 0)
		$sStage = (getDbValue("name", "tbl_stages", "id='$iParent'").'<br />&raquo; '.$sStage);
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
  <form name="frmRecord" id="frmRecord">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr valign="top">
        <td width="60%">
		  <b>Inspector</b><br />
		  <?= getDbValue("name", "tbl_admins", "id='$iUser'") ?><br />

		  <div class="br10"></div>

		  <b>District</b><br />
		  <div><?= getDbValue("name", "tbl_districts", "id='$iDistrict'") ?> (<?= getDbValue("code", "tbl_provinces", "id=(SELECT province_id FROM tbl_districts WHERE id='$iDistrict')") ?>)</div>

		  <div class="br10"></div>

		  <b>School</b><br />
		  <div><?= getDbValue("CONCAT(name, ' (', code, ')')", "tbl_schools", "id='$iSchool'") ?></div>

		  <div class="br10"></div>

		  <b>Package</b><br />
		  <div><?= getDbValue("title", "tbl_packages", "FIND_IN_SET('$iSchool', schools)", "id DESC") ?></div>

		  <div class="br10"></div>

		  <b>Contract</b><br />
		  <div><?= getDbValue("title", "tbl_contracts", "FIND_IN_SET('$iSchool', schools)", "id DESC") ?></div>

		  <div class="br10"></div>

		  <b>Stage</b><br />
		  <div><?= $sStage ?></div>

		  <div class="br10"></div>

		  <b>Title</b><br />
		  <div><?= $sTitle ?></b><br />

		  <div class="br10"></div>

		  <b>Inspection Date</b><br />
		  <div><?= formatDate($sDate, $_SESSION['DateFormat']) ?></div>

		  <div class="br10"></div>

		  <b>Details</b><br />
		  <div><?= (($sDetails == "" ) ? "N/A" : nl2br($sDetails)) ?></div>

		  <div class="br10"></div>

		  <b>Status</b><br />
		  <div><?= (($sStatus == "P") ? "Pass" : (($sStatus == "R") ? "Re-Inspection" : "Fail")) ?></div>

<?
	if ($sStatus == "P")
	{
?>
	      <div class="br10"></div>

          <b>Stage Completed?</b><br />
	      <div><?= (($sCompleted == "Y") ? "Yes" : "No") ?></div>
<?
	}

	else if ($sStatus == "F")
	{
?>
	      <div class="br10"></div>

          <b>Failure Reason</b><br />
	      <div><?= getDbValue("reason", "tbl_failure_reasons", "id='$iReason'") ?></div>
<?
		if ($iReason == 5)
		{
?>
		  <div class="br10"></div>

		  <b>Comments</b><br />
		  <div><?= (($sComments == "" ) ? "N/A" : nl2br($sComments)) ?></div>
<?
		}
	}

	else if ($sStatus == "R")
	{
?>
		  <div class="br10"></div>

		  <b>Re-Inspection Date</b><br />
		  <div><?= formatDate($sReInspection, $_SESSION['DateFormat']) ?></div>
<?
	}
?>
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


	if ($sPicture != "")
	{
		if (!@file_exists(INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture))
			createImage((INSPECTIONS_IMG_DIR.$sPicture), (INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture), 200, 200);		
?>
		  <b>Picture</b><br />
		  <div><a href="<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sPicture) ?>" class="colorbox"><img src="<?= (SITE_URL.INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture) ?>" width="200" alt="" title="" style="border:solid 1px #666666;" /></a></div>

		  <div class="br10"></div>
<?
	}


	if ($sDocument != "")
	{
		$iPosition  = @strrpos($sDocument, '.');
		$sExtension = @substr($sDocument, $iPosition);

		if (@in_array($sExtension, array(".jpg", ".png", ".jpeg", ".gif")))
		{
			if (!@file_exists(INSPECTIONS_DOC_DIR.'thumbs/'.$sDocument))
				createImage((INSPECTIONS_DOC_DIR.$sDocument), (INSPECTIONS_DOC_DIR.'thumbs/'.$sDocument), 200, 200);				
?>
		  <b>Document</b><br />
		  <div><a href="<?= (SITE_URL.INSPECTIONS_DOC_DIR.$sDocument) ?>" class="colorbox"><img src="<?= (SITE_URL.INSPECTIONS_DOC_DIR.'thumbs/'.$sDocument) ?>" width="200" alt="" title="" /></a></div>

		  <div class="br10"></div>
<?
		}

		else
		{
?>
		  <b>Document</b><br />
		  <div><a href="<?= (SITE_URL.INSPECTIONS_DOC_DIR.$sDocument) ?>"><?= substr($sDocument, strlen("{$iInspectionId}-")) ?></a></div>

		  <div class="br10"></div>
<?
		}
	}


	$sImagesList = array(".jpg", ".jpeg", ".png", ".gif");
	$sDocuments  = array( );
	$sPictures   = array( );


	$sSQL = "SELECT * FROM tbl_inspection_documents WHERE inspection_id='$iInspectionId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iFile = $objDb->getField($i, "id");
		$sFile = $objDb->getField($i, "file");


		$iPosition  = @strrpos($sFile, '.');
		$sExtension = @substr($sFile, $iPosition);

		if (@in_array($sExtension, $sImagesList))
		{
			if (@file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sFile))
			{
				if (!@file_exists(INSPECTIONS_IMG_DIR.'thumbs/'.$sFile))
					createImage((INSPECTIONS_IMG_DIR.$sFile), (INSPECTIONS_IMG_DIR.'thumbs/'.$sFile), 200, 200);
			
				$sPictures[substr($sFile, strlen("{$iInspectionId}-{$iFile}-"))] = (SITE_URL.INSPECTIONS_IMG_DIR.'thumbs/'.$sFile);
			}

			else
			{
				if (!@file_exists(INSPECTIONS_DOC_DIR.'thumbs/'.$sFile))
					createImage((INSPECTIONS_DOC_DIR.$sFile), (INSPECTIONS_DOC_DIR.'thumbs/'.$sFile), 200, 200);
			
				$sPictures[substr($sFile, strlen("{$iInspectionId}-{$iFile}-"))] = (SITE_URL.INSPECTIONS_DOC_DIR.'thumbs/'.$sFile);
			}
		}

		else
			$sDocuments[substr($sFile, strlen("{$iInspectionId}-{$iFile}-"))] = (SITE_URL.INSPECTIONS_DOC_DIR.$sFile);
	}


	if (count($sDocuments) > 0)
	{
?>
          <b>Additional Documents</b><br />

          <ul style="list-style:none; margin:0px; padding:0px;">
<?
		foreach ($sDocuments as $sDocument => $sUrl)
		{
?>
	        <li><a href="<?= $sUrl ?>"><?= $sDocument ?></a></li>
<?
		}
?>
          </ul>
<?
	}
?>
          <div class="br10"></div>
		</td>
	  </tr>
	</table>
<?
	if (count($sPictures) > 0)
	{
?>
    <br />
    <hr />
    <b>Pictures</b><br />

    <ul style="list-style:none; margin:0px; padding:0px;">
<?
		foreach ($sPictures as $sPicture => $sUrl)
		{
?>
      <li style="float:left; margin:5px 5px 0px 0px;"><a href="<?= str_replace("thumbs/", "", $sUrl) ?>" class="colorbox"><img src="<?= $sUrl ?>" width="200" alt="" title="" style="border:solid 1px #666666;" /></a></li>
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
    <br />

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



	$sSQL = "SELECT id, stage_id, title, `date`, status FROM tbl_inspections WHERE school_id='$iSchool' AND id!='$iInspectionId' ORDER BY `date` DESC";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
?>
    <br />
    <br />

    <h3>Inspections History</h3>

    <div class="grid">
	  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
	    <tr class="header" valign="top">
		  <td width="5%">#</td>
		  <td width="40%">Stage</td>
		  <td width="35%">Title</td>
		  <td width="10%">Date</td>
		  <td width="10%">Status</td>
	    </tr>
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId     = $objDb->getField($i, "id");
			$iStage  = $objDb->getField($i, "stage_id");
			$sTitle  = $objDb->getField($i, "title");
			$sDate   = $objDb->getField($i, "date");
			$sStatus = $objDb->getField($i, "status");
?>

		  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		    <td><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></td>
		    <td><?= getDbValue("name", "tbl_stages", "id='$iStage'") ?></td>
		    <td><?= $sTitle ?></td>
		    <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
		    <td><?= (($sStatus == "P") ? "Pass" : (($sStatus == "R") ? "Re-Inspection" : "Fail")) ?></td>
		  </tr>
<?
		}
?>
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
	$objDbGlobal->close( );

	@ob_end_flush( );
?>
