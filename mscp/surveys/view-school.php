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

	$iSchoolId = IO::intValue("SchoolId");

	$sSQL = "SELECT * FROM tbl_schools WHERE id='$iSchoolId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sName              = $objDb->getField(0, "name");
	$sCode              = $objDb->getField(0, "code");
	$iType              = $objDb->getField(0, "type_id");
	$iBlocks            = $objDb->getField(0, "blocks");
	$iStudents          = $objDb->getField(0, "students");
	$fCost              = $objDb->getField(0, "cost");
    $fRevisedCost       = $objDb->getField(0, "revised_cost");
	$iDistrict          = $objDb->getField(0, "district_id");
	$sAddress           = $objDb->getField(0, "address");
	$sTehsil            = $objDb->getField(0, "tehsil");
	$sUc                = $objDb->getField(0, "uc");
	$sLatitude          = $objDb->getField(0, "latitude");
	$sLongitude         = $objDb->getField(0, "longitude");
	$sPhone             = $objDb->getField(0, "phone");
	$sFax               = $objDb->getField(0, "fax");
	$sEmail             = $objDb->getField(0, "email");
	$sPicture           = $objDb->getField(0, "picture");
	$sDropped           = $objDb->getField(0, "dropped");
	$sQualified         = $objDb->getField(0, "qualified");
	$sAdopted           = $objDb->getField(0, "adopted");
	$sStatus            = $objDb->getField(0, "status");
	
	$iExClassRooms      = $objDb->getField(0, "ex_class_rooms");
	$iExStudentToilets  = $objDb->getField(0, "ex_student_toilets");
	$iExStaffRooms      = $objDb->getField(0, "ex_staff_rooms");
	$iExStaffToilets    = $objDb->getField(0, "ex_staff_toilets");
	$iExScienceLabs     = $objDb->getField(0, "ex_science_labs");
	$iExItLabs          = $objDb->getField(0, "ex_it_labs");
	$iExExamHalls       = $objDb->getField(0, "ex_exam_halls");
	$iExLibrary         = $objDb->getField(0, "ex_library");
	$iExClerkOffices    = $objDb->getField(0, "ex_clerk_offices");
	$iExPrincipalOffice = $objDb->getField(0, "ex_principal_office");
	$iExParkingStand    = $objDb->getField(0, "ex_parking_stand");
	$iExChowkidarHut    = $objDb->getField(0, "ex_chowkidar_hut");
	$iExSoakagePit      = $objDb->getField(0, "ex_soakage_pit");
	$iExWaterSupply     = $objDb->getField(0, "ex_water_supply");
	$iExStores          = $objDb->getField(0, "ex_stores");
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
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr valign="top">
		<td width="400">
		  <label for="txtName">School Name</label>
		  <div><input type="text" name="txtName" id="txtName" value="<?= formValue($sName) ?>" maxlength="100" size="40" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtCode">EMIS Code</label>
		  <div><input type="text" name="txtCode" id="txtCode" value="<?= $sCode ?>" maxlength="10" size="20" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddType">Type</label>

		  <div>
			<select name="ddType" id="ddType">
			  <option value=""></option>
<?
	$sTypesList = getList("tbl_school_types", "id", "`type`");

	foreach ($sTypesList as $iTypeId => $sType)
	{
?>
			  <option value="<?= $iTypeId ?>"<?= (($iTypeId == $iType) ? ' selected' : '') ?>><?= $sType ?></option>
<?
	}
?>			  
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtStudents">No of Students</label>
		  <div><input type="text" name="txtStudents" id="txtStudents" value="<?= $iStudents ?>" maxlength="10" size="15" class="textbox" /></div>
		  
		  <div class="br10"></div>
		  
		  <label for="txtBlocks">No of Blocks</label>
		  <div><input type="text" name="txtBlocks" id="txtBlocks" value="<?= $iBlocks ?>" maxlength="2" size="15" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtCost">Initial Contract Cost <span>(Optional)</span></label>
		  <div><input type="text" name="txtCost" id="txtCost" value="<?= $fCost ?>" maxlength="15" size="15" class="textbox" /></div>

		  <div class="br10"></div>
		  
		  <label for="txtRevisedCost">Revised Contract Price <span>(Optional)</span></label>
		  <div><input type="text" name="txtRevisedCost" id="txtRevisedCost" value="<?= $fRevisedCost ?>" maxlength="15" size="15" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="ddDistrict">District</label>

		  <div>
		    <select name="ddDistrict" id="ddDistrict">
			  <option value=""></option>
<?
	$sSQL = "SELECT id, name FROM tbl_provinces ORDER BY name";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iProvince = $objDb->getField($i, "id");
		$sProvince = $objDb->getField($i, "name");
?>
			  <optgroup label="<?= $sProvince ?>">
<?
		$sSQL = "SELECT id, name FROM tbl_districts WHERE province_id='$iProvince' ORDER BY name";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iDistrictId = $objDb2->getField($j, "id");
			$sDistrict   = $objDb2->getField($j, "name");
?>
			    <option value="<?= $iDistrictId ?>"<?= (($iDistrict == $iDistrictId) ? ' selected' : '') ?>><?= $sDistrict ?></option>
<?
		}
?>
			  </optgroup>
<?
	}
?>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtPhone">Phone <span>(optional)</span></label>
		  <div><input type="text" name="txtPhone" id="txtPhone" value="<?= $sPhone ?>" maxlength="20" size="20" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtFax">Fax <span>(optional)</span></label>
		  <div><input type="text" name="txtFax" id="txtFax" value="<?= $sFax ?>" maxlength="20" size="20" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtEmail">Email Address <span>(optional)</span></label>
		  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= $sEmail ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtAddress">Address</label>
		  <div><textarea name="txtAddress" id="txtAddress" style="width:320px; height:60px;"><?= formValue($sAddress) ?></textarea></div>

		  <div class="br10"></div>
		  
		  <label for="txtTehsil">Tehsil <span>(optional)</span></label>
		  <div><input type="text" name="txtTehsil" id="txtTehsil" value="<?= $sTehsil ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtUc">UC <span>(optional)</span></label>
		  <div><input type="text" name="txtUc" id="txtUc" value="<?= $sUc ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>		  

		  <label for="txtLatitude">Map Coordinates <span>(Latitude, Longitude)</span></label>

		  <div>
			<input type="text" name="txtLatitude" id="txtLatitude" value="<?= $sLatitude ?>" maxlength="30" size="15" class="textbox" />
			-
			<input type="text" name="txtLongitude" id="txtLongitude" value="<?= $sLongitude ?>" maxlength="30" size="15" class="textbox" />
		  </div>

		  <div class="br10"></div>

		  <label for="ddDropped">Dropped</label>

		  <div>
		    <select name="ddDropped" id="ddDropped">
			  <option value="N"<?= (($sDropped == 'N') ? ' selected' : '') ?>>No</option>
			  <option value="Y"<?= (($sDropped == 'Y') ? ' selected' : '') ?>>Yes</option>
		    </select>
		  </div>

		  <div class="br10"></div>

		  <label for="ddQualified">Qualified</label>

		  <div>
		    <select name="ddQualified" id="ddQualified">
			  <option value="Y"<?= (($sQualified == 'Y') ? ' selected' : '') ?>>Yes</option>
			  <option value="N"<?= (($sQualified == 'N') ? ' selected' : '') ?>>No</option>			  
		    </select>
		  </div>
		  
		  <div class="br10"></div>

		  <label for="ddAdopted">Adopted</label>

		  <div>
		    <select name="ddAdopted" id="ddAdopted">
			  <option value="N"<?= (($sAdopted == 'N') ? ' selected' : '') ?>>No</option>
			  <option value="Y"<?= (($sAdopted == 'Y') ? ' selected' : '') ?>>Yes</option>
		    </select>
		  </div>		  
		  
		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>		  
        </td>

        <td>
		  <label for="txtDescription">Description <span>(optional)</span></label>
		  <iframe id="Description" frameborder="1" width="100%" height="450" src="editor-contents.php?Table=tbl_schools&Field=description&Id=<?= $iSchoolId ?>"></iframe>
		  
		  <div class="br10"></div>
		  <div class="br10"></div>
		  <h3>Existing School Infrastructure</h3>
		  <div class="br10"></div>		   

		  <table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr valign="top">
			  <td width="50%">
				<label for="txtExClassRooms">No of Classrooms</label>
				<div><input type="text" name="txtExClassRooms" id="txtExClassRooms" value="<?= $iExClassRooms ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExStudentToilets">No of Student Toilets</label>
				<div><input type="text" name="txtExStudentToilets" id="txtExStudentToilets" value="<?= $iExStudentToilets ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExStaffRooms">No of Staff Rooms</label>
				<div><input type="text" name="txtExStaffRooms" id="txtExStaffRooms" value="<?= $iExStaffRooms ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExStaffToilets">No of Staff Toilets</label>
				<div><input type="text" name="txtExStaffToilets" id="txtExStaffToilets" value="<?= $iExStaffToilets ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExScienceLabs">Science Labs</label>
				<div><input type="text" name="txtExScienceLabs" id="txtExScienceLabs" value="<?= $iExScienceLabs ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExItLabs">IT Labs</label>
				<div><input type="text" name="txtExItLabs" id="txtExItLabs" value="<?= $iExItLabs ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExExamHalls">Exam Halls</label>
				<div><input type="text" name="txtExExamHalls" id="txtExExamHalls" value="<?= $iExExamHalls ?>" maxlength="10" size="15" class="textbox" /></div>
				
			    <div class="br10"></div>

				<label for="txtExStores">No of Stores</label>
				<div><input type="text" name="txtExStores" id="txtExStores" value="<?= $iExStores ?>" maxlength="10" size="15" class="textbox" /></div>
			  </td>

			  <td width="50%">
				<label for="txtExLibrary">Library</label>
				<div><input type="text" name="txtExLibrary" id="txtExLibrary" value="<?= $iExLibrary ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExClerkOffices">Clerk Offices</label>
				<div><input type="text" name="txtExClerkOffices" id="txtExClerkOffices" value="<?= $iExClerkOffices ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExPrincipalOffice">Principal Office</label>
				<div><input type="text" name="txtExPrincipalOffice" id="txtExPrincipalOffice" value="<?= $iExPrincipalOffice ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExParkingStand">Parking / Cycle Stand</label>
				<div><input type="text" name="txtExParkingStand" id="txtExParkingStand" value="<?= $iExParkingStand ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExChowkidarHut">Chowkidar Hut</label>
				<div><input type="text" name="txtExChowkidarHut" id="txtExChowkidarHut" value="<?= $iExChowkidarHut ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExSoakagePit">Soakage Pit</label>
				<div><input type="text" name="txtExSoakagePit" id="txtExSoakagePit" value="<?= $iExSoakagePit ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExWaterSupply">Water Supply</label>
				<div><input type="text" name="txtExWaterSupply" id="txtExWaterSupply" value="<?= $iExWaterSupply ?>" maxlength="10" size="15" class="textbox" /></div>
			  </td>
			</tr>
		  </table>

<?
	$sSQL = "SELECT * FROM tbl_school_blocks WHERE school_id='$iSchoolId' ORDER BY block";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );

	
	for ($i = 1; $i <= $iBlocks; $i ++)
	{
		$sName            = $objDb->getField(($i - 1), "name");
		$sStoreyType      = $objDb->getField(($i - 1), "storey_type");
		$sDesignType      = $objDb->getField(($i - 1), "design_type");
		$sWorkType        = $objDb->getField(($i - 1), "work_type");
		$fCoveredArea     = $objDb->getField(($i - 1), "covered_area");
		$iClassRooms      = $objDb->getField(($i - 1), "class_rooms");
		$iStudentToilets  = $objDb->getField(($i - 1), "student_toilets");
		$iStaffRooms      = $objDb->getField(($i - 1), "staff_rooms");
		$iStaffToilets    = $objDb->getField(($i - 1), "staff_toilets");
		$iScienceLabs     = $objDb->getField(($i - 1), "science_labs");
		$iItLabs          = $objDb->getField(($i - 1), "it_labs");
		$iExamHalls       = $objDb->getField(($i - 1), "exam_halls");
		$iLibrary         = $objDb->getField(($i - 1), "library");
		$iClerkOffices    = $objDb->getField(($i - 1), "clerk_offices");
		$iPrincipalOffice = $objDb->getField(($i - 1), "principal_office");
		$iParkingStand    = $objDb->getField(($i - 1), "parking_stand");
		$iChowkidarHut    = $objDb->getField(($i - 1), "chowkidar_hut");
		$iSoakagePit      = $objDb->getField(($i - 1), "soakage_pit");
		$iWaterSupply     = $objDb->getField(($i - 1), "water_supply");			
		$iStores          = $objDb->getField(($i - 1), "stores");			
?>
		  <div class="br10"></div>
		  <div class="br10"></div>
		  <h3>Block # <?= $i ?>  (Proposed Infrastructure)</h3>
		  <div class="br10"></div>		  
		  
		  <label for="txtName<?= $i ?>">Block Name</label>
		  <div><input type="text" name="txtName<?= $i ?>" id="txtName<?= $i ?>" value="<?= formValue($sName) ?>" maxlength="100" size="44" class="textbox" /></div>

		  <br />

		  <table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr valign="top">
			  <td width="50%">
			    <label for="ddStoreyType<?= $i ?>">Storey Type</label>

			    <div>
				  <select name="ddStoreyType<?= $i ?>" id="ddStoreyType<?= $i ?>">
				    <option value="S"<?= (($sStoreyType == 'S') ? ' selected' : '') ?>>Single</option>
				    <option value="D"<?= (($sStoreyType == 'D') ? ' selected' : '') ?>>Double</option>
					<option value="T"<?= (($sStoreyType == 'T') ? ' selected' : '') ?>>Triple</option>
				  </select>
			    </div>

			    <div class="br10"></div>
				
			    <label for="ddWorkType<?= $i ?>">Work Type</label>

			    <div>
				  <select name="ddWorkType<?= $i ?>" id="ddWorkType<?= $i ?>">
				    <option value="N"<?= (($sWorkType == 'N') ? ' selected' : '') ?>>New Construction</option>
				    <option value="R"<?= (($sWorkType == 'R') ? ' selected' : '') ?>>Rehabilitation Only</option>
				  </select>
			    </div>

			    <div class="br10"></div>				
		  
				<label for="txtClassRooms<?= $i ?>">No of Classrooms</label>
				<div><input type="text" name="txtClassRooms<?= $i ?>" id="txtClassRooms<?= $i ?>" value="<?= $iClassRooms ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtStudentToilets<?= $i ?>">No of Student Toilets</label>
				<div><input type="text" name="txtStudentToilets<?= $i ?>" id="txtStudentToilets<?= $i ?>" value="<?= $iStudentToilets ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtStaffRooms<?= $i ?>">No of Staff Rooms</label>
				<div><input type="text" name="txtStaffRooms<?= $i ?>" id="txtStaffRooms<?= $i ?>" value="<?= $iStaffRooms ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtStaffToilets<?= $i ?>">No of Staff Toilets</label>
				<div><input type="text" name="txtStaffToilets<?= $i ?>" id="txtStaffToilets<?= $i ?>" value="<?= $iStaffToilets ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtScienceLabs<?= $i ?>">Science Labs</label>
				<div><input type="text" name="txtScienceLabs<?= $i ?>" id="txtScienceLabs<?= $i ?>" value="<?= $iScienceLabs ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtItLabs<?= $i ?>">IT Labs</label>
				<div><input type="text" name="txtItLabs<?= $i ?>" id="txtItLabs<?= $i ?>" value="<?= $iItLabs ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtExamHalls<?= $i ?>">Exam Halls</label>
				<div><input type="text" name="txtExamHalls<?= $i ?>" id="txtExamHalls<?= $i ?>" value="<?= $iExamHalls ?>" maxlength="10" size="15" class="textbox" /></div>
				
			    <div class="br10"></div>

				<label for="txtStores<?= $i ?>">No of Stores</label>
				<div><input type="text" name="txtStores<?= $i ?>" id="txtStores<?= $i ?>" value="<?= $iStores ?>" maxlength="10" size="15" class="textbox" /></div>
			  </td>

			  <td width="50%">
			    <label for="ddDesignType<?= $i ?>">Design Type</label>

			    <div>
				  <select name="ddDesignType<?= $i ?>" id="ddDesignType<?= $i ?>">
				    <option value="R"<?= (($sDesignType == 'R') ? ' selected' : '') ?>>Regular</option>
				    <option value="B"<?= (($sDesignType == 'B') ? ' selected' : '') ?>>Bespoke</option>
				  </select>
			    </div>

			    <div class="br10"></div>
				
			    <label for="txtCoveredArea<?= $i ?>">Covered Area <span>(sft)</span></label>
			    <div><input type="text" name="txtCoveredArea<?= $i ?>" id="txtCoveredArea<?= $i ?>" value="<?= $fCoveredArea ?>" maxlength="15" size="15" class="textbox" /></div>

			    <div class="br10"></div>

				<label for="txtLibrary<?= $i ?>">Library</label>
				<div><input type="text" name="txtLibrary<?= $i ?>" id="txtLibrary<?= $i ?>" value="<?= $iLibrary ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtClerkOffices<?= $i ?>">Clerk Offices</label>
				<div><input type="text" name="txtClerkOffices<?= $i ?>" id="txtClerkOffices<?= $i ?>" value="<?= $iClerkOffices ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtPrincipalOffice<?= $i ?>">Principal Office</label>
				<div><input type="text" name="txtPrincipalOffice<?= $i ?>" id="txtPrincipalOffice<?= $i ?>" value="<?= $iPrincipalOffice ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtParkingStand<?= $i ?>">Parking / Cycle Stand</label>
				<div><input type="text" name="txtParkingStand<?= $i ?>" id="txtParkingStand<?= $i ?>" value="<?= $iParkingStand ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtChowkidarHut<?= $i ?>">Chowkidar Hut</label>
				<div><input type="text" name="txtChowkidarHut<?= $i ?>" id="txtChowkidarHut<?= $i ?>" value="<?= $iChowkidarHut ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtSoakagePit<?= $i ?>">Soakage Pit</label>
				<div><input type="text" name="txtSoakagePit<?= $i ?>" id="txtSoakagePit<?= $i ?>" value="<?= $iSoakagePit ?>" maxlength="10" size="15" class="textbox" /></div>

				<div class="br10"></div>

				<label for="txtWaterSupply<?= $i ?>">Water Supply</label>
				<div><input type="text" name="txtWaterSupply<?= $i ?>" id="txtWaterSupply<?= $i ?>" value="<?= $iWaterSupply ?>" maxlength="10" size="15" class="textbox" /></div>
			  </td>
			</tr>
		  </table>
<?
	}
	
	
	if ($sPicture != "")
	{
?>
		  <div style="width:304px; margin-top:15px;">
		    <div style="border:solid 1px #888888; padding:1px;"><img src="<?= (SITE_URL.SCHOOLS_IMG_DIR.$sPicture) ?>" width="300" alt="" title="" /></div>
		  </div>
<?
	}
?>
        </td>
      </tr>
    </table>
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