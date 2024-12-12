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


	$iSurveyId = IO::intValue("SurveyId");


	$sSQL = "SELECT * FROM tbl_surveys WHERE id='$iSurveyId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool         = $objDb->getField(0, "school_id");
	$sEnumerator     = $objDb->getField(0, "enumerator");
	$sDate           = $objDb->getField(0, "date");
	$sOperational    = $objDb->getField(0, "operational");
	$sPefProgramme   = $objDb->getField(0, "pef_programme");
	$sLandAvailable  = $objDb->getField(0, "land_available");
	$sLandDispute    = $objDb->getField(0, "land_dispute");
	$sOtherFunding   = $objDb->getField(0, "other_funding");
	$iClassRooms     = $objDb->getField(0, "class_rooms");
	$iEducationRooms = $objDb->getField(0, "education_rooms");
	$sShelterLess    = $objDb->getField(0, "shelter_less");
	$sMultiGrading   = $objDb->getField(0, "multi_grading");
	$iAvgAttendance  = $objDb->getField(0, "avg_attendance");
	$sPreSelection   = $objDb->getField(0, "pre_selection");
	$sComments       = $objDb->getField(0, "comments");
	$sStatus         = $objDb->getField(0, "status");
	
	
	$iProvince = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
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
	<label for="txtCode">EMIS Code</label>
	<div><input type="text" name="txtCode" id="txtCode" value="<?= getDbValue("code", "tbl_schools", "id='$iSchool'") ?>" maxlength="10" size="30" class="textbox" /></div>

	<div class="br10"></div>
	
	<label for="txtEnumerator">Enumerator Name</label>
	<div><input type="text" name="txtEnumerator" id="txtEnumerator" value="<?= formValue($sEnumerator) ?>" maxlength="100" size="30" class="textbox" /></div>

	<div class="br10"></div>
	
	<label for="txtDate">Survey Date</label>
	<div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

	<div class="br10"></div>
		  
	<label for="ddOperational">Is the school operational?</label>

	<div>
	  <select name="ddOperational" id="ddOperational">
		<option value="Y"<?= (($sOperational == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sOperational != "Y") ? ' selected' : '') ?>>No</option>
	  </select>
	  
	  <select name="ddNonOperational" id="ddNonOperational"<?= (($sOperational == "Y") ? ' class="hidden"' : '') ?>>
		<option value=""></option>
		<option value="Ghost school"<?= (($sOperational == "Ghost school") ? ' selected' : '') ?>>Ghost school</option>
		<option value="Denotified school"<?= (($sOperational == "Denotified school") ? ' selected' : '') ?>>Denotified school</option>
		<option value="Merged out school"<?= (($sOperational == "Merged out school") ? ' selected' : '') ?>>Merged out school</option>
		<option value="No students"<?= (($sOperational == "No students") ? ' selected' : '') ?>>No students</option>
		<option value="No teachers"<?= (($sOperational == "No teachers") ? ' selected' : '') ?>>No teachers</option>
		<option value="Insufficient facilities/ infrastructure"<?= (($sOperational == "Insufficient facilities/ infrastructure") ? ' selected' : '') ?>>Insufficient facilities/ infrastructure</option>
		<option value="Inaccessible"<?= (($sOperational == "Inaccessible") ? ' selected' : '') ?>>Inaccessible</option>
		<option value="Dispute"<?= (($sOperational == "Dispute") ? ' selected' : '') ?>>Dispute</option>
		<option value="Security"<?= (($sOperational == "Security") ? ' selected' : '') ?>>Security</option>
		<option value="Consolidated"<?= (($sOperational == "Consolidated") ? ' selected' : '') ?>>Consolidated</option>
		<option value="Other"<?= (($sOperational == "Other") ? ' selected' : '') ?>>Other</option>
	  </select>
	</div>
	
<?
	$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='1'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q01-")) ?></a></li>
<?
		}
?>
	  </ul>
	</div>		
<?
	}

	
	
	if ($sOperational == "Y")
	{
		if ($iProvince == 1)
		{
?>
	<div class="br10"></div>
		  
	<label for="ddPefProgramme">Is the school part of the PEF (Punjab Education Foundation) Programme?</label>

	<div>
	  <select name="ddPefProgramme" id="ddPefProgramme">
		<option value=""></option>
		<option value="Y"<?= (($sPefProgramme == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sPefProgramme == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>

<?
			$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='9'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );
			
			if ($iCount > 0)
			{		
?>
	<div>
	  <ul>	
<?
				for ($i = 0; $i < $iCount; $i ++)
				{
					$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q09-")) ?></a></li>
<?
				}
?>
	  </ul>
	</div>		
<?
			}			
		}
		
		
		if ($sPefProgramme == "" || $sPefProgramme == "N")
		{
?>
	<div class="br10"></div>
		  
	<label for="ddLandAvailable">Does the school have enough land for new construction?</label>

	<div>
	  <select name="ddLandAvailable" id="ddLandAvailable">
		<option value=""></option>
		<option value="Y"<?= (($sLandAvailable == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sLandAvailable == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>

<?
			$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='2'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );
			
			if ($iCount > 0)
			{		
?>
	<div>
	  <ul>	
<?
				for ($i = 0; $i < $iCount; $i ++)
				{
					$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q02-")) ?></a></li>
<?
				}
?>
	  </ul>
	</div>		
<?
			}
		
		
			if ($sLandAvailable == "Y" || $iProvince == 2)
			{
?>
	<div class="br10"></div>
		  
	<label for="ddLandDispute">Is the school having any land dispute?</label>

	<div>
	  <select name="ddLandDispute" id="ddLandDispute">
		<option value=""></option>
		<option value="Y"<?= (($sLandDispute != "N") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sLandDispute == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	  
	  <select name="ddDispute" id="ddDispute"<?= (($sLandDispute != "N") ? '' : ' class="hidden"') ?>>
	    <option value=""></option>
	    <option value="Property is rented"<?= (($sLandDispute == "Property is rented") ? ' selected' : '') ?>>Property is rented</option>
	    <option value="Not government property"<?= (($sLandDispute == "Not government property") ? ' selected' : '') ?>>Not government property</option>
	    <option value="Occupied by anyone else"<?= (($sLandDispute == "Occupied by anyone else") ? ' selected' : '') ?>>Occupied by anyone else</option>
	    <option value="No land mutation"<?= (($sLandDispute == "No land mutation") ? ' selected' : '') ?>>No land mutation</option>
	    <option value="Litigation issues"<?= (($sLandDispute == "Litigation issues") ? ' selected' : '') ?>>Litigation issues</option>
	    <option value="Restrictive covenant issues"<?= (($sLandDispute == "Restrictive covenant issues") ? ' selected' : '') ?>>Restrictive covenant issues</option>
	  </select>
	</div>
	
<?
				$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='3'";
				$objDb->query($sSQL);

				$iCount = $objDb->getCount( );
				
				if ($iCount > 0)
				{		
?>
	<div>
	  <ul>	
<?
					for ($i = 0; $i < $iCount; $i ++)
					{
						$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q03-")) ?></a></li>
<?
					}
?>
	  </ul>
	</div>		
<?
				}
		
		
				if ($sLandDispute == "N")
				{
?>	
	<div class="br10"></div>
		  
	<label for="ddOtherFunding">Is the school involved in any other project providing funding for classroom infrastructure?</label>

	<div>
	  <select name="ddOtherFunding" id="ddOtherFunding">
		<option value=""></option>
		<option value="Y"<?= (($sOtherFunding == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sOtherFunding == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>

<?
					$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='4'";
					$objDb->query($sSQL);

					$iCount = $objDb->getCount( );
					
					if ($iCount > 0)
					{		
?>
	<div>
	  <ul>	
<?
						for ($i = 0; $i < $iCount; $i ++)
						{
							$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q04-")) ?></a></li>
<?
						}
?>
	  </ul>
	</div>		
<?
					}
		
		
					if ($sOtherFunding == "N")
					{
?>		
	<div class="br10"></div>
		  
	<label for="txtClassRooms">How many classrooms does your school have?</label>
	<div><input type="text" name="txtClassRooms" id="txtClassRooms" value="<?= $iClassRooms ?>" maxlength="2" size="10" class="textbox" /></div>

	<div class="br10"></div>
		  
	<label for="txtEducationRooms">Out of the total number how many classrooms are in use for educational purposes?</label>
	<div><input type="text" name="txtEducationRooms" id="txtEducationRooms" value="<?= $iEducationRooms ?>" maxlength="2" size="10" class="textbox" /></div>
<?
						$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='5'";
						$objDb->query($sSQL);

						$iCount = $objDb->getCount( );
						
						if ($iCount > 0)
						{		
?>
	<div>
	  <ul>	
<?
							for ($i = 0; $i < $iCount; $i ++)
							{
								$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q05-")) ?></a></li>
<?
							}
?>
	  </ul>
	</div>		
<?
						}
?>
	<div class="br10"></div>
		  
	<label for="ddShelterLess">Are there any shelter-less grades being taught?</label>

	<div>
	  <select name="ddShelterLess" id="ddShelterLess">
		<option value=""></option>
		<option value="Y"<?= (($sShelterLess == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sShelterLess == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>
<?
						$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='6'";
						$objDb->query($sSQL);

						$iCount = $objDb->getCount( );
						
						if ($iCount > 0)
						{		
?>
	<div>
	  <ul>	
<?
							for ($i = 0; $i < $iCount; $i ++)
							{
								$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q06-")) ?></a></li>
<?
							}
?>
	  </ul>
	</div>		
<?
						}
?>
	<div class="br10"></div>
		  
	<label for="ddMultiGrading">Are there more than 2 grades being taught in one classroom (multi-grading)?</label>

	<div>
	  <select name="ddMultiGrading" id="ddMultiGrading">
		<option value=""></option>
		<option value="Y"<?= (($sMultiGrading == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sMultiGrading == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>			
<?
						$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='7'";
						$objDb->query($sSQL);

						$iCount = $objDb->getCount( );
						
						if ($iCount > 0)
						{		
?>
	<div>
	  <ul>	
<?
							for ($i = 0; $i < $iCount; $i ++)
							{
								$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q07-")) ?></a></li>
<?
							}
?>
	  </ul>
	</div>		
<?
						}
?>
	<div class="br10"></div>
	
	<label for="txtAvgAttendance">What is the average attendance of school?</label>
	<div><input type="text" name="txtAvgAttendance" id="txtAvgAttendance" value="<?= $iAvgAttendance ?>" maxlength="4" size="10" class="textbox" /></div>
<?
						$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='8'";
						$objDb->query($sSQL);

						$iCount = $objDb->getCount( );
						
						if ($iCount > 0)
						{		
?>
	<div>
	  <ul>	
<?
							for ($i = 0; $i < $iCount; $i ++)
							{
								$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q08-")) ?></a></li>
<?
							}
?>
	  </ul>
	</div>		
<?
						}
					}
				}
			}
		}
	}
?>

	<div class="br10"></div>
		  
	<label for="ddPreSelection">Does the School Qualify Pre-Selection?</label>

	<div>
	  <select name="ddPreSelection" id="ddPreSelection">
	    <option value=""></option>
		<option value="Y"<?= (($sPreSelection == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sPreSelection != "Y") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>  
	
<?
	$sSQL = "SELECT picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' AND section_id='0' AND question_id='10'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$sPicture = $objDb->getField($i, "picture");
?>
	    <li><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSurveyId}-Q01-")) ?></a></li>
<?
		}
?>
	  </ul>
	</div>		
<?
	}
?>

	<div class="br10"></div>			

	<label for="txtComments">Any other relevant Comments <span>(Optional)</span></label>
	<div><textarea name="txtComments" id="txtComments" rows="10" style="width:500px;"><?= $sComments ?></textarea></div>
	
	<div class="br10"></div>

	<label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="C"<?= (($sStatus == 'C') ? ' selected' : '') ?>>Completed</option>
		<option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Complete</option>
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