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
	$objDb4      = new Database( );

	if ($_POST)
		@include("save-inspection.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <link type="text/css" rel="stylesheet" href="plugins/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css" />

  <script type="text/javascript" src="plugins/plupload/plupload.full.min.js"></script>
  <script type="text/javascript" src="plugins/plupload/jquery.ui.plupload/jquery.ui.plupload.js"></script>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/inspections.js"></script>
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
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Inspections</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Inspection</a></li>
<?
	}


	$sInspectionsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sInspectionsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Inspection?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Inspection Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Inspections?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Inspection Records?<br />
	      </div>
		  
		  <div align="right"><button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-weekly-progress-report.php">Export Weekly Progress Report</button></div>
		  <br />


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_inspections', $sInspectionsSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="15%">Title</th>
			      <th width="10%">Date</th>
			      <th width="28%">Stage</th>
			      <th width="14%">School</th>
			      <th width="8%">Status</th>
			      <th width="8%">Completed</th>
			      <th width="12%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sSchoolsList = getList("tbl_schools", "id", "name", "id IN (SELECT DISTINCT(school_id) FROM tbl_inspections)");
		$sStagesList  = array( );


		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' ORDER BY name";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iParent = $objDb->getField($i, "id");
			$sParent = $objDb->getField($i, "name");


			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParent' ORDER BY name";
			$objDb2->query($sSQL);

			$iCount2 = $objDb2->getCount( );

			if ($iCount2 == 0)
				$sStagesList[$iParent] = $sParent;


			for ($j = 0; $j < $iCount2; $j ++)
			{
				$iStage = $objDb2->getField($j, "id");
				$sStage = $objDb2->getField($j, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY name";
				$objDb3->query($sSQL);

				$iCount3 = $objDb3->getCount( );

				if ($iCount3 == 0)
					$sStagesList[$iStage] = ($sParent." &raquo; ".$sStage);


				for ($k = 0; $k < $iCount3; $k ++)
				{
					$iSubStage = $objDb3->getField($k, "id");
					$sSubStage = $objDb3->getField($k, "name");


					$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iSubStage' ORDER BY name";
					$objDb4->query($sSQL);

					$iCount4 = $objDb4->getCount( );

					if ($iCount4 == 0)
						$sStagesList[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);


					for ($l = 0; $l < $iCount4; $l ++)
					{
						$iFourthStage = $objDb4->getField($l, "id");
						$sFourthStage = $objDb4->getField($l, "name");

						$sStagesList[$iFourthStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage." &raquo; ".$sFourthStage);
					}
				}
			}
		}


		$sSQL = "SELECT id, school_id, stage_id, title, `date`, file, picture, status, stage_completed, created_at, modified_at,
						IF (stage_completed='Y', (SELECT weightage FROM tbl_stages WHERE id=tbl_inspections.stage_id), '0') AS _Weightage,
						(SELECT name FROM tbl_admins WHERE id=tbl_inspections.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=tbl_inspections.modified_by) AS _ModifiedBy
		         FROM tbl_inspections
		         WHERE $sInspectionsSQL
		         ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId         = $objDb->getField($i, "id");
			$iSchool     = $objDb->getField($i, "school_id");
			$iStage      = $objDb->getField($i, "stage_id");
			$sTitle      = $objDb->getField($i, "title");
			$sDate       = $objDb->getField($i, "date");
			$sPicture    = $objDb->getField($i, "picture");
			$sDocument   = $objDb->getField($i, "file");
			$sStatus     = $objDb->getField($i, "status");
			$sCompleted  = $objDb->getField($i, "stage_completed");
			$fWeightage  = $objDb->getField($i, "_Weightage");
			$sCreatedAt  = $objDb->getField($i, "created_at");
			$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt = $objDb->getField($i, "modified_at");
			$sModifiedBy = $objDb->getField($i, "_ModifiedBy");

			switch ($sStatus)
			{
				case "P" : $sStatus = "Pass"; break;
				case "R" : $sStatus = "Re-Inspection"; break;
				case "F" : $sStatus = "Fail"; break;
				default  : $sStatus = "N/A"; break;
			}


			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sTitle ?></td>
		          <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
		          <td><?= $sStagesList[$iStage] ?></td>
		          <td><?= $sSchoolsList[$iSchool] ?></td>
		          <td><?= $sStatus ?></td>
		          <td><?= formatNumber($fWeightage) ?>%</td>

		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
					<img class="icon icnMeasurements" id="<?= $iId ?>" src="images/icons/boqs.png" alt="Measurements" title="Measurements" />
<?
			}

			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}

			if ($sPicture != "" && @file_exists($sRootDir.INSPECTIONS_IMG_DIR.$sPicture))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.INSPECTIONS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
<?
			}

			if ($sDocument != "" && @file_exists($sRootDir.INSPECTIONS_DOC_DIR.$sDocument))
			{
?>
					<a href="<?= $sCurDir ?>/download-inspection-document.php?Id=<?= $iId ?>&File=<?= $sDocument ?>"><img class="icnDownload" id="<?= (SITE_URL.INSPECTIONS_DOC_DIR.$sDocument) ?>" src="images/icons/download.gif" alt="Download" title="Download" /></a>
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
		$sCode   = IO::intValue('txtCode');
		$iBlock  = IO::intValue('ddBlock');
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		    <input type="hidden" name="DuplicateDocument" id="DuplicateDocument" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			  <tr valign="top">
				<td width="600">
				  <label for="txtCode">EMIS Code</label>
				  <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="20" class="textbox" /> (Press Tab after entering the EMIS Code)</div>
												  
				  <div class="br10"></div>

				  <label for="ddBlock">Block</label>

				  <div>
				    <select name="ddBlock" id="ddBlock">
					  <option value=""></option>
<?
		$sBlocksList = getList("tbl_school_blocks", "block", "CONCAT(block, ' - ', name)", "school_id='$iSchool'");

		foreach ($sBlocksList as $iBlockId => $sBlock)
		{
?> 
					  <option value="<?= $iBlockId ?>"<?= (($iBlockId == $iBlock) ? ' selected' : '') ?>><?= $sBlock ?></option>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="ddStage">Stage</label>

				  <div>
				    <select name="ddStage" id="ddStage" style="width:506px; max-width:506px;">
					  <option value=""></option>
<?
		$sStagesList = array( );
		
		if ($iSchool > 0 && $iBlock > 0)
		{
			$sSQL = "SELECT storey_type, design_type, work_type FROM tbl_school_blocks WHERE school_id='$iSchool' AND block='$iBlock'";
			$objDb->query($sSQL);

			$sStoreyType = $objDb->getField(0, "storey_type");
			$sDesignType = $objDb->getField(0, "design_type");
			$sWorkType   = $objDb->getField(0, "work_type");

		
			$sBlockType = (($sWorkType == "R") ? "R" : (($sDesignType == "B") ? "B" : $sStoreyType));

		
			$sSQL = "SELECT id, name FROM tbl_stages WHERE status='A' AND parent_id='0' AND `type`='$sBlockType' ORDER BY position";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iParent = $objDb->getField($i, "id");
				$sParent = $objDb->getField($i, "name");


				$sSQL = "SELECT id, name FROM tbl_stages WHERE status='A' AND parent_id='$iParent' AND `type`='$sBlockType' ORDER BY position";
				$objDb2->query($sSQL);

				$iCount2 = $objDb2->getCount( );

				if ($iCount2 == 0)
					$sStagesList[$iParent] = $sParent;


				for ($j = 0; $j < $iCount2; $j ++)
				{
					$iStage = $objDb2->getField($j, "id");
					$sStage = $objDb2->getField($j, "name");


					$sSQL = "SELECT id, name FROM tbl_stages WHERE status='A' AND parent_id='$iStage' AND `type`='$sBlockType' ORDER BY position";
					$objDb3->query($sSQL);

					$iCount3 = $objDb3->getCount( );

					if ($iCount3 == 0)
						$sStagesList[$iStage] = ($sParent." &raquo; ".$sStage);


					for ($k = 0; $k < $iCount3; $k ++)
					{
						$iSubStage = $objDb3->getField($k, "id");
						$sSubStage = $objDb3->getField($k, "name");


						$sSQL = "SELECT id, name FROM tbl_stages WHERE status='A' AND parent_id='$iSubStage' AND `type`='$sBlockType' ORDER BY position";
						$objDb4->query($sSQL);

						$iCount4 = $objDb4->getCount( );

						if ($iCount4 == 0)
							$sStagesList[$iSubStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage);


						for ($l = 0; $l < $iCount4; $l ++)
						{
							$iFourthStage = $objDb4->getField($l, "id");
							$sFourthStage = $objDb4->getField($l, "name");

							$sStagesList[$iFourthStage] = ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage." &raquo; ".$sFourthStage);
						}
					}
				}
			}
		}
	
	
		foreach ($sStagesList as $iStage => $sStage)
		{
?>
					  <option value="<?= $iStage ?>"<?= ((IO::intValue('ddStage') == $iStage) ? ' selected' : '') ?>><?= $sStage ?></option>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="txtTitle">Title</label>
				  <div><input type="text" name="txtTitle" id="txtTitle" value="<?= IO::strValue('txtTitle', true) ?>" maxlength="100" size="50" class="textbox" style="width:500px;" /></div>

				  <div class="br10"></div>

				  <label for="filePicture">Picture</label>
				  <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="50" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="fileDocument">Document</label>
				  <div><input type="file" name="fileDocument" id="fileDocument" value="<?= IO::strValue('fileDocument') ?>" size="50" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtDate">Date</label>
				  <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= IO::strValue('txtDate') ?>" maxlength="10" size="10" class="textbox" readonly /></div>

				  <div class="br10"></div>

				  <label for="txtDetails">Details <span>(Optional)</span></label>
				  <div><textarea name="txtDetails" id="txtDetails" rows="10" style="width:500px;"><?= IO::strValue('txtDetails') ?></textarea></div>

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="P"<?= ((IO::strValue('ddStatus') == 'P') ? ' selected' : '') ?>>Pass</option>
					  <option value="F"<?= ((IO::strValue('ddStatus') == 'F') ? ' selected' : '') ?>>Fail</option>
					  <option value="R"<?= ((IO::strValue('ddStatus') == 'R') ? ' selected' : '') ?>>Re-Inspection</option>
				    </select>
				  </div>

				  <div id="Failed"<?= ((IO::strValue("ddStatus") == "F") ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="ddReason">Failure Reason</label>

				    <div>
				      <select name="ddReason" id="ddReason">
					    <option value=""></option>
<?
		$sStageReasons = getDbValue("failure_reasons", "tbl_stages", ("id='".IO::intValue("ddStage")."'"));
		$sReasonsList  = getList("tbl_failure_reasons", "id", "reason", "FIND_IN_SET(id, '$sStageReasons')");

		foreach ($sReasonsList as $iReason => $sReason)
		{
?>
					    <option value="<?= $iReason ?>"<?= ((IO::intValue('ddReason') == $iReason) ? ' selected' : '') ?>><?= $sReason ?></option>
<?
		}
?>
				      </select>
				    </div>


					<div id="Comments"<?= ((IO::strValue("ddStatus") == "F" && IO::intValue('ddReason') == 5) ? '' : ' class="hidden"') ?>>
					  <div class="br10"></div>

					  <label for="txtComments">Comments</label>
					  <div><textarea name="txtComments" id="txtComments" rows="3" style="width:500px;"><?= IO::strValue("txtComments") ?></textarea></div>
					</div>
				  </div>

				  <div id="Passed"<?= ((IO::strValue("ddStatus") == "P" || IO::strValue("ddStatus") == "") ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="ddCompleted">Stage Completed?</label>

				    <div>
				      <select name="ddCompleted" id="ddCompleted">
					    <option value="N"<?= ((IO::strValue('ddCompleted') == "N") ? ' selected' : '') ?>>No</option>
					    <option value="Y"<?= ((IO::strValue('ddCompleted') == "Y") ? ' selected' : '') ?>>Yes</option>
				      </select>
				    </div>
				  </div>

				  <div id="ReInspection"<?= ((IO::strValue("ddStatus") == "R") ? '' : ' class="hidden"') ?>>
				    <div class="br10"></div>

				    <label for="txtReInspection">Re-Inspection Date</label>
				    <div class="date"><input type="text" name="txtReInspection" id="txtReInspection" value="<?= IO::strValue('txtReInspection') ?>" maxlength="10" size="10" class="textbox" readonly /></div>
				  </div>

				  <br />
				  <button id="BtnSave">Save Inspection</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <div id="Files" style="width:98%; height:350px;">Loading ...</div>
				</td>
			  </tr>
			</table>
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
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>