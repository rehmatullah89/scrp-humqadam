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

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$iInspectionId = IO::intValue("InspectionId");
	$sTitle        = IO::strValue("Title");
	$iDistrict     = IO::intValue("District");
	$iSchool       = IO::intValue("School");
	$iStage        = IO::intValue("Stage");


	if ($sTitle != "" && $iDistrict > 0 && $iSchool > 0 && $iStage > 0)
	{
		$sSQL = "SELECT id FROM tbl_inspections WHERE (title LIKE '$sTitle' AND district_id='$iDistrict' AND school_id='$iSchool' AND stage_id='$iStage')";

		if ($iInspectionId > 0)
			$sSQL .= " AND id!='$iInspectionId' ";

		if ($objDb->query($sSQL) == true)
		{
			if ($objDb->getCount( ) == 1)
				print "USED";
		}
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>