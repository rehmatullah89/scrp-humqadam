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

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iQuestionId = IO::intValue("QuestionId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		include("update-question.php");


	$sSQL = "SELECT * FROM tbl_survey_questions WHERE id='$iQuestionId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSection   = $objDb->getField(0, "section_id");
	$iLink      = $objDb->getField(0, "link_id");
	$sLink      = $objDb->getField(0, "link_value");	
	$sType      = $objDb->getField(0, "type");
	$sQuestion  = $objDb->getField(0, "question");
	$sOptions   = $objDb->getField(0, "options");	
	$sOther     = $objDb->getField(0, "other");
	$sPicture   = $objDb->getField(0, "picture");
	$sMandatory = $objDb->getField(0, "mandatory");
	$sInputType = $objDb->getField(0, "input_type");
	$sHint      = $objDb->getField(0, "hint");
	$iPosition  = $objDb->getField(0, "position");	
	$sStatus    = $objDb->getField(0, "status");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-question.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="QuestionId" id="QuestionId" value="<?= $iQuestionId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateQuestion" id="DuplicateQuestion" value="0" />
	<div id="RecordMsg" class="hidden"></div>

    <label for="ddSection">Section</label>

    <div>
	  <select name="ddSection" id="ddSection" style="width:95%;">
	    <option value=""></option>
<?
	$sSectionsList = getList("tbl_survey_sections", "id", "name", "type='V'", "position");

	foreach ($sSectionsList as $iSectionId => $sSection)
	{
?>
	    <option value="<?= $iSectionId ?>"<?= (($iSectionId == $iSection) ? ' selected' : '') ?>><?= $sSection ?></option>
<?
	}
?>
	  </select>
    </div>

    <div class="br10"></div>

	<label for="ddType">Type</label>

	<div>
	  <select name="ddType" id="ddType">
		<option value=""></option>
		<option value="YN"<?= (($sType == "YN") ? ' selected' : '') ?>>Yes / No</option>
		<option value="SS"<?= (($sType == "SS") ? ' selected' : '') ?>>Single Selection</option>
		<option value="MS"<?= (($sType == "MS") ? ' selected' : '') ?>>Multi Selection</option>
		<option value="SL"<?= (($sType == "SL") ? ' selected' : '') ?>>Single Line Text</option>
		<option value="ML"<?= (($sType == "ML") ? ' selected' : '') ?>>Multi Line Text</option>
	  </select>
	</div>

	<div class="br10"></div>
	
	<label for="txtQuestion">Question</label>
	<div><textarea name="txtQuestion" id="txtQuestion" rows="3" style="width:95%;"><?= $sQuestion ?></textarea></div>
	
	<div id="InputType"<?= (($sType == "SL") ? '' : ' class="hidden"') ?>>
	  <div class="br10"></div>

	  <label for="ddInputType">InputType</label>

	  <div>
		<select name="ddInputType" id="ddInputType">
		  <option value="T"<?= (($sInputType == "T") ? ' selected' : '') ?>>Text</option>
		  <option value="N"<?= (($sInputType == "N") ? ' selected' : '') ?>>Number</option>
		  <option value="D"<?= (($sInputType == "D") ? ' selected' : '') ?>>Decimal Number</option>
		  <option value="A"<?= (($sInputType == "A") ? ' selected' : '') ?>>Alphabets Only</option>
		  <option value="E"<?= (($sInputType == "E") ? ' selected' : '') ?>>Email Address</option>
		  <option value="P"<?= (($sInputType == "P") ? ' selected' : '') ?>>Phone Number</option>				  
		</select>
	  </div>			
	</div>
	
	<div id="Options"<?= (($sType == "SS" || $sType == "MS") ? '' : ' class="hidden"') ?>>
	  <div class="br10"></div>
	
	  <label for="txtOptions">Options <span>(One per Line)</span></label>
	  <div><textarea name="txtOptions" id="txtOptions" rows="5" style="width:95%;"><?= $sOptions ?></textarea></div>
	</div>

	<div class="br10"></div>
	
	<label for="cbOther" class="noPadding"><input type="checkbox" name="cbOther" id="cbOther" value="Y" <?= (($sOther == 'Y') ? 'checked' : '') ?> /> Show Textbox for "Other" Option</label>
	
	<div class="br10"></div>

	<label for="cbPicture" class="noPadding"><input type="checkbox" name="cbPicture" id="cbPicture" value="Y" <?= (($sPicture == 'Y') ? 'checked' : '') ?> /> Show Field for "Picture" Attachment</label>
	
	<div class="br10"></div>

	<label for="ddMandatory">Mandatory</label>

	<div>
	  <select name="ddMandatory" id="ddMandatory">
		<option value="Y"<?= (($sMandatory == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sMandatory == "N") ? ' selected' : '') ?>>No</option>
	  </select>
	</div>

	<div class="br10"></div>

	<label for="txtLink">Linked with Question <span>(ID/Option)</span></label>
	
	<div>
	  <input type="text" name="txtLink" id="txtLink" value="<?= (($iLink > 0) ? $iLink : "") ?>" maxlength="3" size="5" class="textbox" />

	  <select name="ddLink" id="ddLink">
		<option value=""></option>
		<option value="Y"<?= (($sLink == "Y") ? ' selected' : '') ?>>Yes</option>
		<option value="N"<?= (($sLink == "N") ? ' selected' : '') ?>>No</option>				
	  </select>			
	</div>
	
	<div class="br10"></div>
	
	<label for="txtHint">Guidance Note <span>(Optional)</span></label>
	<div><textarea name="txtHint" id="txtHint" rows="3" style="width:95%;"><?= $sHint ?></textarea></div>
	
	<div class="br10"></div>

	<label for="txtPosition">Position</label>
	<div><input type="text" name="txtPosition" id="txtPosition" value="<?= $iPosition ?>" maxlength="10" size="10" class="textbox" /></div>

	<div class="br10"></div>

	<label for="ddStatus">Status</label>

	<div>
	  <select name="ddStatus" id="ddStatus">
		<option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
		<option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
	  </select>
	</div>

    <br />
    <button id="BtnSave">Save Question</button>
    <button id="BtnCancel">Cancel</button>
  </form>
</div>


</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>