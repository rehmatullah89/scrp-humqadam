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


	$iQuestions    = getDbValue("COUNT(1)", "tbl_survey_questions");
	$sSectionsList = getList("tbl_survey_sections", "id", "name", "type='V'", "position");

	print '<select id="Section">';
	print '<option value="">All Sections</option>';

	foreach ($sSectionsList as $iSection => $sSection)
	{
		print @utf8_encode('<option value="'.(($iQuestions > 50) ? $iSection : $sSection).'">'.$sSection.'</option>');
	}

	print '</select>';


	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>