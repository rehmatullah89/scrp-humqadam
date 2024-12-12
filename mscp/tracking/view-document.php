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
	$objDb2      = new Database( );
	$objDb3      = new Database( );
	$objDb4      = new Database( );

	if ($sUserRights["Edit"] != "Y")
		exitPopup(true);


	$iDocumentId = IO::intValue("DocumentId");
	$iIndex        = IO::intValue("Index");

	if ($_POST)
		@include("update-document.php");


	$sSQL = "SELECT * FROM tbl_documents WHERE id='$iDocumentId'";
	$objDb->query($sSQL);

	if ($objDb->getCount( ) != 1)
		exitPopup( );
        
        $iDocType    = $objDb->getField(0, "type_id");
        $sDate       = $objDb->getField(0, "date");
        $iSchool     = $objDb->getField(0, "school_id");
        $iDistrict   = $objDb->getField(0, "district_id");
        $sComments   = $objDb->getField(0, "comments");
        $iCreatedBy  = $objDb->getField(0, "created_by");
        $sCreatedAt  = $objDb->getField(0, "created_at");
        $iModifiedBy = $objDb->getField(0, "modified_by");
        $sModifiedAt = $objDb->getField(0, "modified_at");
        
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <link type="text/css" rel="stylesheet" href="plugins/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css" />

  <script type="text/javascript" src="plugins/plupload/plupload.full.min.js"></script>
  <script type="text/javascript" src="plugins/plupload/jquery.ui.plupload/jquery.ui.plupload.js"></script>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/edit-document.js"></script>
</head>

<body class="popupBg">

<div id="PopupDiv">
<?
	@include("{$sAdminDir}includes/messages.php");
?>
  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
	<input type="hidden" name="DocumentId" id="DocumentId" value="<?= $iDocumentId ?>" />
	<input type="hidden" name="Index" value="<?= $iIndex ?>" />
	<input type="hidden" name="DuplicateDocument" id="DuplicateDocument" value="0" />
	<div id="RecordMsg" class="hidden"></div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	  <tr valign="top">
		<td width="600">
                         <div class="br10"></div><div class="br10"></div>        
                        <label for="ddDocType">Document Type</label>
		    
			<div>
			  <select name="ddDocType" id="ddDocType">
			    <option value="">Select Document Type</option>
<?
                $sDocumentTypes = getList("tbl_document_types", "id", "title", "status='A'");
                $sCode = getDbValue("code", "tbl_schools", "id='$iSchool'");
                
		foreach ($sDocumentTypes as $iDocTypeId => $sDocType)
		{
?>
			    <option value="<?= $iDocTypeId ?>"<?= (($iDocType == $iDocTypeId) ? ' selected' : '') ?>><?= $sDocType ?></option>
<?
		}
?>    
			  </select>
                        </div>

		    <div class="br10"></div>
			
		    <label for="txtCode">EMIS Code</label>
		    <div><input type="text" name="txtCode" id="txtCode" value="<?= $sCode ?>" maxlength="10" size="20" class="textbox" /></div>

                     <div class="br10"></div>

                    <label for="filePicture">Picture</label>
                    <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="50" class="textbox" /></div>

                    <div class="br10"></div>

                    <label for="fileDocument">Document</label>
                    <div><input type="file" name="fileDocument" id="fileDocument" value="<?= IO::strValue('fileDocument') ?>" size="50" class="textbox" /></div>

                    <div class="br10"></div>

                    <label for="txtDate">Date</label>
                    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= $sDate ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		    <div class="br10"></div>
			
		    <label for="txtComments">Comments</label>
                    <div><textarea name="txtComments" id="txtComments" cols="50" rows="9"><?=$sComments?></textarea></div>
                    
	
		</td>

		<td>
		  <div id="Files" style="width:98%; height:350px;">Loading ...</div>
<?
	$sSQL = "SELECT * FROM tbl_document_files WHERE document_id='$iDocumentId' ORDER BY id";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	if ($iCount > 0)
	{
?>
		  <h3>All Documents</h3>

		  <ul style="list-style:none; margin:0px; padding:0px;">
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iFile = $objDb->getField($i, "id");
			$sFile = $objDb->getField($i, "file");

                        $count      = $i +1;
			$iPosition  = @strrpos($sFile, '.');
			$sExtension = @substr($sFile, $iPosition);
			$sImages    = array(".jpg", ".jpeg", ".png", ".gif");
?>
		    <li>
<?
			if (@in_array($sExtension, $sImages))
			{
				if (@file_exists($sRootDir.DOCUMENTS_DIR.$sFile))
				{
?>
                                  <a href="<?= (SITE_URL.DOCUMENTS_DIR.$sFile) ?>" class="colorbox">Doc#<?=$count?></a>
<?
				}

			}

			else
			{
?>
		  	  <a href="<?= (SITE_URL.DOCUMENTS_DIR.$sFile) ?>"><?= substr($sFile, strlen("{$iDocumentId}-{$iFile}-")) ?></a>
<?
			}
?>
		    </li>
<?
		}
?>
		  </ul>

		  <div class="br5"></div>
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
	$objDb2->close( );
	$objDb3->close( );
	$objDb4->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>