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


	if ($_POST)
		@include("save-survey.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/surveys.js"></script>
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

      <div id="PageTabs" rel="<?= $_SERVER['REQUEST_URI'] ?>">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Baseline Surveys</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Survey</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Survey?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Survey Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Surveys?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Survey Records?<br />
	      </div>
		  
		  <div align="right">
<?
//	if ($_SESSION['AdminTypeId'] == 1)
	{
?>
                <button id="BtnVfExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-school-blocks.php">Export Data Verifications</button>
                <button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-surveys-csv.php">Export Surveys (CSV)</button>
                        
<?
	}
/*
	else
	{
?>
			<button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-surveys-excel.php">Export Surveys (Excel)</button>
<?
	}
*/

	$sSurveysSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSurveysSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";
?>
		  </div>
			
		  <br/>
		
		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_surveys', $sSurveysSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="15%">School</th>
				  <th width="8%">Code</th>
				  <th width="11%">District</th>
			      <th width="13%">Enumerator</th>
			      <th width="10%">Date</th>			
                  <th width="10%">Sync Status</th>
				  <th width="10%">Survey Status</th>
			      <th width="18%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sDistrictsList = getList("tbl_districts", "id", "name");
		
		
		$sSurveysSQL = " AND FIND_IN_SET(bs.district_id, '{$_SESSION['AdminDistricts']}')";

		if ($_SESSION["AdminSchools"] != "")
			$sSurveysSQL .= " AND FIND_IN_SET(bs.school_id, '{$_SESSION['AdminSchools']}') ";
		
		
		$sSQL = "SELECT bs.id, bs.completed, bs.enumerator, bs.date, bs.status, bs.qualified, bs.created_at, bs.modified_at, bs.app,
						s.code, s.name, s.district_id,
						(SELECT name FROM tbl_admins WHERE id=bs.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=bs.modified_by) AS _ModifiedBy
		         FROM tbl_surveys bs, tbl_schools s
		         WHERE bs.school_id=s.id $sSurveysSQL
				 ORDER BY bs.id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId         = $objDb->getField($i, "id");
			$sEnumerator = $objDb->getField($i, "enumerator");
			$sDate       = $objDb->getField($i, "date");
			$sStatus     = $objDb->getField($i, "status");
			$sQualified  = $objDb->getField($i, "qualified");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");
			$sCreatedAt  = $objDb->getField($i, "created_at");
			$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt = $objDb->getField($i, "modified_at");
			$sModifiedBy = $objDb->getField($i, "_ModifiedBy");
			$sApp        = $objDb->getField($i, "app");
			$sCompleted  = $objDb->getField($i, "completed");
			
			
			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 3, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSchool ?></td>
				  <td><?= $sCode ?></td>
		          <td><?= $sDistrictsList[$iDistrict] ?></td>
				  <td><?= $sEnumerator ?></td>
				  <td><?= formatDate($sDate, $_SESSION['DateFormat']) ?></td>
                  <td><?= (($sApp == 'Y' && $sStatus == 'I') ? "Syncing" : "Synced") ?></td>
		          <td><?= (($sCompleted == "Y") ? "Completed" : "In-Complete") ?></td>

		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y" && ($sStatus == "C" || $sApp != "Y"))
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}
			
			if ($sQualified == "Y")
			{
?>
					<img class="icnSurvey icon" id="<?= $iId ?>" src="images/icons/stats.gif" alt="Survey Details" title="Survey Details" rel="<?= $sSchool ?>" />
<?					
			}			

			if ($sUserRights["Delete"] == "Y" && ($sStatus == "C" || $sApp != "Y" || $_SESSION['AdminId'] == 1))
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
					<img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
<?
			if ($sStatus == "C")
			{
?>
					<a href="<?= $sCurDir ?>/export-survey.php?Id=<?= $iId ?>"><img class="icnPdf" src="images/icons/pdf.png" alt="Export Baseline Survey" title="Export Baseline Survey" /></a>
                                        <a href="<?= $sCurDir ?>/export-sor-form.php?Id=<?= $iId ?>"><img class="icnPdf2" src="images/icons/pdf2.png" alt="Export SOR Form" title="Export SOR Form" /></a>
<?                                      
			}
?>
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
		$iSchool   = getDbValue("id", "tbl_schools", ("code='".IO::strValue("txtCode")."'"));
		$iProvince = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
			<input type="hidden" id="Province" value="<?= $iProvince ?>" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="txtCode">EMIS Code</label>
		    <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="30" class="textbox" /></div>

		    <div class="br10"></div>
			
		    <label for="txtEnumerator">Enumerator Name</label>
		    <div><input type="text" name="txtEnumerator" id="txtEnumerator" value="<?= ((IO::strValue('txtDate') == "") ? $_SESSION['AdminName'] : IO::strValue('txtEnumerator', true)) ?>" maxlength="100" size="30" class="textbox" /></div>

		    <div class="br10"></div>
			
		    <label for="txtDate">Survey Date</label>
		    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= ((IO::strValue('txtDate') == "") ? date("Y-m-d") : IO::strValue('txtDate')) ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		    <div class="br10"></div>
				  
		    <label for="ddOperational">Is the school operational?</label>

		    <div>
			  <select name="ddOperational" id="ddOperational" rel="operational|N">
			    <option value=""></option>
			    <option value="Y"<?= ((IO::strValue("ddOperational") == "Y") ? ' selected' : '') ?>>Yes</option>
				<option value="N"<?= ((IO::strValue("ddOperational") == "N") ? ' selected' : '') ?>>No</option>
			  </select>
			  
			  <select name="ddNonOperational" id="ddNonOperational"<?= ((IO::strValue("ddOperational") == "N") ? '' : ' class="hidden"') ?>>
			    <option value=""></option>
			    <option value="Ghost school"<?= ((IO::strValue("ddNonOperational") == "Ghost school") ? ' selected' : '') ?>>Ghost school</option>
				<option value="Denotified school"<?= ((IO::strValue("ddNonOperational") == "Denotified school") ? ' selected' : '') ?>>Denotified school</option>
				<option value="Merged out school"<?= ((IO::strValue("ddNonOperational") == "Merged out school") ? ' selected' : '') ?>>Merged out school</option>
				<option value="No students"<?= ((IO::strValue("ddNonOperational") == "No students") ? ' selected' : '') ?>>No students</option>
				<option value="No teachers"<?= ((IO::strValue("ddNonOperational") == "No teachers") ? ' selected' : '') ?>>No teachers</option>
				<option value="Insufficient facilities/ infrastructure"<?= ((IO::strValue("ddNonOperational") == "Insufficient facilities/ infrastructure") ? ' selected' : '') ?>>Insufficient facilities/ infrastructure</option>
				<option value="Inaccessible"<?= ((IO::strValue("ddNonOperational") == "Inaccessible") ? ' selected' : '') ?>>Inaccessible</option>
				<option value="Dispute"<?= ((IO::strValue("ddNonOperational") == "Dispute") ? ' selected' : '') ?>>Dispute</option>
				<option value="Security"<?= ((IO::strValue("ddNonOperational") == "Security") ? ' selected' : '') ?>>Security</option>
				<option value="Consolidated"<?= ((IO::strValue("ddNonOperational") == "Consolidated") ? ' selected' : '') ?>>Consolidated</option>
				<option value="Other"<?= ((IO::strValue("ddNonOperational") == "Other") ? ' selected' : '') ?>>Other</option>
			  </select>			  
		    </div>
			
			
			<div id="PefProgramme" class="operational<?= (($iProvince == 1) ? '' : ' hidden') ?>">
		      <div class="br10"></div>
				  
		      <label for="ddPefProgramme">Is the school part of the PEF (Punjab Education Foundation) Programme?</label>

		      <div>
			    <select name="ddPefProgramme" id="ddPefProgramme" rel="pef|Y">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddPefProgramme") == "Y") ? ' selected' : '') ?>>Yes</option>
			      <option value="N"<?= ((IO::strValue("ddPefProgramme") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>			  
			</div>			

		    <div class="pef operational">
			  <div class="br10"></div>
				  
		      <label for="ddLandAvailable">Does the school have enough land for new construction?</label>

		      <div>
			    <select name="ddLandAvailable" id="ddLandAvailable" rel="land|N">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddLandAvailable") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddLandAvailable") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>
			</div>  

		    <div class="pef operational land">
			  <div class="br10"></div>
				  
		      <label for="ddLandDispute">Is the school having any land dispute?</label>

		      <div>
			    <select name="ddLandDispute" id="ddLandDispute" rel="dispute|Y">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddLandDispute") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddLandDispute") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
				
			    <select name="ddDispute" id="ddDispute"<?= ((IO::strValue("ddLandDispute") == "Y") ? '' : ' class="hidden"') ?>>
			      <option value=""></option>
			      <option value="Property is rented"<?= ((IO::strValue("ddDispute") == "Property is rented") ? ' selected' : '') ?>>Property is rented</option>
				  <option value="Not government property"<?= ((IO::strValue("ddDispute") == "Not government property") ? ' selected' : '') ?>>Not government property</option>
				  <option value="Occupied by anyone else"<?= ((IO::strValue("ddDispute") == "Occupied by anyone else") ? ' selected' : '') ?>>Occupied by anyone else</option>
				  <option value="No land mutation"<?= ((IO::strValue("ddDispute") == "No land mutation") ? ' selected' : '') ?>>No land mutation</option>
				  <option value="Litigation issues"<?= ((IO::strValue("ddDispute") == "Litigation issues") ? ' selected' : '') ?>>Litigation issues</option>
				  <option value="Restrictive covenant issues"<?= ((IO::strValue("ddDispute") == "Restrictive covenant issues") ? ' selected' : '') ?>>Restrictive covenant issues</option>
			    </select>					
		      </div>
			</div>  
			
		    <div class="pef operational land dispute">
		      <div class="br10"></div>
				  
		      <label for="ddOtherFunding">Is the school involved in any other project providing funding for classroom infrastructure?</label>

		      <div>
			    <select name="ddOtherFunding" id="ddOtherFunding" rel="funding|Y">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddOtherFunding") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddOtherFunding") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>
			</div>  
			
		    <div class="pef operational land dispute funding">		
		      <div class="br10"></div>
				  
		      <label for="txtClassRooms">How many classrooms does your school have?</label>
			  <div><input type="text" name="txtClassRooms" id="txtClassRooms" value="<?= IO::strValue("txtClassRooms") ?>" maxlength="2" size="10" class="textbox" /></div>

		      <div class="br10"></div>
				  
		      <label for="txtEducationRooms">Out of the total number how many classrooms are in use for educational purposes?</label>
			  <div><input type="text" name="txtEducationRooms" id="txtEducationRooms" value="<?= IO::strValue("txtEducationRooms") ?>" maxlength="2" size="10" class="textbox" /></div>
			</div>  

		    <div class="pef operational land dispute funding">
		      <div class="br10"></div>
				  
		      <label for="ddShelterLess">Are there any shelter-less grades being taught?</label>

		      <div>
			    <select name="ddShelterLess" id="ddShelterLess">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddShelterLess") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddShelterLess") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>
			</div>  

		    <div class="pef operational land dispute funding">			
		      <div class="br10"></div>
				  
		      <label for="ddMultiGrading">Are there more than 2 grades being taught in one classroom (multi-grading)?</label>

		      <div>
			    <select name="ddMultiGrading" id="ddMultiGrading">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddMultiGrading") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddMultiGrading") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>			
			</div>  

		    <div class="pef operational land dispute funding">			
		      <div class="br10"></div>
			
		      <label for="txtAvgAttendance">What is the average attendance of school?</label>
			  <div><input type="text" name="txtAvgAttendance" id="txtAvgAttendance" value="<?= IO::strValue("txtAvgAttendance") ?>" maxlength="4" size="10" class="textbox" /></div>
			</div>  
			
		    <div class="pef operational land dispute funding">			
		      <div class="br10"></div>
				  
		      <label for="ddPreSelection">Does the School Qualify Pre-Selection?</label>

		      <div>
			    <select name="ddPreSelection" id="ddPreSelection">
			      <option value=""></option>
			      <option value="Y"<?= ((IO::strValue("ddPreSelection") == "Y") ? ' selected' : '') ?>>Yes</option>
				  <option value="N"<?= ((IO::strValue("ddPreSelection") == "N") ? ' selected' : '') ?>>No</option>
			    </select>
		      </div>			
			</div>			

		    <div class="br10"></div>			

		    <label for="txtComments">Any other relevant Comments <span>(Optional)</span></label>
		    <div><textarea name="txtComments" id="txtComments" rows="10" style="width:500px;"><?= IO::strValue('txtComments') ?></textarea></div>
		
		    <br />
		    <button id="BtnSave">Save Survey</button>
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
	$objDbGlobal->close( );

	@ob_end_flush( );
?>