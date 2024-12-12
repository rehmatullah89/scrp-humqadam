
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
	$("#txtMobile").mask("03nnnnnnnnn", { placeholder:"" });


	$("input.province").click(function( )
	{
		$("input.province").each(function( )
		{
			var iProvince = $(this).val( );
			var sClass    = ("province" + iProvince);


			if ($(this).prop("checked") == true)
			{
				$(".district").each(function( )
				{
					if ($(this).hasClass(sClass))
						$(this).prop("checked", true);
				});
			}

			else
			{
				$(".district").each(function( )
				{
					if ($(this).hasClass(sClass))
						$(this).prop("checked", false);
				});
			}
		});
	});


	$(document).on("click", "label span a", function( )
	{
		var sData   = $(this).attr("rel");
		var sParams = sData.split("|");

		$("." + sParams[1]).each(function( )
		{
			if (sParams[0] == "Check")
				$(this).prop("checked", true);

			else
				$(this).prop("checked", false);
		});


		setSchoolsSelection( );


		return false;
	});


    setSchoolsSelection( );


    $(document).on("click", ".province, .district", function( )
    {
		setSchoolsSelection( );
	});



	$("#ddType").change(function( )
	{
		if ($(this).val( ) == "")
			return;


		$.post("ajax/management/get-type-rights.php",
			{ Type:$(this).val( ) },

			function (sResponse)
			{
				$("#UserRights").html(sResponse);
			},

			"text");
	});


	$(document).on("click", "#View, #Add, #Edit, #Delete, #All", function( )
	{
		var iCount  = $("#PageCount").val( );
		var bStatus = true;

		for (var i = 0; i < iCount; i ++)
		{
			if ($("#cb" + this.id + i).is(":checked") == false)
			{
				bStatus = false;

				break;
			}
		}


		bStatus = ((bStatus == false) ? true : false);


		for (var i = 0; i < iCount; i ++)
		{
			if (this.id == "All")
			{
				$("#cb" + this.id + i).prop("checked", ((bStatus == true) ? false : true));
				$("#cb" + this.id + i).trigger("click");
			}

			else
				$("#cb" + this.id + i).prop("checked", bStatus);


			if ($("#cbAdd" + i).is(":checked") || $("#cbEdit" + i).is(":checked") || $("#cbDelete" + i).is(":checked"))
				$("#cbView" + i).prop("checked", true);

			if ($("#cbView" + i).is(":checked") && $("#cbAdd" + i).is(":checked") && $("#cbEdit" + i).is(":checked") && $("#cbDelete" + i).is(":checked"))
				$("#cbAll" + i).prop("checked", true);
		}

		return false;
	});


	$(document).on("click", "input[type='checkbox']", function( )
	{
		var iId = this.id.replace("cbView", "").replace("cbAdd", "").replace("cbEdit", "").replace("cbDelete", "").replace("cbAll", "");

		if (this.id == ("cbAll" + iId))
		{
			if ($("#cbAll" + iId).is(":checked"))
			{
				$("#cbView" + iId).prop("checked", true);
				$("#cbAdd" + iId).prop("checked", true);
				$("#cbEdit" + iId).prop("checked", true);
				$("#cbDelete" + iId).prop("checked", true);
			}

			else
			{
				$("#cbView" + iId).prop("checked", false);
				$("#cbAdd" + iId).prop("checked", false);
				$("#cbEdit" + iId).prop("checked", false);
				$("#cbDelete" + iId).prop("checked", false);
			}
		}

		else
		{
			if ($("#cbAdd" + iId).is(":checked") || $("#cbEdit" + iId).is(":checked") || $("#cbDelete" + iId).is(":checked"))
				$("#cbView" + iId).prop("checked", true);


			if ($("#cbView" + iId).is(":checked") && $("#cbAdd" + iId).is(":checked") && $("#cbEdit" + iId).is(":checked") && $("#cbDelete" + iId).is(":checked"))
				$("#cbAll" + iId).prop("checked", true);

			else
				$("#cbAll" + iId).prop("checked", false);
		}
	});


	$("#txtEmail").blur(function( )
	{
		if ($("#txtEmail").val( ) == "")
			return;


		$.post("ajax/management/check-user.php",
			{ UserId:$("#UserId").val( ), Email:$("#txtEmail").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided email address is already in use. Please provide another email address.");

					$("#DuplicateEmail").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateEmail").val("0");
				}
			},

			"text");
	});



	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtName", "B", "Please enter the Name."))
			return false;

		if (!objFV.validate("txtMobile", "B,N,L(11)", "Please enter a valid Mobile Number."))
			return false;

		if (!objFV.validate("txtEmail", "B,E", "Please enter a valid Email Address."))
			return false;

		if (objFV.value("txtPassword") != "")
		{
			if (!objFV.validate("txtPassword", "B,L(4)", "Please enter a valid password (Min Length = 4)."))
				return false;
		}

		if (objFV.value("filePicture") != "")
		{
			if (!checkImage(objFV.value("filePicture")))
			{
				showMessage("#RecordMsg", "alert", "Invalid File Format. Please select an image file of type jpg, gif or png.");

				objFV.focus("filePicture");
				objFV.select("filePicture");

				return false;
			}
		}


		var bProvinces = false;
		var bDistricts = false;

		$("input.province").each(function( )
		{
				if ($(this).prop("checked") == true)
					bProvinces = true;
		});

		$("input.district").each(function( )
		{
				if ($(this).prop("checked") == true)
					bDistricts = true;
		});


		if (bProvinces == false)
		{
			showMessage("#UserMsg", "info", "Please select at-least One Province.");

			return false;
		}
/*
		if (bDistricts == false)
		{
			showMessage("#UserMsg", "info", "Please select at-least One District.");

			return false;
		}
*/

		if (objFV.value("DuplicateEmail") == "1")
		{
			showMessage("#RecordMsg", "info", "The provided email address is already in use. Please provide another email address.");

			objFV.focus("txtEmail");
			objFV.select("txtEmail");

			return false;
		}


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});



function setSchoolsSelection( )
{
	var sProvinces = "0";
	var sDistricts = "0";

	$("input.province").each(function( )
	{
			if ($(this).prop("checked") == true)
				sProvinces += ("," + $(this).val( ));
	});

	$("input.district").each(function( )
	{
			if ($(this).prop("checked") == true)
				sDistricts += ("," + $(this).val( ));
	});


	$("ul.token-input-list").remove( );
	$("ul.token-input-list-facebook").remove( );

	$("#txtSchools").tokenInput(("ajax/get-schools-list.php?Provinces=" + sProvinces + "&Districts=" + sDistricts),
	{
		queryParam         :  "School",
		minChars           :  2,
		tokenLimit         :  2000,
		hintText           :  "Search the School (EMIS Code)",
		noResultsText      :  "No matching School found",
		theme              :  "facebook",
		preventDuplicates  :  true,
		prePopulate        :  eval($("#Schools").html( )),
		onAdd              :  function( ) {   }
	});
}