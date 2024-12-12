
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
	$("#frmRecord input[type=radio]").click(function( )
	{
		var iQuestion = $(this).attr("name").replace("rbQuestion", "");
		var sValue    = $(this).val( );
		
		
		$("#frmRecord .question").each(function( )
		{
			var sLink = $(this).attr("link");
			var sType = $(this).attr("type");
			
			
			if (sLink != "")
			{
				var sQuestion = sLink.split("|");
				
				if (sQuestion[0] == iQuestion)
				{
					if (sQuestion[1] != sValue)
					{
						if (sType == "SS" || sType == "YN")
							$(this).find("input[type=radio]").attr("checked", false);
						
						else if (sType == "MS")
							$(this).find("input[type=checkbox]").attr("checked", false);
						
						else if (sType == "SL")
							$(this).find("input").val("");
						
						else if (sType == "ML")
							$(this).find("textarea").val("");
						
						$(this).find("input.other").val("");				
						$(this).find("input").attr("disabled", true);
						$(this).find("textarea").attr("disabled", true);
					}
					
					else
					{
						$(this).find("input").attr("disabled", false);
						$(this).find("textarea").attr("disabled", false);					
					}
					
					
					
					var iSubQuestion = $(this).attr("id").replace("Question", "");
					var sSubValue    = $("input[name='rbQuestion" + iSubQuestion + "']:checked").val( );
					
					$("#frmRecord .question").each(function( )
					{
						var sLink = $(this).attr("link");
						var sType = $(this).attr("type");
						
						
						if (sLink != "")
						{
							var sQuestion = sLink.split("|");
							
							if (sQuestion[0] == iSubQuestion)
							{						
								if (sQuestion[1] != sSubValue)
								{
									if (sType == "SS" || sType == "YN")
										$(this).find("input[type=radio]").attr("checked", false);
									
									else if (sType == "MS")
										$(this).find("input[type=checkbox]").attr("checked", false);
									
									else if (sType == "SL")
										$(this).find("input").val("");
									
									else if (sType == "ML")
										$(this).find("textarea").val("");
									
									
									$(this).find("input.other").val("");
									$(this).find("input").attr("disabled", true);
									$(this).find("textarea").attr("disabled", true);
								}
								
								else
								{
									$(this).find("input").attr("disabled", false);
									$(this).find("textarea").attr("disabled", false);					
								}
							}
						}
					});
				}
			}
		});
	});
	
	
	$("#frmRecord .question").each(function( )
	{
		var iQuestion = $(this).attr("id").replace("Question", "");
		var sType     = $(this).attr("type");
		
		if (sType == "YN")
		{
			if ($("#frmRecord #rbQuestionY" + iQuestion).prop("checked") == true)
				$("#frmRecord #rbQuestionY" + iQuestion).trigger("click");
			
			else if ($("#frmRecord #rbQuestionN" + iQuestion).prop("checked") == true)
				$("#frmRecord #rbQuestionN" + iQuestion).trigger("click");
		}
	});

	
	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

		
		if ($("#SectionId").val( ) == "15")
		{
			if (objFV.value("fileSitePlan") != "")
			{
				if (!checkImage(objFV.value("fileSitePlan")))
				{
					showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

					objFV.focus("fileSitePlan");
					objFV.select("fileSitePlan");

					return false;
				}
			}
			
			
			if (objFV.value("fileDrawing") != "")
			{
				if (!checkAutoCad(objFV.value("fileDrawing")))
				{
					showMessage("#RecordMsg", "alert", "Invalid File Format. Please select a valid AutoCAD 2004 File.");

					objFV.focus("fileDrawing");
					objFV.select("fileDrawing");

					return false;
				}
			}


			if (objFV.value("fileSitePlanPdf") != "")
			{
				if (!checkPdfFile(objFV.value("fileSitePlanPdf")))
				{
					showMessage("#RecordMsg", "alert", "Invalid File Format. Please select a valid PDF File.");

					objFV.focus("fileSitePlanPdf");
					objFV.select("fileSitePlanPdf");

					return false;
				}
			}


			if (objFV.value("fileStructure") != "")
			{
				if (!checkAutoCad(objFV.value("fileStructure")))
				{
					showMessage("#RecordMsg", "alert", "Invalid File Format. Please select a valid AutoCAD 2004 File.");

					objFV.focus("fileStructure");
					objFV.select("fileStructure");

					return false;
				}
			}


			if (objFV.value("fileStructurePdf") != "")
			{
				if (!checkPdfFile(objFV.value("fileStructurePdf")))
				{
					showMessage("#RecordMsg", "alert", "Invalid File Format. Please select a valid PDF File.");

					objFV.focus("fileStructurePdf");
					objFV.select("fileStructurePdf");

					return false;
				}
			}			
		}

		
		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );
		
		return true;
	});

	
    $("#txtServingDate, #txtSignDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});
});