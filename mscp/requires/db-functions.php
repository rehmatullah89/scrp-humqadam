<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.3-tree.com/imc/                                                               **
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

	function getPagingInfo($sTable, $sConditions, $iPageSize, $iPageId)
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		if (@strpos($sTable, "SELECT") !== FALSE)
			$sSQL = $sTable;

		else
			$sSQL = "SELECT COUNT(1) FROM $sTable $sConditions";

		$objDbGlobal->query($sSQL);

		if (@strpos($sTable, "SELECT") !== FALSE)
			$iTotalRecords = $objDbGlobal->getCount( );

		else
			$iTotalRecords = $objDbGlobal->getField(0, 0);


		if ($iTotalRecords > 0)
		{
			$iPageCount = @floor($iTotalRecords / $iPageSize);

			if (($iTotalRecords % $iPageSize) > 0)
				$iPageCount += 1;
		}

		$iStart = (($iPageId * $iPageSize) - $iPageSize);

		return array($iTotalRecords, $iPageCount, $iStart);
	}


	function getDbValue($sField, $sTable, $sConditions = "", $sOrderBy = "", $sGroupBy = "", $sLimit = "1")
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		if ($sConditions != "")
			$sConditions = " WHERE $sConditions";

		if ($sOrderBy != "")
			$sOrderBy = " ORDER BY {$sOrderBy}";

		if ($sGroupBy != "")
			$sGroupBy = " GROUP BY {$sGroupBy}";


		$sSQL = "SELECT {$sField} FROM {$sTable} {$sConditions} {$sOrderBy} {$sGroupBy} LIMIT {$sLimit}";
		$objDbGlobal->query($sSQL);

		return $objDbGlobal->getField(0, 0);
	}


	function getNextId($sTable)
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		$sSQL = "SELECT MAX(id) FROM {$sTable}";
		$objDbGlobal->query($sSQL);


		return ($objDbGlobal->getField(0, 0) + 1);
	}


	function checkLogin($bFlag = true)
	{
		global $objDbGlobal;

		if ($_SESSION["AdminId"] != "")
		{
			if (!$objDbGlobal)
				$objDbGlobal = new Database( );


			$sSQL = "SELECT name, email, level, records, status FROM tbl_admins WHERE id='{$_SESSION["AdminId"]}'";

			if ($objDbGlobal->query($sSQL) == true)
			{
				if ($objDbGlobal->getCount( ) == 1)
				{
					if ($objDbGlobal->getField(0, "status") == "A")
					{
						$_SESSION["AdminName"]    = $objDbGlobal->getField(0, "name");
						$_SESSION["AdminEmail"]   = $objDbGlobal->getField(0, "email");
						$_SESSION["AdminLevel"]   = $objDbGlobal->getField(0, "level");
						$_SESSION["PageRecords"] = $objDbGlobal->getField(0, "records");
					}

					else
					{
						unset($_SESSION["AdminId"]);
						unset($_SESSION["AdminName"]);
						unset($_SESSION["AdminEmail"]);
						unset($_SESSION["AdminLevel"]);
						unset($_SESSION["PageRecords"]);

						redirect((SITE_URL.ADMIN_CP_DIR."/"));
					}
				}

				else
					redirect((SITE_URL.ADMIN_CP_DIR."/"));
			}

			else
			{
				redirect((SITE_URL.ADMIN_CP_DIR."/"));
			}
		}


		if ($bFlag == true)
		{
			if ($_SESSION["AdminId"] == "")
			{
				$_SESSION['Referer'] = ($_SERVER['PHP_SELF'].(($_SERVER['QUERY_STRING'] != "") ? "?" : "").$_SERVER['QUERY_STRING']);
?>
	<script type="text/javascript">
	<!--
		top.document.location = "<?= (SITE_URL.ADMIN_CP_DIR."/") ?>";
	-->
	</script>
<?
				exit( );
			}
		}

		else if ($bFlag == false)
		{
			if ($_SESSION["AdminId"] != "")
				redirect((SITE_URL.ADMIN_CP_DIR."/dashboard.php"));
		}
	}


	function getUserRights( )
	{
		global $objDbGlobal;
		global $sCurPage;
		global $sCurDir;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		$sSQL = "SELECT id FROM tbl_admin_pages WHERE module LIKE '$sCurDir' AND FIND_IN_SET('\'$sCurPage\'', files)";
		$objDbGlobal->query($sSQL);

		$iPageId = $objDbGlobal->getField(0, 0);


		$sSQL = "SELECT `view`, `add`, `edit`, `delete` FROM tbl_admin_rights WHERE admin_id='{$_SESSION["AdminId"]}' AND page_id='$iPageId'";
		$objDbGlobal->query($sSQL);


		$sRights = array( );

		$sRights['Add']    = (($objDbGlobal->getField(0, 'add') != "Y") ? "N" : "Y");
		$sRights['Edit']   = (($objDbGlobal->getField(0, 'edit') != "Y") ? "N" : "Y");
		$sRights['Delete'] = (($objDbGlobal->getField(0, 'delete') != "Y") ? "N" : "Y");
		$sRights['View']   = (($objDbGlobal->getField(0, 'view') != "Y") ? "N" : "Y");

		return $sRights;
	}


	function checkUserRights($sPage, $sDir, $sAction)
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		$sSQL = "SELECT id FROM tbl_admin_pages WHERE module LIKE '$sDir' AND FIND_IN_SET('\'$sPage\'', files)";
		$objDbGlobal->query($sSQL);

		$iPageId = $objDbGlobal->getField(0, 0);


		$sSQL = "SELECT `{$sAction}` FROM tbl_admin_rights WHERE admin_id='{$_SESSION["AdminId"]}' AND page_id='$iPageId'";
		$objDbGlobal->query($sSQL);

		return (($objDbGlobal->getField(0, 0) == "Y") ? true : false);
	}


	function getList($sTable, $sKey, $sValue, $sConditions = "", $sOrderBy = "", $sGroupBy = "")
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		$sList = array( );


		if ($sConditions != "")
			$sConditions = (" WHERE ".$sConditions);

		if ($sOrderBy == "")
			$sOrderBy = $sValue;

		if ($sGroupBy != "")
			$sGroupBy = "GROUP BY $sGroupBy";


		$sSQL = "SELECT $sKey, $sValue FROM $sTable $sConditions $sGroupBy ORDER BY $sOrderBy";
		$objDbGlobal->query($sSQL);

		$iCount = $objDbGlobal->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
			$sList[$objDbGlobal->getField($i, 0)] = $objDbGlobal->getField($i, 1);

		return $sList;
	}
?>