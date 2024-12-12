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

	function getNextId($sTable)
	{
		global $objDbGlobal;

		if (!$objDbGlobal)
			$objDbGlobal = new Database( );


		$sSQL = "SELECT MAX(id) FROM {$sTable}";
		$objDbGlobal->query($sSQL);


		return ($objDbGlobal->getField(0, 0) + 1);
	}


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


		$sSQL = "SELECT {$sField} FROM {$sTable} {$sConditions} {$sGroupBy} {$sOrderBy} LIMIT {$sLimit}";
		$objDbGlobal->query($sSQL);

		return $objDbGlobal->getField(0, 0);
	}


	function getList($sTable, $sKey, $sValue, $sConditions = "", $sOrderBy = "", $sGroupBy = "", $iLimit = 0)
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

		if ($iLimit > 0)
			$sLimit = "LIMIT $iLimit";


		$sSQL = "SELECT $sKey, $sValue FROM $sTable $sConditions $sGroupBy ORDER BY $sOrderBy $sLimit";
		$objDbGlobal->query($sSQL);

		$iCount = $objDbGlobal->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
			$sList[$objDbGlobal->getField($i, 0)] = $objDbGlobal->getField($i, 1);

		return $sList;
	}


	function getPageUrl($iPageId = 1, $sSefUrl = "")
	{
		global $sSefMode;

		if ($sSefMode == "Y")
		{
			if ($sSefUrl == "")
				$sSefUrl = getDbValue("sef_url", "tbl_web_pages", "id='$iPageId'");

			return (SITE_URL.$sSefUrl);
		}

		else
			return (SITE_URL."?PageId={$iPageId}");
	}


	function getNewsUrl($iNewsId, $sSefUrl = "")
	{
		global $sSefMode;

		if ($sSefMode == "Y")
		{
			if ($sSefUrl == "")
				$sSefUrl = getDbValue("sef_url", "tbl_news", "id='$iNewsId'");

			return (SITE_URL.'news/'.$sSefUrl);
		}

		else
		{
			$iPageId = getDbValue("id", "tbl_web_pages", "php_url='news.php'");

			return (SITE_URL."index.php?PageId={$iPageId}&NewsId={$iNewsId}");
		}
	}
?>