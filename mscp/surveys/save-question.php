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

	$_SESSION["Flag"] = "";

	$iSection   = IO::intValue("ddSection");
	$sType      = IO::strValue("ddType");
	$sQuestion  = IO::strValue("txtQuestion");
	$sOptions   = IO::strValue("txtOptions");
	$sOther     = IO::strValue("cbOther");
	$sPicture   = IO::strValue("cbPicture");
	$sMandatory = IO::strValue("ddMandatory");	
	$sInputType = IO::strValue("ddInputType");
	$iLink      = IO::intValue("txtLink");
	$sLink      = IO::strValue("ddLink");
	$sHint      = IO::strValue("txtHint");
	$iPosition  = IO::intValue("txtPosition");	
	$sStatus    = IO::strValue("ddStatus");	
	$bError     = true;
	$iQuestion  = getNextId("tbl_survey_questions");

	if ($iSection == 0 || $sType == "" || $sQuestion == "" || (($sType == "MS" || $sType == "SS") && $sOptions == "") || $sMandatory == "" || ($sType == "SL" && $sInputType == "") || ($iLink > 0 && $sLink == "") || $iPosition < 0 || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
	
	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_survey_questions WHERE (section_id='$iSection' AND question LIKE '$sQuestion')";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_QUESTION_EXISTS";
	}
	
	if ($_SESSION["Flag"] == "" && $iLink > 0)
	{
		if (getDbValue("COUNT(1)", "tbl_survey_questions", "id='$iLink'") == 0 || $iLink == $iQuestion)
			$_SESSION["Flag"] = "LINK_QUESTION_NOT_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$iNext = (($iPosition > 0) ? $iPosition : (getDbValue("MAX(position)", "tbl_survey_questions", "section_id='$iSection'") + 1));
		$bFlag = $objDb->execute("BEGIN");
		
		
		$sSQL = "INSERT INTO tbl_survey_questions SET id          = '$iQuestion',
													  section_id  = '$iSection',
													  `type`      = '$sType',
													  question    = '$sQuestion',
													  options     = '$sOptions',
													  other       = '$sOther',
													  picture     = '$sPicture',
													  mandatory   = '$sMandatory',
													  input_type  = '$sInputType',
													  link_id     = '$iLink',
													  link_value  = '$sLink',
													  hint        = '$sHint',
													  status      = '$sStatus',
													  position    = '$iNext',
													  created_by  = '{$_SESSION['AdminId']}',
													  created_at  = NOW( ),
													  modified_by = '{$_SESSION['AdminId']}',
													  modified_at = NOW( )";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true && $iPosition > 0)
		{
			$sSQL  = "UPDATE tbl_survey_questions SET position='$iPosition' WHERE id='$iStage'";
			$bFlag = $objDb->execute($sSQL);
			
			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_survey_questions SET position=(position + '1') WHERE position>='$iPosition' AND section_id='$iSection' AND id!='$iQuestion'";
				$bFlag = $objDb->execute($sSQL);
			}
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");
			
			redirect("questions.php", "SURVEY_QUESTION_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>