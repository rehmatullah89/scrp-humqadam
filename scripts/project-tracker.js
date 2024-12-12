
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

var objTimeline;
var objMap;
var objCluster;
var bLoaded    = false;
var objMarkers = [];
var objPopups  = [];
var objStyles  = [ { url:'images/map/zoom1.png', textColor:'#ffffff', width:53, height:52, textSize:10 },
				   { url:'images/map/zoom2.png', textColor:'#ffffff', width:56, height:55, textSize:11 },
				   { url:'images/map/zoom3.png', textColor:'#ffffff', width:66, height:65, textSize:12 },
				   { url:'images/map/zoom4.png', textColor:'#ffffff', width:78, height:77, textSize:13 },
				   { url:'images/map/zoom5.png', textColor:'#ffffff', width:90, height:89, textSize:14 } ];


var client = new ZeroClipboard(document.getElementById("BtnCopyLink"));

client.on("ready", function( readyEvent )
{
	client.on("aftercopy", function(event)
	{
//		alert(event.data["text/plain"]);
	});
});

				   
$(document).ready(function( )
{
	$("#Package").select2(
	{
		allowClear               :  true,
		placeholder              :  "Select a Package",
		minimumResultsForSearch  :  Infinity
	});


	$("#Province").select2(
	{
		allowClear               :  true,
		placeholder              :  "Select a Province",
		minimumResultsForSearch  :  Infinity
	});


	$("#District").select2(
	{
		allowClear   :  true,
		placeholder  :  "Select a District"
	});


	$("#frmSearch #Province").change(function( )
	{
		$("#District").select2("close").val(null).trigger("change");


		$.post("ajax/get-districts.php",
			{ Province:$(this).val( ) },

			function (sResponse)
			{
				$("#District").html(sResponse);
				//$("#District").select2("open");
			},

			"text");
	});


	$("#frmSearch #SearchButton .fa").click(function( )
	{
		$("#frmSearch").trigger("submit");
	});
	
	
	$("#frmSearch #Status, #frmSearch #Keywords, #frmSearch #Package, #frmSearch #Province, #frmSearch #District").change(function( )
	{
		var sLink = $("#ShareLink").attr("rel");
		
		sLink = (sLink + "?Status=" + $("#frmSearch #Status").val( ));
		sLink = (sLink + "&Keywords=" + $("#frmSearch #Keywords").val( ));
		sLink = (sLink + "&Package=" + $("#frmSearch #Package").val( ));
		sLink = (sLink + "&Province=" + $("#frmSearch #Province").val( ));
		sLink = (sLink + "&District=" + $("#frmSearch #District").val( ));

		$("#ShareLink").val(sLink);
	});
	
	
	$("#frmSearch #Keywords, #frmSearch #Package, #frmSearch #Province, #frmSearch #District").blur(function( )
	{
		var sLink = $("#ShareLink").attr("rel");
		
		sLink = (sLink + "?Status=" + $("#frmSearch #Status").val( ));
		sLink = (sLink + "&Keywords=" + $("#frmSearch #Keywords").val( ));
		sLink = (sLink + "&Package=" + $("#frmSearch #Package").val( ));
		sLink = (sLink + "&Province=" + $("#frmSearch #Province").val( ));
		sLink = (sLink + "&District=" + $("#frmSearch #District").val( ));

		$("#ShareLink").val(sLink);
	});	

	
	$("#frmSearch").submit(function( )
	{
		if ($("#frmSearch #Status").val( ) == "" && $("#frmSearch #Keywords").val( ) == "" && $("#frmSearch #Package").val( ) == "" && $("#frmSearch #Province").val( ) == "")
		{
			$("#frmSearch #Keywords").focus( );

			return false;
		}

		
		$("#BtnCopyLink").removeClass("hidden");
		

		for (var i = 0; i < objPopups.length; i ++)
			objPopups[i].close( );


		if ($("#Map #Status").length == 1)
		{
			$("#Map #Overlay").hide("fade");
			$("#Map #Status").hide("fade");
		}

		if ($("#Map #Details").length == 1)
			$("#Map #Details").hide("fade");

		if ($("#SchoolTimeline").css("display") == "block")
			$("#SchoolTimeline").hide("blind");


		if ($("#frmSearch #Package").val( ) != "" || $("#frmSearch #Province").val( ) != "")
		{
			var objSchoolsChart = new FusionCharts("scripts/FusionCharts/charts/Pie2D.swf", ("SchoolsChart" + Math.random( )), "100%", "250", "0", "1");

			$.postq("Graphs", "ajax/get-schools-xml.php",
				$("#frmSearch").serialize( ),

				function (sResponse)
				{
					objSchoolsChart.setXMLData(sResponse);
					objSchoolsChart.render("SchoolsChartArea");
				},

				"text");


			var objPackagesChart = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", ("PackagesChart" + Math.random( )), "100%", "250", "0", "1");

			$.postq("Graphs", "ajax/get-packages-xml.php",
				$("#frmSearch").serialize( ),

				function (sResponse)
				{
					objPackagesChart.setXMLData(sResponse);
					objPackagesChart.render("PackagesChartArea");
				},

				"text");



			var objDeadlinesChart = new FusionCharts("scripts/FusionCharts/charts/Column2D.swf", ("DeadlinesChart" + Math.random( )), "100%", "250", "0", "1");

			$.postq("Graphs", "ajax/get-deadlines-xml.php",
				$("#frmSearch").serialize( ),

				function (sResponse)
				{
					objDeadlinesChart.setXMLData(sResponse);
					objDeadlinesChart.render("DeadlinesChartArea");
				},

				"text");



			var objChart1 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", ("KpiChart1" + Math.random( )), "100%", "260", "0", "1");

			$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
			{
				Type      :  "ProjectActivity",
				Package   :  $("#frmSearch #Package").val( ),
				Province  :  $("#frmSearch #Province").val( ),
				District  :  $("#frmSearch #District").val( )
			},

			function (sResponse)
			{
				objChart1.setXMLData(sResponse);
				objChart1.render("KpisChartArea1");
			},

			"text");



			var objChart2 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", ("KpiChart2" + Math.random( )), "100%", "260", "0", "1");

			$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
			{
				Type      :  "Contracts",
				Package   :  $("#frmSearch #Package").val( ),
				Province  :  $("#frmSearch #Province").val( ),
				District  :  $("#frmSearch #District").val( )
			},

			function (sResponse)
			{
				objChart2.setXMLData(sResponse);
				objChart2.render("KpisChartArea2");
			},

			"text");



			var objChart3 = new FusionCharts("scripts/FusionCharts/charts/Doughnut2D.swf", ("KpiChart3" + Math.random( )), "100%", "260", "0", "1");

			$.postq("Graphs", "ajax/get-kpi-chart-xml.php",
			{
				Type      :  "Dropped",
				Package   :  $("#frmSearch #Package").val( ),
				Province  :  $("#frmSearch #Province").val( ),
				District  :  $("#frmSearch #District").val( )
			},

			function (sResponse)
			{
				objChart3.setXMLData(sResponse);
				objChart3.render("KpisChartArea3");
			},

			"text");
		}


		if ($("#frmSearch #Status").val( ) != "" || $("#frmSearch #Package").val( ) != "" || $("#frmSearch #Province").val( ) != "")
		{
			$.post("ajax/get-statistics.php",
				$("#frmSearch").serialize( ),

				function (sResponse)
				{
					var sParams = sResponse.split("|-|");

					$("#Statistics").html(sParams[0]);
					$("#GeoTimelineOverall").html(sParams[1]);
				},

				"text");
		}


		setTimeout(function( )
		{
			$("#Map #Status").remove( );
			$("#Map #Details").remove( );

			if (objTimeline)
				objTimeline.remove( );


			$.post("ajax/get-school-markers.php",
				$("#frmSearch").serialize( ),

				function (sResponse)
				{
					eval(sResponse);
				},

				"text");
		}, 200);
	});




	var objLatLng  = new google.maps.LatLng(32.3894007,69.3522957);
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
		if (bLoaded == false)
		{
			setTimeout(function( )
			{
				$.post("ajax/get-school-markers.php",
					$("#frmSearch").serialize( ),

					function (sResponse)
					{
						eval(sResponse);
					},

					"text");
			}, 3000);

			bLoaded = true;
		}
	});


	$(document).on("click", "#Map #Status h1 img", function( )
	{
		$("#Map #Overlay").hide("fade");
		$("#Map #Status").hide("fade");
//			$("#Statistics").hide("blind")

		if ($("#Map #Details").length == 1)
		{
			$("#Map #Details").hide("fade");

			if ($("#Map #Details #Accordion .accordian").length > 0)
				$("#Map #Details #Accordion .accordian").accordion("destroy");
		}

		if ($("#SchoolTimeline").css("display") == "block")
			$("#SchoolTimeline").hide("blind");


		setTimeout(function( )
		{
			$("#Map #Status").remove( );

			if ($("#Map #Details").length == 1)
				$("#Map #Details").remove( );

			$("#Timeline").html("");

			if (objTimeline)
				objTimeline.remove( );
		}, 300);
	});


	$(document).on("click", "#Map #Details h1 img", function( )
	{
		$("#Map #Details").hide("fade");

		if ($("#Map #Details #Accordion .accordian").length > 0)
			$("#Map #Details #Accordion .accordian").accordion("destroy");


		setTimeout(function( )
		{
			$("#Map #Details").remove( );
		}, 300);
	});


	$(document).on("click", "#Map #Status ul li.subStages", function( )
	{
		var iSchool = $("#Map #Status h1").attr("rel");
		var iStage  = $(this).attr("rel");
		var sStage  = $(this).text( );


		if ($("#Map #Details").length == 1)
		{
			$("#Map #Details #Accordion .accordian").accordion("destroy");
			$("#Map #Details #Inner").html('<h1></h1><center><img src="images/loading.gif" width="400" height="50" alt="" title="" vspace="130" /></center>');
		}

		else
			$("#Map").append('<div id="Details"><div id="Inner"><h1></h1><center><img src="images/loading.gif" width="400" height="50" alt="" title="" vspace="130" /></center></div></div>');


		$("#Map #Details #Inner h1").html('<img src="images/icons/close.png" alt="Close" title="Close" />' + sStage);


		$.post("ajax/get-school-stage-status.php",
			{ School:iSchool, Stage:iStage },

			function (sResponse)
			{
				$("#Map #Details #Inner center").remove( );
				$("#Map #Details #Inner").append(sResponse);


				$("#Map #Details #Accordion .accordian").accordion(
				{
					icons        :  { header:"stageClosed", activeHeader:"stageOpen", headerSelected:"stageOpen" },
					heightStyle  :  "content",
					autoHeight   :  false,
					collapsible  :  true,
					active       :  false,

					beforeActivate  :  function(event, ui)
					{
						if (ui.newHeader.attr("rel") === undefined)
						{

						}

						else
						{
							$("#Map #Details #Accordion .accordian").each(function( )
							{
								if (ui.newHeader.attr("rel") != $(this).attr("rel") && $(this).find("h3").hasClass("ui-state-active"))
									$("#Map #Details #Accordion #Stage" + $(this).attr("rel") + " > h3").trigger("click");
							});
						}
					}
				});



				$("#Map #Details #Accordion div.stages").each(function( )
				{
					if ($(this).height( ) > 200)
					{
						$(this).find("div").mCustomScrollbar(
						{
							set_width          :  false,
							set_height         :  "200px",
							horizontalScroll   :  false,
							scrollInertia      :  550,
							scrollEasing       :  "easeOutCirc",
							mouseWheel         :  "pixels",
							mouseWheelPixels   :  50,
							autoDraggerLength  :  true,
							advanced           :  { updateOnContentResize : true },
							scrollButtons      :  {  enable        :  false,
													 scrollType    :  "continuous",
													 scrollSpeed   :  20,
													 scrollAmount  :  40
												  }
						});
					}
				});


				if ($("#Map #Details #Accordion").height( ) > 333)
				{
					$("#Map #Details #Accordion").mCustomScrollbar(
					{
						set_width          :  false,
						set_height         :  "333px",
						horizontalScroll   :  false,
						scrollInertia      :  550,
						scrollEasing       :  "easeOutCirc",
						mouseWheel         :  "pixels",
						mouseWheelPixels   :  50,
						autoDraggerLength  :  true,
						advanced           :  { updateOnContentResize : true },
						scrollButtons      :  {  enable        :  false,
												 scrollType    :  "continuous",
												 scrollSpeed   :  20,
												 scrollAmount  :  40
											  }
					});
				}
			},

			"text");
	});


	$(document).on("click", "#Map #Status #Actions a.timeline", function( )
	{
		var iSchool    = parseInt($(this).attr("id"));
		var iDocuments = parseInt($(this).attr("rel"));

		if ($("#SchoolTimeline").css("display") != "block")
			$("#SchoolTimeline").show("blind");

		if (objTimeline)
			objTimeline.remove( );


		if (iDocuments > 0)
		{
			$("#SchoolTimeline #Timeline").html("");

			objTimeline = createStoryJS(
			{
				type               :  'timeline',
				width              :  '100%',
				height             :  '215',
				source             :  ('ajax/get-school-timeline.php?School=' + iSchool + "&Time=" + new Date().getTime()),
				embed_id           :  'Timeline',
				start_at_end       :  true,
				start_at_slide     :  0,
				hash_bookmark      :  false,
				start_zoom_adjust  :  0
			});
		}

		else
			$("#SchoolTimeline #Timeline").html("<br /><br /><center>No Data Available</center><br /><br />");


		return false;
	});


	$(document).on("click", "#SchoolTimeline div.vco-navigation div.thumbnail img", function( )
	{
		if (!$(this).parent( ).parent( ).parent( ).parent( ).hasClass("active"))
			return;


		$("#cbox2School").html("");


		var sUrl   = $(this).attr("src").replace("thumbs/", "");
		var sId    = $(this).parent( ).parent( ).parent( ).parent( ).attr("id");
		var iChild = 0;
		var sHtml  = "";


		$("#SchoolTimeline div.marker").each(function(iIndex)
		{
			if ($(this).attr("id") == sId)
				iChild = iIndex;
		});

		$("#SchoolTimeline div.slider-item").each(function(iIndex)
		{
			if (iIndex == iChild)
				sHtml = $(this).find(".credit").html( );
		});

		if (sHtml == "" || sHtml == null)
			setTimeout(function( ) { $(this).trigger("click"); }, 100);


		$objGallery = $("a.documents" + (iChild - 1)).colorbox2(
		{
			rel           :  ("documents" + (iChild - 1)),
			minWidth      :  "1000px",
			minHeight     :  "600px",
			innerWidth    :  "1200px",
			innerHeight   :  "800px",
			maxWidth      :  "90%",
			maxHeight     :  "90%",
			opacity       :  "0.50",
			overlayClose  :  true,
			title         :  '',

			onComplete    :  function( )
			{
				$("#cbox2LoadedContent").css("margin-bottom", "0px");
				$("#cbox2School").css("float", "none");
				$("#cbox2School").html(sHtml);

				var sOptions = $("#cbox2School #Options").html( );

				$("#cbox2School #Options").remove( );
				$("#cbox2Title").html(sOptions);
			}
		});

		$objGallery.eq(0).click( );
	});



	$("#Map #BtnActive").click(function( )
	{
		$("#Map #Buttons .button").removeClass("selected");
		$("#Map #BtnActive").addClass("selected");

		$("#frmSearch #Status").val("Active");
		$("#frmSearch").trigger("submit");
	});


	$("#Map #BtnInActive").click(function( )
	{
		$("#Map #Buttons .button").removeClass("selected");
		$("#Map #BtnInActive").addClass("selected");

		$("#frmSearch #Status").val("InActive");
		$("#frmSearch").trigger("submit");
	});


	$("#Map #BtnDelayed").click(function( )
	{
		$("#Map #Buttons .button").removeClass("selected");
		$("#Map #BtnDelayed").addClass("selected");

		$("#frmSearch #Status").val("Delayed");
		$("#frmSearch").trigger("submit");
	});


	$("#Map #BtnOnTime").click(function( )
	{
		$("#Map #Buttons .button").removeClass("selected");
		$("#Map #BtnOnTime").addClass("selected");

		$("#frmSearch #Status").val("OnTime");
		$("#frmSearch").trigger("submit");
	});


	$("#Map #BtnAll").click(function( )
	{
		$("#Map #Buttons .button").removeClass("selected");
		$("#Map #BtnAll").addClass("selected");

		$("#frmSearch #Status").val("All");
		$("#frmSearch").trigger("submit");
	});


	$(document).on("click", ".stageStatus a", function( )
	{
	 	$(".tooltip").tooltipster('hide');
	});


	$(document).on("click", "#CloseTimeline", function( )
	{
	 	hideTimeline( );
	});


	$(document).on("click", "#Statistics #Progress", function( )
	{
		document.location = ($("base").attr("href") + "export-tracker.php?" + $("#Statistics #Progress").attr("params"));
	});
	
	
	$(document).on("click", "#Statistics #Planned", function( )
	{
		document.location = ($("base").attr("href") + "export-planned.php?" + $("#Statistics #Planned").attr("params"));
	});	
	
	
	$(document).on("click", "#Map #ActiveCount, #Map #CompletedCount, #Map #AdoptedCount", function( )
	{
		document.location = ($(this).attr("rel") + "?" + $("#frmSearch").serialize( ));
	});	
});



function hideTimeline( )
{
	if ($("#SchoolTimeline").css("display") == "block")
		$("#SchoolTimeline").hide("blind");

	if (objTimeline)
		objTimeline.remove( );
}


function showSchoolStatus(iSchool)
{
	$("#Map #Overlay").show("fade");


	if ($("#Map #Status").length == 1)
	{
		$("#Map #Status #Accordion").accordion("destroy");
		$("#Map #Status #Inner").html('<img src="images/loading.gif" width="400" height="50" alt="" title="" vspace="174" />');
	}

	else
		$("#Map").append('<div id="Status"><div id="Inner"><img src="images/loading.gif" width="400" height="50" alt="" title="" vspace="174" /></div></div>');



	$.post("ajax/get-school-status.php",
		{ School:iSchool },

		function (sResponse)
		{
			var sHtml = sResponse.split("|-|");

			$("#Map #Status #Inner").html(sHtml[0]);
			$("#Statistics").html(sHtml[1]);
			$("#GeoTimelineOverall").html(sHtml[2]);
//				$("#Statistics").show("blind");


			$(".tooltip").tooltipster(
			{
				arrow          :  true,
				contentAsHTML  :  true,
				interactive    :  true,
				theme          :  'tooltipster-light',

				functionReady  : function( )
				{
					$("a.inspection").colorbox({ width:"900px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
				}
			});



//			$("#Map #Status #Actions a.timeline").trigger("click");


			$("#Map #Status #Accordion").accordion(
			{
				icons        :  { header:"stageClosed", activeHeader:"stageOpen", headerSelected:"stageOpen" },
				heightStyle  :  "content",
				autoHeight   :  false,
				collapsible  :  true,
				active       :  'none'
			});


			$("#Map #Status #Accordion div.stages").each(function( )
			{
				if ($(this).height( ) > 115)
				{
					$(this).find("div").mCustomScrollbar(
					{
						set_width          :  false,
						set_height         :  "115px",
						horizontalScroll   :  false,
						scrollInertia      :  550,
						scrollEasing       :  "easeOutCirc",
						mouseWheel         :  "pixels",
						mouseWheelPixels   :  50,
						autoDraggerLength  :  true,
						advanced           :  { updateOnContentResize : true },
						scrollButtons      :  {  enable        :  false,
												 scrollType    :  "continuous",
												 scrollSpeed   :  20,
												 scrollAmount  :  40
											  }
					});
				}
			});
		},

		"text");
}