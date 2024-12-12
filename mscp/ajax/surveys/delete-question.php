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

	header("Expires: Tue, 01 Jan 2000 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	if ($sUserRights["Delete"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$sQuestions = IO::strValue("Questions");

	if ($sQuestions != "")
	{
		$iQuestions = @explode(",", $sQuestions);


		$objDb->execute("BEGIN");

		for ($i = 0; $i < count($iQuestions); $i ++)
		{
			$sSQL  = "DELETE FROM tbl_survey_questions WHERE id='{$iQuestions[$i]}'";
			$bFlag = $objDb->execute($sSQL);

			if ($bFlag == true)
			{
				$sSQL  = "DELETE FROM tbl_survey_answers WHERE question_id='{$iQuestions[$i]}'";
				$bFlag = $objDb->execute($sSQL);
			}


			if ($bFlag == false)
				break;
		}

		if ($bFlag == true)
		{
			$objDb->execute("COMMIT");

			if (count($iQuestions) > 1)
				print "success|-|The selected Question Records have been Deleted successfully.";

			else
				print "success|-|The selected Question Record has been Deleted successfully.";
		}

		else
		{
			print "error|-|An error occured while processing your request, please try again.";

			$objDb->execute("ROLLBACK");
		}
	}

	else
		print "info|-|Inavlid Question Record Delete request.";


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>