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

        $iType              = IO::intValue("ddType");
	$sName              = IO::strValue("txtName");
	$sPhone             = IO::strValue("txtPhone");
	$sStatus            = IO::strValue("ddStatus");
	$iSchool            = IO::intValue("SchoolId");	
	
	if ($sName == "" || $iType == "" || $sStatus == "")
		$_SESSION["Flag"] = "INCOMPLETE_FORM";

	if ($_SESSION["Flag"] == "")
	{
                $Id = getNextId('tbl_school_members');

                $sSQL = "INSERT INTO tbl_school_members SET id              = '$Id',  
                                                            school_id       = '$iSchoolId',
                                                            name            = '$sName',
                                                            type_id          = '$iType',
                                                            phone           = '$sPhone',
                                                            status          = '$sStatus'";
                $bFlag = $objDb->execute($sSQL);
	}
        
?>
<script type="text/javascript">
<!--
<?
    if($bFlag == true)
    {
?>

parent.$.colorbox.close( );
parent.showMessage("#GridMsg", "success", "School Member has been added successfully.");

<?
    }
?>
-->
</script>