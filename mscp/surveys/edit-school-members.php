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


	$iSchoolId  = IO::intValue("SchoolId");
        $iIndex     = IO::intValue("Index");

	if ($_POST)
		@include("update-school-members.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-school-members.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
    <h4><?=  getDbValue("name", "tbl_schools", "id='$iSchoolId'") . " School Members";?></h4>  
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
	<input type="hidden" name="SchoolId" id="SchoolId" value="<?= $iSchoolId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
        <div id="RecordMsg" class="hidden"></div>


	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
		<td width="400">

		  <label for="ddType">Type</label>

		  <div>
			<select name="ddType" id="ddType">
			  <option value=""></option>
<?
	$sTypesList = getList("tbl_school_member_types", "id", "`type`");

	foreach ($sTypesList as $iTypeId => $sType)
	{
?>
			  <option value="<?= $iTypeId ?>"><?= $sType ?></option>
<?
	}
?>			  
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtName">Member Name</label>
                  <div><input type="text" name="txtName" id="txtName" value="" maxlength="50" size="15" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtPhone">Phone <span>(optional)</span></label>
		  <div><input type="text" name="txtPhone" id="txtPhone" value="" maxlength="20" size="20" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A">Active</option>
			  <option value="I">In-Active</option>
		    </select>
		  </div>

		  <br />
		  <button id="BtnSave">Save Member</button>
		  <button id="BtnCancel">Cancel</button>
        </td>

      </tr>
    </table>
  </form>
    <br/><br/>
<?
        $sSQL = "SELECT * FROM tbl_school_members WHERE school_id='$iSchoolId'";
	$objDb->query($sSQL);

        $iCount = $objDb->getCount( );
        
        if($iCount > 0)
        {
?>
    <h4>Existing Members</h4>
    <div class="grid">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align: left;">
            <thead>
                <tr class="header"  valign="top">
                  <th width="5%">#</th>
                  <th width="10%">Type</th>
                  <th width="40%">Member</th>
                  <th width="20%">Phone</th>
                  <th width="10%">Status</th>
                  <th width="15%">Options</th>
                </tr>
          </thead>
            <tbody>
    <?
            for ($i = 0; $i < $iCount; $i ++)
            {    
                $iId                 = $objDb->getField($i, "id");
                $sName              = $objDb->getField($i, "name");
                $iType              = $objDb->getField($i, "type_id");
                $sPhone             = $objDb->getField($i, "phone");
                $sStatus            = $objDb->getField($i, "status");

    ?>
            <tr>
                <td><?=$i+1?></td>
                <td><?=@$sTypesList[$iType]?></td>
                <td><?=$sName?></td>
                <td><?=$sPhone?></td>
                <td><?=($sStatus == 'A'?'Active':'In-Active')?></td>
                <td><?
                
                if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}
                if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
                ?></td>
            </tr>
    <?
            }
    ?>
            </tbody>
        </table>
    </div>
<?
            }
?>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>