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
		@include("save-school.php");


	$sDistricts = array( );

	$sSQL = "SELECT id, name FROM tbl_provinces ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iProvince = $objDb->getField($i, "id");
		$sProvince = $objDb->getField($i, "name");

		$sDistricts["P{$iProvince}"] = $sProvince;


		$sSQL = "SELECT id, name FROM tbl_districts WHERE province_id='$iProvince' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iDistrict = $objDb2->getField($j, "id");
			$sDistrict = $objDb2->getField($j, "name");

			$sDistricts[$iDistrict] = $sDistrict;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="plugins/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="plugins/ckeditor/adapters/jquery.js"></script>
  <script type="text/javascript" src="plugins/ckfinder/ckfinder.js"></script>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/schools.js"></script>
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
      <input type="hidden" id="OpenTab" value="<?= ((($_POST && $bError == true) || $_SESSION["Flag"] != "") ? 1 : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Schools</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New School</a></li>
<?
	}
	
	
	$sSchoolsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sSchoolsSQL .= " AND FIND_IN_SET(id, '{$_SESSION['AdminSchools']}') ";	
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete School?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this School?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Schools?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Schools?<br />
	      </div>

		  
	      <div align="right"><button id="BtnExport" rel="<?= (SITE_URL.ADMIN_CP_DIR) ?>/<?= $sCurDir ?>/export-schools.php">Export Schools</button></div>
	      <br />

		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_schools', $sSchoolsSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid" rel="tbl_schools">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="16%">School</th>
			      <th width="7%">Code</th>
			      <th width="7%">Type</th>
			      <th width="10%">Storey</th>
			      <th width="10%">Design</th>
			      <th width="10%">Students</th>
			      <th width="12%">Revised Cost</th>
			      <th width="10%">District</th>
			      <th width="13%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sTypesList = getList("tbl_school_types", "id", "`type`");
	
	
	if ($iTotalRecords <= 50)
	{
		$sSQL = "SELECT id, district_id, type_id, name, code, students, storey_type, design_type, cost, picture, status,
		                (SELECT COUNT(1) FROM tbl_inspections WHERE school_id=tbl_schools.id) AS _Inspections
		         FROM tbl_schools
				 WHERE $sSchoolsSQL
		         ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId          = $objDb->getField($i, "id");
			$iDistrict    = $objDb->getField($i, "district_id");
			$sName        = $objDb->getField($i, "name");
			$iType        = $objDb->getField($i, "type_id");
			$sCode        = $objDb->getField($i, "code");
			$sStoreyType  = $objDb->getField($i, "storey_type");
			$sDesignType  = $objDb->getField($i, "design_type");
			$iStudents    = $objDb->getField($i, "students");
			$fRevisedCost = $objDb->getField($i, "revised_cost");
			$sPicture     = $objDb->getField($i, "picture");
			$sStatus      = $objDb->getField($i, "status");
			$iInspections = $objDb->getField($i, "_Inspections");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= ($i + 1) ?></td>
		          <td><?= $sName ?></td>
		          <td><?= $sCode ?></td>
		          <td><?= $sTypesList[$iType] ?></td>
		          <td><?= (($sStoreyType == "S") ? "Single" : (($sStoreyType == "D") ? "Double" : "Triple")) ?></td>
		          <td><?= (($sDesignType == "R") ? "Regular" : "Bespoke") ?></td>
		          <td><?= formatNumber($iStudents, false) ?></td>
		          <td><?= formatNumber($fRevisedCost) ?></td>
		          <td><?= $sDistricts[$iDistrict] ?></td>

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

			if ($sPicture != "" && @file_exists($sRootDir.SCHOOLS_IMG_DIR.$sPicture))
			{
?>
					<img class="icnPicture" id="<?= (SITE_URL.SCHOOLS_IMG_DIR.$sPicture) ?>" src="images/icons/picture.png" alt="Picture" title="Picture" />
<?
			}
?>
                            <img class="icon icnMembers" id="<?= $iId ?>" src="images/icons/members.png" alt="Members" title="Members" />            
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
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
		    <input type="hidden" name="DuplicateSchool" id="DuplicateSchool" value="0" />
			<div id="RecordMsg" class="hidden"></div>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tr valign="top">
				<td width="450">
				  <label for="txtName">School Name</label>
				  <div><input type="text" name="txtName" id="txtName" value="<?= IO::strValue('txtName', true) ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtCode">EMIS Code</label>
				  <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="20" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddType">Type</label>

				  <div>
				    <select name="ddType" id="ddType">
					  <option value=""></option>
<?
		foreach ($sTypesList as $iType => $sType)
		{
?>
					  <option value="<?= $iType ?>"<?= ((IO::intValue('ddType') == $iType) ? ' selected' : '') ?>><?= $sType ?></option>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>
				  
				  <label for="txtBlocks">No of Blocks</label>
				  <div><input type="text" name="txtBlocks" id="txtBlocks" value="<?= ((IO::intValue("txtBlocks") == 0) ? 1 : IO::intValue("txtBlocks")) ?>" maxlength="2" size="15" class="textbox" /></div>

				  <div class="br10"></div>				    

				  <label for="txtStudents">No of Students</label>
				  <div><input type="text" name="txtStudents" id="txtStudents" value="<?= IO::strValue("txtStudents") ?>" maxlength="10" size="15" class="textbox" /></div>

				  <div class="br10"></div>
				  
				  <label for="txtCost">Initial Contract Price <span>(Optional)</span></label>
				  <div><input type="text" name="txtCost" id="txtCost" value="<?= IO::strValue("txtCost") ?>" maxlength="15" size="15" class="textbox" /></div>
                                  
			     <div class="br10"></div>
  
				  <label for="txtRevisedCost">Revised Contract Price <span>(Optional)</span></label>
				  <div><input type="text" name="txtRevisedCost" id="txtRevisedCost" value="<?= IO::strValue("txtRevisedCost") ?>" maxlength="15" size="15" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="ddDistrict">District</label>

				  <div>
				    <select name="ddDistrict" id="ddDistrict">
					  <option value=""></option>
<?
		$bStart = false;

		foreach ($sDistricts as $iDistrict => $sDistrict)
		{
			if ($iDistrict{0} == "P")
			{
				if ($bStart == true)
				{
?>
			          </optgroup>
<?
				}
?>
			          <optgroup label="<?= $sDistrict ?>">
<?
				$bStart = true;
			}

			else
			{
?>
			          <option value="<?= $iDistrict ?>"<?= ((IO::intValue('ddDistrict') == $iDistrict) ? ' selected' : '') ?>><?= $sDistrict ?></option>
<?
			}
		}

		if ($bStart == true)
		{
?>
			          </optgroup>
<?
		}
?>
				    </select>
				  </div>

				  <div class="br10"></div>

				  <label for="txtPhone">Phone <span>(optional)</span></label>
				  <div><input type="text" name="txtPhone" id="txtPhone" value="<?= IO::strValue('txtPhone') ?>" maxlength="20" size="20" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtFax">Fax <span>(optional)</span></label>
				  <div><input type="text" name="txtFax" id="txtFax" value="<?= IO::strValue('txtFax') ?>" maxlength="20" size="20" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtEmail">Email Address <span>(optional)</span></label>
				  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= IO::strValue('txtEmail') ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="filePicture">Logo <span>(optional)</span></label>
				  <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="40" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtAddress">Address</label>
				  <div><textarea name="txtAddress" id="txtAddress" style="width:320px; height:60px;"><?= IO::strValue('txtAddress', true) ?></textarea></div>

				  <div class="br10"></div>
				  
				  <label for="txtTehsil">Tehsil <span>(optional)</span></label>
				  <div><input type="text" name="txtTehsil" id="txtTehsil" value="<?= IO::strValue('txtTehsil') ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>
				  
				  <label for="txtUc">UC <span>(optional)</span></label>
				  <div><input type="text" name="txtUc" id="txtUc" value="<?= IO::strValue('txtUc') ?>" maxlength="100" size="44" class="textbox" /></div>

				  <div class="br10"></div>

				  <label for="txtLatitude">Map Coordinates <span>(Latitude, Longitude)</span></label>

				  <div>
				    <input type="text" name="txtLatitude" id="txtLatitude" value="<?= IO::strValue('txtLatitude') ?>" maxlength="30" size="15" class="textbox" />
				    -
				    <input type="text" name="txtLongitude" id="txtLongitude" value="<?= IO::strValue('txtLongitude') ?>" maxlength="30" size="15" class="textbox" />
				  </div>
				  
				  <div class="br10"></div>

				  <label for="ddDangerous">Dangerous</label>

				  <div>
				    <select name="ddDangerous" id="ddDangerous">
					  <option value="Y"<?= ((IO::strValue('ddDangerous') == 'Y') ? ' selected' : '') ?>>Yes</option>
					  <option value="N"<?= ((IO::strValue('ddDangerous') == 'N') ? ' selected' : '') ?>>No</option>					  
				    </select>
				  </div>
				  
				  <div class="br10"></div>

				  <label for="ddQualified">Qualified</label>

				  <div>
				    <select name="ddQualified" id="ddQualified">
					  <option value="Y"<?= ((IO::strValue('ddQualified') == 'Y') ? ' selected' : '') ?>>Yes</option>
					  <option value="N"<?= ((IO::strValue('ddQualified') == 'N') ? ' selected' : '') ?>>No</option>					  
				    </select>
				  </div>
				  
				  <div class="br10"></div>

				  <label for="ddAdopted">Adopted</label>

				  <div>
				    <select name="ddAdopted" id="ddAdopted">
					  <option value="N"<?= ((IO::strValue('ddAdopted') == 'N') ? ' selected' : '') ?>>No</option>
					  <option value="Y"<?= ((IO::strValue('ddAdopted') == 'Y') ? ' selected' : '') ?>>Yes</option>
				    </select>
				  </div>				  

				  <div class="br10"></div>

				  <label for="ddStatus">Status</label>

				  <div>
				    <select name="ddStatus" id="ddStatus">
					  <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
					  <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
				    </select>
				  </div>

				  <br />
				  <button id="BtnSave">Save School</button>
				  <button id="BtnReset">Clear</button>
				</td>

				<td>
				  <label for="txtDescription">Description <span>(optional)</span></label>
				  <div><textarea name="txtDescription" id="txtDescription" style="width:100%; height:300px;"><?= IO::strValue('txtDescription') ?></textarea></div>

				  <div class="br10"></div>
				  <div class="br10"></div>
				  

				  <h3>Existing School Infrastructre</h3>
				  <div class="br10"></div>				    				  

				  <table border="0" cellpadding="0" cellspacing="0" width="100%">
				    <tr valign="top">
				      <td width="50%">
					    <label for="txtExClassRooms">No of Classrooms</label>
					    <div><input type="text" name="txtExClassRooms" id="txtExClassRooms" value="<?= IO::strValue("txtExClassRooms") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExStudentToilets">No of Student Toilets</label>
					    <div><input type="text" name="txtExStudentToilets" id="txtExStudentToilets" value="<?= IO::strValue("txtExStudentToilets") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExStaffRooms">No of Staff Rooms</label>
					    <div><input type="text" name="txtExStaffRooms" id="txtExStaffRooms" value="<?= IO::strValue("txtExStaffRooms") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExStaffToilets">No of Staff Toilets</label>
					    <div><input type="text" name="txtExStaffToilets" id="txtExStaffToilets" value="<?= IO::strValue("txtExStaffToilets") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExScienceLabs">Science Labs</label>
					    <div><input type="text" name="txtExScienceLabs" id="txtExScienceLabs" value="<?= IO::strValue("txtExScienceLabs") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExItLabs">IT Labs</label>
					    <div><input type="text" name="txtExItLabs" id="txtExItLabs" value="<?= IO::strValue("txtExItLabs") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExExamHalls">Exam Halls</label>
					    <div><input type="text" name="txtExExamHalls" id="txtExExamHalls" value="<?= IO::strValue("txtExExamHalls") ?>" maxlength="10" size="15" class="textbox" /></div>
						
					    <div class="br10"></div>

					    <label for="txtExStores">No of Stores</label>
					    <div><input type="text" name="txtExStores" id="txtExStores" value="<?= IO::strValue("txtExStores") ?>" maxlength="10" size="15" class="textbox" /></div>
				      </td>

				      <td width="50%">			  
					    <label for="txtExLibrary">Library</label>
					    <div><input type="text" name="txtExLibrary" id="txtExLibrary" value="<?= IO::strValue("txtExLibrary") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExClerkOffices">Clerk Offices</label>
					    <div><input type="text" name="txtExClerkOffices" id="txtExClerkOffices" value="<?= IO::strValue("txtExClerkOffices") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExPrincipalOffice">Principal Office</label>
					    <div><input type="text" name="txtExPrincipalOffice" id="txtExPrincipalOffice" value="<?= IO::strValue("txtExPrincipalOffice") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExParkingStand">Parking / Cycle Stand</label>
					    <div><input type="text" name="txtExParkingStand" id="txtExParkingStand" value="<?= IO::strValue("txtExParkingStand") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExChowkidarHut">Chowkidar Hut</label>
					    <div><input type="text" name="txtExChowkidarHut" id="txtExChowkidarHut" value="<?= IO::strValue("txtExChowkidarHut") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExSoakagePit">Soakage Pit</label>
					    <div><input type="text" name="txtExSoakagePit" id="txtExSoakagePit" value="<?= IO::strValue("txtExSoakagePit") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExWaterSupply">Water Supply</label>
					    <div><input type="text" name="txtExWaterSupply" id="txtExWaterSupply" value="<?= IO::strValue("txtExWaterSupply") ?>" maxlength="10" size="15" class="textbox" /></div>
				      </td>
				    </tr>
				  </table>
				  
				  
				  <br />
				  <br />
				  <h3>Block # 1 (Proposed Infrastructure)</h3>
				  <div class="br10"></div>				    				  

				  <table border="0" cellpadding="0" cellspacing="0" width="100%">
				    <tr valign="top">
				      <td width="50%">
					    <label for="ddStoreyType">Storey Type</label>

					    <div>
						  <select name="ddStoreyType" id="ddStoreyType">
						    <option value="S"<?= ((IO::strValue('ddStoreyType') == 'S') ? ' selected' : '') ?>>Single</option>
						    <option value="D"<?= ((IO::strValue('ddStoreyType') == 'D') ? ' selected' : '') ?>>Double</option>
						    <option value="T"<?= ((IO::strValue('ddStoreyType') == 'T') ? ' selected' : '') ?>>Triple</option>
						  </select>
					    </div>

					    <div class="br10"></div>

					    <label for="ddDesignType">Design Type</label>

					    <div>
						  <select name="ddDesignType" id="ddDesignType">
						    <option value="R"<?= ((IO::strValue('ddDesignType') == 'R') ? ' selected' : '') ?>>Regular</option>
						    <option value="B"<?= ((IO::strValue('ddDesignType') == 'B') ? ' selected' : '') ?>>Bespoke</option>
						  </select>
					    </div>

					    <div class="br10"></div>
				  
					    <label for="txtClassRooms">No of Classrooms</label>
					    <div><input type="text" name="txtClassRooms" id="txtClassRooms" value="<?= IO::strValue("txtClassRooms") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtStudentToilets">No of Student Toilets</label>
					    <div><input type="text" name="txtStudentToilets" id="txtStudentToilets" value="<?= IO::strValue("txtStudentToilets") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtStaffRooms">No of Staff Rooms</label>
					    <div><input type="text" name="txtStaffRooms" id="txtStaffRooms" value="<?= IO::strValue("txtStaffRooms") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtStaffToilets">No of Staff Toilets</label>
					    <div><input type="text" name="txtStaffToilets" id="txtStaffToilets" value="<?= IO::strValue("txtStaffToilets") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtScienceLabs">Science Labs</label>
					    <div><input type="text" name="txtScienceLabs" id="txtScienceLabs" value="<?= IO::strValue("txtScienceLabs") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtItLabs">IT Labs</label>
					    <div><input type="text" name="txtItLabs" id="txtItLabs" value="<?= IO::strValue("txtItLabs") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtExamHalls">Exam Halls</label>
					    <div><input type="text" name="txtExamHalls" id="txtExamHalls" value="<?= IO::strValue("txtExamHalls") ?>" maxlength="10" size="15" class="textbox" /></div>
						
					    <div class="br10"></div>

					    <label for="txtStores">No of Stores</label>
					    <div><input type="text" name="txtStores" id="txtStores" value="<?= IO::strValue("txtStores") ?>" maxlength="10" size="15" class="textbox" /></div>
				      </td>

					  
				      <td width="50%">
					    <label for="ddWorkType">Work Type</label>

					    <div>
						  <select name="ddWorkType" id="ddWorkType">
						    <option value="N"<?= ((IO::strValue('ddWorkType') == 'N') ? ' selected' : '') ?>>New Construction</option>
						    <option value="R"<?= ((IO::strValue('ddWorkType') == 'R') ? ' selected' : '') ?>>Rehabilitation Only</option>
						  </select>
					    </div>

					    <div class="br10"></div>

					    <label for="txtCoveredArea">Covered Area <span>(sft)</span></label>
					    <div><input type="text" name="txtCoveredArea" id="txtCoveredArea" value="<?= IO::strValue("txtCoveredArea") ?>" maxlength="15" size="15" class="textbox" /></div>

					    <div class="br10"></div>		
				  
					    <label for="txtLibrary">Library</label>
					    <div><input type="text" name="txtLibrary" id="txtLibrary" value="<?= IO::strValue("txtLibrary") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtClerkOffices">Clerk Offices</label>
					    <div><input type="text" name="txtClerkOffices" id="txtClerkOffices" value="<?= IO::strValue("txtClerkOffices") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtPrincipalOffice">Principal Office</label>
					    <div><input type="text" name="txtPrincipalOffice" id="txtPrincipalOffice" value="<?= IO::strValue("txtPrincipalOffice") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtParkingStand">Parking / Cycle Stand</label>
					    <div><input type="text" name="txtParkingStand" id="txtParkingStand" value="<?= IO::strValue("txtParkingStand") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtChowkidarHut">Chowkidar Hut</label>
					    <div><input type="text" name="txtChowkidarHut" id="txtChowkidarHut" value="<?= IO::strValue("txtChowkidarHut") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtSoakagePit">Soakage Pit</label>
					    <div><input type="text" name="txtSoakagePit" id="txtSoakagePit" value="<?= IO::strValue("txtSoakagePit") ?>" maxlength="10" size="15" class="textbox" /></div>

					    <div class="br10"></div>

					    <label for="txtWaterSupply">Water Supply</label>
					    <div><input type="text" name="txtWaterSupply" id="txtWaterSupply" value="<?= IO::strValue("txtWaterSupply") ?>" maxlength="10" size="15" class="textbox" /></div>
				      </td>
				    </tr>
				  </table>				  

				</td>
			  </tr>
			</table>
			</div>
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
	$objDbGlobal->close( );

	@ob_end_flush( );
?>