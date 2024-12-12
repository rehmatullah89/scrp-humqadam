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

	$iSorId    = IO::intValue("SorId");
	$sCode     = IO::strValue("Code");
        
	if ($sCode != "")
	{
		$iSchool = getDbValue("id", "tbl_schools", "`code`='$sCode'");
		
		if ($iSchool == 0)
			print "INVALID";
		
		else
		{
			$sSQL = "SELECT id FROM tbl_school_sors WHERE school_id='$iSchool'";

			if ($iSorId > 0)
				$sSQL .= " AND id!='$iSorId' ";

			if ($objDb->query($sSQL) == true)
			{
				if ($objDb->getCount( ) == 1)
                                    print "USED";
                        }
                        
                 }
        }


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>