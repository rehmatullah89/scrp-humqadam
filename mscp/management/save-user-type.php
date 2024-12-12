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

	$sTitle  = IO::strValue("txtTitle");
	$sStatus = IO::strValue("ddStatus");
	$bError  = true;


	if ($sTitle == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";


	if ($_SESSION["Flag"] == "")
	{
		$sSQL = "SELECT * FROM tbl_admin_types WHERE title LIKE '$sTitle'";

		if ($objDb->query($sSQL) == true && $objDb->getCount( ) == 1)
			$_SESSION["Flag"] = "USER_TYPE_EXISTS";
	}


	if ($_SESSION["Flag"] == "")
	{
		$objDb->execute("BEGIN");


		$iType = getNextId("tbl_admin_types");

		$sSQL = "INSERT INTO tbl_admin_types SET id     = '$iType',
										    	 title  = '$sTitle',
										    	 status = '$sStatus'";
		$bFlag = $objDb->execute($sSQL);


		if ($bFlag == true)
		{
			$iPageCount = IO::intValue("PageCount");

			for ($i = 0; $i < $iPageCount; $i ++)
			{
				$iPageId = IO::intValue("PageId{$i}");
				$sView   = IO::strValue("cbView{$i}");
				$sAdd    = IO::strValue("cbAdd{$i}");
				$sEdit   = IO::strValue("cbEdit{$i}");
				$sDelete = IO::strValue("cbDelete{$i}");

				$sSQL = "INSERT INTO tbl_admin_type_rights SET type_id  = '$iType',
															   page_id  = '$iPageId',
															   `view`   = '$sView',
															   `add`    = '$sAdd',
															   `edit`   = '$sEdit',
															   `delete` = '$sDelete'";
				$bFlag = $objDb->execute($sSQL);

				if ($bFlag == false)
					break;
			}
		}


		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			redirect("users.php?OpenTab=2", "USER_TYPE_ADDED");
		}

		else
		{
			$objDb->execute("ROLLBACK");

			$_SESSION["Flag"] = "DB_ERROR";
		}
	}
?>