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


	$iSurveyId  = IO::intValue("SurveyId");
	$iSectionId = IO::intValue("SectionId");


	$sSQL = "SELECT school_id, enumerator, `date` FROM tbl_surveys WHERE id='$iSurveyId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool     = $objDb->getField(0, "school_id");
	$sEnumerator = $objDb->getField(0, "enumerator");
	$sDate       = $objDb->getField(0, "date");
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
	    <td width="50%">
		  <b>School</b><br />
		  <?= getDbValue("CONCAT(code, ' - ', name)", "tbl_schools", "id='$iSchool'") ?><br />
		  <br />
		  
		  <b>Section</b><br />
		  <?= getDbValue("name", "tbl_survey_sections", "id='$iSectionId'") ?><br />
		</td>
		
	    <td width="50%">
		  <b>Enumerator</b><br />
		  <?= $sEnumerator ?><br />
		  <br />
		  
		  <b>Date</b><br />
		  <?= formatDate($sDate, $_SESSION['DateFormat']) ?><br />
		</td>
	  </tr>
	</table>
	
	<hr />
		  
<?
	if (getDbValue("type", "tbl_survey_sections", "id='$iSectionId'") == "V")
		@include("survey-dynamic-section.php");
	
	else if ($iSectionId == 3)
		@include("survey-teacher-section.php");
	
	else if ($iSectionId == 4)
		@include("student-enrollment-section.php");
        
	else if ($iSectionId == 5)
		@include("student-attendance-numbers-section.php");
        
	else if ($iSectionId == 13)
		@include("facility-roomcount-condition-section.php");
        
	else if ($iSectionId == 14)
		@include("school-facilities-section.php");
	
	else if ($iSectionId == 15)
		@include("survey-checklist.php");
        
	else if ($iSectionId == 16)
		@include("declaration-section.php");
	
	
	$sStatus = getDbValue("status", "tbl_survey_sections", "survey_id='$iSurveyId' AND section_id='$iSectionId'");
?>

	<hr />

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
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>