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

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
</head>

<body>

<div id="MainDiv">

<!--  Header Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/header.php");
?>
<!--  Header Section Ends Here  -->


<!--  Navigation Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/navigation.php");
?>
<!--  Navigation Section Ends Here  -->


<!--  Body Section Starts Here  -->
  <div id="Body">
<?
	@include("{$sAdminDir}includes/breadcrumb.php");
?>

    <div id="Contents">
<?
	@include("{$sAdminDir}includes/messages.php");


	if ($iTracking > 0)
	{
?>
      <fieldset class="first">
        <legend><a href="tracking/">Tracking</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iTracking; $i ++)
		{
			$sFile = substr($sTracking[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="tracking/<?= $sFile ?>">
		      <img src="images/tracking/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sTracking[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}
	
	
	if ($iSurveys > 0)
	{
?>
      <fieldset>
        <legend><a href="surveys/">Surveys</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iSurveys; $i ++)
		{
			$sFile = substr($sSurveys[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="surveys/<?= $sFile ?>">
		      <img src="images/surveys/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sSurveys[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}
	

	if ($iSettings > 0)
	{
?>
      <fieldset>
        <legend><a href="settings/">Settings</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iSettings; $i ++)
		{
			$sFile = substr($sSettings[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="settings/<?= $sFile ?>">
		      <img src="images/settings/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sSettings[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}


	if ($iContents > 0)
	{
?>

      <fieldset>
        <legend><a href="contents/">Contents</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iContents; $i ++)
		{
			$sFile = substr($sContents[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="contents/<?= $sFile ?>">
		      <img src="images/contents/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sContents[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}

	
	if ($iModules > 0)
	{
?>
      <fieldset>
        <legend><a href="modules/">Modules</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iModules; $i ++)
		{
			$sFile = substr($sModules[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="modules/<?= $sFile ?>">
		      <img src="images/modules/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sModules[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}


	if ($iManagement > 0)
	{
?>
      <fieldset>
        <legend><a href="management/">Management</a></legend>

	    <ul>
<?
		for ($i = 0; $i < $iManagement; $i ++)
		{
			$sFile = substr($sManagement[$i]['Files'], 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
		  <li>
		    <a href="management/<?= $sFile ?>">
		      <img src="images/management/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
		      <span><?= $sManagement[$i]['Section'] ?></span>
		    </a>
		  </li>
<?
		}
?>
	    </ul>
      </fieldset>
<?
	}
?>

    </div>
  </div>
<!--  Body Section Ends Here  -->


<!--  Footer Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/footer.php");
?>
<!--  Footer Section Ends Here  -->

</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>