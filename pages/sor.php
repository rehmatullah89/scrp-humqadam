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
	
	if ($_SESSION["AdminId"] == 0)
	{
		$_SESSION['RequestUrl'] = $_SERVER['REQUEST_URI'];
		
		redirect(SITE_URL);
	}
?>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry&sensor=false"></script>

  <section id="Map" style="border:solid 1px #888888;">
    <div id="GoogleMap" style="height:602px;"></div>
  </section>


  <script type="text/javascript">
  <!--
  	var objMap;
  	var objPopups = [];

	$(document).ready(function( )
	{
		var objLatLng  = new google.maps.LatLng(30.376454,73.550889);
		var iZoomLevel = 6;

		var objSettings =
		{
			backgroundColor    : '#f3f1ed',
			zoom               : iZoomLevel,
			center             : objLatLng,
			mapTypeId          : google.maps.MapTypeId.ROADMAP,
			disableDefaultUI   : false,
			zoomControl        : true,
			mapTypeControl     : false,
			scaleControl       : false,
			streetViewControl  : false,
			rotateControl      : true,
			zoomControlOptions : { position: google.maps.ControlPosition.LEFT_TOP }
		};


		objMap = new google.maps.Map(document.getElementById("GoogleMap"), objSettings);


		google.maps.event.addListenerOnce(objMap, 'idle', function( )
		{
<?
	$iDistricts    = @explode(",", $_SESSION['AdminDistricts']);
	$sDistrictsSQL = "";

	foreach ($iDistricts as $iDistrict)
	{
		if ($sDistrictsSQL != "")
			$sDistrictsSQL .= " OR ";

		$sDistrictsSQL .= " FIND_IN_SET('$iDistrict', districts) ";
	}

	if ($sDistrictsSQL != "")
		$sDistrictsSQL = " AND ($sDistrictsSQL) ";


	$sEngineerTypes   = getList("tbl_admin_types", "id", "title");
	$sActiveEngineers = "";


	$sSQL = "SELECT id, type_id, name, mobile, location_time, location_address, latitude, longitude, picture
	         FROM tbl_admins
	         WHERE status='A' AND type_id='6' AND TIMESTAMPDIFF(MINUTE, location_time, NOW( )) <= '1440' $sDistrictsSQL
	         ORDER BY name";
	$objDb->query($sSQL);


	$iActiveEngineers = $objDb->getCount( );

	for ($i = 0; $i < $iActiveEngineers; $i ++)
	{
		$iEngineer  = $objDb->getField($i, "id");
		$iType      = $objDb->getField($i, "type_id");
		$sEngineer  = $objDb->getField($i, "name");
		$sMobile    = $objDb->getField($i, "mobile");
		$sPicture   = $objDb->getField($i, "picture");
		$sDateTime  = $objDb->getField($i, "location_time");
		$sAddress   = $objDb->getField($i, "location_address");
		$sLatitude  = $objDb->getField($i, "latitude");
		$sLongitude = $objDb->getField($i, "longitude");


		if ($sPicture == "" || !@file_exists(ADMINS_IMG_DIR.'thumbs/'.$sPicture))
			$sPicture = "default.jpg";


		$sActiveEngineers .= (($i + 1).". {$sEngineer}<br />");
		$sLocationDateTime   = formatDate($sDateTime, "d-M-Y h:i A");
		$sPicture            = (ADMINS_IMG_DIR.'thumbs/'.$sPicture);
		$sLastSurvey         = "";


		$sSQL = "SELECT id, school_id, created_at FROM tbl_sors WHERE admin_id='$iEngineer' ORDER BY id DESC LIMIT 1";
		$objDb2->query($sSQL);

		if ($objDb2->getCount( ) == 1)
		{
			$iSurvey   = $objDb2->getField(0, "id");
			$iSchool   = $objDb2->getField(0, "school_id");
			$sDateTime = $objDb2->getField(0, "created_at");


			$sSchool     = getDbValue("CONCAT(code, '<br />', name)", "tbl_schools", "id='$iSchool'");
			$sLastSurvey = ("<br /><b>Last SOR:</b><br />EMIS Code: {$sSchool}<br /><a href='sor-details.php?Id={$iSurvey}' class='sor'>".formatDate($sDateTime, "d-M-Y h:i A")."</a><br /><br />");
		}


		$sEngineerInfo  = ("<table border='0' cellspacing='0' cellpadding='0' width='400'>");
		$sEngineerInfo .= ("<tr valign='top'>");
		$sEngineerInfo .= ("<td width='120'><div style='border:solid 1px #cccccc; padding:1px;'><img src='{$sPicture}' width='100%' alt='' title='' /></div></td>");
		$sEngineerInfo .= ("<td width='15'></td>");
		$sEngineerInfo .= ("<td><b style='font-weight:bold;'>{$sEngineer}</b><br />{$sEngineerTypes[$iType]}<br /><img src='images/icons/mobile.png' width='16' height='16' alt='' title='' align='left' style='margin-right:8px;' /> {$sMobile}<br /><br />{$sLastSurvey}<b>Last Location:</b> <small>({$sLocationDateTime})</small><br />".utf8_encode(str_replace(array("\r\n", "\n"), "<br />", htmlentities($sAddress)))."</td>");
		$sEngineerInfo .= ("</tr>");
		$sEngineerInfo .= ("</table>");
?>
			var objLatLong<?= $i ?> = new google.maps.LatLng(<?= $sLatitude ?>, <?= $sLongitude ?>);
			var objMarker<?= $i ?>  = new google.maps.Marker({ position:objLatLong<?= $i ?>, map:objMap, icon:'images/map/inspector.png', title:'<?= $sEngineer ?>', animation:google.maps.Animation.DROP });
			var objInfoWin<?= $i ?> = new google.maps.InfoWindow({ content:"<?= $sEngineerInfo ?>" });

			objMarker<?= $i ?>.setAnimation(google.maps.Animation.BOUNCE);

			google.maps.event.addListener(objMarker<?= $i ?>, 'click', function( )
			{
				for (var i = 0; i < objPopups.length; i ++)
					objPopups[i].close( );


				objInfoWin<?= $i ?>.open(objMap, objMarker<?= $i ?>);


				//$("a.sor").colorbox({ width:"900px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
			});


			objPopups.push(objInfoWin<?= $i ?>);
<?
	}


	if ($iActiveEngineers == 1)
	{
?>
			var objLatLong = new google.maps.LatLng(<?= $sLatitude ?>, <?= $sLongitude ?>);

			objMap.setCenter(objLatLong);
			objMap.setZoom(10);
<?
	}
?>
		});
	});
  -->
  </script>
