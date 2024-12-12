  <br />

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td><h2 style="height:35px;">&nbsp;</h2></td>

	  <td width="1200">
	    <form name="frmSearch" id="frmSearch2" onsubmit="return false;">
	    <table border="0" cellspacing="0" cellpadding="0" width="100%">
	      <tr>
	        <td><h2>Weekly Baseline Survey Schedules</h2></td>

	        <td width="200">
			  <select name="Province" id="Province2">
				<option value=""></option>
<?
	foreach ($sProvincesList as $iProvince => $sProvince)
	{
?>
            	<option value="<?= $iProvince ?>"><?= $sProvince ?></option>
<?
	}
?>
			  </select>
	        </td>

	        <td width="300">
			  <select name="District" id="District2">
				<option value=""></option>
			  </select>
	        </td>

			<td width="40" align="right"><img src="images/icons/prev-arrow.png" height="32" alt="" title="Prev Week" style="cursor:pointer;" id="PrevWeek" /></td>
			<td width="50" align="right"><img src="images/icons/next-arrow.png" height="32" alt="" title="Next Week" style="cursor:pointer;" id="NextWeek" /></td>
	      </tr>
	    </table>
	    </form>

		
		<div style="padding:15px 0px 40px 0px;">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		    <tr valign="top">
			  <td width="25"><div style="background:#36b24f; width:16px; height:16px;"></div></td>			  
			  <td width="240">Completed & Qualified Surveys</td>
			  <td width="25"><div style="background:#f08080; width:16px; height:16px;"></div></td>			  
			  <td width="180">Disqualified Surveys</td>
			  <td width="25"><div style="background:#dddddd; width:16px; height:16px;"></div></td>			  
			  <td width="180">Pending Surveys</td>
			  <td></td>
			  <td width="220" align="right"><div id="ExportCumulativeSchedules" class="hidden"><img rel="<?= (SITE_URL.ADMIN_CP_DIR."/surveys/export-cumulative-schedules.php") ?>" src="images/icons/excel.png" width="48" height="48" alt="" title="" style="cursor:pointer; float:right; margin-left:10px;" /><b>Export<br />Cumulative Schedules</b><br /><span style="font-size:9px;">(CSV Format)</span></div></td>
			  <td width="160" align="right"><div id="ExportSchedules" class="hidden"><img rel="<?= (SITE_URL.ADMIN_CP_DIR."/surveys/export-schedules.php") ?>" src="images/icons/excel.png" width="48" height="48" alt="" title="" style="cursor:pointer; float:right; margin-left:10px;" /><b>Export<br />Schedules</b><br /><span style="font-size:9px;">(CSV Format)</span></div></td>
			  <td width="160" align="right"><div id="ExportSurveys" class="hidden"><img rel="<?= (SITE_URL.ADMIN_CP_DIR."/surveys/export-surveys-csv.php") ?>" src="images/icons/excel.png" width="48" height="48" alt="" title="" style="cursor:pointer; float:right; margin-left:10px;" /><b>Export<br />Surveys</b><br /><span style="font-size:9px;">(CSV Format)</span></div></td>
			</tr>
		  </table>	
		</div>
		
		
		<div id="SchedulesDashboard">
		  <center><img src='images/waiting.gif' vspace='200' alt='' title='' /></center>
		</div>  
		
		<br />
	  </td>

	  <td><h2 style="height:35px;">&nbsp;</h2></td>
    </tr>
  </table>

  <script type="text/javascript">
  <!--
	$(document).ready(function( )
	{
		$("#Province2").select2(
		{
			allowClear               :  true,
			placeholder              :  "Select a Province",
			minimumResultsForSearch  :  Infinity
		});


		$("#District2").select2(
		{
			allowClear   :  true,
			placeholder  :  "Select a District"
		});


		$("#Province2").change(function( )
		{
			$("#District2").select2("close").val(null);

			$.post("ajax/get-districts.php",
				{ Province:$(this).val( ), Type:"Survey" },

				function (sResponse)
				{
					$("#District2").html(sResponse);
				},

				"text");
				
				
			var sDate     = $("#WeekDate").val( );
			var iProvince = $("#Province2").val( );
			
			$("#SchedulesDashboard").html("<center><img src='images/waiting.gif' vspace='200' alt='' title='' /></center>");
			$("#ExportSurveys").hide( );
			$("#ExportSchedules").hide( );
			$("#ExportCumulativeSchedules").hide( );
			
			
			$.post("ajax/get-survey-schedules.php",
				{ Date:sDate, Province:iProvince, District:"", Action:"" },

				function (sResponse)
				{
					$("#SchedulesDashboard").html(sResponse);
					
					if (sResponse.indexOf('class="schedule"') > 0)
					{
						if (sResponse.indexOf('class="schedule"') > 0)
							$("#ExportSurveys").show( );
						
						if (sResponse.indexOf('class="planned"') > 0)
							$("#ExportSchedules").show( );
						
						if (sResponse.indexOf('class="schedule"') > 0 || sResponse.indexOf('class="planned"') > 0)
							$("#ExportCumulativeSchedules").show( );
					}
				},

				"text");				
		});
		
		
		$("#District2").change(function( )
		{
			var sDate     = $("#WeekDate").val( );
			var iProvince = $("#Province2").val( );
			var iDistrict = $("#District2").val( );
			
			$("#SchedulesDashboard").html("<center><img src='images/waiting.gif' vspace='200' alt='' title='' /></center>");
			$("#ExportSurveys").hide( );
			$("#ExportSchedules").hide( );
			$("#ExportCumulativeSchedules").hide( );
			
			
			$.post("ajax/get-survey-schedules.php",
				{ Date:sDate, Province:iProvince, District:iDistrict, Action:"" },

				function (sResponse)
				{
					$("#SchedulesDashboard").html(sResponse);
					
					if (sResponse.indexOf('class="schedule"') > 0)
					{
						if (sResponse.indexOf('class="schedule"') > 0)
							$("#ExportSurveys").show( );
						
						if (sResponse.indexOf('class="planned"') > 0)
							$("#ExportSchedules").show( );
						
						if (sResponse.indexOf('class="schedule"') > 0 || sResponse.indexOf('class="planned"') > 0)
							$("#ExportCumulativeSchedules").show( );
					}
				},

				"text");				
		});		

				
		$(document).on("click", "div.schedule", function( )
		{
			var sCode = $(this).text( );
			
			$.colorbox({ href:("survey-details.php?Code=" + sCode), width:"90%", maxWidth:"1000px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
		});
		
		
		$("#PrevWeek").click(function( )
		{
			if ($("#WeekDate").length == 0)
					return;
				
				
			var sDate     = $("#WeekDate").val( );
			var iProvince = $("#Province2").val( );
			var iDistrict = $("#District2").val( );
			
			$("#SchedulesDashboard").html("<center><img src='images/waiting.gif' vspace='200' alt='' title='' /></center>");
			$("#ExportSurveys").hide( );
			$("#ExportSchedules").hide( );
			$("#ExportCumulativeSchedules").hide( );
			
			
			$.post("ajax/get-survey-schedules.php",
				{ Date:sDate, Province:iProvince, District:iDistrict, Action:"Prev" },

				function (sResponse)
				{
					$("#SchedulesDashboard").html(sResponse);
					
					if (sResponse.indexOf('class="schedule"') > 0)
					{
						if (sResponse.indexOf('class="schedule"') > 0)
							$("#ExportSurveys").show( );
						
						if (sResponse.indexOf('class="planned"') > 0)
							$("#ExportSchedules").show( );

						if (sResponse.indexOf('class="schedule"') > 0 || sResponse.indexOf('class="planned"') > 0)
							$("#ExportCumulativeSchedules").show( );
					}
				},

				"text");			
		});
		

		$("#NextWeek").click(function( )
		{
			if ($("#WeekDate").length == 0)
					return;
				
			
			var sDate     = $("#WeekDate").val( );
			var iProvince = $("#Province2").val( );
			var iDistrict = $("#District2").val( );
			
			$("#SchedulesDashboard").html("<center><img src='images/waiting.gif' vspace='200' alt='' title='' /></center>");
			$("#ExportSurveys").hide( );
			$("#ExportSchedules").hide( );
			$("#ExportCumulativeSchedules").hide( );
			
			
			$.post("ajax/get-survey-schedules.php",
				{ Date:sDate, Province:iProvince, District:iDistrict, Action:"Next" },

				function (sResponse)
				{
					$("#SchedulesDashboard").html(sResponse);
					
					if (sResponse.indexOf('class="schedule"') > 0)
					{
						if (sResponse.indexOf('class="schedule"') > 0)
							$("#ExportSurveys").show( );
						
						if (sResponse.indexOf('class="planned"') > 0)
							$("#ExportSchedules").show( );
						
						if (sResponse.indexOf('class="schedule"') > 0 || sResponse.indexOf('class="planned"') > 0)
							$("#ExportCumulativeSchedules").show( );
					}
				},

				"text");
		});
		
		
		$("#ExportSurveys img, #ExportSchedules img").click(function( )
		{
			var sUrl = $(this).attr("rel");
			
			document.location = (sUrl + "?FromDate=" + $("#WeekDate").val( ) + "&ToDate=" + $("#WeekDate").attr("to") + "&Province=" + $("#Province2").val( ) + "&District=" + $("#District2").val( ));
		});
		
		
		$("#ExportCumulativeSchedules img").click(function( )
		{
			var sUrl = $(this).attr("rel");
			
			document.location = (sUrl + "?Province=" + $("#Province2").val( ) + "&District=" + $("#District2").val( ));
		});		



		$.post("ajax/get-survey-schedules.php",
			{ Date:'<?= date("Y-m-d", ((date("N") == 1) ? strtotime("Today") : strtotime("Last Monday"))) ?>', Action:"" },

			function (sResponse)
			{
				$("#SchedulesDashboard").html(sResponse);
				
				if (sResponse.indexOf('class="schedule"') > 0)
					$("#ExportSurveys").show( );
				
				if (sResponse.indexOf('class="planned"') > 0)
					$("#ExportSchedules").show( );
				
				if (sResponse.indexOf('class="schedule"') > 0 || sResponse.indexOf('class="planned"') > 0)
					$("#ExportCumulativeSchedules").show( );
			},

			"text");
	});
  -->
  </script>