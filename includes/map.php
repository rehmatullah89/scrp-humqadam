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

	$iMilestoneStageS = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='S'", "position DESC");
	$iMilestoneStageD = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='D'", "position DESC");
	$iMilestoneStageT = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='T'", "position DESC");
	$iMilestoneStageB = getDbValue("position", "tbl_stages", "parent_id='0' AND `type`='B'", "position DESC");
	$iMilestoneStages = array( );

	$sSQL = "SELECT id FROM tbl_stages WHERE ((`type`='S' AND position>'$iMilestoneStageS') OR (`type`='D' AND position>'$iMilestoneStageD') OR (`type`='T' AND position>'$iMilestoneStageT') OR (`type`='B' AND position>'$iMilestoneStageB')) ORDER BY position";
	$objDb->query($sSQL);
	
	$iCount = $objDb->getCount( );
	
	for ($i = 0; $i < $iCount; $i ++)
		$iMilestoneStages[] = $objDb->getField($i, 0);
		
	$sMilestoneStages = @implode(",", $iMilestoneStages);

		
		
	$sKeywords = IO::strValue("Keywords");
	$iPackage  = IO::intValue("Package");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
	$sStatus   = IO::strValue("Status");


	$sConditions = "status='A' AND dropped!='Y'
				    AND province_id IN ({$_SESSION['AdminProvinces']})
				    AND district_id IN ({$_SESSION['AdminDistricts']})";

	if ($_SESSION["AdminSchools"] != "")
		$sConditions .= " AND id IN ({$_SESSION['AdminSchools']}) ";


	if ($sKeywords != "")
	{
		$sKeywords    = str_replace(" ", "%", $sKeywords);

		$sConditions .= " AND (name LIKE '%{$sKeywords}%' OR code LIKE '{$sKeywords}' OR address LIKE '%{$sKeywords}%') ";
	}

	if ($iPackage > 0 || $iProvince > 0)
	{
		$sConditions .= " AND (";

		if ($iPackage > 0)
		{
			$sSchools = getDbValue("schools", "tbl_packages", "id='$iPackage'");

			$sConditions .= " FIND_IN_SET(id, '$sSchools') ";
		}

		if ($iPackage > 0 && $iProvince > 0)
			$sConditions .= " OR ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " (";

		if ($iProvince > 0)
			$sConditions .= " province_id='$iProvince' ";

		if ($iDistrict > 0)
			$sConditions .= " AND district_id='$iDistrict' ";

		if ($iProvince > 0 && $iDistrict > 0)
			$sConditions .= " )";

		$sConditions .= ")";
	}
	

	$iActiveSchools    = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND qualified='Y' AND adopted='Y' AND id IN (SELECT DISTINCT(school_id) FROM tbl_inspections WHERE FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}') AND stage_id IN ($sMilestoneStages) $sSubConditions)");
	$iAdoptedSchools   = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND adopted='Y'");
	$iQualifiedSchools = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND qualified='Y'");
	$iCompletedSchools = getDbValue("COUNT(1)", "tbl_schools", "{$sConditions} AND completed='Y'");
?>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry&sensor=false"></script>

  <section id="Map">
    <div id="GoogleMap"></div>
    <div id="Overlay"></div>

    <div id="Buttons">
      <div id="Counts">
	    <div id="QualifiedCount"><span>Qualified</span><?= formatNumber($iQualifiedSchools, false) ?></div>
	    <div id="AdoptedCount"><span>Adopted</span><?= formatNumber($iAdoptedSchools, false) ?></div>
		<div id="ActiveCount" style="cursor:pointer;" rel="<?= (SITE_URL."/export-active-schools.php") ?>"><span>Active</span><?= formatNumber(($iActiveSchools - $iCompletedSchools), false) ?></div>
		<div id="CompletedCount" style="cursor:pointer;" rel="<?= (SITE_URL.ADMIN_CP_DIR."/tracking/export-weekly-progress-report.php") ?>"><span>Completed</span><?= formatNumber($iCompletedSchools, false) ?></div>
	  </div>

      <div><input type="button" id="BtnActive" value="Active Schools" class="button selected" /></div>
      <div class="br10"></div>

      <div><input type="button" id="BtnInActive" value="In-Active Schools" class="button" /></div>
      <div><div class="br10"></div>

      <div><input type="button" id="BtnDelayed" value="Delayed Schools" class="button" /></div>
      <div><div class="br10"></div>

      <div><input type="button" id="BtnOnTime" value="On-Time Schools" class="button" /></div>
      <div><div class="br10"></div>

      <div><input type="button" id="BtnAll" value="ALL Schools" class="button" /></div>
    </div>
  </section>

  <section id="SearchBar">
    <form name="frmSearch" id="frmSearch" onsubmit="return false;">
      <input type="hidden" name="Status" id="Status" value="<?= (($sStatus == "") ? "Active" : $sStatus) ?>" />
	  <textarea class="hidden" id="ShareLink"  rel="<?= getPageUrl(9) ?>"><?= getPageUrl(9) ?>?Status=<?= $sStatus ?>&Package=<?= $iPackage ?>&Province=<?= $iProvince ?>&District=<?= $iDistrict ?>&Keywords=<?= $sKeywords ?></textarea>

      <div id="SearchFields">
        <div id="FieldsBg">
          <span class="fa fa-pencil"></span>
          <input type="text" name="Keywords" id="Keywords" value="<?= $sKeywords ?>" maxlength="50" autocomplete="off" placeholder="School Name / Code" />

          <select name="Package" id="Package">
            <option value=""></option>
<?
	$sPackagesList = getList("tbl_packages", "id", "title", "status='A'");

	foreach ($sPackagesList as $iPackageId => $sPackage)
	{
?>
            <option value="<?= $iPackageId ?>"<?= (($iPackageId == $iPackage) ? " selected" : "") ?>><?= $sPackage ?></option>
<?
	}
?>
          </select>

          <select name="Province" id="Province">
            <option value=""></option>
<?
	$sProvincesList = getList("tbl_provinces", "id", "name");

	foreach ($sProvincesList as $iProvinceId => $sProvince)
	{
?>
            <option value="<?= $iProvinceId ?>"<?= (($iProvinceId == $iProvince) ? " selected" : "") ?>><?= $sProvince ?></option>
<?
	}
?>
          </select>

          <select name="District" id="District">
            <option value=""></option>
<?
	$sDistrictsList = getList("tbl_districts", "id", "name", "province_id='$iProvince'");

	foreach ($sDistrictsList as $iDistrictId => $sDistrict)
	{
?>
            <option value="<?= $iDistrictId ?>"<?= (($iDistrictId == $iDistrict) ? " selected" : "") ?>><?= $sDistrict ?></option>
<?
	}
?>
          </select>
		</div>
      </div>

      <div id="SearchButton">
        <input type="submit" name="btnSearch" id="BtnSearch" value="SEARCH" class="button" />
        <span class="fa fa-search"></span>
      </div>
    </form>
	
	<input type="button" id="BtnCopyLink" class="button hidden" value="Copy Search Link" data-clipboard-target="ShareLink" />
  </section>

  <section id="SchoolTimeline">
	<div class="timelineArea">
	  <h1 class="line"><span>School Development Timeline</span></h1>

	  <div id="Inner">
	    <div id="Timeline"></div>
	  </div>

	  <img src="images/icons/close-timeline.png" alt="Close" title="Close" id="CloseTimeline" style="position:absolute; right:-10px; top:-10px; z-index:99999; cursor:pointer;" />
	</div>
  </section>
