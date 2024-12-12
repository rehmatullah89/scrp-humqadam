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

	//if ($sUserRights["Edit"] != "Y")
	//	exitPopup(true);


	$iPackageId     = IO::intValue("PackageId");
        $iLots          = IO::intValue("Lots");       
	$sSchools       = getDbValue("schools", "tbl_packages","id='$iPackageId'");
        $sPackage       = getDbValue("title", "tbl_packages","id='$iPackageId'");
        $sSchoolsList   = getList("tbl_schools", "id", "CONCAT(code,'-',name)", "FIND_IN_SET(id, '$sSchools')");
        
        if ($_POST)
		@include("update-package-lots.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-package.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="PackageId" id="PackageId" value="<?= $iPackageId ?>" />
	<input type="hidden" name="Lots" value="<?= $iLots ?>" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="450">
                <h4><?=$sPackage?></h4>
                <div class="br10"></div>
<?              
        $sSQL = "SELECT * FROM tbl_package_lots WHERE  package_id = '$iPackageId' Order By lot_id";
        $objDb->query($sSQL);
        $iCount = $objDb->getCount( );

        for($i=0; $i < $iLots; $i++){  
            
            $sTitle   = $objDb->getField($i, "title");
            $sSchools = $objDb->getField($i, "schools");
            $iSchools = explode(',', $sSchools);
			
			if ($sTitle == "")
					$sTitle = ("Lot - ".($i + 1));
?> 

                 <label for="txtTitle" style="font-weight: bold;">Lot # <?=$i+1?></label>
                 <div class="br10"></div>
                 
                 <label for="txtTitle<?=$i?>">Title</label>
		 <div><input type="text" name="txtTitle_<?=$i?>" id="txtTitle_<?=$i?>" value="<?= formValue($sTitle) ?>" maxlength="200" size="25" class="textbox" required/></div>
                 <div class="br10"></div>

                 <label for="ddSchools">Schools</label>
				 
                 <div id="ddSchools" class="multiSelect" style="width:95%; min-height:180px;">
                    <table border="0" cellpadding="0" cellspacing="1" width="100%">
<?
                    foreach($sSchoolsList as $iSchool => $sSchool)
                    {
?>
                    <tr>
                        <td width="25"><input type="checkbox" class="checkbox" name="cbSchool_<?=$i?>[]" id="cbSchool_<?= $i ?>_<?= $iSchool ?>" index="<?= $i ?>" SchoolId="<?= $iSchool ?>" value="<?= $iSchool ?>" <?= ((@in_array($iSchool, $iSchools)) ? 'checked' : '') ?> /></td>
			<td><label for="cbSchool_<?= $i ?>_<?= $iSchool ?>"><?= $sSchool ?></label></td>
                    </tr>
<?
                    }
?>
                    </table>
		 </div>
                 <hr/>
<?
		}
?>
                 <br />
		  <button id="BtnSave">Save Lots</button>
		  <button id="BtnCancel">Cancel</button>
                </td>     
	  </tr>
	</table>
  </form>
</div>

<script type="text/javascript">
	<!--
$(".checkbox").change(function() {
    
    var SchoolId = $(this).attr("SchoolId");
    var Index = $(this).attr("index");
    var Lots  = "<?=$iLots?>";
    
    if(this.checked) {
        for(i=0; i < Lots; i++)
        {
            if(Index != i)
                $("#cbSchool_"+i+"_"+SchoolId).prop("disabled", true);
        }
    }else{
        for(i=0; i < Lots; i++)
        {
            $("#cbSchool_"+i+"_"+SchoolId).prop("disabled", false);
        }
    }
    
 });
      	-->
</script>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>