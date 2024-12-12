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

	$sManagement = array( );
	$sContents   = array( );
	$sSettings   = array( );
	$sTracking   = array( );
	$sSurveys    = array( );
	$sModules    = array( );


	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Management'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iManagement = $objDb->getCount( );

	for ($i = 0; $i < $iManagement; $i ++)
	{
		$sManagement[$i]['Section'] = $objDb->getField($i, 0);
		$sManagement[$i]['Files']   = $objDb->getField($i, 1);
	}



	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Contents'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iContents = $objDb->getCount( );

	for ($i = 0; $i < $iContents; $i ++)
	{
		$sContents[$i]['Section'] = $objDb->getField($i, 0);
		$sContents[$i]['Files']   = $objDb->getField($i, 1);
	}



	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Settings'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iSettings = $objDb->getCount( );

	for ($i = 0; $i < $iSettings; $i ++)
	{
		$sSettings[$i]['Section'] = $objDb->getField($i, 0);
		$sSettings[$i]['Files']   = $objDb->getField($i, 1);
	}

	

	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Tracking'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iTracking = $objDb->getCount( );

	for ($i = 0; $i < $iTracking; $i ++)
	{
		$sTracking[$i]['Section'] = $objDb->getField($i, 0);
		$sTracking[$i]['Files']   = $objDb->getField($i, 1);
	}
	
	
	
	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Surveys'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iSurveys = $objDb->getCount( );

	for ($i = 0; $i < $iSurveys; $i ++)
	{
		$sSurveys[$i]['Section'] = $objDb->getField($i, 0);
		$sSurveys[$i]['Files']   = $objDb->getField($i, 1);
	}



	$sSQL = "SELECT ap.section, ap.files
	         FROM tbl_admin_pages ap, tbl_admin_rights ar
	         WHERE ap.id=ar.page_id AND ar.`view`='Y' AND ar.admin_id='{$_SESSION["AdminId"]}' AND ap.module='Modules'
	         ORDER BY ap.position";
	$objDb->query($sSQL);

	$iModules = $objDb->getCount( );

	for ($i = 0; $i < $iModules; $i ++)
	{
		$sModules[$i]['Section'] = $objDb->getField($i, 0);
		$sModules[$i]['Files']   = $objDb->getField($i, 1);
	}
?>
  <div id="Navigation">
<?
	if ($_SESSION["AdminId"] != "")
	{
?>
    <ul>
	  <li>
	    <a href="dashboard.php">Dashboard<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
	      <li><a href="my-account.php">My Account</a></li>
	      <li><a href="logout.php">Logout</a></li>
	    </ul>
	  </li>
<?
		if ($iTracking > 0)
		{
?>

	  <li>
	    <a href="tracking/">Tracking<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iTracking; $i ++)
			{
				$sFile = substr($sTracking[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="tracking/<?= $sFile ?>"><?= $sTracking[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}
		
		
		if ($iSurveys > 0)
		{
?>

	  <li>
	    <a href="surveys/">Surveys<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iSurveys; $i ++)
			{
				$sFile = substr($sSurveys[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="surveys/<?= $sFile ?>"><?= $sSurveys[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}

		
		if ($iSettings > 0)
		{
?>

	  <li>
	    <a href="settings/">Settings<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iSettings; $i ++)
			{
				$sFile = substr($sSettings[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="settings/<?= $sFile ?>"><?= $sSettings[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}


		if ($iContents > 0)
		{
?>

	  <li>
	    <a href="contents/">Contents<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iContents; $i ++)
			{
				$sFile = substr($sContents[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="contents/<?= $sFile ?>"><?= $sContents[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}

		
		if ($iModules > 0)
		{
?>

	  <li>
	    <a href="modules/">Modules<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iModules; $i ++)
			{
				$sFile = substr($sModules[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="modules/<?= $sFile ?>"><?= $sModules[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}


		if ($iManagement > 0)
		{
?>

	  <li>
	    <a href="management/">Management<img src="images/themes/<?= $_SESSION["CmsTheme"] ?>/nav-arrow.png" alt="" title="" /></a>

	    <ul>
<?
			for ($i = 0; $i < $iManagement; $i ++)
			{
				$sFile = substr($sManagement[$i]['Files'], 1);
				$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li><a href="management/<?= $sFile ?>"><?= $sManagement[$i]['Section'] ?></a></li>
<?
			}
?>
	    </ul>
	  </li>
<?
		}
?>
    </ul>
<?
	}
?>
  </div>
