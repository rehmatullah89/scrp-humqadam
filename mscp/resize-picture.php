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

	@require_once("requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	$sFields = array(
	                  "Admin"        => array("Table"     => "tbl_admins",
	                                          "Field"     => "picture",
	                                          "SourceDir" => (ADMINS_IMG_DIR.'originals/'),
	                                          "TargetDir" => (ADMINS_IMG_DIR.'thumbs/'),
	                                          "Width"     => ADMINS_IMG_WIDTH,
	                                          "Height"    => ADMINS_IMG_HEIGHT),

	                  "News"         => array("Table"     => "tbl_news",
	                                          "Field"     => "picture",
	                                          "SourceDir" => (NEWS_IMG_DIR.'originals/'),
	                                          "TargetDir" => (NEWS_IMG_DIR.'thumbs/'),
	                                          "Width"     => NEWS_IMG_WIDTH,
	                                          "Height"    => NEWS_IMG_HEIGHT)
	                );


	$sType = IO::strValue("Type");
	$iId   = IO::intValue("Id");

	$sSQL = "SELECT {$sFields[$sType]['Field']} FROM {$sFields[$sType]['Table']} WHERE id='$iId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sPicture = $objDb->getField(0, 0);


	if ($_POST)
		@include("save-resize-picture.php");


	@list($iWidth, $iHeight) = @getimagesize($sRootDir.$sFields[$sType]['SourceDir'].$sPicture);

	$iMinHeight = ($sFields[$sType]['Height'] + $sFields[$sType]['Height'] + 140);

	$iWidth  = (($iWidth < 300) ? 300 : $iWidth);
	$iHeight = (($iHeight < $iMinHeight) ? $iMinHeight : $iHeight);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="plugins/jcrop/jquery.jcrop.js"></script>

  <link type="text/css" rel="stylesheet" href="plugins/jcrop/jquery.jcrop.css" media="screen" />
</head>

<body class="popupBg">

<div id="PopupDiv">
  <form name="frmResize" id="frmResize" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="Type" value="<?= $sType ?>" />
  <input type="hidden" name="Id" value="<?= $iId ?>" />
  <input type="hidden" id="x" name="x" value="0" />
  <input type="hidden" id="y" name="y" value="0" />
  <input type="hidden" id="x2" name="x2" value="<?= $sFields[$sType]['Width'] ?>" />
  <input type="hidden" id="y2" name="y2" value="<?= $sFields[$sType]['Height'] ?>" />
  <input type="hidden" id="w" name="w" value="<?= $sFields[$sType]['Width'] ?>" />
  <input type="hidden" id="h" name="h" value="<?= $sFields[$sType]['Height'] ?>" />

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
	  <td>
	    <div id="Source" style="width:800px; height:600px; overflow:auto; background:#eeeeee;">
		  <img id="Picture" src="<?= (SITE_URL.$sFields[$sType]['SourceDir'].$sPicture) ?>" alt="" title="" />
	    </div>
	  </td>

	  <td width="20"></td>

	  <td width="<?= ($sFields[$sType]['Width'] + 4) ?>">
	    <center><b>Thumb Preview</b></center>
	    <div class="br5"></div>

	    <div style="width:<?= $sFields[$sType]['Width'] ?>px; height:<?= $sFields[$sType]['Height'] ?>px; padding:1px; border:solid 1px #888888;">
		  <div style="width:<?= $sFields[$sType]['Width'] ?>px; height:<?= $sFields[$sType]['Height'] ?>px; overflow:hidden;">
		    <img id="Preview" src="<?= (SITE_URL.$sFields[$sType]['SourceDir'].$sPicture) ?>?<?= @rand(9999, 9999999999) ?>" width="<?= $sFields[$sType]['Width'] ?>" height="<?= $sFields[$sType]['Height'] ?>" alt="" title="" />
		  </div>
	    </div>

	    <br />
	    <center><b>Current Thumb</b></center>
	    <div class="br5"></div>

	    <div style="width:<?= $sFields[$sType]['Width'] ?>px; height:<?= $sFields[$sType]['Height'] ?>px; padding:1px; border:solid 1px #888888;">
		  <div style="width:<?= $sFields[$sType]['Width'] ?>px; height:<?= $sFields[$sType]['Height'] ?>px; overflow:hidden;">
		    <img src="<?= (SITE_URL.$sFields[$sType]['TargetDir'].$sPicture) ?>?<?= @rand(9999, 9999999999) ?>" width="<?= $sFields[$sType]['Width'] ?>" height="<?= $sFields[$sType]['Height'] ?>" alt="" title="" />
		  </div>
	    </div>

	    <br />
	    <div id="CropMsg"></div>
	  </td>
    </tr>

    <tr>
	  <td colspan="3" height="40" valign="bottom">
	    <button id="BtnSave">Crop & Save</button>
	    <button id="BtnCancel">Cancel</button>
	  </td>
    </tr>
  </table>
  </form>

  <script type="text/javascript">
  <!--
	  $(document).ready(function( )
	  {
		  if ($(window).width( ) > <?= ($iWidth + $sFields[$sType]['Width'] + 64) ?> || $(window).height( ) > <?= ($iHeight + 80) ?>)
		  {
			  if ($(window).width( ) > <?= ($iWidth + $sFields[$sType]['Width'] + 64) ?> && $(window).height( ) > <?= ($iHeight + 80) ?>)
			  	  parent.$.colorbox.resize( { innerWidth:'<?= ($iWidth + $sFields[$sType]['Width'] + 64) ?>px', innerHeight:'<?= ($iHeight + 80) ?>px' } )

 			  else if ($(window).width( ) <= <?= ($iWidth + $sFields[$sType]['Width'] + 64) ?> && $(window).height( ) > <?= ($iHeight + $sFields[$sType]['Height'] + 80) ?>)
			  	  parent.$.colorbox.resize( { innerWidth:$(window).width( ), innerHeight:'<?= ($iHeight + 80) ?>px' } )

			  else if ($(window).width( ) > <?= ($iWidth + $sFields[$sType]['Width'] + 64) ?> && $(window).height( ) <= <?= ($iHeight + $sFields[$sType]['Height'] + 80) ?>)
			 	  parent.$.colorbox.resize( { innerWidth:'<?= ($iWidth + $sFields[$sType]['Width'] + 64) ?>px', innerHeight:$(window).height( ) } )
		  }


		  $("#Source").css("width", (($(window).width( ) - <?= ($sFields[$sType]['Width'] + 64) ?>) + "px"));
		  $("#Source").css("height", (($(window).height( ) - 80) + "px"));


		  $('#Picture').Jcrop(
		  {
			  onChange    : showPreview,
			  onSelect    : showPreview,
			  aspectRatio : <?= $sFields[$sType]['Width'] ?> / <?= $sFields[$sType]['Height'] ?>,
			  minSize     : [<?= $sFields[$sType]['Width'] ?>, <?= $sFields[$sType]['Height'] ?>],
			  setSelect   : [0, 0, <?= $sFields[$sType]['Width'] ?>, <?= $sFields[$sType]['Height'] ?>],
			  boxWidth    : (($(window).width( ) - <?= ($sFields[$sType]['Width'] + 64) ?>) + "px"),
			  boxHeight   : (($(window).height( ) - 80) + "px")
		  });


		  function showPreview(coords)
		  {
			  if (parseInt(coords.w) > 0)
			  {
				  var rx = (<?= $sFields[$sType]['Width'] ?> / coords.w);
				  var ry = (<?= $sFields[$sType]['Height'] ?> / coords.h);

				  $('#Preview').css(
				  {
					  width      : (Math.round(rx * <?= $iWidth ?>) + 'px'),
					  height     : (Math.round(ry * <?= $iHeight ?>) + 'px'),
					  marginLeft : ('-' + Math.round(rx * coords.x) + 'px'),
					  marginTop  : ('-' + Math.round(ry * coords.y) + 'px')
				  });
			  }

			  $('#x').val(coords.x);
			  $('#y').val(coords.y);
			  $('#x2').val(coords.x2);
			  $('#y2').val(coords.y2);
			  $('#w').val(coords.w);
			  $('#h').val(coords.h);
		  }


		  $('#frmResize').submit(function( )
		  {
			  if (parseInt($('#w').val( )) > 0)
				  return true;

			  showMessage("#CropMsg", "alert", "Please make a selection first.");

			  return false;
		  });


		  $('#BtnCancel').click(function( )
		  {
			  parent.$.colorbox.close( );

			  return false;
		  });


		  $("#BtnSave").button({ icons:{ primary:'ui-icon-disk' } });
		  $("#BtnCancel").button({ icons:{ primary:'ui-icon-closethick' } });
	  });
  -->
  </script>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>