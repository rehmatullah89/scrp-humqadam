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
		@include("save-question.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?
	@include("{$sAdminDir}includes/meta-tags.php");
?>
  <script type="text/javascript" src="scripts/<?= $sCurDir ?>/questions.js"></script>
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

      <div id="PageTabs">
	    <ul>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-1"><b>Questions</b></a></li>
<?
	if ($sUserRights["Add"] == "Y")
	{
?>
	      <li><a href="<?= $_SERVER['REQUEST_URI'] ?>#tabs-2">Add New Question</a></li>
<?
	}
?>
	    </ul>


	    <div id="tabs-1">
	      <div id="GridMsg" class="hidden"></div>

	      <div id="ConfirmDelete" title="Delete Question?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete this Question Record?<br />
	      </div>

	      <div id="ConfirmMultiDelete" title="Delete Questions?" class="hidden dlgConfirm">
	        <span class="ui-icon ui-icon-trash"></span>
	        Are you sure, you want to Delete the selected Question Records?<br />
	      </div>


		  <div class="dataGrid ex_highlight_row">
		    <input type="hidden" id="TotalRecords" value="<?= $iTotalRecords = getDbValue('COUNT(1)', 'tbl_survey_questions') ?>" />
		    <input type="hidden" id="RecordsPerPage" value="<?= $_SESSION["PageRecords"] ?>" />

			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblData" id="DataGrid">
			  <thead>
			    <tr>
			      <th width="5%">#</th>
			      <th width="40%">Question</th>
			      <th width="28%">Section</th>
				  <th width="7%">Position</th>
			      <th width="8%">Status</th>
			      <th width="12%">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSectionsList = getList("tbl_survey_sections", "id", "name", "type='V'", "position");
	
	
	if ($iTotalRecords <= 50)
	{
		$sSQL = "SELECT id, section_id, question, position, status, created_at, modified_at,
						(SELECT name FROM tbl_admins WHERE id=tbl_survey_questions.created_by) AS _CreatedBy,
						(SELECT name FROM tbl_admins WHERE id=tbl_survey_questions.modified_by) AS _ModifiedBy
		         FROM tbl_survey_questions
		         ORDER BY id";
		$objDb->query($sSQL);

		$iCount = $objDb->getCount( );

		for ($i = 0; $i < $iCount; $i ++)
		{
			$iId         = $objDb->getField($i, "id");
			$iSection    = $objDb->getField($i, "section_id");
			$sQuestion   = $objDb->getField($i, "question");
			$iPosition   = $objDb->getField($i, "position");
			$sStatus     = $objDb->getField($i, "status");
			$sCreatedAt  = $objDb->getField($i, "created_at");
			$sCreatedBy  = $objDb->getField($i, "_CreatedBy");
			$sModifiedAt = $objDb->getField($i, "modified_at");
			$sModifiedBy = $objDb->getField($i, "_ModifiedBy");


			$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

			if ($sCreatedAt != $sModifiedAt)
				$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iId, 3, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sQuestion ?></td>
		          <td><?= $sSectionsList[$iSection] ?></td>
				  <td><?= $iPosition ?></td>
		          <td><?= (($sStatus == "A") ? "Active" : "In-Active") ?></td>

		          <td>
		            <img class="icon details" id="<?= $iId ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
			if ($sUserRights["Edit"] == "Y")
			{
?>
					<img class="icnToggle" id="<?= $iId ?>" src="images/icons/<?= (($sStatus == 'A') ? 'success' : 'error') ?>.png" alt="Toggle Status" title="Toggle Status" />
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
		$sUsersList = getList("tbl_admins", "id", "name", "status='A'");
?>
		<div id="tabs-2">
		  <form name="frmRecord" id="frmRecord" method="post" action="<?= @htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
		    <input type="hidden" name="DuplicateQuestion" id="DuplicateQuestion" value="0" />
			<div id="RecordMsg" class="hidden"></div>

		    <label for="ddSection">Section</label>

		    <div>
			  <select name="ddSection" id="ddSection">
			    <option value=""></option>
<?
		foreach ($sSectionsList as $iSection => $sSection)
		{
?>
				<option value="<?= $iSection ?>"<?= ((IO::intValue('ddSection') == $iSection) ? ' selected' : '') ?>><?= $sSection ?></option>
<?
		}
?>
			  </select>
		    </div>

		    <div class="br10"></div>

		    <label for="ddType">Type</label>

		    <div>
			  <select name="ddType" id="ddType">
			    <option value=""></option>
			    <option value="YN"<?= ((IO::strValue("ddType") == "YN") ? ' selected' : '') ?>>Yes / No</option>
				<option value="SS"<?= ((IO::strValue("ddType") == "SS") ? ' selected' : '') ?>>Single Selection</option>
				<option value="MS"<?= ((IO::strValue("ddType") == "MS") ? ' selected' : '') ?>>Multi Selection</option>
				<option value="SL"<?= ((IO::strValue("ddType") == "SL") ? ' selected' : '') ?>>Single Line Text</option>
				<option value="ML"<?= ((IO::strValue("ddType") == "ML") ? ' selected' : '') ?>>Multi Line Text</option>
			  </select>
		    </div>
			
		    <div class="br10"></div>
			
		    <label for="txtQuestion">Question</label>
		    <div><textarea name="txtQuestion" id="txtQuestion" rows="3" style="width:500px;"><?= IO::strValue('txtQuestion') ?></textarea></div>
			
			<div id="InputType"<?= ((IO::strValue("ddType") == "SL") ? '' : ' class="hidden"') ?>>
		      <div class="br10"></div>

		      <label for="ddInputType">InputType</label>

		      <div>
			    <select name="ddInputType" id="ddInputType">
			      <option value="T"<?= ((IO::strValue("ddInputType") == "T") ? ' selected' : '') ?>>Text</option>
				  <option value="N"<?= ((IO::strValue("ddInputType") == "N") ? ' selected' : '') ?>>Number</option>
				  <option value="D"<?= ((IO::strValue("ddInputType") == "D") ? ' selected' : '') ?>>Decimal Number</option>
				  <option value="A"<?= ((IO::strValue("ddInputType") == "A") ? ' selected' : '') ?>>Alphabets Only</option>
				  <option value="E"<?= ((IO::strValue("ddInputType") == "E") ? ' selected' : '') ?>>Email Address</option>
				  <option value="P"<?= ((IO::strValue("ddInputType") == "P") ? ' selected' : '') ?>>Phone Number</option>				  
			    </select>
		      </div>			
			</div>  
			
			<div id="Options"<?= ((IO::strValue("ddType") == "SS" || IO::strValue("ddType") == "MS") ? '' : ' class="hidden"') ?>>
		      <div class="br10"></div>
			
		      <label for="txtOptions">Options <span>(One per Line)</span></label>
		      <div><textarea name="txtOptions" id="txtOptions" rows="5" style="width:500px;"><?= IO::strValue('txtOptions') ?></textarea></div>
			</div>

		    <div class="br10"></div>
			
			<label for="cbOther" class="noPadding"><input type="checkbox" name="cbOther" id="cbOther" value="Y" <?= ((IO::strValue('cbOther') == 'Y') ? 'checked' : '') ?> /> Show Textbox for "Other" Option</label>

		    <div class="br10"></div>
			
			<label for="cbPicture" class="noPadding"><input type="checkbox" name="cbPicture" id="cbPicture" value="Y" <?= ((IO::strValue('cbPicture') == 'Y') ? 'checked' : '') ?> /> Show Field for "Picture" Attachment</label>
			
		    <div class="br10"></div>

		    <label for="ddMandatory">Mandatory</label>

		    <div>
			  <select name="ddMandatory" id="ddMandatory">
			    <option value="Y"<?= ((IO::strValue("ddMandatory") == "Y") ? ' selected' : '') ?>>Yes</option>
				<option value="N"<?= ((IO::strValue("ddMandatory") == "N") ? ' selected' : '') ?>>No</option>
			  </select>
		    </div>

			<div class="br10"></div>

			<label for="txtLink">Linked with Question <span>(ID/Option)</span></label>
			
			<div>
			  <input type="text" name="txtLink" id="txtLink" value="<?= IO::strValue("txtLink") ?>" maxlength="3" size="5" class="textbox" />

			  <select name="ddLink" id="ddLink">
			    <option value=""></option>
			    <option value="Y"<?= ((IO::strValue('ddLink') == "Y") ? ' selected' : '') ?>>Yes</option>
			    <option value="N"<?= ((IO::strValue('ddLink') == "N") ? ' selected' : '') ?>>No</option>				
			  </select>			
			</div>
			
			<div class="br10"></div>
			
			<label for="txtHint">Guidance Note <span>(Optional)</span></label>
			<div><textarea name="txtHint" id="txtHint" rows="3" style="width:500px;"><?= IO::strValue('txtHint') ?></textarea></div>
			
			<div class="br10"></div>

			<label for="txtPosition">Position <span>(Optional)</span></label>
			<div><input type="text" name="txtPosition" id="txtPosition" value="<?= IO::strValue("txtPosition") ?>" maxlength="10" size="10" class="textbox" /></div>

		    <div class="br10"></div>

		    <label for="ddStatus">Status</label>

		    <div>
			  <select name="ddStatus" id="ddStatus">
			    <option value="A"<?= ((IO::strValue('ddStatus') == 'A') ? ' selected' : '') ?>>Active</option>
			    <option value="I"<?= ((IO::strValue('ddStatus') == 'I') ? ' selected' : '') ?>>In-Active</option>
			  </select>
		    </div>
			
		    <br />
		    <button id="BtnSave">Save Question</button>
		    <button id="BtnReset">Clear</button>
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