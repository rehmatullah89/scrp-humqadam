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


	$sRecords = IO::strValue("Records");
	$sTable   = IO::strValue("Table");

	if ($sRecords != "")
	{
		$iRecords   = @explode(",", $sRecords);
		$iPositions = array( );


		$sSQL = "SELECT position FROM {$sTable} WHERE FIND_IN_SET(id, '$sRecords') ORDER BY position";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
			$iPositions[] = $objDb->getField($i, 0);



		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iRecords); $i ++)
		{
			$sSQL  = "UPDATE {$sTable} SET position='{$iPositions[$i]}' WHERE id='{$iRecords[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == false)
				break;
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			print "success|-|The Position of selected Record has been Updated successfully.";
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An ERROR occured while processing your request, please try again.";
		}
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>