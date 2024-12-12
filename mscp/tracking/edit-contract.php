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


	$iContractId = IO::intValue("ContractId");
	$iIndex      = IO::intValue("Index");

	if ($_POST)
		@include("update-contract.php");


	$sSQL = "SELECT * FROM tbl_contracts WHERE id='$iContractId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sTitle      = $objDb->getField(0, "title");
	$iContractor = $objDb->getField(0, "contractor_id");
	$sDetails    = $objDb->getField(0, "details");
	$sStartDate  = $objDb->getField(0, "start_date");
	$sEndDate    = $objDb->getField(0, "end_date");
	$iSchools    = $objDb->getField(0, "schools");
	$sStatus     = $objDb->getField(0, "status");


	$sSchools = array( );

	$sSQL = "SELECT id, name, code FROM tbl_schools WHERE FIND_IN_SET(id, '$iSchools')";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iId   = $objDb->getField($i, "id");
		$sName = $objDb->getField($i, "name");
		$sCode = $objDb->getField($i, "code");

		$sSchools[] = array("id" => $iId, "name" => "{$sName} ({$sCode})");
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-contract.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
	<input type="hidden" name="ContractId" id="ContractId" value="<?= $iContractId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateContract" id="DuplicateContract" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="450">
		  <label for="txtTitle">Title</label>
		  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= formValue($sTitle) ?>" maxlength="200" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddContractor">Contractor</label>

		  <div>
			<select name="ddContractor" id="ddContractor">
			  <option value=""></option>
<?
	$sContractorsList = getList("tbl_contractors", "id", "company");

	foreach ($sContractorsList as $iContractorId => $sContractor)
	{
?>
			  <option value="<?= $iContractorId ?>"<?= (($iContractorId == $iContractor) ? ' selected' : '') ?>><?= $sContractor ?></option>
<?
	}
?>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtDetails">Details <span>(Optional)</span></label>
		  <div><textarea name="txtDetails" id="txtDetails" rows="4" cols="42"><?= $sDetails ?></textarea></div>

		  <div class="br10"></div>

		  <label for="txtStartDate">Start Date</label>
		  <div class="date"><input type="text" name="txtStartDate" id="txtStartDate" value="<?= $sStartDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		  <div class="br10"></div>

		  <label for="txtEndDate">End Date</label>
		  <div class="date"><input type="text" name="txtEndDate" id="txtEndDate" value="<?= $sEndDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>

		  <br />
		  <button id="BtnSave">Save Contract</button>
		  <button id="BtnCancel">Cancel</button>
		</td>

		<td>
		  <h4 style="width:400px;">Schools</h4>
		  <input type="text" name="txtSchools" id="txtSchools" value="" />
		  <div class="hidden" id="Schools"><?= @json_encode($sSchools) ?></div>
		</td>
	  </tr>
	</table>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>