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

	if ($_POST)
		@include("save-stage.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/stages.js"></script>
</head>

<body>

<div id="MainDiv">

<!--  Header Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/header.php");
?>
<!--  Header Section Ends Here  -->


<!--  Navigation Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/navigation.php");
?>
<!--  Navigation Section Ends Here  -->


<!--  Body Section Starts Here  -->
  <div id="Body">
<?
	@include("{$sAdminDir}includes/breadcrumb.php");
?>

    <div id="Contents">
      <input type="hidden" id="OpenTab" value="<?= (($_POST && $bError == true) ? 1 : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Project Stages</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Stage</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Stage?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Stage?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Stages?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Stages?<br />
	      </div>
              
              <div align="right"><button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-stages.php">Export Stages</button></div><br/>

		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_stages') ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_stages">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="20%">Stage</th>
			      <th width="35%">Parent Stage</th>
			      <th width="10%">Unit</th>
			      <th width="10%">Weightage</th>
			      <th width="10%">Duration</th>
			      <th width="10%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sStages = array( );


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iParent = $objDb->getField($i, "id");
			$sParent = $objDb->getField($i, "name");

			$sStages[$iParent] = $sParent;


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY name";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );

			for ($j = 0; $j < $iCount2; $j ++)
			{
				$iStage = $objDb2->getField($j, "id");
				$sStage = $objDb2->getField($j, "name");

				$sStages[$iStage] = ($sParent." &raquo; ".$sStage);


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY name";
				$objDb3->query($sSQL);

				$iCount3 = $objDb3->getCount( );

				for ($k = 0; $k < $iCount3; $k ++)
				{
					$iSubStage = $objDb3->getField($k, "id");
					$sSubStage = $objDb3->getField($k, "name");

					$sStages[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);
				}
			}
		}


		
		$sSQL = "SELECT id, parent_id, name, unit, weightage, days, status, position,
		                (SELECT COUNT(1) FROM tbl_inspections WHERE stage_id=tbl_stages.id) AS _Inspections
		         FROM tbl_stages
		         ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId          = $objDb->getField($i, "id");
			$iParent      = $objDb->getField($i, "parent_id");
			$sName        = $objDb->getField($i, "name");
			$sUnit        = $objDb->getField($i, "unit");
			$fWeightage   = $objDb->getField($i, "weightage");
			$iDays        = $objDb->getField($i, "days");
			$sStatus      = $objDb->getField($i, "status");
			$iPosition    = $objDb->getField($i, "position");
			$iInspections = $objDb->getField($i, "_Inspections");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= $iPosition ?></td>
		          <td><?= $sName ?></td>
		          <td><?= $sStages[$iParent] ?></td>
		          <td><?= $sUnit ?></td>
		          <td><?= formatNumber($fWeightage) ?></td>
		          <td><?= formatNumber($iDays, false) ?></td>

		          <td>
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}

			if ($sUserRights["Delete"] == "Y" && $iInspections == 0)
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
		            <img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
		          </td>
		        </tr>
<?
		}
	}
?>
	          </tbody>
            </table>
		  </div>

	      <div id="SelectButtons"<?= (($iTotalRecords > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
	        <div class="br10"></div>

	        <div align="right">
		      <button id="BtnSelectAll">Select All</button>
		      <button id="BtnSelectNone">Clear Selection</button>
	        </div>
	      </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicateStage" id="DuplicateStage" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<label for="ddType">School Type</label>

		    <div>
			  <select name="ddType" id="ddType">
			    <option value=""></option>
			    <option value="S"<?= ((IO::strValue('ddType') == 'S') ? ' selected' : '') ?>>Single Storey</option>
			    <option value="D"<?= ((IO::strValue('ddType') == 'D') ? ' selected' : '') ?>>Double Storey</option>
				<option value="T"<?= ((IO::strValue('ddType') == 'T') ? ' selected' : '') ?>>Triple Storey</option>
				<option value="B"<?= ((IO::strValue('ddType') == 'B') ? ' selected' : '') ?>>Bespoke</option>
			  </select>
		    </div>
			
		    <div class="br10"></div>

		    <label for="ddWorkType">Work Type</label>

		    <div>
			  <select name="ddWorkType" id="ddWorkType">
			    <option value="B"<?= ((IO::strValue('ddWorkType') == 'B') ? ' selected' : '') ?>>New Construction & Rehabilitation</option>
			    <option value="N"<?= ((IO::strValue('ddWorkType') == 'N') ? ' selected' : '') ?>>New Construction</option>
			    <option value="R"<?= ((IO::strValue('ddWorkType') == 'R') ? ' selected' : '') ?>>Rehabilitation Only</option>
			  </select>
		    </div>

			<div class="br10"></div>

			<label for="ddNature">Stage Nature</label>

		    <div>
			  <select name="ddNature" id="ddNature">
			    <option value=""></option>
			    <option value="P"<?= ((IO::strValue('ddNature') == 'P') ? ' selected' : '') ?>>Parent Stage</option>
			    <option value="S"<?= ((IO::strValue('ddNature') == 'S') ? ' selected' : '') ?>>Sub Stage</option>
			  </select>
		    </div>

		    <div id="Parent"<?= ((IO::strValue("ddNature") == "S") ? "" : ' class="hidden"') ?>>
		      <div class="br10"></div>

		      <label for="ddParent">Parent Stage</label>

		      <div>
			    <select name="ddParent" id="ddParent">
			      <option value=""></option>
<?
		$sStages = array( );


		$sSQL = ("SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='".IO::strValue("ddType")."' ORDER BY position");
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iParent = $objDb->getField($i, "id");
			$sParent = $objDb->getField($i, "name");

			$sStages[$iParent] = $sParent;


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY position";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );

			for ($j = 0; $j < $iCount2; $j ++)
			{
				$iStage = $objDb2->getField($j, "id");
				$sStage = $objDb2->getField($j, "name");

				$sStages[$iStage] = ($sParent." &raquo; ".$sStage);


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
				$objDb3->query($sSQL);

				$iCount3 = $objDb3->getCount( );

				for ($k = 0; $k < $iCount3; $k ++)
				{
					$iSubStage = $objDb3->getField($k, "id");
					$sSubStage = $objDb3->getField($k, "name");

					$sStages[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);
				}
			}
		}

		
		foreach ($sStages as $iStage => $sStage)
		{
?>
			      <option value="<?= $iStage ?>" <?= ((IO::intValue('ddParent') == $iStage) ? ' selected' : '') ?>><?= $sStage ?></option>
<?
		}
?>
			    </select>
		      </div>
		    </div>

		    <div class="br10"></div>

		    <label for="txtName">Stage Name</label>
		    <div><input type="text" name="txtName" id="txtName" value="<?= IO::strValue('txtName', true) ?>" maxlength="100" size="37" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="ddUnit">Measurement Unit <span>(Optional)</span></label>

		    <div>
			  <select name="ddUnit" id="ddUnit">
			    <option value=""<?= ((IO::strValue('ddUnit') == '') ? ' selected' : '') ?>></option>
			    <option value="cft"<?= ((IO::strValue('ddUnit') == 'cft') ? ' selected' : '') ?>>cft</option>
			    <option value="sft"<?= ((IO::strValue('ddUnit') == 'sft') ? ' selected' : '') ?>>sft</option>
			    <option value="Kg"<?= ((IO::strValue('ddUnit') == 'Kg') ? ' selected' : '') ?>>Kg</option>
			    <option value="No"<?= ((IO::strValue('ddUnit') == 'No') ? ' selected' : '') ?>>No</option>
			  </select>
		    </div>

			<div class="br10"></div>
			
			<label for="txtWeightage">Weightage <span>(Optional)</span></label>
			<div><input type="text" name="txtWeightage" id="txtWeightage" value="<?= IO::strValue("txtWeightage") ?>" maxlength="10" size="10" class="textbox" /></div>

			<div class="br10"></div>

			<label for="txtDays">Activity Duration <span>(Days)</span></label>
			<div><input type="text" name="txtDays" id="txtDays" value="<?= IO::strValue("txtDays") ?>" maxlength="10" size="10" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="">Failure Reasons <span>(Optional)</span></label>

		    <div class="multiSelect" style="width:350px; height:200px;">
			  <table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
		$sReasonsList = getList("tbl_failure_reasons", "id", "reason");

		foreach ($sReasonsList as $iReason => $sReason)
		{
?>
			    <tr valign="top">
				  <td width="25"><input type="checkbox" class="reason" name="cbReasons[]" id="cbReason<?= $iReason ?>" value="<?= $iReason ?>" <?= ((@in_array($iReason, IO::getArray('cbReasons'))) ? 'checked' : '') ?> /></td>
				  <td><label for="cbReason<?= $iReason ?>"><?= $sReason ?></label></td>
			    </tr>
<?
		}
?>
			  </table>
		    </div>
			
		    <div class="br10"></div>
			
			<label for="cbSkip" class="noPadding"><input type="checkbox" name="cbSkip" id="cbSkip" value="Y" <?= ((IO::strValue('cbSkip') == 'Y') ? 'checked' : '') ?> /> Skip in Progress Calculations</label>			
			
			<div class="br10"></div>

			<label for="txtPosition">Position <span>(Optional)</span></label>
			<div><input type="text" name="txtPosition" id="txtPosition" value="<?= IO::strValue("txtPosition") ?>" maxlength="10" size="10" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
			    <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
			  </select>
		    </div>
			
		    <br />
		    <button id="BtnSave">Save Stage</button>
		    <button id="BtnReset">Clear</button>
		  </form>
	    </div>
<?
	}
?>
	  </div>

    </div>
  </div>
<!--  Body Section Ends Here  -->


<!--  Footer Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/footer.php");
?>
<!--  Footer Section Ends Here  -->

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