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

	if ($_SESSION["AdminId"] == 0 || $_SESSION["AdminTypeId"] == 12)
	{
		if ($_SESSION["AdminId"] == 0)
			$_SESSION['RequestUrl'] = $_SERVER['REQUEST_URI'];
		
		redirect(SITE_URL);
	}
?>
  <?= $sPageContents ?>
  <br />

  <section id="InspectorStats">
    <div id="Tabs">
	  <ul>
<?
	$sProvincesList  = getList("tbl_provinces", "id", "name", "FIND_IN_SET(id, '{$_SESSION['AdminProvinces']}')");
	$sAdminTypesList = getList("tbl_admin_types", "id", "title", "FIND_IN_SET(id, '6,8,9')", "FIELD(id,6,8,9)");



	$iIndex = 1;

	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
	    <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-<?= $iIndex ++ ?>"><?= $sProvince ?></a></li>
<?
	}
?>
	  </ul>


<?
	$iIndex = 1;

	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
	  <div id="tabs-<?= $iIndex ++ ?>" class="stats">
<?
		foreach ($sAdminTypesList as $iAdminType => $sAdminType)
		{
?>
		<h2><?= $sAdminType ?></h2>

		<ul class="navigator">
<?
			$iDistricts    = @explode(",", $_SESSION['AdminDistricts']);
			$sDistrictsSQL = "";

			foreach ($iDistricts as $iDistrict)
			{
				if ($sDistrictsSQL != "")
					$sDistrictsSQL .= " OR ";

				$sDistrictsSQL .= " FIND_IN_SET('$iDistrict', districts) ";
			}

			if ($sDistrictsSQL != "")
				$sDistrictsSQL = " AND ($sDistrictsSQL) ";



			$sSQL = "SELECT id, name, email, mobile, picture, districts, schools,
							(SELECT COUNT(1) FROM tbl_inspections WHERE admin_id=tbl_admins.id) AS _Inspections,
							(SELECT GROUP_CONCAT(DISTINCT(school_id) SEPARATOR ',') FROM tbl_inspections WHERE admin_id=tbl_admins.id AND DATEDIFF(NOW( ), `date`) <= '7') AS _UniqueSchools
					 FROM tbl_admins
					 WHERE status='A' AND type_id='$iAdminType' AND FIND_IN_SET('$iProvince', provinces) $sDistrictsSQL
					 ORDER BY name";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($i = 0; $i < $iCount; $i ++)
			{
				$iAdmin         = $objDb->getField($i, "id");
				$sName          = $objDb->getField($i, "name");
				$sEmail         = $objDb->getField($i, "email");
				$sMobile        = $objDb->getField($i, "mobile");
				$sPicture       = $objDb->getField($i, "picture");
				$sDistricts     = $objDb->getField($i, "districts");
				$sSchools       = $objDb->getField($i, "schools");
				$iInspections   = $objDb->getField($i, "_Inspections");
				$sUniqueSchools = $objDb->getField($i, "_UniqueSchools");

				if ($sPicture == "" || !@file_exists(ADMINS_IMG_DIR.'thumbs/'.$sPicture))
					$sPicture = "default.jpg";
				
				$iUniqueSchools = @explode(",", $sUniqueSchools);
				$iUniqueVisits  = count($iUniqueSchools);
				
				if ($sSchools != "")
					$sSchoolsList = getList("tbl_schools", "id", "CONCAT(code, ' - ', name)", "status='A' AND FIND_IN_SET(id, '$sSchools')");
?>
		  <li class="topLevel">
		    <div class="picture"><img src="<?= (ADMINS_IMG_DIR.'thumbs/'.$sPicture) ?>" alt="<?= $sName ?>" title="<?= $sName ?>" /></div>
		    <b><?= $sName ?></b>
		    <i><?= $sEmail ?></i><br />
		    Contact No: <?= $sMobile ?><br />
		    <br />
		    Inspections: <?= formatNumber($iInspections, false) ?><br />
			Unique Visits (<small>Last 7 Days</small>): <?= formatNumber($iUniqueVisits, false) ?><br />
		    <br />
		    <br />
		    <b>Districts Assigned:</b>
			<?= getDbValue("GROUP_CONCAT(name SEPARATOR ', ')", "tbl_districts", "FIND_IN_SET(id, '$sDistricts')") ?><br />
<?
				if ($sSchools != "")
				{
?>
			<br />
			<b>Schools Assigned (<?= count($sSchoolsList) ?>):</b>
<?
					foreach ($sSchoolsList as $iSchool => $sSchool)
					{
?>
                	  <span<?= ((@in_array($iSchool, $iUniqueSchools)) ? ' class="blue"' : '') ?>><?= $sSchool ?></span><br />
<?
					}
				}
?>
		  </li>
<?
			}
?>
		</ul>

		<div class="br10"></div>
		<br />
<?
		}
?>
	  </div>
<?
	}
?>
    </div>
  </section>


  <script type="text/javascript">
  <!--
	$(document).ready(function( )
	{
		$("#InspectorStats #Tabs").tabs( );
	});
  -->
  </script>
