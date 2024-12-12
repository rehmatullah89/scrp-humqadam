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

	function redirect($sPage, $sError = "")
	{
		if ($sError != "")
			$_SESSION["Flag"] = $sError;

		if ($sPage == "")
			$sPage = SITE_URL;

		header("Location: $sPage");
		exit( );
	}


	function exitPopup($bRightsIssue = false)
	{
		$sClass   = "error";
		$sMessage = "An ERROR occured while processing your request, please try again.";

		if ($bRightsIssue == true)
		{
			$sClass   = "info";
			$sMessage = "You don't have Rights to access the requested section. Please contact the Website Administrator for details.";
		}
?>
	<script type="text/javascript">
	<!--
		parent.$.colorbox.close( );
		parent.showMessage("#PageMsg", "<?= $sClass ?>", "<?= $sMessage ?>");
	-->
	</script>
<?
		exit( );
	}


	function formValue($sValue)
	{
		return htmlentities(html_entity_decode($sValue, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
	}


	function formatDate($sDate, $sFormat = "d-M-Y")
	{
		if ($sDate == "" || $sDate == "0000-00-00" || $sDate == "1970-01-01" || $sDate == "0000-00-00 00:00:00" || $sDate == "1970-01-01 00:00:00")
			return "";

		else
			return date($sFormat, strtotime($sDate));
	}


	function formatTime($sTime, $sFormat = "h:i A")
	{
		if ($sTime == "" || $sTime == "00:00:00")
			return "";

		else
			return date($sFormat, strtotime($sTime));
	}


	function formatNumber($sNumber, $bDecimals = true, $iDecimals = 2)
	{
		if ($bDecimals == false)
			$iDecimals = 0;

		return @number_format($sNumber, $iDecimals, '.', ',');
	}


	function createImage($sSrcFile, $sDestFile, $iImgWidth, $iImgHeight)
	{
		@list($iWidth, $iHeight, $sType, $sAttributes) = @getimagesize($sSrcFile);

		$fRatio = @($iWidth / $iHeight);


		$iPosition  = @strrpos($sSrcFile, '.');
		$sExtension = @substr($sSrcFile, $iPosition);

		switch($sExtension)
		{
			case '.jpg'  : $objPicture = @imagecreatefromjpeg($sSrcFile);
						   break;

			case '.jpeg' : $objPicture = @imagecreatefromjpeg($sSrcFile);
						   break;

			case '.png'  : $objPicture = @imagecreatefrompng($sSrcFile);
						   break;

			case '.gif'  : $objPicture = @imagecreatefromgif($sSrcFile);
						   break;
		}


		// Resize, Cener & Crop
		if ($_SESSION['ImageResize'] == "C")
		{
			if (@($iImgWidth / $iImgHeight) > $fRatio)
			{
				$iNewWidth  = $iImgWidth;
				$iNewHeight = @($iImgWidth / $fRatio);
			}

			else
			{
				$iNewWidth  = ($iImgHeight * $fRatio);
				$iNewHeight = $iImgHeight;
			}

			$iMidX = @($iNewWidth / 2);
			$iMidY = @($iNewHeight / 2);
			$iLeft = @($iMidX - ($iImgWidth / 2));
			$iTop  = @($iMidY - ($iImgHeight / 2));


			$objTemp = @imagecreatetruecolor($iNewWidth, $iNewHeight);

			if ($sExtension == ".png" || $sExtension == ".gif")
			{
				@imagealphablending($objTemp, false);
				@imagesavealpha($objTemp,true);
				@imagecolortransparent($objTemp, @imagecolorallocatealpha($objTemp, 0, 0, 0, 127));
			}

			@imagecopyresampled($objTemp, $objPicture, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);


			$objThumb = @imagecreatetruecolor($iImgWidth, $iImgHeight);

			if ($sExtension == ".png" || $sExtension == ".gif")
			{

				@imagealphablending($objThumb, false);
				@imagesavealpha($objThumb,true);
				@imagecolortransparent($objThumb, @imagecolorallocatealpha($objThumb, 0, 0, 0, 127));
			}

			@imagecopyresampled($objThumb, $objTemp, 0, 0, $iLeft, $iTop, $iImgWidth, $iImgHeight, $iImgWidth, $iImgHeight);
		}


		// Resize & Fit to Size
		else
		{
			$iNewWidth  = $iImgWidth;
			$iNewHeight = $iImgHeight;
			$iLeft      = 0;
			$iTop       = 0;

			if (@($iNewWidth / $iNewHeight) > $fRatio)
			   $iNewWidth = ($iNewHeight * $fRatio);

			else
			   $iNewHeight = @($iNewWidth / $fRatio);


			if ($iNewWidth < $iImgWidth)
				$iLeft = @ceil(($iImgWidth - $iNewWidth) / 2);

			if ($iNewHeight < $iImgHeight)
				$iTop = @ceil(($iImgHeight - $iNewHeight) / 2);


			$objTemp = @imagecreatetruecolor($iNewWidth, $iNewHeight);

			if ($sExtension == ".png" || $sExtension == ".gif")
			{
				@imagealphablending($objTemp, false);
				@imagesavealpha($objTemp,true);
				@imagecolortransparent($objTemp, @imagecolorallocatealpha($objTemp, 0, 0, 0, 127));
			}

			@imagecopyresampled($objTemp, $objPicture, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);


			$objThumb = @imagecreatetruecolor($iImgWidth, $iImgHeight);

			if ($sExtension == ".png" || $sExtension == ".gif")
			{
				@imagealphablending($objThumb, false);
				@imagesavealpha($objThumb,true);
				@imagecolortransparent($objTemp, @imagecolorallocatealpha($objThumb, 0, 0, 0, 127));
			}

			else
				@imagefill($objThumb, 0, 0, @imagecolorallocate($objThumb, 255, 255, 255));


			@imagecopy($objThumb, $objTemp, $iLeft, $iTop, 0, 0, $iNewWidth, $iNewHeight);
		}


		if ($sExtension == ".png")
			@imagepng($objThumb, $sDestFile, 9);

		else if ($sExtension == ".gif")
			@imagegif($objThumb, $sDestFile);

		else
			@imagejpeg($objThumb, $sDestFile, 100);


		@imagedestroy($objTemp);
		@imagedestroy($objThumb);
		@imagedestroy($objPicture);
	}


	function decrypt($sData, $sKey)
	{
		$sResult = "";
		$sData   = decode_base64($sData);

		for($i = 0; $i < strlen($sData); $i ++)
		{
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
			$sChar    = chr(ord($sChar) - ord($sKeyChar));
			$sResult .= $sChar;
		}

		return $sResult;
	}


	function decode_base64($sData)
	{
		$sBase64 = strtr($sData, '-_', '+/');

		return base64_decode($sBase64);
	}


 	function getExcelCol($iColumn)
    {
        $iColumn = (($iColumn < 0) ? 0 : $iColumn);
        $sColumn = chr(($iColumn % 26) + 65);

        $iQuotient = @floor($iColumn / 26);

        while ($iQuotient > 0)
        {
            $sColumn   .= chr(($iQuotient % 26) + (($iQuotient % 26) == 0 ? 90 : 64));
            $iQuotient -= 26;
            $iQuotient  = @ceil($iQuotient / 26);
        }

        return strrev($sColumn);
    }


	function getSefUrl($sValue)
	{
		$sValue = trim($sValue);
		$sValue = strtolower($sValue);
		$sValue = stripslashes($sValue);

		$sValue = str_replace('�','a',$sValue);
		$sValue = str_replace('�','e',$sValue);
		$sValue = str_replace('�','i',$sValue);
		$sValue = str_replace('�','o',$sValue);
		$sValue = str_replace('�','u',$sValue);
		$sValue = str_replace('�','a',$sValue);
		$sValue = str_replace('�','e',$sValue);
		$sValue = str_replace('�','i',$sValue);
		$sValue = str_replace('�','o',$sValue);
		$sValue = str_replace('�','u',$sValue);
		$sValue = str_replace('&aacute;','a',$sValue);
		$sValue = str_replace('&eacute;','e',$sValue);
		$sValue = str_replace('&iacute;','i',$sValue);
		$sValue = str_replace('&oacute;','o',$sValue);
		$sValue = str_replace('&uacute;','u',$sValue);
		$sValue = str_replace('&ntilde;','n',$sValue);
		$sValue = str_replace('�','n',$sValue);
		$sValue = str_replace('�','n',$sValue);
		$sValue = str_replace('�','a',$sValue);
		$sValue = str_replace('�','e',$sValue);
		$sValue = str_replace('�','i',$sValue);
		$sValue = str_replace('�','o',$sValue);
		$sValue = str_replace('�','u',$sValue);
		$sValue = str_replace('�','a',$sValue);
		$sValue = str_replace('�','e',$sValue);
		$sValue = str_replace('�','i',$sValue);
		$sValue = str_replace('�','o',$sValue);
		$sValue = str_replace('�','u',$sValue);
		$sValue = str_replace('&auml;','a',$sValue);
		$sValue = str_replace('&euml;','e',$sValue);
		$sValue = str_replace('&iuml;','i',$sValue);
		$sValue = str_replace('&ouml;','o',$sValue);
		$sValue = str_replace('&uuml;','u',$sValue);

		$sValidChars = "abcdefghijklmnopqrstuvwxyz0123456789-";
		$iLength     = @strlen($sValue);
		$sTempValue  = "";

		for ($i = 0; $i < $iLength; $i ++)
		{
			if (strstr($sValidChars, $sValue{$i}))
				$sTempValue .= $sValue{$i};

			else
				$sTempValue .= "-";
		}

		$sValue = $sTempValue;

		while (strpos($sValue, "--") !== FALSE)
		{
			$sValue = str_replace("--", "-", $sValue);
		}

		if ($sValue{0} == "-")
			$sValue = substr($sValue, 1);

		if ($sValue{strlen($sValue) - 1} == "-")
			$sValue = substr($sValue, 0, (strlen($sValue) - 1));

		return $sValue;
	}
	
	
    function numberToWord($iNumber)
    {
        if (($iNumber < 0) || ($iNumber > 999999999))
            return $iNumber;

        $iMillions = @floor($iNumber / 1000000);  /* Millions */
            $iNumber -= $iMillions * 1000000;

        $iLacs = @floor($iNumber / 100000);       /* Lac */
            $iNumber -= $iLacs * 100000;

        $iThousands = @floor($iNumber / 1000);    /* Thousands */
            $iNumber -= $iThousands * 1000;

        $iHundreds = @floor($iNumber / 100);      /* Hundreds */
            $iNumber -= $iHundreds * 100;

        $iTens = @floor($iNumber / 10);           /* Tens (deca) */
            $iOnes = $iNumber % 10;               /* Ones */


        $sNumber = "";

        if ($iMillions)
            $sNumber .= (numberToWord($iMillions)." Million");

        if ($iLacs)
            $sNumber .= (((empty($sNumber) ? "" : " ").numberToWord($iLacs)." Lac"));

        if ($iThousands)
            $sNumber .= (((empty($sNumber) ? "" : " ").numberToWord($iThousands)." Thousand"));

        if ($iHundreds)
            $sNumber .= (((empty($sNumber) ? "" : " ").numberToWord($iHundreds)." Hundred"));


        $sOnes = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                       "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $sTens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");

        if ($iTens || $iOnes)
        {
            if (!empty($sNumber))
                $sNumber .= " and ";


            if ($iTens < 2)
                $sNumber .= $sOnes[($iTens * 10) + $iOnes];

            else
            {
                $sNumber .= $sTens[$iTens];

                if ($iOnes)
                    $sNumber .= ("-".$sOnes[$iOnes]);
            }
        }

        if (empty($sNumber))
            $sNumber = "zero";

        return $sNumber;
    }	
?>