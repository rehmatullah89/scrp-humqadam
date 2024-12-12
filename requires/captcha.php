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

	@session_start( );

	$sChars = "abcdefghijklmnopqrstuvwxyz0123456789";
	$sCode  = substr(str_shuffle($sChars), 0, 5);

	$_SESSION['SpamCode']    = $sCode;
	$_SESSION['Md5SpamCode'] = md5(strtolower($sCode));

	$sLetter1 = $sCode{0};
	$sLetter2 = $sCode{1};
	$sLetter3 = $sCode{2};
	$sLetter4 = $sCode{3};
	$sLetter5 = $sCode{4};

	$objImage = @imagecreatefromjpeg("../images/code-bg.jpg");

	$sFont = ("../fonts/verdana.ttf");

	$iAngle    = 0;
	$iFontSize = 13;

	$sColors[0] = array(122, 229, 112);
	$sColors[1] = array(85, 178, 85);
	$sColors[2] = array(226, 108, 97);
	$sColors[3] = array(141, 214, 210);
	$sColors[4] = array(214, 141, 205);
	$sColors[5] = array(100, 138, 204);

	$iColor1 = @rand(0, 5);
	$iColor2 = @rand(0, 5);
	$iColor3 = @rand(0, 5);
	$iColor4 = @rand(0, 5);
	$iColor5 = @rand(0, 5);

	$sTextColor1 = @imagecolorallocate($objImage, $sColors[$iColor1][0], $sColors[$iColor1][1], $sColors[$iColor1][2]);
	$sTextColor2 = @imagecolorallocate($objImage, $sColors[$iColor2][0], $sColors[$iColor2][1], $sColors[$iColor2][2]);
	$sTextColor3 = @imagecolorallocate($objImage, $sColors[$iColor3][0], $sColors[$iColor3][1], $sColors[$iColor3][2]);
	$sTextColor4 = @imagecolorallocate($objImage, $sColors[$iColor4][0], $sColors[$iColor4][1], $sColors[$iColor4][2]);
	$sTextColor5 = @imagecolorallocate($objImage, $sColors[$iColor5][0], $sColors[$iColor5][1], $sColors[$iColor5][2]);

	@imagettftext($objImage, $iFontSize, $iAngle, 10, 17, $sTextColor1, $sFont, $sLetter1);
	@imagettftext($objImage, $iFontSize, $iAngle, 32, 17, $sTextColor2, $sFont, $sLetter2);
	@imagettftext($objImage, $iFontSize, $iAngle, 54, 17, $sTextColor3, $sFont, $sLetter3);
	@imagettftext($objImage, $iFontSize, $iAngle, 76, 17, $sTextColor4, $sFont, $sLetter4);
	@imagettftext($objImage, $iFontSize, $iAngle, 98, 17, $sTextColor5, $sFont, $sLetter5);

	header('Content-type: image/jpeg');

	@imagejpeg($objImage, null, 100);
	@imagedestroy($objImage);
?>