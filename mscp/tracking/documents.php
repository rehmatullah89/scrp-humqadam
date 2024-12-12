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


	if ($_POST)
		@include("save-document.php");
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
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/documents.js"></script>
</head>

<body>

<div id="MainDiv">

<!--  Header Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/header.php");
?>
<!--  Header Section Ends Here  -->


<!--  Navigation Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/navigation.php");
?>
<!--  Navigation Section Ends Here  -->


<!--  Body Section Starts Here  -->
  <div id="Body">
<?
	@include("{$sAdminDir}includes/breadcrumb.php");
?>

    <div id="Contents">
      <input type="hidden" id="OpenTab" value="<?= (($_POST && $bError == true) ? 1 : 0) ?>" />
<?
	@include("{$sAdminDir}includes/messages.php");
?>

      <div id="PageTabs" rel="<?= $_SERVER['REQUEST_URI'] ?>">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Documents</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Document</a></li>
<?
	}
	
	
	$sDocumentsSQL = "FIND_IN_SET(district_id, '{$_SESSION['AdminDistricts']}')";

	if ($_SESSION["AdminSchools"] != "")
		$sDocumentsSQL .= " AND FIND_IN_SET(school_id, '{$_SESSION['AdminSchools']}') ";	
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Docuement?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Document?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Docuements?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Documents?<br />
	      </div>
		  
		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_documents', $sDocumentsSQL) ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
				  <th width="20%">School</th>
				  <th width="10%">Code</th>
                                  <th width="10%">District</th>
				  <th width="15%">Document Type</th>
				  <th width="15%">User</th>
				  <th width="10%">Date Time</th>
			      <th width="15%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	if ($iTotalRecords <= 50)
	{
		$sDistrictsList = getList("tbl_districts", "id", "name");
		
		
		$sDocumentsSQL = " AND FIND_IN_SET(d.district_id, '{$_SESSION['AdminDistricts']}')";

		if ($_SESSION["AdminSchools"] != "")
			$sDocumentsSQL .= " AND FIND_IN_SET(d.school_id, '{$_SESSION['AdminSchools']}') ";

		
		$sSQL = "SELECT d.id, d.created_at, d.modified_at,
                        s.code, s.name, s.district_id,
                                                (SELECT title FROM tbl_document_types WHERE id=d.type_id) AS _DocType,
						(SELECT name FROM tbl_admins WHERE id=d.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=d.modified_by) AS _ModifiedBy
		         FROM tbl_documents d, tbl_schools s
		         WHERE d.school_id=s.id $sDocumentsSQL
                         ORDER BY d.id";
                $objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId         = $objDb->getField($i, "id");
			$sDocType    = $objDb->getField($i, "_DocType");
			$sSchool     = $objDb->getField($i, "name");
			$sCode       = $objDb->getField($i, "code");
			$iDistrict   = $objDb->getField($i, "district_id");
			$sCreatedAt  = $objDb->getField($i, "created_at");
			$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt = $objDb->getField($i, "modified_at");
			$sModifiedBy = $objDb->getField($i, "_ModifiedBy");


			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 5, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSchool ?></td>
                          <td><?= $sCode ?></td>
                          <td><?= $sDistrictsList[$iDistrict] ?></td>
                          <td><?= $sDocType ?></td>
                          <td><?= $sCreatedBy ?></td>
                          <td><?= formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}") ?></td>

		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnEdit" id="<?= $iId ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
			}
			
			if ($sUserRights["Delete"] == "Y")
			{
?>
					<img class="icnDelete" id="<?= $iId ?>" src="images/icons/delete.gif" alt="Delete" title="Delete" />
<?
			}
?>
					<img class="icnView" id="<?= $iId ?>" src="images/icons/view.gif" alt="View" title="View" />
                        </td>
		      </tr>
<?
		}
	}
?>
	          </tbody>
            </table>
		  </div>

	      <div id="SelectButtons"<?= (($iTotalRecords > 5 && $sUserRights["Delete"] == "Y") ? '' : ' class="hidden"') ?>>
	        <div class="br10"></div>

	        <div align="right">
		      <button id="BtnSelectAll">Select All</button>
		      <button id="BtnSelectNone">Clear Selection</button>
		    </div>
	      </div>
		</div>


<?
	if ($sUserRights["Add"] == "Y")
	{
		$iSchool          = getDbValue("id", "tbl_schools", ("code='".IO::strValue("txtCode")."'"));
                $sDocumentTypes = getList("tbl_document_types", "id", "title", "status='A'");
                
?>
            <div id="tabs-2">
              <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" enctype="multipart/form-data">
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
		foreach ($sDocumentTypes as $iDocTypeId => $sDocType)
		{
?>
			    <option value="<?= $iDocTypeId ?>"<?= ((IO::strValue("ddDocType") == $sDocType) ? ' selected' : '') ?>><?= $sDocType ?></option>
<?
		}
?>    
			  </select>
                        </div>

		    <div class="br10"></div>
			
		    <label for="txtCode">EMIS Code</label>
		    <div><input type="text" name="txtCode" id="txtCode" value="<?= IO::strValue("txtCode") ?>" maxlength="10" size="20" class="textbox" /></div>

                    <div class="br10"></div>

                    <label for="filePicture">Picture</label>
                    <div><input type="file" name="filePicture" id="filePicture" value="<?= IO::strValue('filePicture') ?>" size="50" class="textbox" /></div>

                    <div class="br10"></div>

                    <label for="fileDocument">Document</label>
                    <div><input type="file" name="fileDocument" id="fileDocument" value="<?= IO::strValue('fileDocument') ?>" size="50" class="textbox" /></div>

                    <div class="br10"></div>

                    <label for="txtDate">Date</label>
                    <div class="date"><input type="text" name="txtDate" id="txtDate" value="<?= IO::strValue('txtDate') ?>" maxlength="10" size="10" class="textbox" readonly /></div>

		    <div class="br10"></div>
			
		    <label for="txtComments">Comments</label>
                    <div><textarea name="txtComments" id="txtComments" cols="50" rows="9"><?=IO::strValue("txtComments")?></textarea></div>
                    
		    <br />
		    <button id="BtnSave">Save Document</button>
		    <button id="BtnReset">Clear</button>
                    </td>
                    <td>
                        <div id="Files" style="width:98%; height:300px;">Loading ...</div>
                    </td>
            </tr>
          </table>      
		  </form>
	    </div>
<?
	}
?>
	  </div>

    </div>
  </div>
<!--  Body Section Ends Here  -->


<!--  Footer Section Starts Here  -->
<?
	@include("{$sAdminDir}includes/footer.php");
?>
<!--  Footer Section Ends Here  -->

</div>

</body>
</html>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>