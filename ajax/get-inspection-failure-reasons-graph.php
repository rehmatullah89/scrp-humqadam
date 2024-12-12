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


	$sFromDate = IO::strValue("FromDate");
	$sToDate   = IO::strValue("ToDate");
	$iProvince = IO::intValue("Province");
	$iDistrict = IO::intValue("District");
        
       	
	$sFrInspectionSQL = " AND FIND_IN_SET(i.district_id, '{$_SESSION['AdminDistricts']}') ";
	
	if ($iDistrict > 0)
		$sFrInspectionSQL .= " AND i.district_id='$iDistrict' ";
	
	else if ($iProvince > 0)
		$sFrInspectionSQL .= " AND i.district_id IN (SELECT id FROM tbl_districts WHERE province_id='$iProvince') ";

	if ($_SESSION["AdminSchools"] != "")
		$sFrInspectionSQL .= " AND FIND_IN_SET(i.school_id, '{$_SESSION['AdminSchools']}') ";

        if($sFromDate != '' && $sToDate != '')
                $sFrInspectionSQL .= " AND (i.date BETWEEN '$sFromDate' AND '$sToDate') ";
                
	$sSQL = "SELECT count(1) as _FrCount, fr.reason as _Reason  FROM tbl_inspections i, tbl_failure_reasons fr WHERE i.failure_reason_id = fr.id AND i.failure_reason_id != '0' $sFrInspectionSQL Group By _Reason";
	$objDb->query($sSQL);
        
        $iCount = $objDb->getCount( );
        
?>
			<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspection-failure-reasons'>
<?
                        for($i=0; $i < $iCount; $i++){
            
                            $iFailureReasonCount  = $objDb->getField($i, "_FrCount");
                            $sFailureReason       = $objDb->getField($i, "_Reason");
                        
                            print "<set tooltext='' label='$sFailureReason' value='$iFailureReasonCount' link='' />";
                        }
?>
			</chart>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>