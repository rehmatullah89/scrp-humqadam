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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );


	$sDate     = IO::strValue("Date");
	$sAction   = IO::strValue("Action");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");	
	
	
	if ($sDate == "")
		$sDate = date("Y-m-d", ((date("N") == 1) ? strtotime("Today") : strtotime("Last Monday")));

	
	@list($iYear, $iMonth, $iDay) = @explode("-", $sDate);

	$iDay   = intval($iDay);	
	$iMonth = intval($iMonth);

	
	if ($sAction == "Prev")
	{
		$sFromDate = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay - 7), $iYear));
		$sToDate   = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay - 2), $iYear));
	}
	
	else if ($sAction == "Next")
	{
		$sFromDate = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay + 7), $iYear));
		$sToDate   = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay + 12), $iYear));
	}

	else 
	{
		$sFromDate = $sDate;
		$sToDate   = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay + 5), $iYear));
	}	
?>
		  <input type="hidden" id="WeekDate" value="<?= $sFromDate ?>" to="<?= $sToDate ?>" />

		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		    <tr>
		      <td width="20%" bgcolor="#e6e6e6"><b style="padding-left:10px; color:#444444;">Week # <?= date("W", strtotime($sFromDate)) ?> &nbsp; (<?= date("M d", strtotime($sFromDate)) ?> - <?= date("M d", strtotime($sToDate)) ?>)</b></td>
			
			  <td width="80%" bgcolor="#f6f6f6">
			    <table border="0" cellspacing="0" cellpadding="10" width="100%">
			      <tr>
<?
	@list($iYear, $iMonth, $iDay) = @explode("-", $sFromDate);

	$iDay   = intval($iDay);	
	$iMonth = intval($iMonth);

	
	for ($i = 0; $i < 6; $i ++)
	{
?>
                    <td width="16.66%" align="center"><b style="color:#444444;"><?= date('l', mktime(0, 0, 0, $iMonth, ($iDay + $i), $iYear)) ?></b><br /><small><?= date('d-M-Y', mktime(0, 0, 0, $iMonth, ($iDay + $i), $iYear)) ?></small></td>
<?
	}
?>
                  </tr>
                </table>  
              </td>
		    </tr>
          
		    <tr>
		      <td colspan="2"><div style="border-bottom:solid 3px #aaaaaa; padding-top:2px;"></div></td>
		    </tr>    


<?
	$sConditions = "";
	
	if ($iDistrict > 0)
		$sConditions .= " AND district_id='$iDistrict' ";

	else if ($iProvince > 0)
		$sConditions .= " AND district_id IN (SELECT id FROM tbl_districts WHERE province_id='$iProvince') ";


    $sDistrictsList       = getList("tbl_districts", "id", "name");
    $sAdminsList          = getList("tbl_admins", "id", "name", "type_id='12'");
    $sSchoolCodesList     = getList("tbl_schools", "id", "code", "id IN (SELECT school_id FROM tbl_survey_schedules WHERE (`date` BETWEEN '$sFromDate' AND '$sToDate')) $sConditions");
    $sSchoolNamesList     = getList("tbl_schools", "id", "name", "id IN (SELECT school_id FROM tbl_survey_schedules WHERE (`date` BETWEEN '$sFromDate' AND '$sToDate')) $sConditions");
    $sSurveyQualifiedList = getList("tbl_surveys", "school_id", "qualified", "school_id IN (SELECT school_id FROM tbl_survey_schedules WHERE (`date` BETWEEN '$sFromDate' AND '$sToDate')) $sConditions");


    $sSQL = "SELECT district_id, admin_id, school_id, `date`, status FROM tbl_survey_schedules WHERE (`date` BETWEEN '$sFromDate' AND '$sToDate') $sConditions ORDER BY district_id, admin_id, `date`";
    $objDb->query($sSQL);
	
    $iCount = $objDb->getCount( );
	
	if ($iCount == 0)
	{
?>
		    <tr>
		      <td colspan="2"><div style="padding:100px; text-align:center; border:dotted 1px #dddddd;">No Survey Scheduled in this Week</div></td>
		    </tr>
<?
	}
    
	
    $iPreviousDistrict = 0;
    $iPreviousAdmin    = 0;
    $iPreviousDate     = 0;
    
    for ($i = 0; $i < $iCount; $i ++)
    {
        $iCurrentDistrict = $objDb->getField($i, 'district_id');
        $iCurrentAdmin    = $objDb->getField($i, 'admin_id');
        $sCurrentDate     = $objDb->getField($i, 'date');
        $iSchool          = $objDb->getField($i, 'school_id');
        $sStatus          = $objDb->getField($i, 'status');


		if ($iCurrentDistrict != $iPreviousDistrict)
		{
?>
		    <tr>
		      <td colspan="2" bgcolor="#d6d6d6" style="border:dotted 1px #dddddd;"><b style="display:block; padding:5px 0px 5px 10px;"><?= $sDistrictsList[$iCurrentDistrict] ?></b></td>
		    </tr>
<?
		}
?>

		    <tr valign="top">
<?
		if ($iCurrentAdmin != $iPreviousAdmin || $iCurrentDistrict != $iPreviousDistrict)
		{
			$iPreviousAdmin = $iCurrentAdmin;
?>
              <td style="border:dotted 1px #dddddd;"><div style="padding:10px 0px 0px 10px;"><?= $sAdminsList[$iCurrentAdmin] ?></div></td>
<?
		}
		
		else
		{
?>
              <td></td>    
<?
		}

		$iPreviousDistrict = $iCurrentDistrict;  
?>
              <td>
			    <table border="0" cellpadding="0" cellspacing="0" width="100%">
			      <tr valign="top">
 <?
            for ($j = 0; $j < 6; $j ++)
            {
?>
			        <td width="16.66%" style="border:dotted 1px #dddddd;">
			          <div style="padding:6px;">    
<?
				$sDate = date("Y-m-d", mktime(0, 0, 0, $iMonth, ($iDay + $j), $iYear));
				
				
                if ($sDate == $sCurrentDate)
                {
					$iIndex = 0;

					
					do
					{
						$iCurrentDistrict = $objDb->getField($i, 'district_id');
						$iCurrentAdmin    = $objDb->getField($i, 'admin_id');
						$sCurrentDate     = $objDb->getField($i, 'date');
						$iSchool          = $objDb->getField($i, 'school_id');
						$sStatus          = $objDb->getField($i, 'status');
						
						
						$iPreviousAdmin    = $iCurrentAdmin;
						$iPreviousDistrict = $iCurrentDistrict; 
                                
						if ($sStatus == "C")
						{
?>
                        <div class="schedule" style="float:left; height:20px; line-height:20px; width:68px; margin:0px <?= ((($iIndex % 2) == 0) ? 6 : 0) ?>px 5px 0px; background:<?= (($sSurveyQualifiedList[$iSchool] == 'Y') ? '#36b24f' : '#f08080') ?>; border:solid 1px <?= (($sSurveyQualifiedList[$iSchool] == 'Y') ? '#259a3c' : '#cf5b5b') ?>; color:#ffffff; text-align:center; font-size:9px; cursor:pointer;" title="<?= $sSchoolNamesList[$iSchool] ?>"><?= $sSchoolCodesList[$iSchool] ?></div>
<?
						}
						
						else
						{
?>
                        <div class="planned" style="float:left; height:20px; line-height:20px; width:68px; margin:0px <?= ((($iIndex % 2) == 0) ? 6 : 0) ?>px 5px 0px; background:#e6e6e6; border:solid 1px #cccccc; text-align:center; font-size:9px;" title="<?= $sSchoolNamesList[$iSchool] ?>"><?= $sSchoolCodesList[$iSchool] ?></div>
<?
						}
						
						
						if (($i + 1) == $iCount)
							break;

						
						$iNextDistrict = $objDb->getField(($i + 1), 'district_id');
                        $iNextAdmin    = $objDb->getField(($i + 1), 'admin_id');
                        $sNextDate     = $objDb->getField(($i + 1), 'date');
                   
                        if ($iNextAdmin != $iPreviousAdmin || $iNextDistrict != $iPreviousDistrict || $sDate != $sNextDate)
							break;
                                    
                        $i ++;    
						$iIndex ++;
                    }
                    while(1);
                }
?>
                      </div>
                    </td>
<?
				if (($i + 1) < $iCount)
				{				
					$iNextDistrict = $objDb->getField(($i + 1), 'district_id');
					$iNextAdmin    = $objDb->getField(($i + 1), 'admin_id');
					
					if ($iNextAdmin == $iPreviousAdmin && $iNextDistrict == $iPreviousDistrict)
					{
						$i ++;

						$sCurrentDate = $sNextDate;
					}
				}
            }
    ?>
			      </tr>
		        </table>

              </td>
		    </tr>
<?
	}
?>

		    <tr>
		      <td colspan="2"><div style="border-top:solid 3px #aaaaaa; padding-bottom:2px;"></div></td>
		    </tr>

		    <tr>
		      <td bgcolor="#e6e6e6"><b style="padding-left:10px; color:#444444;">Week # <?= date("W", strtotime($sFromDate)) ?> &nbsp; (<?= date("M d", strtotime($sFromDate)) ?> - <?= date("M d", strtotime($sToDate)) ?>)</b></td>
			
			  <td bgcolor="#f6f6f6">
			    <table border="0" cellspacing="0" cellpadding="10" width="100%">
			      <tr>
<?
	for ($i = 0; $i < 6; $i ++)
	{
?>
                    <td width="16.66%" align="center"><b style="color:#444444;"><?= date('l', mktime(0, 0, 0, $iMonth, ($iDay + $i), $iYear)) ?></b><br /><small><?= date('d-M-Y', mktime(0, 0, 0, $iMonth, ($iDay + $i), $iYear)) ?></small></td>
<?
	}
?>
                  </tr>
                </table>  
              </td>
		    </tr>
          </table>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>