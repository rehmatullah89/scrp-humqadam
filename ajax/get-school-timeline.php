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
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );
	$objDb3      = new Database( );
	$objDb4      = new Database( );


	$iSchool = IO::intValue("School");


	$sSQL = "SELECT name, code, storey_type, design_type, district_id FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool     = $objDb->getField(0, "name");
	$sCode       = $objDb->getField(0, "code");
	$iDistrict   = $objDb->getField(0, "district_id");
	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");		


	$sSQL = "SELECT name, province_id FROM tbl_districts WHERE id='$iDistrict'";
	$objDb->query($sSQL);

	$sDistrict = $objDb->getField(0, "name");
	$iProvince = $objDb->getField(0, "province_id");


	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$sProvince   = getDbValue("code", "tbl_provinces", "id='$iProvince'");
	$sStart      = getDbValue("start_date", "tbl_contracts", "status='A' AND FIND_IN_SET('$iSchool', schools)", "start_date");
	$sPackage    = getDbValue("title", "tbl_packages", "FIND_IN_SET('$iSchool', schools)");

	if ($sStart == "" || $sStart == "0000-00-00")
		$sStart = getDbValue("MIN(`date`)", "tbl_inspections", "school_id='$iSchool'");

	if ($sStart == "" || $sStart == "0000-00-00")
		$sStart = date("Y-m-d");


	$sInspectors = getList("tbl_admins", "id", "name");
	$sStages     = array( );
	$sUnits      = array( );


	$sSQL = "SELECT id, name FROM tbl_stages WHERE status='A' AND parent_id='0' AND `type`='$sSchoolType' ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");
		$sParent = $objDb->getField($i, "name");

		$sStages[$iParent] = $sParent;


		$sSQL = "SELECT id, name, unit FROM tbl_stages WHERE status='A' AND parent_id='$iParent' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");
			$sStage = $objDb2->getField($j, "name");
			$sUnit  = $objDb2->getField($j, "unit");

			$sStages[$iStage] = $sStage;
			$sUnits[$iStage]  = $sUnit;


			$sSQL = "SELECT id, name, unit FROM tbl_stages WHERE status='A' AND parent_id='$iStage' ORDER BY name";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");
				$sUnit     = $objDb3->getField($k, "unit");

				$sStages[$iSubStage] = $sSubStage;
				$sUnits[$iSubStage]  = $sUnit;


				$sSQL = "SELECT id, name, unit FROM tbl_stages WHERE status='A' AND parent_id='$iSubStage' ORDER BY name";
				$objDb4->query($sSQL);

				$iCount4 = $objDb4->getCount( );

				for ($l = 0; $l < $iCount4; $l ++)
				{
					$iFourthLevel = $objDb4->getField($l, "id");
					$sFourthLevel = $objDb4->getField($l, "name");
					$sUnit        = $objDb4->getField($l, "unit");

					$sStages[$iFourthLevel] = $sFourthLevel;
					$sUnits[$iFourthLevel]  = $sUnit;
				}
			}
		}
	}



	$iInspections = getDbValue("COUNT(1)", "tbl_inspections", "school_id='$iSchool'");

	if ($iInspections > 30)
		$iInspections -= 30;

	else
		$iInspections = 0;


	header("Content-type: application/json");

	$sSQL = "SELECT id, admin_id, latitude, longitude, location, stage_id, title, details, `date`, picture, file, created_at
	         FROM tbl_inspections
	         WHERE school_id='$iSchool'
	         ORDER BY `date`
	         LIMIT {$iInspections}, 30";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
?>
{
	"timeline":
	{
		"headline"  : "<?= $sSchool ?>",
		"type"      : "default",
		"text"      : "Project Timeline",
		"startDate" : "<?= date("Y,m,d", strtotime($sStart)) ?>",

		"asset":
		{
			"media"   : "<?= SITE_URL ?>images/humqadam.svg",
			"credit"  : "-",
			"caption" : "-"
		},

		"date":
		[
<?
	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId        = $objDb->getField($i, "id");
		$iInspector = $objDb->getField($i, "admin_id");
		$sLatitude  = $objDb->getField($i, "latitude");
		$sLongitude = $objDb->getField($i, "longitude");
		$sLocation  = $objDb->getField($i, "location");
		$iStage     = $objDb->getField($i, "stage_id");
		$sTitle     = $objDb->getField($i, "title");
		$sDetails   = $objDb->getField($i, "details");
		$sDate      = $objDb->getField($i, "date");
		$sFile      = $objDb->getField($i, "file");
		$sPicture   = $objDb->getField($i, "picture");
		$sDateTime  = $objDb->getField($i, "created_at");


		if ($sPicture == "" || !@file_exists("../".INSPECTIONS_IMG_DIR.$sPicture))
			$sPicture = "humqadam.svg";

		else
		{
			if (!@file_exists("../".INSPECTIONS_IMG_DIR."thumbs/".$sPicture))
				createImage(("../".INSPECTIONS_IMG_DIR.$sPicture), ("../".INSPECTIONS_IMG_DIR."thumbs/".$sPicture), 24, 24);
		}


		$sTitle     = @htmlentities($sTitle);
		$sDetails   = @htmlentities(str_replace(array("\r\n", "\n"), "", trim($sDetails)));
		$sDateTime  = formatDate($sDateTime, "d-M-Y h:i A");
		$sInspector = "";
		$sGpsTag    = "N/A";


		if ($iInspector > 0)
			$sInspector = "Inspector: <b>{$sInspectors[$iInspector]}</b>";

		if ($sLatitude != "" && $sLongitude != "")
		{
			if ($sLocation != "")
				$sGpsTag = rtrim(str_replace(array("\r\n", "\n"), ", ", $sLocation), ", ");

			else
				$sGpsTag = "Unknown";
		}



		$sHtml  = "<table border='0' cellspacing='0' cellpadding='4' width='100%'><tr valign='top'><td width='55%'>Title: <b>{$sTitle}</b><br />Date: <b>{$sDate}</b><br />GPS Tag: <b>{$sGpsTag}</b><br />Date/Time: <b>{$sDateTime}</b></td><td width='45%'>School: <b>{$sSchool}</b><br />EMIS Code: <b>{$sCode}</b><br />District: <b>{$sDistrict}, {$sProvince}</b><br />Package: <b>{$sPackage}</b><br />{$sInspector}</td></tr><tr><td colspan='2'>{$sDetails}</td></tr></table>";
		$sLinks = ("<a href='".(SITE_URL.INSPECTIONS_IMG_DIR.$sPicture)."' class='documents{$i}'></a>");
		$sHtml .= ("<div id='Options' style='display:none;'><a href='".(SITE_URL.INSPECTIONS_DOC_DIR.$sFile)."' target='_blank' download='".substr($sFile, strlen("{$iId}-"))."'><img src='images/icons/docs.png' width='24' alt='' title='Docs' /></a><a href='".(SITE_URL.INSPECTIONS_DOC_DIR.$sFile)."' target='_blank' download='".substr($sFile, strlen("{$iId}-"))."'><img src='images/icons/pdf.png' width='24' hspace='5' alt='' title='Report' /></a></div>");


		$sSQL = "SELECT file FROM tbl_inspection_documents WHERE inspection_id='$iId' ORDER BY id";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );
		$sImages = array(".jpg", ".jpeg", ".png", ".gif");

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$sFile = $objDb2->getField($j, "file");


			$iPosition  = @strrpos($sFile, '.');
			$sExtension = @substr($sFile, $iPosition);

			if (@in_array($sExtension, $sImages))
			{
				if (@file_exists("../".INSPECTIONS_IMG_DIR.$sFile))
					$sLinks .= ("<a href='".(SITE_URL.INSPECTIONS_IMG_DIR.$sFile)."' class='documents{$i}'></a>");

				else if (@file_exists("../".INSPECTIONS_DOC_DIR.$sFile))
					$sLinks .= ("<a href='".(SITE_URL.INSPECTIONS_DOC_DIR.$sFile)."' class='documents{$i}'></a>");
			}
		}
?>
			{
				"startDate" : "<?= formatDate($sDate, "Y,m,j") ?>",
				"endDate"   : "<?= formatDate($sDate, "Y,m,j") ?>",
				"headline"  : "<?= $sStages[$iStage] ?> (<?= formatDate($sDate, "d-M-Y") ?>)",
				"tag"       : "",
				"text"      : "<p><?= $sTitle ?></p>",

				"asset"     :
				{
					"media"     : "<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sPicture) ?>",
					"thumbnail" : "<?= (SITE_URL.INSPECTIONS_IMG_DIR."thumbs/".$sPicture) ?>",
					"credit"    : "<?= $sHtml ?><?= $sLinks ?>",
					"caption"   : "<?= (($sFile != "" && @file_exists("../".INSPECTIONS_DOC_DIR.$sFile)) ? (SITE_URL.INSPECTIONS_DOC_DIR.$sFile) : "") ?>"
				}
			}<?= (($i < ($iCount - 1)) ? "," : "") ?>
<?
	}


	if ($iCount == 0)
	{
?>
			{
				"startDate" : "<?= formatDate($sStart, "Y,m,j") ?>",
				"endDate"   : "<?= formatDate($sStart, "Y,m,j") ?>",
				"headline"  : "Not Started Yet",
				"tag"       : "",
				"text"      : "<p>Not Started</p>",

				"asset"     :
				{
					"media"     : "<?= (SITE_URL.INSPECTIONS_IMG_DIR."humqadam.svg") ?>",
					"thumbnail" : "<?= (SITE_URL.INSPECTIONS_IMG_DIR."thumbs/humqadam.svg") ?>",
					"credit"    : "",
					"caption"   : ""
				}
			}
<?
	}
?>
		]
	}
}
<?
	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>
