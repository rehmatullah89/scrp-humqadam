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

	$iPosition  = @strrpos($sPicture, '.');
	$sExtension = @substr($sPicture, $iPosition);

	switch($sExtension)
	{
		case '.jpg'  : $objPicture = @imagecreatefromjpeg($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);
					   break;

		case '.jpeg' : $objPicture = @imagecreatefromjpeg($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);
					   break;

		case '.png'  : $objPicture = @imagecreatefrompng($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);
					   break;

		case '.gif'  : $objPicture = @imagecreatefromgif($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);
					   break;

		default      : $objPicture = @imagecreatefromgd2($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);
					   break;
	}

	$iThumbWidth  = IO::intValue('w');
	$iThumbHeight = IO::intValue('h');
	$iThumbX      = IO::intValue('x');
	$iThumbY      = IO::intValue('y');
	$iImgWidth    = $sFields[$sType]['Width'];
	$iImgHeight   = $sFields[$sType]['Height'];

	$objThumb = @imagecreatetruecolor($iImgWidth, $iImgHeight);

	if ($sExtension == ".png" || $sExtension == ".gif")
	{
		@imagealphablending($objThumb, false);
		@imagesavealpha($objThumb,true);
		@imagecolortransparent($objThumb, @imagecolorallocatealpha($objThumb, 0, 0, 0, 127));
	}


	@imagecopyresampled($objThumb, $objPicture, 0, 0, $iThumbX, $iThumbY, $iImgWidth, $iImgHeight, $iThumbWidth, $iThumbHeight);


	if ($sExtension == ".png")
		@imagepng($objThumb, ($sRootDir.$sFields[$sType]['TargetDir'].$sPicture), 9);

	else if ($sExtension == ".gif")
		@imagegif($objThumb, ($sRootDir.$sFields[$sType]['TargetDir'].$sPicture));

	else
		@imagejpeg($objThumb, ($sRootDir.$sFields[$sType]['TargetDir'].$sPicture), 100);


	@imagedestroy($objThumb);
	@imagedestroy($objPicture);
?>
	<script type="text/javascript">
	<!--
		parent.$.colorbox.close( );
		parent.showMessage("#GridMsg", "success", "The select Picture Thumbnail has been Updated successfully.");
	-->
	</script>
<?
			exit( );
?>