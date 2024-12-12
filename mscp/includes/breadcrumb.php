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
?>
    <div id="Page">
      <img id="Indicator" src="images/indicator.gif" alt="" title="" />
<?
	if ($sCurDir == ADMIN_CP_DIR)
	{
		if ($sCurPage == "dashboard.php")
		{
?>
      <img src="images/dashboard.png" title="" alt="" />
      <h1>Dashboard</h1>
      <span>What would you like to do today!</span><br />
<?
		}

		else if ($sCurPage == "my-account.php")
		{
?>
      <img src="images/my-account.png" title="" alt="" />
      <h1>My Account</h1>
      <a href="dashboard.php">Dashboard</a> &gt; <span>My Account</span><br />
<?
		}
	}

	else
	{
		if ($sCurPage == "index.php")
		{
?>
      <img src="images/<?= $sCurDir ?>.png" title="" alt="" />
      <h1><?= ucfirst($sCurDir) ?></h1>
      <a href="dashboard.php">Dashboard</a> &gt; <span><?= ucfirst($sCurDir) ?></span><br />
<?
		}

		else
		{
			$sSQL = "SELECT module, section, files FROM tbl_admin_pages WHERE module LIKE '$sCurDir' AND FIND_IN_SET('\'$sCurPage\'', files)";
			$objDb->query($sSQL);

			$sModule = $objDb->getField(0, 0);
			$sPage   = $objDb->getField(0, 1);
			$sFiles  = $objDb->getField(0, 2);

			$sFile = substr($sFiles, 1);
			$sFile = substr($sFile, 0, strpos($sFile, "'"));
?>
      <img src="images/<?= $sCurDir ?>/<?= str_replace('.php', '.png', $sFile) ?>" title="" alt="" />
      <h1><?= $sPage ?></h1>
      <a href="dashboard.php">Dashboard</a> &gt; <a href="<?= $sCurDir ?>/"><?= $sModule ?></a> &gt; <span><?= $sPage ?></span><br />
<?
		}
	}
?>
    </div>
