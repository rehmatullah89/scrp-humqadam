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

	@require_once("requires/common.php");

	unset($_SESSION["AdminId"]);
	unset($_SESSION["AdminName"]);
	unset($_SESSION["AdminEmail"]);
	unset($_SESSION["AdminProvinces"]);
	unset($_SESSION["AdminDistricts"]);
	unset($_SESSION["AdminSchools"]);
	unset($_SESSION["AdminLevel"]);
	unset($_SESSION["PageRecords"]);
	unset($_SESSION["CmsTheme"]);
	unset($_SESSION["SiteTitle"]);
	unset($_SESSION["DateFormat"]);
	unset($_SESSION["TimeFormat"]);
	unset($_SESSION["ImageResize"]);

	header("Location: ./");
?>