
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
	$("#View, #Add, #Edit, #Delete").click(function( )
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


	$("input[type='checkbox']").click(function( )
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


	$("#txtTitle").blur(function( )
	{
		if ($("#txtTitle").val( ) == "")
			return;


		$.post("ajax/management/check-user-type.php",
			{ TypeId:$("#TypeId").val( ), Title:$("#txtTitle").val( ) },

			function (sResponse)
			{
				if (sResponse == "USED")
				{
					showMessage("#RecordMsg", "info", "The provided Title is already in use. Please provide another Title.");

					$("#DuplicateType").val("1");
				}

				else
				{
					$("#RecordMsg").hide( );
					$("#DuplicateType").val("0");
				}
			},

			"text");
	});


	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");


		if (!objFV.validate("txtTitle", "B", "Please enter the Type Title."))
			return false;


		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
});