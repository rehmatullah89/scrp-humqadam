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
?>
<footer>
  <section>
    <table border="0" cellspacing="0" cellpadding="0" width="1200" align="center">
      <tr valign="top">
        <td width="20%">
          <img src="images/humqadam.svg" height="90" alt="" title="" /><br />
          <br />
          House No. 3, Street 11,<br />
          F-7/2, Islamabad,<br />
          Pakistan<br />
          <br />
          <b>Phone:</b> +92 51 8485050<br />
          <b>Email:</b> <a href="mailto:info@humqadam.pk">info@humqadam.pk</a><br />
          <b>Web:</b> <a href="http://www.humqadam.pk" target="_blank">www.humqadam.pk</a><br />
        </td>

        <td width="45%">
          <img src="images/imc.png" height="90" alt="" title="" /><br />
          <br />
          <p>IMC Worldwide is an international development consultancy that partners with local communities, governments, international development agencies, NGOs, and the private sector to address some of the world's primary development challenges.</p>
          <p>IMC has recently been awarded a project to develop a disaster risk insurance framework for disaster-prone communities in Pakistan. Previous projects have included the rehabilitation of Kotri Barrage, and carrying out the Pakistan 4th Annual Earthquake Review.</p>
        </td>

        <td width="7%"></td>

        <td width="28%">
          <h4>IMC Partnerships</h4>
          <img src="images/partnerships.png" width="300" alt="" title="" style="border:solid 1px #aaaaaa;" />
        </td>
      </tr>
    </table>
  </section>


  <div id="Copyright">
    <div>
      <span class="fRight">Copyright <?= date("Y") ?> &copy; <?= $sCopyright ?></span>
<?
	$sSQL = "SELECT id, title, sef_url FROM tbl_web_pages WHERE FIND_IN_SET('F', placements) AND status='P' $sNavSQL ORDER BY position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iPage   = $objDb->getField($i, "id");
		$sPage   = $objDb->getField($i, "title");
		$sSefUrl = $objDb->getField($i, "sef_url");
?>
      <a href="<?= getPageUrl($iPage, $sSefUrl) ?>"<?= (($iPageId == $iPage) ? ' class="selected"' : '') ?>><?= $sPage ?></a><?= (($i < ($iCount - 1)) ? " | " : "") ?>
<?
	}
?>
    </div>
  </div>
</footer>


<div id="Ajax">
  <img src="images/processing.gif" width="64" height="64" alt="" title="" />
  <span>Processing your request ...</span>
</div>


<div id="BackToTop"></div>

<?
	if ($sFooterCode != "" && @strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
		print $sFooterCode;
?>
