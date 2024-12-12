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


	if ($iSection == 0 || $sType == "" || $sQuestion == "" || (($sType == "MS" || $sType == "SS") && $sOptions == "") || $sMandatory == "" || ($sType == "SL" && $sInputType == "") || ($iLink > 0 && $sLink == "") || $iPosition < 0 || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";
	
	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_survey_questions WHERE (section_id='$iSection' AND question LIKE '$sQuestion') AND id!='$iQuestionId'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "SURVEY_QUESTION_EXISTS";
	}
	
	if ($_SESSION["Flag"] == "" && $iLink > 0)
	{
		if (getDbValue("COUNT(1)", "tbl_survey_questions", "id='$iLink'") == 0 || $iLink == $iQuestionId)
			$_SESSION["Flag"] = "LINK_QUESTION_NOT_EXISTS";
	}	

	if ($_SESSION["Flag"] == "")
	{
		$bFlag = $objDb->execute("BEGIN");
		
		$sSQL = "UPDATE tbl_survey_questions SET section_id  = '$iSection',
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
												 position    = '$iPosition',
		                                         modified_by = '{$_SESSION['AdminId']}',
		                                         modified_at = NOW( )
		          WHERE id='$iQuestionId'";
		$bFlag = $objDb->execute($sSQL);

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");


			$sSection = getDbValue("name", "tbl_survey_sections", "id='$iSection'");


			$sSQL = "SELECT created_at, modified_at,
							(SELECT name FROM tbl_admins WHERE id=tbl_survey_questions.created_by) AS _CreatedBy,
							(SELECT name FROM tbl_admins WHERE id=tbl_survey_questions.modified_by) AS _ModifiedBy
					 FROM tbl_survey_questions
					 WHERE id='$iQuestionId'";
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

		sFields[0] = "<?= @htmlentities($sQuestion) ?>";
		sFields[1] = "<?= @htmlentities($sSection) ?>";
		sFields[2] = "<?= $iPosition ?>";
		sFields[3] = "<?= (($sStatus == "A") ? "Active" : "In-Active") ?>";
		sFields[4] = '<img class="icon details" id="<?= $iStudentId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" /> ';
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnToggle" id="<?= $iQuestionId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" /> ');
		sFields[4] = (sFields[4] + '<img class="icnEdit" id="<?= $iQuestionId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" /> ');
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
		sFields[4] = (sFields[4] + '<img class="icnDelete" id="<?= $iQuestionId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" /> ');
<?
			}
?>
		sFields[4] = (sFields[4] + '<img class="icnView" id="<?= $iQuestionId ?>" src="images/icons/view.gif" alt="View" title="View" /> ');

		parent.updateRecord(<?= $iQuestionId ?>, <?= $iIndex ?>, sFields);
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Question Record has been Updated successfully.");
	-->
	</script>
<?
			exit( );
		}

		else
		{
			$objDb->execute("ROLLBACK");
			
			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>