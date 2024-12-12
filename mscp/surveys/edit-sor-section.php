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

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iSorId     = IO::intValue("SorId");
	$iSectionId = IO::intValue("SectionId");
	$iIndex     = IO::intValue("Index");

	if ($_POST)
		@include("update-sor-section.php");


	$sSQL = "SELECT school_id, engineer_id, `date` FROM tbl_sors WHERE id='$iSorId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool      = $objDb->getField(0, "school_id");
        $iDistrictEng = $objDb->getField(0, "engineer_id");
	$sDate        = $objDb->getField(0, "date");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-sor-section.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
	<input type="hidden" name="SorId" id="SorId" value="<?= $iSorId ?>" />
	<input type="hidden" name="SectionId" id="SectionId" value="<?= $iSectionId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<div id="RecordMsg" class="hidden"></div>
	
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
	    <td width="50%">
		  <b>School</b><br />
		  <?= getDbValue("CONCAT(code, ' - ', name)", "tbl_schools", "id='$iSchool'") ?><br />
		  <br />
		  
		  <b>Section</b><br />
		  <?= getDbValue("name", "tbl_sor_sections", "id='$iSectionId'") ?><br />
		</td>
		
	    <td width="50%">
		  <b>District Engineer</b><br />
		  <?= getDbValue("name", "tbl_admins", "id='$iDistrictEng'") ?><br />
		  <br />
		  
		  <b>Date</b><br />
		  <?= formatDate($sDate, $_SESSION['DateFormat']) ?><br />
		</td>
	  </tr>
	</table>
	
	<hr />
	
<?
	if ($iSectionId == 1)
		@include("sor-section-a.php");
	
	else if ($iSectionId == 2)
		@include("sor-section-b.php");
        
	else if ($iSectionId == 3)
		@include("sor-section-c.php");
        
	else if ($iSectionId == 4)
		@include("sor-section-d.php");
        
?>

	<hr />
    <button id="BtnSave">Save Section</button> 
    <button id="BtnCancel">Cancel</button>
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