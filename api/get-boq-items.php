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


	$sUser   = IO::strValue("User");
	$iSchool = IO::intValue("School");



	$aResponse           = array( );
	$aResponse['Status'] = "ERROR";


	if ($sUser == "" || $iSchool == 0)
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, provinces, districts, schools, status FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else if ($objDb->getField(0, "status") != "A")
			$aResponse["Message"] = "User Account is Disabled";

		else
		{
			$iUser      = $objDb->getField(0, "id");
			$sName      = $objDb->getField(0, "name");
			$sProvinces = $objDb->getField(0, "provinces");
			$sDistricts = $objDb->getField(0, "districts");
			$sSchools   = $objDb->getField(0, "schools");

			$iProvinces = @explode(",", $sProvinces);
			$iDistricts = @explode(",", $sDistricts);
			$iSchools   = @explode(",", $sSchools);



			$sSQL = "SELECT district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
			$objDb->query($sSQL);

			$iDistrict = $objDb->getField(0, "district_id");
			$iProvince = $objDb->getField(0, "province_id");


			if ($objDb->getCount( ) == 0)
				$aResponse["Message"] = "Invalid Request, no School Found!";

			else if ( ($sSchools != "" && !@in_array($iSchool, $iSchools)) || ($sSchools == "" && (!@in_array($iProvince, $iProvinces) || !@in_array($iDistrict, $iDistricts))) )
				$aResponse["Message"] = "Request denied, You don't have permissions for requested School!";

			else
			{
				$sItemsList = getList("tbl_boqs", "id", "title", "status='A'", "position");
				$sItemsUnit = getList("tbl_boqs", "id", "unit", "status='A'", "position");


				$aResponse['Status']   = "OK";
				$aResponse['BoqItems'] = $sItemsList;
				$aResponse['BoqUnits'] = $sItemsUnit;
			}
		}
	}

	print @json_encode($aResponse);


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>