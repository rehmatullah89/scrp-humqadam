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
	$objDb3      = new Database( );


	$iScheduleId = IO::intValue("ScheduleId");

	$sSQL = "SELECT * FROM tbl_contract_schedules WHERE id='$iScheduleId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$iContract  = $objDb->getField(0, "contract_id");
	$iSchool    = $objDb->getField(0, "school_id");
	$sStartDate = $objDb->getField(0, "start_date");
	$sEndDate   = $objDb->getField(0, "end_date");


	$sSQL = "SELECT name, storey_type, design_type FROM tbl_schools WHERE id='$iSchool'";
	$objDb->query($sSQL);

	$sSchool     = $objDb->getField(0, "name");
	$sStoreyType = $objDb->getField(0, "storey_type");
	$sDesignType = $objDb->getField(0, "design_type");	

	$sSchoolType = (($sDesignType == "B") ? "B" : $sStoreyType);
	$iMainStage  = getDbValue("id", "tbl_stages", "status='A' AND parent_id='0' AND `type`='$sSchoolType'", "position DESC");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
</head>

<body class="popupBg">

<div id="PopupDiv">
  <form name="frmRecord" id="frmRecord">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="30%">
		  <label><b>Contract</b></label>
		  <div><?= getDbValue("title", "tbl_contracts", "id='$iContract'") ?></div>
		</td>

		<td width="30%">
		  <label><b>School</b></label>
		  <div><?= $sSchool ?></div>
		</td>

		<td width="20%">
		  <label><b>Start Date</b></label>
		  <div><?= formatDate($sStartDate, $_SESSION["DateFormat"]) ?></div>
		</td>

		<td width="20%">
		  <label><b>End Date</b></label>
		  <div><?= formatDate($sEndDate, $_SESSION["DateFormat"]) ?></div>
		</td>
	  </tr>
	</table>

	<hr />

	  <div class="grid">
	    <table width="100%" cellspacing="0" cellpadding="4" border="1" bordercolor="#ffffff">
		  <tr class="header" valign="top">
		    <td width="50">#</td>
		    <td>Stage</td>
		    <td width="125" align="center">Start Date</td>
		    <td width="125" align="center">End Date</td>
		  </tr>
<?
	$sStages = "0";


	$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iMainStage' AND `type`='$sSchoolType' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParent = $objDb->getField($i, "id");

		$sStages .= ",{$iParent}";


		$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");

			$sStages .= ",{$iStage}";


			$sSQL = "SELECT id FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");

				$sStages .= ",{$iSubStage}";
			}
		}
	}



	$sSQL = "SELECT csd.start_date, csd.end_date, 
					s.id, s.name
	         FROM tbl_contract_schedule_details csd, tbl_stages s
	         WHERE csd.stage_id=s.id AND csd.schedule_id='$iScheduleId' AND s.type='$sSchoolType'
			ORDER BY FIELD(s.id,$sStages)";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iStage     = $objDb->getField($i, "id");
		$sStage     = $objDb->getField($i, "name");
		$sStartDate = $objDb->getField($i, "start_date");
		$sEndDate   = $objDb->getField($i, "end_date");

		$sStartDate = (($sStartDate == "0000-00-00") ? "" : $sStartDate);
		$sEndDate   = (($sEndDate == "0000-00-00") ? "" : $sEndDate);
?>

		  <tr class="<?= ((($i % 2) == 0) ? 'even' : 'odd') ?>">
		    <td><?= ($i + 1) ?></td>
		    <td><?= $sStage ?></td>
		    <td align="center"><div class="date"><input type="text" name="txtStartDate<?= $iStage ?>" id="txtStartDate<?= $iStage ?>" value="<?= $sStartDate ?>" maxlength="10" size="10" class="textbox" /></div></td>
		    <td align="center"><div class="date"><input type="text" name="txtEndDate<?= $iStage ?>" id="txtEndDate<?= $iStage ?>" value="<?= $sEndDate ?>" maxlength="10" size="10" class="textbox" /></div></td>
		  </tr>
<?
	}
?>
	    </table>
	</div>
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>