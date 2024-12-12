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


	$sUser       = IO::strValue("User");
	$sLatitude   = IO::strValue("Latitude");
	$sLongitude  = IO::strValue("Longitude");
	$sAddress    = "";
	$sAddressSQL = "";


	$aResponse            = array( );
	$aResponse["Status"]  = "ERROR";

	if ($sUser == "" || $sLatitude == "" || $sLongitude == "")
		$aResponse["Message"] = "Invalid Request";

	else
	{
		$sSQL = "SELECT id, name, email, location_time FROM tbl_admins WHERE MD5(id)='$sUser'";
		$objDb->query($sSQL);

		if ($objDb->getCount( ) == 0)
			$aResponse["Message"] = "Invalid User";

		else
		{
			$iUser         = $objDb->getField(0, "id");
			$sName         = $objDb->getField(0, "name");
			$sEmail        = $objDb->getField(0, "email");
			$sLocationTime = $objDb->getField(0, "location_time");


			if (strtotime($sLocationTime) < (strtotime(date("Y-m-d H:i:s")) - 900))
			{
				$sLocation = @json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng={$sLatitude},{$sLongitude}&sensor=false"), true);

				if ($sLocation["results"][0]["address_components"][0]["long_name"] != "")
					$sAddress .= ($sLocation["results"][0]["address_components"][0]["long_name"]."\n");

				if ($sLocation["results"][0]["address_components"][1]["long_name"] != "")
					$sAddress .= ($sLocation["results"][0]["address_components"][1]["long_name"]."\n");

				if ($sLocation["results"][0]["address_components"][2]["long_name"] != "")
					$sAddress .= ($sLocation["results"][0]["address_components"][2]["long_name"]."\n");

				if ($sLocation["results"][0]["address_components"][3]["long_name"] != "")
					$sAddress .= ($sLocation["results"][0]["address_components"][3]["long_name"]."\n");

				$sAddress .= "\n";


				$sAddressSQL = " , location_address='$sAddress' ";
			}


			$sSQL = "UPDATE tbl_admins SET latitude='$sLatitude', longitude='$sLongitude', location_time=NOW() $sAddressSQL WHERE id='$iUser'";
			$objDb->execute($sSQL, true, $iUser, $sName, $sEmail);


			$aResponse["Status"]  = "OK";
			$aResponse["Address"] = $sAddress;
		}
	}


	print @json_encode($aResponse);


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>