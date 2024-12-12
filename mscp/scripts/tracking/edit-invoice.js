
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
	$("#txtDate").datepicker(
	{
		showOn          : "both",
		buttonImage     : "images/icons/calendar.gif",
		buttonImageOnly : true,
		dateFormat      : "yy-mm-dd"
	});


	$("#txtInvoiceNo").blur(function( )
	{
		if ($("#txtInvoiceNo").val( ) == "")
			return;


		$.post("ajax/tracking/check-invoice.php",
			{ InvoiceId:$("#InvoiceId").val( ), InvoiceNo:$("#txtInvoiceNo").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided Invoice No already exists in System.");

					$("#DuplicateInvoice").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateInvoice").val("0");
				}
			},

			"text");
	});


	$("#ddContract").change(function( )
	{
		$.post("ajax/get-contract-schools.php",
			{ Contract:$("#ddContract").val( ) },

			function (sResponse)
			{
				$("#ddSchool").html(sResponse);
			},

			"text");
	});


	$("#ddSchool").change(function( )
	{
		if ($("#ddSchool").val( ) == "")
			return;


		$.post("ajax/tracking/get-invoice-inspections.php",
			{ School:$("#ddSchool").val( ) },

			function (sResponse)
			{
				$("#Inspections").html(sResponse);
			},
			"text");
	});



	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("ddContract", "B", "Please select the Contract."))
			return false;

		if (!objFV.validate("ddSchool", "B", "Please select the School."))
			return false;

		if (!objFV.validate("txtInvoiceNo", "B", "Please enter the Invoice No."))
			return false;
		
		if (!objFV.validate("txtTitle", "B", "Please enter the Invoice Title."))
			return false;

		if (!objFV.validate("txtDate", "B", "Please enter the Date."))
			return false;


		if (objFV.value("DuplicateInvoice") == "1")
		{
			showMessage("#RecordMsg", "info", "The provided Invoice No already exists in System.");

			objFV.focus("ddContract");
			objFV.select("ddContract");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});