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


	$sContractors = IO::strValue("Contractors");

	if ($sContractors != "")
	{
		$iContractors = @explode(",", $sContractors);
		$sLogos       = array( );
		$sPictures    = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iContractors); $i ++)
		{
			$sSQL = "SELECT picture, logo FROM tbl_contractors WHERE id='{$iContractors[$i]}'";
			$objDb->query($sSQL);

			if ($objDb->getField(0, "logo") != "")
				$sLogos[] = $objDb->getField(0, "logo");

			if ($objDb->getField(0, "picture") != "")
				$sPictures[] = $objDb->getField(0, "picture");


			$sSQL  = "DELETE FROM tbl_contractors WHERE id='{$iContractors[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "UPDATE tbl_contracts SET contractor='0', status='I' WHERE contractor_id='{$iContractors[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_contractor_boqs WHERE contractor_id='{$iContractors[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iContractors) > 1)
				print "success|-|The selected Contractors have been Deleted successfully.";

			else
				print "success|-|The selected Contractor has been Deleted successfully.";


			for ($i = 0; $i < count($sLogos); $i ++)
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."logos/".$sLogos[$i]);

			for ($i = 0; $i < count($sPictures); $i ++)
				@unlink($sRootDir.CONTRACTORS_IMG_DIR."persons/".$sPictures[$i]);
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid Contractor Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>