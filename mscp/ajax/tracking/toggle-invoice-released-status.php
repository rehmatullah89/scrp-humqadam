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
	@require_once("{$sRootDir}requires/fpdf/fpdf.php");
	@require_once("{$sRootDir}requires/fpdi/fpdi.php");	

	$objDbGlobal = new Database( );
	$objDb       = new Database( );
	$objDb2      = new Database( );

	if ($sUserRights["Edit"] != "Y")
	{
		print "info|-|You don't have enough Rights to perform the requested operation.";

		exit( );
	}


	$iInvoiceId = IO::intValue("InvoiceId");

	if ($iInvoiceId > 0)
	{
		$sSQL = "UPDATE tbl_invoices SET released=IF(released='Y', 'N', 'Y'), released_at=IF(released='Y', '0000-00-00 00:00:00', NOW( )), released_by=IF(released='Y', '0', '{$_SESSION['AdminId']}') WHERE id='$iInvoiceId'";

		if ($objDb->execute($sSQL) == true)
		{
			$sSQL = "SELECT * FROM tbl_invoices WHERE id='$iInvoiceId'";
			$objDb->query($sSQL);

			$iContract    = $objDb->getField(0, "contract_id");
			$iSchool      = $objDb->getField(0, "school_id");
			$sInvoiceNo   = $objDb->getField(0, "invoice_no");
			$sDate        = $objDb->getField(0, "date");
			$iAmount      = $objDb->getField(0, "amount");
			$sDetails     = $objDb->getField(0, "details");
			$sInspections = $objDb->getField(0, "inspections");
			$sReleased    = $objDb->getField(0, "released");
			
			if ($sReleased == "Y")
			{
				$sSQL = "SELECT code, name, storey_type, design_type, district_id, province_id FROM tbl_schools WHERE id='$iSchool'";
				$objDb->query($sSQL);

				$sSchool     = $objDb->getField(0, "name");
				$sCode       = $objDb->getField(0, "code");
				$sStoreyType = $objDb->getField(0, "storey_type");
				$sDesignType = $objDb->getField(0, "design_type");
				$iDistrict   = $objDb->getField(0, "district_id");
				$iProvince   = $objDb->getField(0, "province_id");
				
				
				$sSQL = "SELECT site_title, general_name, general_email, date_format, time_format FROM tbl_settings WHERE id='1'";
				$objDb->query($sSQL);

				$sSiteTitle   = $objDb->getField(0, "site_title");
				$sSenderName  = $objDb->getField(0, "general_name");
				$sSenderEmail = $objDb->getField(0, "general_email");
				$sDateFormat  = $objDb->getField(0, "date_format");
				$sTimeFormat  = $objDb->getField(0, "time_format");
				
				
				$sSQL = "SELECT subject, message, status FROM tbl_email_templates WHERE id='6'";
				$objDb->query($sSQL);

				$sSubject = $objDb->getField(0, "subject");
				$sBody    = $objDb->getField(0, "message");


				if ($objDb->getField(0, "status") == "A")
				{
					$sSubject = @str_replace("{SITE_TITLE}", $sSiteTitle, $sSubject);
					$sSubject = @str_replace("{INVOICE_NO}", $sInvoiceNo, $sSubject);
					$sSubject = @str_replace("{EMIS_CODE}", $sCode, $sSubject);
					

					$sBody    = @str_replace("{INVOICE_NO}", $sInvoiceNo, $sBody);
					$sBody    = @str_replace("{CONTRACT}", getDbValue("title", "tbl_contracts", "id='$iContract'"), $sBody);
					$sBody    = @str_replace("{SCHOOL}", $sSchool, $sBody);
					$sBody    = @str_replace("{EMIS_CODE}", $sCode, $sBody);
					$sBody    = @str_replace("{DISTRICT}", getDbValue("name", "tbl_districts", "id='$iDistrict'"), $sBody);
					$sBody    = @str_replace("{INVOICE_AMOUNT}", formatNumber($iAmount, false), $sBody);
					$sBody    = @str_replace("{INVOICE_DATE}", formatDate($sDate, $sDateFormat), $sBody);
					$sBody    = @str_replace("{INVOICE_DETAILS}", $sDetails, $sBody);
					$sBody    = @str_replace("{SITE_TITLE}", $sSiteTitle, $sBody);
					$sBody    = @str_replace("{SITE_URL}", SITE_URL, $sBody);


					$objEmail = new PHPMailer( );

					$objEmail->Subject = $sSubject;
					$objEmail->MsgHTML($sBody);
					$objEmail->SetFrom($sSenderEmail, $sSenderName);

					$objEmail->AddAddress("omer@3-tree.com", "Omer Rauf");
					$objEmail->AddAddress("Isfundiar.Kasuri@humqadam.pk", "Isfundiar Kasuri");
					$objEmail->AddAddress("Imran.Shakir@humqadam.pk", "Imran Shakir");
					$objEmail->AddAddress("Roba.Bashir@humqadam.pk", "Roba Bashir");
					$objEmail->AddAddress("Khalid.Mehmood@humqadam.pk", "Khalid Mehmood");
					$objEmail->AddAddress("Samman.Islam@humqadam.pk", "Samman Islam");
					$objEmail->AddAddress("tim.stiff@imcworldwide.com", "Tim Stiff");


					$bExport = true;
					
					@include("{$sRootDir}mscp/tracking/export-invoice.php");
					

					if (@file_exists($sRootDir.TEMP_DIR."{$sInvoiceNo}.pdf"))
						$objEmail->AddAttachment(($sRootDir.TEMP_DIR."{$sInvoiceNo}.pdf"), "{$sInvoiceNo}.pdf");

					if (@strpos($_SERVER['HTTP_HOST'], "localhost") === FALSE)
						$objEmail->Send( );
					
					
					if (@file_exists($sRootDir.TEMP_DIR."{$sInvoiceNo}.pdf"))
						@unlink($sRootDir.TEMP_DIR."{$sInvoiceNo}.pdf");
				}
			}
			

			print "success|-|The selected Invoice status has been Toggled successfully.";
		}

		else
			print "error|-|An error occured while processing your request, please try again.";
	}

	else
		print "info|-|Inavlid Toggle status request.";


	$objDb->close( );
	$objDb2->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>