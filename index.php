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
	$objDb2      = new Database( );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("includes/meta-tags.php");
?>
</head>

<body>

<?
	@include("includes/header.php");
?>


<main>
<?
	if ($iPageId == 1)
		@include("includes/banners.php");

	else if ($iPageId == 9)
		@include("includes/map.php");
?>

  <section id="Contents">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr valign="top">
        <td></td>

        <td width="860" class="tdMain">
<?
	@include("includes/messages.php");


	if ($sPhpUrl != "")
	{
		@include("pages/{$sPhpUrl}");
	}

	else
	{
?>
          <?= $sPageContents ?>
<?
	}
?>
        </td>

        <td width="40"></td>
        <td width="25" bgcolor="#f6f6f6"></td>

        <td width="275" bgcolor="#f6f6f6" class="tdMain">
<?
	@include("includes/panel.php");
?>
        </td>

        <td bgcolor="#f6f6f6"></td>
      </tr>
    </table>
  </section>
</main>


<?
	if ($sPhpUrl == "monitoring-dashboard.php")
		@include("includes/monitoring-dashboard.php");
	
	if ($sPhpUrl == "baseline-surveys.php")
		@include("includes/baseline-surveys.php");

	@include("includes/footer.php");
?>

</body>
</html>
<?
	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>