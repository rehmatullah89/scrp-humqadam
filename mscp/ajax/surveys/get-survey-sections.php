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

	header("Expires: Tue, 01 Jan 2010 12:12:12 GMT");
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	@require_once("../../requires/common.php");

	$objDbGlobal = new Database( );
	$objDb       = new Database( );

	
	$iSurveyId     = IO::intValue("SurveyId");
	
	$sApp          = getDbValue("app", "tbl_surveys", "id='$iSurveyId'");
	$sSurveyStatus = getDbValue("status", "tbl_surveys", "id='$iSurveyId'");
?>
		  <div class="dataGrid ex_highlight_row">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tblSectionsData" id="SectionsGrid">
			  <thead>
			    <tr>
			      <th width="5%" align="left">#</th>
			      <th width="75%" align="left">Section</th>			  
				  <th width="8%" align="left">Status</th>
			      <th width="12%" align="left">Options</th>
			    </tr>
			  </thead>

			  <tbody>
<?
	$sSQL = "SELECT ss.id, ss.name, sd.status, sd.created_at, sd.modified_at,
					(SELECT name FROM tbl_admins WHERE id=sd.created_by) AS _CreatedBy,
					(SELECT name FROM tbl_admins WHERE id=sd.modified_by) AS _ModifiedBy
			 FROM tbl_survey_details sd, tbl_survey_sections ss
			 WHERE sd.section_id=ss.id AND sd.survey_id='$iSurveyId'
			 ORDER BY ss.position";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );

	for ($i = 0; $i < $iCount; $i ++)
	{
		$iSection    = $objDb->getField($i, "id");
		$sSection    = $objDb->getField($i, "name");
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
		          <td class="position"><?= str_pad($iSection, 2, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSection ?></td>
		          <td><?= (($sStatus == "C") ? "Completed" : "In-Complete") ?></td>

		          <td>
		            <img class="icon details" survey="<?= $iSurveyId ?>" section="<?= $iSection ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
		if ($sUserRights["Edit"] == "Y" && ($sSurveyStatus == "C" || $sApp != "Y"))
		{
?>
					<img class="icnEdit" survey="<?= $iSurveyId ?>" section="<?= $iSection ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}
?>
					<img class="icnView" survey="<?= $iSurveyId ?>" section="<?= $iSection ?>" src="images/icons/view.gif" alt="View" title="View" />
		          </td>
		        </tr>
<?
	}
?>
	          </tbody>
            </table>
		  </div>
<?
	$objDb->close( );
	$objDbGlobal->close( );

	@ob_end_flush( );
?>