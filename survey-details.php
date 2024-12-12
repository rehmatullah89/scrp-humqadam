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


	$iSurveyId = IO::intValue("Id");
	$sCode     = IO::strValue("Code");
	
	if ($sCode != "")
		$iSurveyId = getDbValue("id", "tbl_surveys", "school_id=(SELECT id FROM tbl_schools WHERE code='$sCode')");

	

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
	$sQualified      = $objDb->getField(0, "qualified");


	$iProvince = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
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
	<a href="<?= ADMIN_CP_DIR ?>/surveys/export-survey.php?Id=<?= $iSurveyId ?>" style="position:absolute; right:30px; top:15px; display:block; text-align:center;"><img src="images/icons/pdf.png" width="48" height="48" alt="" title="" /><br /><b>Download PDF</b></a>

	<b>EMIS Code</b><br />
	<div><?= getDbValue("code", "tbl_schools", "id='$iSchool'") ?></div>

	<div class="br10"></div>
	
	<b>Enumerator Name</b><br />
	<div><?= formValue($sEnumerator) ?></div>

	<div class="br10"></div>
	
	<b>Survey Date</b><br />
	<div><?= $sDate ?></div>

	<div class="br10"></div>
		  
	<b>Is the school operational?</b><br />
	<div><?= (($sOperational == "Y") ? "Yes" : "No ({$sOperational})") ?></div>
	
<?
	if ($sOperational == "Y")
	{
		if ($iProvince == 1)
		{
?>
	<div class="br10"></div>
		  
	<b>Is the school part of the PEF (Punjab Education Foundation) Programme?</b><br />
	<div><?= (($sPefProgramme == "Y") ? 'Yes' : 'No') ?></div>

<?
		}
		
		
		if ($sPefProgramme == "" || $sPefProgramme == "N")
		{
?>
	<div class="br10"></div>
		  
	<b>Does the school have enough land for new construction?</b><br />
	<div><?= (($sLandAvailable == "Y") ? 'Yes' : 'No') ?></div>

<?
			if ($sLandAvailable == "Y" || $iProvince == 2)
			{
?>
	<div class="br10"></div>
		  
	<b>Is the school having any land dispute?</b><br />
	<div><?= (($sLandDispute == "N") ? 'No' : "Yes ({$sLandDispute})") ?></div>
<?
				if ($sLandDispute == "N")
				{
?>	
	<div class="br10"></div>
		  
	<b>Is the school involved in any other project providing funding for classroom infrastructure?</b><br />
	<div><?= (($sOtherFunding == "Y") ? 'Yes' : 'No') ?></div>

<?
					if ($sOtherFunding == "N")
					{
?>		
	<div class="br10"></div>
		  
	<b>How many classrooms does your school have?</b><br />
	<div><?= $iClassRooms ?></div>

	<div class="br10"></div>
		  
	<b>Out of the total number how many classrooms are in use for educational purposes?</b><br />
	<div><?= $iEducationRooms ?></div>

	<div class="br10"></div>
		  
	<b>Are there any shelter-less grades being taught?</b><br />
	<div><?= (($sShelterLess == "Y") ? 'Yes' : 'No') ?></div>

	<div class="br10"></div>
		  
	<b>Are there more than 2 grades being taught in one classroom (multi-grading)?</b><br />
	<div><?= (($sMultiGrading == "Y") ? 'Yes' : 'No') ?></div>			

	<div class="br10"></div>
	
	<b>What is the average attendance of school?</b><br />
	<div><?= $iAvgAttendance ?></div>
	
	<div class="br10"></div>
	
	<b>Does the School Qualify Pre-Selection?</b><br />
	<div><?= (($sPreSelection == "Y") ? 'Yes' : 'No') ?></div>			
<?
					}
				}
			}
		}
	}
?>

	<div class="br10"></div>			

	<b>Any other relevant Comments</b><br />
	<div><?= (($sComments == "") ? "N/A" : nl2br($sComments)) ?></div>
	
	<div class="br10"></div>

	<b>Status</b><br />
	<div><?= (($sStatus == 'C') ? 'Completed' : 'In-Complete') ?></div>
<?
	if ($sQualified == "Y")
	{
?>
    <hr />
    <b>Survey Details</b><br />
	<br />

    <div class="grid">
	  <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
		<tr class="header">
		  <td width="6%" align="center">#</td>
		  <td width="70%" align="left">Section</td>			  
		  <td width="12%" align="left">Status</td>
		  <td width="12%" align="center">Options</td>
		</tr>
<?
		$sSQL = "SELECT ss.id, ss.name, sd.status FROM tbl_survey_details sd, tbl_survey_sections ss WHERE sd.section_id=ss.id AND sd.survey_id='$iSurveyId' ORDER BY ss.position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSection = $objDb->getField($i, "id");
			$sSection = $objDb->getField($i, "name");
			$sStatus  = $objDb->getField($i, "status");
?>
		<tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		  <td alin="center"><?= str_pad($iSection, 2, '0', STR_PAD_LEFT) ?></td>
		  <td><?= $sSection ?></td>
		  <td><?= (($sStatus == "C") ? "Completed" : "In-Complete") ?></td>
		  <td align="center"><img class="icnView" survey="<?= $iSurveyId ?>" section="<?= $iSection ?>" src="images/icons/view.gif" alt="View" title="View" style="cursor:pointer;" /></td>
		</tr>
<?
		}
?>
	  </table>
    </div>
	
    <script type="text/javascript">
    <!--
		$(document).ready(function( )
		{
			$(".icnView").click(function( )
			{
				var iSurveyId  = $(this).attr("survey");
				var iSectionId = $(this).attr("section");

				$.colorbox({ href:("<?= ADMIN_CP_DIR ?>/surveys/view-survey-section.php?SurveyId=" + iSurveyId + "&SectionId=" + iSectionId), width:"90%", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
			});
		});
    -->
    </script>	
<?
	}


	$sSQL = "SELECT section_id, question_id, picture FROM tbl_survey_pictures WHERE survey_id='$iSurveyId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );


	if ($iCount > 0)
	{
		$sSectionsList     = getList("tbl_survey_sections", "id", "name");
		$sSectionsTypeList = getList("tbl_survey_sections", "id", "type");
		$sQuestionsList    = getList("tbl_survey_questions", "id", "question");
		
		
		$sSectionsList["0"]      = "Pre-Selection Questions";
		
		$sQuestionsList["0-1"]   = "Is the school operational?";
		$sQuestionsList["0-2"]   = "Does the school have enough land for new construction?";
		$sQuestionsList["0-3"]   = "Is the school having any land dispute?";
		$sQuestionsList["0-4"]   = "Is the school involved in any other project providing funding for classroom infrastructure?";
		$sQuestionsList["0-5"]   = "How many classrooms does your school have?";
		$sQuestionsList["0-6"]   = "Are there any shelter-less grades being taught?";
		$sQuestionsList["0-7"]   = "Are there more than 2 grades being taught in one classroom (multi-grading)?";
		$sQuestionsList["0-8"]   = "What is the average attendance of school?";
		$sQuestionsList["0-9"]   = "Is the school part of the PEF (Punjab Education Foundation) Programme?";
		$sQuestionsList["0-10"]  = "Does the School Qualify Pre-Selection?";
		
		$sQuestionsList["3-1"]   = "Teachers - Sanctioned";
		$sQuestionsList["3-2"]   = "Teachers - Filled";
		$sQuestionsList["3-3"]   = "Teachers - Regularly Attending";
		$sQuestionsList["3-4"]   = "Support Staff - Sanctioned";
		$sQuestionsList["3-5"]   = "Support Staff - Filled";
		$sQuestionsList["3-6"]   = "Support Staff - Regularly Attending";
		$sQuestionsList["3-7"]   = "Management Staff - Sanctioned";
		$sQuestionsList["3-8"]   = "Management Staff - Filled";
		$sQuestionsList["3-9"]   = "Management Staff - Regularly Attending";
		
		$sQuestionsList["4-0"]   = "Register";
		
		for ($i = 0; $i <= 12; $i ++)
			$sQuestionsList["5-{$i}"] = "Morning Shift - Grade {$i}";
		
		for ($j = 0; $j <= 12; $j ++, $i ++)
			$sQuestionsList["5-{$i}"] = "Evening Shift - Grade {$j}";
		
		for ($i = 1; $i <= 10; $i ++)
			$sQuestionsList["13-{$i}"] = "Block # {$i}";
		
		$sQuestionsList["14-1"]   = "Playground for Girls";
		$sQuestionsList["14-2"]   = "Playground for Boys";
		$sQuestionsList["14-3"]   = "Playground for Both";
		$sQuestionsList["14-4"]   = "Boundry Wall";
		$sQuestionsList["14-5"]   = "Main Gate";
		$sQuestionsList["14-6"]   = "Retaining Wall";
?>
    <hr />
    <b>Survey Pictures</b><br />

    <ul style="list-style:none; margin:0px; padding:0px;">
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iSection  = $objDb->getField($i, "section_id");
			$iQuestion = $objDb->getField($i, "question_id");
			$sPicture  = $objDb->getField($i, "picture");
			
			if (!@file_exists(SURVEYS_DOC_DIR.'thumbs/'.$sPicture))
				createImage((SURVEYS_DOC_DIR.$sPicture), (SURVEYS_DOC_DIR.'thumbs/'.$sPicture), 200, 200);
			

			$sTooltip = "<b>{$sSectionsList[$iSection]}</b>";
			
			if ($sSectionsTypeList[$iSection] == "V")
				$sTooltip = "{$sTooltip} &raquo; {$sQuestionsList[$iQuestion]}";
			
			else
			{
				if (@isset($sQuestionsList["{$iSection}-{$iQuestion}"]))
					$sTooltip = "{$sTooltip} &raquo; {$sQuestionsList["{$iSection}-{$iQuestion}"]}";
			}
?>
      <li style="float:left; margin:5px 5px 0px 0px;"><a href="<?= (SITE_URL.SURVEYS_DOC_DIR.$sPicture) ?>" class="colorbox" title="<?= $sTooltip ?>"><img src="<?= (SITE_URL.SURVEYS_DOC_DIR.'thumbs/'.$sPicture) ?>" width="200" alt="" title="<?= $sSectionsList[$iSection] ?>" style="border:solid 1px #666666;" /></a></li>
<?
		}
?>
    </ul>

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
	$objDbGlobal->close( );

	@ob_end_flush( );
?>
