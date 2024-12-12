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
		$sSQL = "SELECT * FROM tbl_survey_schedules WHERE school_id='$iSchool' AND id!='$iScheduleId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_SCHEDULE_EXISTS";
	}
	

	if ($_SESSION["Flag"] == "")
	{
		$iDistrict = getDbValue("district_id", "tbl_schools", "id='$iSchool'");
		$sStatus   = ((getDbValue("COUNT(1)", "tbl_surveys", "school_id='$iSchool'") > 0) ? "C" : "P");
			
			
		$sSQL = "UPDATE tbl_survey_schedules SET school_id   = '$iSchool',
												 district_id = '$iDistrict',
												 admin_id    = '$iEnumerator',
												 `date`      = '$sDate',											 
												 status      = '$sStatus',
												 modified_by = '{$_SESSION['AdminId']}',
												 modified_at = NOW( )
	             WHERE id='$iScheduleId'";
		
		if($objDb->execute($sSQL) == true)
		{
           	$sSchool     = getDbValue("name", "tbl_schools", "id='$iSchool'");
			$sDistrict   = getDbValue("name", "tbl_districts", "id='$iDistrict'");
			$sEnumerator = getDbValue("name", "tbl_admins", "id='$iEnumerator'");


			$sSQL = "SELECT created_at, modified_at,
							(SELECT name FROM tbl_admins WHERE id=tbl_survey_schedules.created_by) AS _CreatedBy,
							(SELECT name FROM tbl_admins WHERE id=tbl_survey_schedules.modified_by) AS _ModifiedBy
					 FROM tbl_survey_schedules
					 WHERE id='$iScheduleId'";
			$objDb->query($sSQL);

			$sCreatedAt  = $objDb->getField(0, "created_at");
			$sCreatedBy  = $objDb->getField(0, "_CreatedBy");
			$sModifiedAt = $objDb->getField(0, "modified_at");
			$sModifiedBy = $objDb->getField(0, "_ModifiedBy");

			
			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
	<script type="text/javascript">
	<!--
		var sFields = new Array( );

		sFields[0] = "<?= @htmlentities($sSchool) ?>";
		sFields[1] = "<?= @htmlentities($sCode) ?>";
		sFields[2] = "<?= @htmlentities($sDistrict) ?>";
        sFields[3] = "<?= @htmlentities($sEnumerator) ?>";		
		sFields[4] = "<?= formatDate($sDate, $_SESSION['DateFormat']) ?>";
		sFields[5] = "<?= (($sStatus == "C") ? "Completed" : "Pending") ?>";
		sFields[6] = '<img class="icon details" id="<?= $iScheduleId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnEdit" id="<?= $iScheduleId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[6] = (sFields[6] + '<img class="icnDelete" id="<?= $iScheduleId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sFields[6] = (sFields[6] + '<img class="icnView" id="<?= $iScheduleId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iScheduleId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Survey Schedule Record has been Updated successfully.");
	-->
	</script>
<?
			exit( );
        }

		else
			$_SESSION["Flag"] = "DB_ERROR";			
	}
?>