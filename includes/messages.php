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
						    'ERROR'       => (($_SESSION["Error"] != "") ? ('An ERROR occured while processing your request.<br /><br />ERROR:'.$_SESSION["Error"]) : 'An Error occured while processing your request. Please try again!'),
						    'DB_ERROR'    => 'An Error is returned from Database while processing your request. Please try again!',
							'MAIL_ERROR'  => 'An error occured while sending you an Email. Please try again.'
						  );

		$sMsgCss = "alert";

		if (@strstr($_SESSION["Flag"], 'EXISTS') || @strstr($_SESSION["Flag"], 'INFO'))
			$sMsgCss = "info";

		else if (@strstr($_SESSION["Flag"], 'ERROR') || @strstr($_SESSION["Flag"], 'INVALID'))
			$sMsgCss = "error";

		else if (@strstr($_SESSION["Flag"], 'OK'))
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