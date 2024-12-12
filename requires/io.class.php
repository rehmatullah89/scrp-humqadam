<?
	/*********************************************************************************************\
	***********************************************************************************************
	**                                                                                           **
	**  SCRP - School Construction and Rehabilitation Programme                                  **
	**  Version 1.0                                                                              **
	**                                                                                           **
	**  http://www.3-tree.com/imc/                                                               **
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

    /* A PHP class to parse Forms/URL Values with convenient Methods
    *
    * @version  1.0
    * @author   Muhammad Tahir Shahzad
    */

	class IO
	{
		function IO( )
		{

		}


		function intValue($sField)
		{
			return intval(trim($_REQUEST[$sField]));
		}

		function strValue($sField, $bHtmlEntities = false)
		{
			$sValue = $_REQUEST[$sField];

			if (get_magic_quotes_gpc( ))
				$sValue = trim($sValue);

			else
				$sValue = trim(addslashes($sValue));

			if ($bHtmlEntities == true)
				$sValue = htmlentities($sValue, ENT_QUOTES, 'UTF-8');


			return $sValue;
			//return tidyHtml($sValue);
		}


		function floatValue($sField)
		{
			return floatval(trim($_REQUEST[$sField]));
		}


		function getArray($sField, $sType = "str")
		{
			if (@is_array($_REQUEST[$sField]))
				$sArray = $_REQUEST[$sField];

			else
			{
				$sArray = array( );

				if (trim($_REQUEST[$sField]) != "")
					$sArray[0] = $_REQUEST[$sField];
			}

			$sArray = @array_map("trim", $sArray);


			if ($sType == "int")
				$sArray = @array_map("intval", $sArray);

			else if ($sType == "float")
				$sArray = @array_map("floatval", $sArray);

			else if ($sType == "str")
			{
				if (!get_magic_quotes_gpc( ))
					$sArray = @array_map("addslashes", $sArray);

				//$sArray = @array_map("tidyHtml", $sArray);
			}


			return $sArray;
		}


		function getFileName($sFile)
		{
			$sValue = @basename($sFile);
			$sValue = @trim($sValue);
			$sValue = @strtolower($sValue);
			$sValue = @stripslashes($sValue);
			$sValue = @str_replace(" ", "-", $sValue);
			$sValue = @str_replace("_", "-", $sValue);

			$sValidChars = "abcdefghijklmnopqrstuvwxyz0123456789-.";
			$iLength     = @strlen($sValue);
			$sFileName   = "";

			for ($i = 0; $i < $iLength; $i ++)
			{
				if (@strstr($sValidChars, $sValue{$i}))
					$sFileName .= $sValue{$i};
			}

			return $sFileName;
		}
	}


	function tidyHtml($sValue)
	{
/*
		if ($sValue != strip_tags($sValue))
		{
			$bConfig = array('indent'                      => false,
							 'output-xhtml'                => true,
							 'numeric-entities'            => true,
							 'doctype'                     => 'auto',
							 'clean'                       => true,
							 'add-xml-space'               => true,
							 'drop-proprietary-attributes' => true,
							 'wrap'                        => 0,
							 'show-body-only'              => true);


			if (@function_exists("tidy_repair_string"))
			{
				$sValue = @tidy_repair_string($sValue, $bConfig, 'utf8');
			}

			else
			{
				try
				{
					$objTidy = new tidy( );

					$sValue  = $objTidy->repairString($sValue, $bConfig, 'utf8');
				}

				catch (Exception $e)
				{

				}
			}
		}
*/
		return $sValue;
	}
?>