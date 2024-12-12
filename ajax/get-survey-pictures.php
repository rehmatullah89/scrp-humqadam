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


	$iPictureId     = IO::intValue("Id");
	$sSubConditions = "";

	if ($_SESSION["AdminSchools"] != "")
		$sSubConditions .= " AND FIND_IN_SET(s.school_id, '{$_SESSION['AdminSchools']}') ";



	$sSQL = "SELECT p.id, p.survey_id, p.picture, s.school_id, s.district_id, s.date, s.qualified
	         FROM tbl_survey_pictures p, tbl_surveys s
	         WHERE p.survey_id=s.id AND p.id < '$iPictureId' AND FIND_IN_SET(s.district_id, '{$_SESSION['AdminDistricts']}') $sSubConditions
	         ORDER BY p.id DESC
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

		$iSchools       = array( );
		$sCodes         = array( );
		$iDistricts     = array( );


		for ($i = 0; ($i < $iCount && $iIndex < 18); $i ++)
		{
			$iPictureId = $objDb->getField($i, "id");
			$iSurvey    = $objDb->getField($i, "survey_id");
			$sDate      = $objDb->getField($i, "date");
			$sPicture   = $objDb->getField($i, "picture");
			$sQualified = $objDb->getField($i, "qualified");
			$iSchool    = $objDb->getField($i, "school_id");
			$iDistrict  = $objDb->getField($i, "district_id");

			if (!@file_exists("../".SURVEYS_DOC_DIR.$sPicture))
				continue;
			
			if (!@file_exists("../".SURVEYS_DOC_DIR.'thumbs/'.$sPicture))
				createImage(("../".SURVEYS_DOC_DIR.$sPicture), ("../".SURVEYS_DOC_DIR.'thumbs/'.$sPicture), 200, 200);


			if (!@in_array($iSchool, $sCodes))
			{
				$sCode = getDbValue("code", "tbl_schools", "id='$iSchool'");

				$sCodes[$iSchool] = $sCode;
			}

			else
				$sCode = $sCodes[$iSchool];
?>
	            <li>
	              <a href="survey-details.php?Id=<?= $iSurvey ?>" class="survey status<?= (($sQualified == "Y") ? "P" : "F") ?>">
	                <img src="<?= (SITE_URL.SURVEYS_DOC_DIR.'thumbs/'.$sPicture) ?>" width="116" height="116" alt="" title="" rel="<?= $iPictureId ?>" /><br />
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
	      <div class="info noHide">No more Survey Picture Available!</div>
	    </div>
<?
	}


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>