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

	$_SESSION["Flag"] = "";

	$sQuestion = IO::strValue("txtQuestion");
	$sAnswer   = IO::strValue("txtAnswer");
	$iCategory = IO::intValue("ddCategory");
	$sStatus   = IO::strValue("ddStatus");
	$bError    = true;


	if ($sQuestion == "" || $sAnswer == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_faqs WHERE category_id='$iCategory' AND question LIKE '$sQuestion'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "FAQ_EXISTS";
	}

	if ($_SESSION["Flag"] == "")
	{
		$iFaqId = getNextId("tbl_faqs");

		$sSQL = "INSERT INTO tbl_faqs SET id          = '$iFaqId',
		                                  category_id = '$iCategory',
		                                  question    = '$sQuestion',
		                                  answer      = '$sAnswer',
                                          position    = '$iFaqId',
                                          status      = '$sStatus',
                                          date_time   = NOW( )";

		if ($objDb->execute($sSQL) == true)
			redirect("faqs.php", "FAQ_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>