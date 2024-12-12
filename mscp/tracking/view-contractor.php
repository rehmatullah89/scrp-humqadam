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

	@require_once("../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	$iContractorId = IO::intValue("ContractorId");

	$sSQL = "SELECT * FROM tbl_contractors WHERE id='$iContractorId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );

	$sCompany    = $objDb->getField(0, "company");
	$sAddress    = $objDb->getField(0, "address");
	$sCity       = $objDb->getField(0, "city");
	$sLogo       = $objDb->getField(0, "logo");	
	$sIndividual = $objDb->getField(0, "individual");
	$sTaxFiler   = $objDb->getField(0, "tax_filer");
	$iInvoicing  = $objDb->getField(0, "invoicing");
	$sTitle      = $objDb->getField(0, "title");
	$sFirstName  = $objDb->getField(0, "first_name");
	$sMiddleName = $objDb->getField(0, "middle_name");
	$sLastName   = $objDb->getField(0, "last_name");
	$sPhone      = $objDb->getField(0, "phone");
	$sMobile     = $objDb->getField(0, "mobile");
	$sEmail      = $objDb->getField(0, "email");
	$sStatus     = $objDb->getField(0, "status");
	$sPicture    = $objDb->getField(0, "picture");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr valign="top">
		<td width="450">	
		  <label for="txtCompany">Company</label>
		  <div><input type="text" name="txtCompany" id="txtCompany" value="<?= formValue($sCompany) ?>" maxlength="100" size="44" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtAddress">Address <span>(Optional)</span></label>
		  <div><textarea name="txtAddress" id="txtAddress" rows="3" cols="42"><?= $sAddress ?></textarea></div>

		  <div class="br10"></div>

		  <label for="txtCity">City</label>
		  <div><input type="text" name="txtCity" id="txtCity" value="<?= formValue($sCity) ?>" maxlength="50" size="25" class="textbox" /></div>

		  <div class="br10"></div>
	  
		  <label for="ddIndividual">Individual</label>

		  <div>
			<select name="ddIndividual" id="ddIndividual">
			  <option value="N"<?= (($sIndividual == 'N') ? ' selected' : '') ?>>No</option>
			  <option value="Y"<?= (($sIndividual == 'Y') ? ' selected' : '') ?>>Yes</option>
			</select>
		  </div>
		  
		  <div class="br10"></div>

		  <label for="ddTaxFiler">Tax Filer</label>

		  <div>
			<select name="ddTaxFiler" id="ddTaxFiler">
			  <option value="Y"<?= (($sTaxFiler == 'Y') ? ' selected' : '') ?>>Yes</option>
			  <option value="N"<?= (($sTaxFiler == 'N') ? ' selected' : '') ?>>No</option>
			</select>
		  </div>
		  
		  <div class="br10"></div>

		  <label for="ddInvoicing">Invoicing Scenario</label>

		  <div>
			<select name="ddInvoicing" id="ddInvoicing">
			  <option value="1"<?= (($iInvoicing == 1) ? ' selected' : '') ?>>Scenario 1</option>
			  <option value="2"<?= (($iInvoicing == 2) ? ' selected' : '') ?>>Scenario 2</option>
			  <option value="3"<?= (($iInvoicing == 3) ? ' selected' : '') ?>>Scenario 3</option>
			</select>
		  </div>
  
		  <div class="br10"></div>

		  <label for="ddStatus">Status</label>

		  <div>
		    <select name="ddStatus" id="ddStatus">
			  <option value="A"<?= (($sStatus == 'A') ? ' selected' : '') ?>>Active</option>
			  <option value="I"<?= (($sStatus == 'I') ? ' selected' : '') ?>>In-Active</option>
		    </select>
		  </div>

<?
	if ($sLogo != "")
	{
?>
		  <div style="width:154px; margin-top:15px;">
		    <div style="border:solid 1px #888888; padding:1px;"><img src="<?= (SITE_URL.CONTRACTORS_IMG_DIR.'logos/'.$sLogo) ?>" width="150" alt="" title="" /></div>
		  </div>
<?
	}
?>
		</td>

		<td>
		  <h3 style="width:350px; margin:0px 0px 15px 0px;">Contact person</h3>
		  
		  <label for="ddTitle">Title</label>

		  <div>
			<select name="ddTitle" id="ddTitle">
			  <option value="Mr"<?= (($sTitle == 'Mr') ? ' selected' : '') ?>>Mr</option>
			  <option value="Ms"<?= (($sTitle == 'Ms') ? ' selected' : '') ?>>Ms</option>
			  <option value="Mrs"<?= (($sTitle == 'Mrs') ? ' selected' : '') ?>>Mrs</option>
			</select>
		  </div>

		  <div class="br10"></div>

		  <label for="txtFirstName">First Name</label>
		  <div><input type="text" name="txtFirstName" id="txtFirstName" value="<?= formValue($sFirstName) ?>" maxlength="50" size="35" class="textbox" /></div>
		  
		  <div class="br10"></div>

		  <label for="txtMiddleName">Middle Name</label>
		  <div><input type="text" name="txtMiddleName" id="txtMiddleName" value="<?= formValue($sMiddleName) ?>" maxlength="50" size="35" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtLastName">Last Name</label>
		  <div><input type="text" name="txtLastName" id="txtLastName" value="<?= formValue($sLastName) ?>" maxlength="50" size="35" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtPhone">Phone</label>
		  <div><input type="text" name="txtPhone" id="txtPhone" value="<?= $sPhone ?>" maxlength="25" size="25" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtMobile">Mobile <span>(Optional)</span></label>
		  <div><input type="text" name="txtMobile" id="txtMobile" value="<?= $sMobile ?>" maxlength="25" size="25" class="textbox" /></div>

		  <div class="br10"></div>

		  <label for="txtEmail">Email <span>(Optional)</span></label>
		  <div><input type="text" name="txtEmail" id="txtEmail" value="<?= $sEmail ?>" maxlength="100" size="35" class="textbox" /></div>
<?
	if ($sPicture != "")
	{
?>
		  <div style="width:154px; margin-top:15px;">
		    <div style="border:solid 1px #888888; padding:1px;"><img src="<?= (SITE_URL.CONTRACTORS_IMG_DIR.'persons/'.$sPicture) ?>" width="150" alt="" title="" /></div>
		  </div>
<?
	}
?>
		</td>  
	  </tr>
	</table>	  
  </form>
</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>