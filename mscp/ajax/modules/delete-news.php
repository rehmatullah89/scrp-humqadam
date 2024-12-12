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

	$sNews = IO::strValue("News");

	if ($sNews != "")
	{
		$iNews     = @explode(",", $sNews);
		$sPictures = array( );


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iNews); $i ++)
		{
			$sSQL = "SELECT picture FROM tbl_news WHERE id='{$iNews[$i]}' AND picture!=''";
			$objDb->query($sSQL);

			if ($objDb->getCount( ) == 1)
				$sPictures[] = $objDb->getField(0, 0);


			$sSQL  = "DELETE FROM tbl_news WHERE id='{$iNews[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iNews) > 1)
				print "success|-|The selected News have been Deleted successfully.";

			else
				print "success|-|The selected News has been Deleted successfully.";


			for ($i = 0; $i < count($sPictures); $i ++)
			{
				@unlink($sRootDir.NEWS_IMG_DIR.'thumbs/'.$sPictures[$i]);
				@unlink($sRootDir.NEWS_IMG_DIR.'originals/'.$sPictures[$i]);
			}
		}

		else
		{
			$objDb->execute("ROLLBACK");

			print "error|-|An error occured while processing your request, please try again.";
		}
	}

	else
		print "info|-|Inavlid News Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>