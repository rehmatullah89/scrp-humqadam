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

	if ($_SESSION["Flag"] != "")
	{
		$sMessages = array(
						    'ERROR'                           => (($_SESSION["Error"] != "") ? "<b>ERROR:</b> {$_SESSION['Error']}" : 'An Error occured while processing your request. Please try again!'),
						    'DB_ERROR'                        => 'An Error is returned from Database while processing your request. Please try again!',
							'MAIL_ERROR'                      => 'An error occured while sending you an Email. Please try again.',
							'ACCESS_DENIED'                   => 'You havn\'t enough rights to access the requested section.',
							'INCOMPLETE_FORM'                 => '<b>Invalid Request</b> Please complete the form properly to add the record.',
							'ALREADY_LOGGED_IN'               => 'You are already Logged into your Account.',

							'WEB_PAGE_ADDED'                  => 'The specified Web Page has been Added into the System successfully.',
							'WEB_PAGE_EXISTS'                 => 'A Web Page with specified SEF URL already exists. Please specify another SEF URL.',

							'META_TAGS_SAVED'                 => 'The selected Page Meta Tags have been Saved into the System successfully.',
							'CONTENTS_SAVED'                  => 'The selected Page Contents have been Saved into the System successfully.',


							'DISTRICT_ADDED'                  => 'The specified District has been Added into the System successfully.',
							'DISTRICT_EXISTS'                 => 'A District with specified SEF URL already exists. Please specify another SEF URL.',
							
							'SCHOOL_TYPE_ADDED'               => 'The specified School Type has been Added into the System successfully.',
							'SCHOOL_TYPE_EXISTS'              => 'A School Type with specified Title already exists. Please specify another Title.',

							'SCHOOL_ADDED'                    => 'The specified School has been Added into the System successfully.',
							'SCHOOL_EXISTS'                   => 'A School with specified EMIS Code already exists. Please specify another Code.',
							'INVALID_EMIS_CODE'               => 'Invalid EMIS Code, No School found against the provided EMIS Code.',
							
							'SURVEY_QUESTION_ADDED'           => 'The specified Survey Question has been Added into the System successfully.',
							'SURVEY_QUESTION_EXISTS'          => 'A Question with specified Text/Section already exists. Please specify another Question.',
							'LINK_QUESTION_NOT_EXISTS'        => 'The provided Linked Question ID is Invalid. Please enter a valid Question ID.',
							
							'SOR_EXISTS'                      => 'SOR against this school already exists!',
                            'SOR_ADDED'                       => 'The specified SOR has been Added into the System successfully.',
							'SOR_DELETED'                     => 'The selected SOR Document has been Deleted successfully.',    
							
							'SURVEY_ADDED'                    => 'The specified Survey Entry has been Added into the System successfully.',
							'SURVEY_EXISTS'                   => 'A Survey Entry with specified Date/School already exists. Please specify another Date/School.',
							
							'SURVEY_SCHEDULE_EXISTS'          => 'A Survey Schedule for specified School already exists. Please specify another School.',
                            'SURVEY_SCHEDULE_ADDED'           => 'The specified School Survey Schedule has been Added into the System successfully.',
							

							'FAILURE_REASON_ADDED'            => 'The specified Failure Reason has been Added into the System successfully.',
							'FAILURE_REASON_EXISTS'           => 'A Reason with specified Text already exists. Please specify another Reason.',

							'STAGE_ADDED'                     => 'The specified Stage has been Added into the System successfully.',
							'STAGE_EXISTS'                    => 'A Stage with specified Name already exists. Please specify another Name.',

							'CONTRACTOR_ADDED'                => 'The specified Contractor has been Added into the System successfully.',
							'CONTRACTOR_EXISTS'               => 'A Contractor with same Company Name already exists in the System.',

							'CONTRACT_ADDED'                  => 'The specified Contract has been Added into the System successfully.',
							'CONTRACT_EXISTS'                 => 'A Contract with specified Title already exists. Please specify another Title.',

							'SCHEDULE_ADDED'                  => 'The specified Construction Schedule has been Added into the System successfully.',
							'SCHEDULE_EXISTS'                 => 'The Construction Schedule of selected Contract/School already exists in the System.',
							'INVALID_SCHEDULE_DATES'          => 'The Construction Schedule of Start/End Dates are invalid.',
							'SCHEDULE_NOT_EXISTS'             => 'No Construction Schedule found for the selected School.',
							'SCHEDULE_COPIED'                 => 'The specified Construction Schedule has been Copied in the selected Schools successfully.',

							'PACKAGE_ADDED'                   => 'The specified Package has been Added into the System successfully.',
							'PACKAGE_EXISTS'                  => 'A Package with specified Title already exists. Please specify another Title.',

							'INSPECTION_ADDED'                => 'The specified Inspection Entry has been Added into the System successfully.',
							'INSPECTION_EXISTS'               => 'An Inspection Entry with specified Title/School/Stage already exists. Please specify another Title/School/Stage.',
							'INSPECTION_FILE_DELETED'         => 'The selected Inspection Document has been Deleted successfully.',
							'INSPECTION_MEASUREMENT_SAVED'    => 'The specified Measurement Entry has been Saved successfully.',
							'INSPECTION_MEASUREMENT_UPDATED'  => 'The selected Measurement Entry has been Updated successfully.',
							'INSPECTION_MEASUREMENT_DELETED'  => 'The selected Measurement Entry has been Deleted successfully.',

							'BOQ_ADDED'                       => 'The specified BOQ Entry has been Added into the System successfully.',
							'BOQ_EXISTS'                      => 'A BOQ Entry with specified Title already exists. Please specify another Title.',

							'INVOICE_ADDED'                   => 'The specified Invoice has been Added into the System successfully.',
							'INVOICE_EXISTS'                  => 'An Invoice with specified Invoice No already exists. Please specify another Invoice No.',


							'FAQ_ADDED'                       => 'The specified FAQ has been Added into the System successfully.',
							'FAQ_EXISTS'                      => 'A FAQ with specified question already exists. Please specify another Question.',
							'FAQ_CATEGORY_ADDED'              => 'The specified Category has been Added into the System successfully.',
							'FAQ_CATEGORY_EXISTS'             => 'A Category with specified Name already exists. Please specify another Name.',

							'NEWS_ADDED'                      => 'The specified News has been Added into the System successfully.',
							'NEWS_EXISTS'                     => 'A News with same SEF URL already exists. Please specify another SEF URL.',

                                                        'DOCUMENT_ADDED'                  => 'The specified Document has been Added into the System successfully.',
							'DOCUMENT_EXISTS'                 => 'A Document with same School & Type already exists. Please specify another Document Type or School Code.',   
                                                        'DOCUMENT_DELETED'                => 'The selected Document File has been Deleted Successfully',
                                                        'DOCUMENT_COUNT_ERROR'            => 'You can not delete all the files for this document!',  
                                                            
						    'MAINTENANCE_UPDATED'             => 'The website Maintenance Mode has been Updated successfully.',
						    'SETTINGS_UPDATED'                => 'The website Settings have been Updated successfully.',

							'USER_TYPE_EXISTS'                => 'The specified User Type already exists. Please specify another User Type.',
						    'USER_TYPE_ADDED'                 => 'The specified Admin User Type has been Added into the System successfully.',
							'USER_EMAIL_EXISTS'               => 'The specified Email Address is already used. Please specify a new email address.',
						    'USER_ADDED'                      => 'The specified Admin User Account has been Added into the System successfully.',

							'BACKUP_DATABASE_TAKEN'           => 'The Backup of the Database has been Taken Successfully',
							'BACKUP_WEBSITE_TAKEN'            => 'The Backup of the Website has been Taken Successfully',
							'BACKUP_DELETED'                  => 'The selected Backup File has been Deleted Successfully',
							'BACKUP_RESTORED'                 => 'The Database has been Restored from the selected Backup File successfully',
							'BACKUP_WRITE_ERROR'              => 'Unable to Create the Backup File.',
							'BACKUP_READ_ERROR'               => 'Unable to Read the Backup File.',
							
							'NOTIFICATION_SENT'               => 'Your Message has been sent to selected Users.'
						  );

		$sMsgCss = "alert";

		if (@strstr($_SESSION["Flag"], 'EXISTS') || @strstr($_SESSION["Flag"], 'ERRORS') || @strstr($_SESSION["Flag"], 'INVALID'))
			$sMsgCss = "info";

		else if (@strstr($_SESSION["Flag"], 'ERROR'))
			$sMsgCss = "error";

		else if (@strstr($_SESSION["Flag"], 'ADDED') || @strstr($_SESSION["Flag"], 'OK') || @strstr($_SESSION["Flag"], 'TAKEN') || @strstr($_SESSION["Flag"], 'DELETED') || @strstr($_SESSION["Flag"], 'UPDATED') || @strstr($_SESSION["Flag"], 'RESTORED') || @strstr($_SESSION["Flag"], 'SAVED') || @strstr($_SESSION["Flag"], 'COPIED') || @strstr($_SESSION["Flag"], 'SENT'))
			$sMsgCss = "success";
	}

	else
		$sMsgCss = "hidden";
?>
      <div id="PageMsg" class="<?= $sMsgCss ?>"><?= $sMessages[$_SESSION["Flag"]] ?></div>
<?
	$_SESSION["Flag"]  = "";
	$_SESSION["Error"] = "";
?>