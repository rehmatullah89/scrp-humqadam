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

	
	$iSorId     = IO::intValue("SorId");
	
        $iSchool    = getDbValue("school_id", "tbl_sors", "id='$iSorId'");
	$sApp       = getDbValue("app", "tbl_sors", "id='$iSorId'");
	$sSorStatus = getDbValue("status", "tbl_sors", "id='$iSorId'");
        $sDangerous = getDbValue("dangerous", "tbl_schools", "id='$iSchool'");
        $iProvince  = getDbValue("province_id", "tbl_schools", "id='$iSchool'");
        $sType      = getDbValue("t.type", "tbl_schools s, tbl_school_types t", "s.type_id=t.id AND s.id='$iSchool'");

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
			 FROM tbl_sor_details sd, tbl_sor_sections ss
			 WHERE sd.section_id=ss.id AND sd.sor_id='$iSorId'
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

                if($iSection == 3){
                   if($sDangerous != 'Y' && $iProvince != 2 && strpos(strtolower($sType), 'hss') !== true)
                       continue;
                }
                
		$sInfo = ("<b>Created By:</b><br />{$sCreatedBy}<br />".formatDate($sCreatedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");

		if ($sCreatedAt != $sModifiedAt)
			$sInfo .= ("<br /><b>Modified By:</b><br />{$sModifiedBy}<br />".formatDate($sModifiedAt, "{$_SESSION['DateFormat']} {$_SESSION['TimeFormat']}")."<br />");
?>
		        <tr id="<?= $iId ?>">
		          <td class="position"><?= str_pad($iSection, 2, '0', STR_PAD_LEFT) ?></td>
		          <td><?= $sSection ?></td>
		          <td><?= (($sStatus == "C") ? "Completed" : "In-Complete") ?></td>

		          <td>
		            <img class="icon details" sor="<?= $iSorId ?>" section="<?= $iSection ?>" src="images/icons/info.png" alt="" title="<?= $sInfo ?>" />
<?
		if ($sUserRights["Edit"] == "Y") //&& ($sSorStatus == "C" || $sApp != "Y")
		{
?>
					<img class="icnEdit" sor="<?= $iSorId ?>" section="<?= $iSection ?>" src="images/icons/edit.gif" alt="Edit" title="Edit" />
<?
		}
?>
					<img class="icnView" sor="<?= $iSorId ?>" section="<?= $iSection ?>" src="images/icons/view.gif" alt="View" title="View" />
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