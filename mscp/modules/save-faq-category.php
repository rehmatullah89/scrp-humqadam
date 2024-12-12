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

	$sName        = IO::strValue("txtName");
	$sDescription = IO::strValue("txtDescription");
	$sStatus      = IO::strValue("ddStatus");
	$bError       = true;


	if ($sName == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_faq_categories WHERE name LIKE '$sName'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "FAQ_CATEGORY_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$iCategoryId = getNextId("tbl_faq_categories");


		$sSQL = "INSERT INTO tbl_faq_categories SET id          = '$iCategoryId',
													name        = '$sName',
													description = '$sDescription',
													position    = '$iCategoryId',
													status      = '$sStatus'";

		if ($objDb->execute($sSQL) == true)
			redirect("faqs.php?OpenTab=2", "FAQ_CATEGORY_ADDED");

		else
			$_SESSION["Flag"] = "DB_ERROR";
	}
?>