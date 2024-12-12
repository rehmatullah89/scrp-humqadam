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

	if ($iPackageId == "" || $iLots <= 0)
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
            $objDb->execute("BEGIN");
            
            $sSQL = "Delete from tbl_package_lots where package_id='$iPackageId'";
            $bFlag = $objDb->execute($sSQL);
            
            if($bFlag == true)
            {    
                for($i=1; $i < $iLots; $i++)
				{
                    $sTitle   = IO::strValue("txtTitle_$i");
                    $sSchools = @implode(",", IO::getArray("cbSchool_$i"));
                    $sOccupiedSchools = getDbValue("GROUP_CONCAT(schools SEPARATOR ',')", "tbl_package_lots", "package_id='$iPackageId'");

                    $bFlag = (count(array_intersect(IO::getArray("cbSchool_$i"), explode(',', $sOccupiedSchools)))) ? true : false;
                    
                    if($bFlag == true){
?>
	<script type="text/javascript">
	<!--
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "info", "The selected School in Package, Already exist in another lot.");
	-->
	</script>
<?         
                        exit( );
                    }


                    $sSQL = "INSERT INTO tbl_package_lots SET lot_id    = '$i',
                                                            package_id = '$iPackageId',   					
                                                            title      = '$sTitle',
                                                            schools = '$sSchools'";
                    $bFlag = $objDb->execute($sSQL);
                    
                    if($bFlag == false)
                        break;
                }
            }
          
		  
		if ($bFlag == true)
		{
                    $objDb->execute("COMMIT");
?>
	<script type="text/javascript">
	<!--
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The selected Package Lots has been Saved/Updated successfully.");
	-->
	</script>
<?
                        exit( );
		}

		else{
                        $objDb->execute("ROLLBACK");
                        
			$_SESSION["Flag"] = "DB_ERROR";
                }
	}
?>