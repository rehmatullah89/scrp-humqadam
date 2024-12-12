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
        
	$sCode       = IO::strValue("txtCode");
	$iEnumerator = IO::intValue("ddEnumerator");
	$sDate       = IO::strValue("txtDate");
	$bError      = true;
	

	if ($sCode == "" || $iEnumerator == 0 || $sDate == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
	
	if ($_SESSION["Flag"] == "")
	{
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
		
		if ($iSchool == 0)
			$_SESSION["Flag"] = "INVALID_EMIS_CODE";
	}
	
	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_survey_schedules WHERE school_id='$iSchool'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_SCHEDULE_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
		$iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
		$sStatus   = ((getDbValue("COUNT(1)", "tbl_surveys", "school_id='$iSchool'") > 0) ? "C" : "P");
				
		
		$iSchedule = getNextId("tbl_survey_schedules");
		
		$sSQL = "INSERT INTO tbl_survey_schedules SET id          = '$iSchedule',
													  admin_id    = '$iEnumerator',
													  district_id = '$iDistrict',
													  school_id   = '$iSchool',
													  `date`      = '$sDate',											 
													  status      = '$sStatus',
													  created_by  = '{$_SESSION['AdminId']}',
													  created_at  = NOW( ),
													  modified_by = '{$_SESSION['AdminId']}',
													  modified_at = NOW( )";
		
		if ($objDb->execute($sSQL) == true)
			redirect("schedules.php", "SURVEY_SCHEDULE_ADDED");
		
		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>