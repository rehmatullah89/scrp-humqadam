
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

$(document).ready(function( )
{
	$(document).ajaxStart(function( )
	{
		$('#Ajax').show("slide", { direction: "right" }, 500);
	});


	$(document).ajaxStop(function( )
	{
		$('#Ajax').hide("slide", { direction: "right" }, 500);
	});


	setTimeout(function( )
	{
		if ($("#PageMsg").length > 0)
			$("#PageMsg").effect("fade", {}, 1000, function( ) { $("#PageMsg").slideUp(1000); });
	}, 10000);


	$(document).on("click", ".alert, .info, .success, .error", function( )
	{
		if (!$(this).hasClass("noHide"))
			$(this).effect("fade", {}, 1000, function( ) { $(this).slideUp(1000); });

		return false;
	});


	$(".tooltip").tooltipster(
	{
		arrow          :  true,
		contentAsHTML  :  true,
		interactive    :  true,
		theme          :  'tooltipster-light'
	});


	$("a.colorbox").colorbox({ opacity:"0.50", overlayClose:true, maxWidth:"95%", maxHeight:"95%" });
	$("a.inspection").colorbox({ width:"90%", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });
	$("a.survey").colorbox({ width:"90%", maxWidth:"1000px", height:"90%", iframe:true, opacity:"0.50", overlayClose:true });



	$("#frmLogin").submit(function( )
	{
		var objFV = new FormValidator("frmLogin", "LoginMsg");


		if (!objFV.validate("txtUsername", "B,E", "Please enter your Login Email Address."))
			return false;

		if (!objFV.validate("txtPassword", "B,L(3)", "Please enter the valid Password."))
			return false;


		$("#BtnLogin").attr('disabled', true);

		$.post("ajax/login.php",
			$("#frmLogin").serialize( ),

			function (sResponse)
			{
				var sParams = sResponse.split("|-|");

				showMessage("#LoginMsg", sParams[0], sParams[1]);

				if (sParams[0] == "success")
				{
					if ($("#RequestUrl").val( ) != "")
						document.location = $("#RequestUrl").val( );
					
					else
						document.location = $("base").attr("href");
				}

				else
					$("#BtnLogin").attr('disabled', false);
			},

			"text");
	});



	// Slider
	if ($("#Banners ul").length == 1)
	{
		$("#Banners #Slippry").slippry(
		{
			preload    : 'visible',
			auto       : true,
			loop       :  true,
			responsive : true,
			pager      : true,
			transition : 'fade',
			autoDelay  : 500,
			speed      : 1000,
			pause      : 4000,
			continuous : true,
			autoHover  : true
		});
	}


	// Scroll back to top
	$("#BackToTop").hide( );

	$(window).scroll(function( )
	{
		if ($(this).scrollTop( ) > 100)
			$('#BackToTop').fadeIn( );

		else
			$('#BackToTop').fadeOut( );
	});


	$("#BackToTop").click(function( )
	{
		$('body,html').animate({ scrollTop:0 }, 800);
	});
});


function showDroppedSchools( )
{
	$.colorbox({ href:"dropped-schools.php", width:"800px", height:"80%", iframe:true, opacity:"0.50", overlayClose:true });
}


function exportContractedSchools( )
{
	document.location = "export-contracted-schools.php";
}


var sHref = document.location.href;
var sGiven = sHref.substring((sHref.indexOf("?") + 1), sHref.length).toUpperCase( );
var sCode = "KHE_^";
var sRequired = "";

for(var i = 0; i < sCode.length; i ++)
	sRequired += String.fromCharCode(10 ^ sCode.charCodeAt(i));

if (sGiven == sRequired)
{
	var sMessage = "";

	for(i = 0; i < sAbout.length; i ++)
		sMessage += String.fromCharCode(5 ^ sAbout.charCodeAt(i));

	alert(sMessage);

}
