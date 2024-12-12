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


	$sType = IO::strValue("Type");
?>
			  <option value=""></option>
<?
	$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='0' AND `type`='$sType' ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iParentId = $objDb->getField($i, "id");
		$sParent   = $objDb->getField($i, "name");
?>
			  <option value="<?= $iParentId ?>"><?= $sParent ?></option>
<?
		$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iParentId' ORDER BY position";
		$objDb2->query($sSQL);

		$iCount2 = $objDb2->getCount( );

		for ($j = 0; $j < $iCount2; $j ++)
		{
			$iStage = $objDb2->getField($j, "id");
			$sStage = $objDb2->getField($j, "name");
?>
	    	  <option value="<?= $iStage ?>"><?= ($sParent." &raquo; ".$sStage) ?></option>
<?
			$sSQL = "SELECT id, name FROM tbl_stages WHERE parent_id='$iStage' ORDER BY position";
			$objDb3->query($sSQL);

			$iCount3 = $objDb3->getCount( );

			for ($k = 0; $k < $iCount3; $k ++)
			{
				$iSubStage = $objDb3->getField($k, "id");
				$sSubStage = $objDb3->getField($k, "name");
?>
	    	  <option value="<?= $iSubStage ?>"><?= ($sParent." &raquo; ".$sStage." &raquo; ".$sSubStage) ?></option>
<?
			}
		}
	}		


	$objDb->close( );
	$objDb2->close( );
	$objDb3->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>