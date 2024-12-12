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

	@require_once("requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );


	$sTable = IO::strValue("Table");
	$sField = IO::strValue("Field");
	$iId    = IO::intValue("Id");


	$sSQL = "SELECT {$sField} FROM {$sTable} WHERE id='$iId'";
	$objDb->query($sSQL);

	$sHtml = $objDb->getField(0, 0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>HTML Contents</title>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />

  <meta name="revisit-after" content="1 Weeks" />
  <meta name="distribution" content="global" />
  <meta name="rating" content="general" />
  <meta http-equiv="imagetoolbar" content="no" />

  <base href="<?= SITE_URL ?>" />

  <link type="text/css" rel="stylesheet" href="css/default.css" />

  <script type="text/javascript" src="scripts/default.js"></script>
</head>

<body>

<div id="MainDiv">
  <div id="Body">
    <div id="BodyDiv">
      <div id="Contents">
        <?= $sHtml ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
<!--
	$(document).ready(function( )
	{
		$("a").click(function( )
		{
			return false;
		});

		$("#Contents").css("height", ($(window).height( ) + "px"));
	});
-->
</script>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>