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
	$objDb2      = new Database( );


	$sPictureId      = IO::strValue("Id");
	$sSubConditions  = "";
	$sInspectionsSQL = " AND FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') ";

	if ($_SESSION["AdminSchools"] != "")
	{
		$sInspectionsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
		$sSubConditions  .= " AND FIND_IN_SET(i.school_id, '{$_SESSION['AdminSchools']}') ";
	}

	@list($sDate, $iInspection, $iPictureId) = @explode("|", $sPictureId);



	$sSQL = "SELECT '0' AS _PictureId, id AS _Inspection, picture AS _Picture, status AS _Status, measurements AS _Measurements, milestone_id AS _Milestone, school_id AS _School, district_id AS _District, `date` AS _Date
			 FROM tbl_inspections
			 WHERE picture!='' AND `date` <= '$sDate' AND id < '$iInspection' $sInspectionsSQL

	         UNION

	         SELECT d.id AS _PictureId, d.inspection_id AS _Inspection, d.file AS _Picture, i.status AS _Status, i.measurements AS _Measurements, i.milestone_id AS _Milestone, i.school_id AS _School, i.district_id AS _District, i.date AS _Date
	         FROM tbl_inspection_documents d, tbl_inspections i
	         WHERE d.inspection_id=i.id AND d.file LIKE '%.jpg' AND i.date <= '$sDate' AND i.id <= '$iInspection' AND d.id > '$iPictureId' AND FIND_IN_SET(i.district_id, '{$_SESSION['AdminDistricts']}') $sSubConditions

	         ORDER BY _Date DESC, _Inspection DESC, _PictureId ASC
	         LIMIT 0, 50";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	$iIndex = 0;

	if ($iCount > 0)
	{
?>
	          <ul>
<?
		$sDistrictsList = getList("tbl_districts", "id", "name");
		$sImagesList    = array(".jpg", ".jpeg", ".png", ".gif");

		$sStatuses      = array( );
		$iSchools       = array( );
		$sCodes         = array( );
		$iDistricts     = array( );


		for ($i = 0; ($i < $iCount && $iIndex < 18); $i ++)
		{
			$iPictureId    = $objDb->getField($i, "_PictureId");
			$iInspection   = $objDb->getField($i, "_Inspection");
			$sDate         = $objDb->getField($i, "_Date");
			$sPicture      = $objDb->getField($i, "_Picture");
			$sStatus       = $objDb->getField($i, "_Status");
			$sMeasurements = $objDb->getField($i, "_Measurements");
			$iMilestone    = $objDb->getField($i, "_Milestone");
			$iSchool       = $objDb->getField($i, "_School");
			$iDistrict     = $objDb->getField($i, "_District");


			$iPosition  = @strrpos($sPicture, '.');
			$sExtension = @substr($sPicture, $iPosition);


			if (!@in_array($sExtension, $sImagesList))
				continue;

			if (!@file_exists("../".INSPECTIONS_IMG_DIR.$sPicture) && !@file_exists("../".INSPECTIONS_DOC_DIR.$sPicture))
				continue;
			
			if (@file_exists("../".INSPECTIONS_IMG_DIR.$sPicture) && !@file_exists("../".INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture))
				createImage(("../".INSPECTIONS_IMG_DIR.$sPicture), ("../".INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture), 200, 200);
			
			if (@file_exists("../".INSPECTIONS_DOC_DIR.$sPicture) && !@file_exists("../".INSPECTIONS_DOC_DIR.'thumbs/'.$sPicture))
				createImage(("../".INSPECTIONS_DOC_DIR.$sPicture), ("../".INSPECTIONS_DOC_DIR.'thumbs/'.$sPicture), 200, 200);


			if (!@in_array($iSchool, $sCodes))
			{
				$sCode = getDbValue("code", "tbl_schools", "id='$iSchool'");

				$sCodes[$iSchool] = $sCode;
			}

			else
				$sCode = $sCodes[$iSchool];
?>
	            <li>
	              <a href="inspection-details.php?Id=<?= $iInspection ?>" class="inspection status<?= $sStatus ?><?= (($sMeasurements == "Y") ? ' measurements' : '') ?><?= (($iMilestone > 0) ? ' milestone' : '') ?>">
	                <img src="<?= ((@file_exists("../".INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture)) ? (SITE_URL.INSPECTIONS_IMG_DIR.'thumbs/'.$sPicture) : (SITE_URL.INSPECTIONS_DOC_DIR.'thumbs/'.$sPicture)) ?>" width="116" height="116" alt="" title="" rel="<?= $sDate ?>|<?= $iInspection ?>|<?= $iPictureId ?>" /><br />
	                <?= $sCode ?>
	              </a>

	              <?= $sDistrictsList[$iDistrict] ?><br />
	            </li>
<?
			$iIndex ++;
		}
?>
	          </ul>

	          <div class="br10"></div>
<?
	}


	if ($iIndex == 0)
	{
?>
	    <div style="padding:100px;">
	      <div class="info noHide">No more Inspection Picture Available!</div>
	    </div>
<?
	}


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>