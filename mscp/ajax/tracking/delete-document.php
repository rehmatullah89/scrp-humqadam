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
	$objDb2      = new Database( );
	$objDb3      = new Database( );
	$objDb4      = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sDocuments = IO::strValue("Documents");

	if ($sDocuments != "")
	{
		$iDocuments = @explode(",", $sDocuments);
		$sDocuments   = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iDocuments); $i ++)
		{
			$iSchool = getDbValue("school_id", "tbl_documents", "id='{$iDocuments[$i]}'");

			$sSQL = "SELECT file FROM tbl_document_files WHERE document_id='{$iDocuments[$i]}'";
			$objDb->query($sSQL);

			$iCount = $objDb->getCount( );

			for ($j = 0; $j < $iCount; $j ++)
				$sDocuments[] = $objDb->getField($j, 0);


			$sSQL  = "DELETE FROM tbl_documents WHERE id='{$iDocuments[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_document_files WHERE document_id='{$iDocuments[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iDocuments) > 1)
				print "success|-|The selected Document Records have been Deleted successfully.";

			else
				print "success|-|The selected Document Record has been Deleted successfully.";

			for ($i = 0; $i < count($sDocuments); $i ++)
			{
				@unlink($sRootDir.'documents/'.$sDocuments[$i]);
			}
		}

		else
		{
			print "error|-|An error occured while processing your request, please try again.";

			$objDb->execute("ROLLBACK");
		}
	}

	else
		print "info|-|Inavlid Document Record Delete request.";


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>