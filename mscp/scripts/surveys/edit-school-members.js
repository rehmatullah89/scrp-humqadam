
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
	
	$("#frmRecord").submit(function( )
	{
		var objFV = new FormValidator("frmRecord", "RecordMsg");

                if (!objFV.validate("ddType", "B", "Please select the Member Type."))
			return false;

		if (!objFV.validate("txtName", "B", "Please enter the Member Name."))
			return false;

		$("#BtnSave").attr('disabled', true);
		$("#RecordMsg").hide( );

		return true;
	});
        
        $(document).on("click", ".icnEdit", function(event)
	{
                var iMemberId = this.id;
		
		$.colorbox({ href:("surveys/edit-school-member.php?MemberId=" + iMemberId ), width:"100%", height:"100%", iframe:true, opacity:"0.50", overlayClose:false });

		event.stopPropagation( );
	});
        
        $(document).on("click", ".icnDelete", function(event)
	{
		var iMemberId = this.id;
		
		$.ajax({
                    type:'POST',
                    url:'ajax/surveys/delete-school-member.php',
                    data:{MemberId:iMemberId},
                    success: function(data){
                         if(data != ''){
                             parent.$.colorbox.close( );
                             parent.showMessage("#GridMsg", "success", "This school member has been deleted successfully.");
                         }else{
                             alert("There is some problem occured!");
                         }
                    }

                })
        });
});