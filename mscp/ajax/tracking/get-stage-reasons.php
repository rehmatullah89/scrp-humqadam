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
?>
	  <option value=""></option>
<?
	$iStage        = IO::intValue("Stage");
	$sStageReasons = getDbValue("failure_reasons", "tbl_stages", "id='$iStage'");
	$sReasonsList  = getList("tbl_failure_reasons", "id", "reason", "FIND_IN_SET(id, '$sStageReasons')");

	foreach ($sReasonsList as $iReason => $sReason)
	{
?>
	  <option value="<?= $iReason ?>"><?= $sReason ?></option>
<?
	}


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>