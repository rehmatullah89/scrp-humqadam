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

	if (!@in_array($iPageId, array(9,11)))
	{
?>
          <div id="Login">
            <h4 class="small">User Area</h4>
<?
		if ($_SESSION['AdminId'] == 0)
		{
?>
            <form name="frmLogin" id="frmLogin" onsubmit="return false;">
              <input type="hidden" id="RequestUrl" value="<?= $_SESSION['RequestUrl'] ?>" />
			  <div id="LoginMsg"></div>

              <input type="text" name="txtUsername" id="txtUsername" value="" maxlength="100" class="textbox" placeholder="Email Address" />
              <div class="br5"></div>
              <input type="password" name="txtPassword" id="txtPassword" value="" maxlength="50" class="textbox" placeholder="Password" />
              <div class="br10"></div>
              <div><input type="submit" name="btnLogin" id="btnLogin" value=" Login " class="button" /></div>
              <br />
              <center><a href="./" onclick="return false;">Forgot password?</a></center>
            </form>
<?
		}

		else
		{
			$sSQL = "SELECT type_id, picture FROM tbl_admins WHERE id='{$_SESSION['AdminId']}'";
			$objDb->query($sSQL);

			$iType    = $objDb->getField(0, "type_id");
			$sPicture = $objDb->getField(0, "picture");


			if ($sPicture == "" || !@file_exists(ADMINS_IMG_DIR.'thumbs/'.$sPicture))
				$sPicture = "default.jpg";
?>
		    <div class="member">
		      <div class="pic"><img src="<?= (ADMINS_IMG_DIR.'thumbs/'.$sPicture) ?>" alt="" title="" /></div>

		      <br />
		      <h5><?= $_SESSION['AdminName'] ?></h5>
		      [
<?
//			if ($iType == 1)
			{
?>
		        <a href="mscp/" target="_blank">Admin Panel</a> |
<?
			}
?>
		        <a href="logout.php">Logout</a>
		      ]<br />
		    </div>
<?
		}
?>
          </div>

<?
		$sSQL = "SELECT * FROM tbl_news WHERE status='A' ORDER BY id DESC LIMIT 2";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		if ($iCount > 0)
		{
?>
          <br />

          <div id="News">
            <h4 class="small">Latest News</h4>

<?
			for ($i = 0; $i < $iCount; $i ++)
			{
				$iNews    = $objDb->getField($i, "id");
				$sTitle   = $objDb->getField($i, "title");
				$sSefUrl  = $objDb->getField($i, "sef_url");
				$sDate    = $objDb->getField($i, "date");
				$sDetails = $objDb->getField($i, "details");
				$sPicture = $objDb->getField($i, "picture");

				if ($sPicture == "" || !@file_exists(NEWS_IMG_DIR.'thumbs/'.$sPicture))
					$sPicture = "default.jpg";
?>
            <div class="hr"></div>

            <table border="0" cellspacing="0" cellpadding="0" width="100%">
              <tr valign="top">
                <td width="80"><a href="<?= getNewsUrl($iNews, $sSefUrl) ?>" class="pic"><img src="<?= (NEWS_IMG_DIR.'thumbs/'.$sPicture) ?>" alt="" title="" /></a></td>
                <td width="12"></td>

                <td>
                  <a href="<?= getNewsUrl($iNews, $sSefUrl) ?>"><?= $sTitle ?></a>
				  <?= formatDate($sDate, "F d, Y") ?>
                </td>
              </tr>
            </table>

            <p><?= substr(strip_tags($sDetails), 0, 100) ?>...</p>
<?
			}
?>
          </div>
<?
		}
	}

	else
	{
?>
          <div id="Login">
            <h4 class="small">User Area</h4>
<?
		$sSQL = "SELECT type_id, picture FROM tbl_admins WHERE id='{$_SESSION['AdminId']}'";
		$objDb->query($sSQL);

		$iType    = $objDb->getField(0, "type_id");
		$sPicture = $objDb->getField(0, "picture");


		if ($sPicture == "" || !@file_exists(ADMINS_IMG_DIR.'thumbs/'.$sPicture))
			$sPicture = "default.jpg";
?>
		    <div class="member">
		      <div class="pic"><img src="<?= (ADMINS_IMG_DIR.'thumbs/'.$sPicture) ?>" alt="" title="" /></div>

		      <br />
		      <h5><?= $_SESSION['AdminName'] ?></h5>
		      [
<?
		if ($iType == 1)
		{
?>
		        <a href="mscp/" target="_blank">Admin Panel</a> |
<?
		}
?>
		        <a href="logout.php">Logout</a>
		      ]<br />
		    </div>
          </div>

          <br />

          <div id="Kpis">
            <h4 class="small">KPI's</h4>
            <div class="hr"></div>

            <div id="SchoolsChartArea">Building Chart ...</div>

            <div class="hr"></div>

            <div id="PackagesChartArea">Building Chart ...</div>

            <div class="hr"></div>

            <div id="DeadlinesChartArea">Building Chart ...</div>

		    <script type="text/javascript">
		    <!--
				FusionCharts.setCurrentRenderer('javascript');


				$(document).ready(function( )
				{
					var objSchoolsChart = new FusionCharts("scripts/FusionCharts/charts/Pie2D.swf", "SchoolsChart", "100%", "250", "0", "1");

					$.postq("Graphs", "ajax/get-schools-xml.php",
					{ },

					function (sResponse)
					{
						objSchoolsChart.setXMLData(sResponse);
						objSchoolsChart.render("SchoolsChartArea");
					},

					"text");



					var objPackagesChart = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", "PackagesChart", "100%", "250", "0", "1");

					$.postq("Graphs", "ajax/get-packages-xml.php",
					{ },

					function (sResponse)
					{
						objPackagesChart.setXMLData(sResponse);
						objPackagesChart.render("PackagesChartArea");
					},

					"text");



					var objDeadlinesChart = new FusionCharts("scripts/FusionCharts/charts/Column2D.swf", "DeadlinesChart", "100%", "250", "0", "1");

					$.postq("Graphs", "ajax/get-deadlines-xml.php",
					{ },

					function (sResponse)
					{
						objDeadlinesChart.setXMLData(sResponse);
						objDeadlinesChart.render("DeadlinesChartArea");
					},

					"text");
				});
		    -->
		    </script>
          </div>
<?
	}
?>