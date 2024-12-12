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

	$iSorId = IO::intValue("SorId");
	$iIndex = IO::intValue("Index");

	$iDistrictEngineersList = getList("tbl_admins", "id", "name", "type_id='6' ");
        
	$sSQL = "SELECT * FROM tbl_sors WHERE id='$iSorId'";
        $objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iSchool            = $objDb->getField(0, "school_id");
        $iDistrict          = $objDb->getField(0, "district_id");
	$sDate              = $objDb->getField(0, "date");
	$sCDC               = $objDb->getField(0, "admin_id");
        $iDistrictEngineer  = $objDb->getField(0, "engineer_id");
        $sPtcRepresentative = $objDb->getField(0, "ptc");
        $sCcsiRepresentative= $objDb->getField(0, "ccsi");
        $sContactNo         = $objDb->getField(0, "contact_no");        
        $sPrincipal         = $objDb->getField(0, "principal");
        
        $sSQL = "SELECT code, name, city, tehsil, village FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);
        
        $sCode    = $objDb->getField(0, "code");
        $sSchool  = $objDb->getField(0, "name");
        $sCity    = $objDb->getField(0, "city");
        $sTehsil  = $objDb->getField(0, "tehsil");
        $sVillage = $objDb->getField(0, "village");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-sor.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="SorId" id="SorId" value="<?= $iSorId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateSor" id="DuplicateSor" value="0" />
	<div id="RecordMsg" class="hidden"></div>
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr valign="top">
				<td width="450">
                                    <label for="txtCode">EMIS Code</label>
                                    <div><input type="text" name="txtCode" id="txtCode" value="<?= $sCode ?>" maxlength="10" size="20" class="textbox" /></div>
                                    <div class="br10"></div>
                                    
                                    <label for="ddEngineer">District Engineer</label>
                                    <div>
                                    <select name="ddEngineer" id="ddEngineer">
					  <option value="">Select District Engineer</option>
<?
		foreach ($iDistrictEngineersList as $iDistrictEngId => $sDistrictEng)
		{
?>
					  <option value="<?= $iDistrictEngId ?>"<?= (($iDistrictEngineer == $iDistrictEngId) ? ' selected' : '') ?>><?= $sDistrictEng ?></option>
<?
		}
?>
				    </select>
                                    </div>
                                    <div class="br10"></div>

                                    <label for="txtPrincipal">Head Teacher/ Principal</label>
                                    <div><input type="text" name="txtPrincipal" id="txtPrincipal" value="<?= $sPrincipal ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtCcsi">CCSI Representative</label>
                                    <div><input type="text" name="txtCcsi" id="txtCcsi" value="<?= $sCcsiRepresentative ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtPtc">PTC/SC Representative</label>
                                    <div><input type="text" name="txtPtc" id="txtPtc" value="<?= $sPtcRepresentative ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtContact">Contact No.</label>
                                    <div><input type="text" name="txtContact" id="txtContact" value="<?= $sContactNo ?>" maxlength="20" size="20" class="textbox" /></div>
                                    <div class="br10"></div>

                                    <label for="txtDate">Date</label>
                                    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= (($sDate == "") ? date("Y-m-d") : $sDate) ?>" maxlength="10" size="10" class="textbox" readonly /></div>
                                    <div class="br10"></div>
                        </td>
                        <td>
                            <h3>Add Participants</h3><br/>
                            <table id="ParticipantsTable" border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align:left;">
                                 <thead>
                                  <tr>
                                    <th width="5%">#</th>
                                     <th width="50%">Name</th>
                                     <th width="45%">Designation</th>
                                  </tr>
                                </thead>

                                <tbody>
<?
                                $sParticipantsList = getList("tbl_sor_participants", "name", "designation", "sor_id='$iSorId'");
                                $i=1;
                                if(count($sParticipantsList) > 0)
                                {
                                    foreach($sParticipantsList as $sParticipant => $sDesignation)
                                    {
?>
                                        <tr>
                                            <td><?=$i?></td><td><input type="text" class="textbox" name="pname_<?=$i?>" id="pname_<?=$i?>" value="<?=$sParticipant?>" style="width:95%;"/></td><td><input type="text" name="pdesignation_<?=$i?>" id="pdesignation_<?=$i?>" class="textbox" value="<?=$sDesignation?>" style="width:95%;"/></td>
                                        </tr>
<?
                                    
                                        $i++;
                                    }
                                }
?>
                                </tbody>
                            </table> 
                        </td>
                    </tr>
               </table>

        <div class="br10"></div>
<!--    <button id="BtnSave">Save SOR</button> 
    <button id="BtnCancel">Cancel</button> -->
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>