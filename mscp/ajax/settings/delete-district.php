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

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sDistricts = IO::strValue("Districts");

	if ($sDistricts != "")
	{
		$iDistricts = @explode(",", $sDistricts);
		$sPictures  = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iDistricts); $i ++)
		{
			$sSQL = "SELECT picture FROM tbl_districts WHERE id='{$iDistricts[$i]}' AND picture!=''";
			$objDb->query($sSQL);

			if ($objDb->getCount( ) == 1)
				$sPictures[] = $objDb->getField(0, 0);


			$sSQL  = "DELETE FROM tbl_districts WHERE id='{$iDistricts[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_schools SET district_id='0' WHERE district_id='{$iDistricts[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iDistricts) > 1)
				print "success|-|The selected Districts have been Deleted successfully.";

			else
				print "success|-|The selected District has been Deleted successfully.";


			for ($i = 0; $i < count($sPictures); $i ++)
				@unlink($sRootDir.DISTRICTS_IMG_DIR.$sPictures[$i]);
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid District Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>