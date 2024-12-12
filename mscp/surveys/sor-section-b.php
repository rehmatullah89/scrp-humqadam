<?
        $sFacilitiesList = getList("tbl_sor_facilities", "id", "name", "status='A' AND position>0", "position");
?>
        <h3>SECTION B - REQUIRED FOR DIS-AGREEMENT WITH BASELINE RESULTS</h3>
        <div class="grid"  style="padding: 5px;">
           <table border="1" cellpadding="5" cellspacing="0" style="text-align: center; border-color: #ffffff; width: 100%">    
    		<tr valign="top" class="header">
			<th style="padding: 5px;" width="40%">DETAILS</th>
                        <th style="padding: 5px;" width="10%">Numbers</th>
                        <th style="padding: 5px;" width="15%">Space Available?</th>
                        <th style="padding: 5px;" width="35%">Comments</th>
		</tr>

<?
        $i=0;
        foreach($sFacilitiesList as $iFacilityId => $sFacility){

            $sSQL = "SELECT * FROM tbl_sor_section_b_details WHERE sor_id='$iSorId' AND facility_id='$iFacilityId'";
            $objDb->query($sSQL);
            $iNumbers       = $objDb->getField(0, "numbers");
            $sSpaceAvailable= $objDb->getField(0, "space_available");
            $sComments    = $objDb->getField(0, "comments");
?>
            <tr class="<?=($i%2==0?'even':'odd')?>">
                <td><b>No. of <?=$sFacility?></b></td><td><input type="text" style="width: 95%;" value="<?=$iNumbers?>" name="Numbers_<?=$iFacilityId?>"></td><td> <input type="radio" name="facility_<?=$iFacilityId?>" value="Y" <?= ($sSpaceAvailable == 'Y'?'checked':'')?>> Yes &nbsp;&nbsp;  <input type="radio" name="facility_<?=$iFacilityId?>" value="N" <?= ($sSpaceAvailable == 'N'?'checked':'')?>> No<br></td><td><input type="text" style="width: 98%;" value="<?=$sComments?>" name="comments_<?=$iFacilityId?>"></td>    
            </tr>
<?
            $i++;
        }
        


?>            
	  </table>
          <br/><br/>
          <span><b>Comments</b><br><textarea style="padding: 10px;" name="comments" rows="4" cols="70"><?=  getDbValue("comments", "tbl_sor_section_b","sor_id='$iSorId'")?></textarea></span>
        </div>
        <div class="br10"></div>
          <div class="br10"></div>

           <h3>SOR Documents</h3>

        <div class="grid" style="padding: 20px;">
            <div style="padding: 2px; font-size: 12px; font-weight: bold;">Upload Files &nbsp;&nbsp;<span style="color: grey; font-size: 9px;">(Jpeg, Tiff, Gif, Bmp, Png & Pdf Only)</span></div>    
                <div style="float: left; width: 33%;">

                   <div style="display: inline-block; margin-left: 5px;">
                    <label for="fileFixedSection">
                        <img src="../mscp/images/icons/upload.png"/>
                    </label>

                    <input name="fileFixedSection[]" id="fileFixedSection" multiple="multiple" type="file" class="textbox" value="" maxlength="200" size="40" />
                </div>
                </div>
            
            <div style="float: left; width: 33%; margin-top:-15px;">
                
<?
	$sSQL = "SELECT id, document FROM tbl_sor_documents WHERE sor_id='$iSorId' AND section_id='$iSectionId'  AND document NOT LIKE '%.pdf%'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
        <div style="padding: 2px; font-size: 12px; font-weight: bold;">SOR Files</div>                                   
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iPicture = $objDb->getField($i, "id");
                        $sPicture = $objDb->getField($i, "document");
                        $exts = explode('.', $sPicture);
                        $extension = end($exts);
                        if($extension != 'pdf')
                        {
?>
	    <li>
		  <a href="<?= (SITE_URL.SORS_DOC_DIR.$sPicture) ?>" class="colorbox"><?= substr($sPicture, strlen("{$iSorId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-sor-document.php?SorId=<?= $iSorId ?>&SectionId=<?= $iSectionId ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
<?
			}
?>
		</li>
<?
                        }
		}
?>
	  </ul>
	</div>	
<?
	}
?>
            </div>
    <div style="float: left; width: 33%; margin-top:-15px;">
<?
	$sSQL = "SELECT id, document FROM tbl_sor_documents WHERE sor_id='$iSorId' AND section_id='$iSectionId' AND document LIKE '%.pdf%'";
	$objDb->query($sSQL);

	$iCount = $objDb->getCount( );
	
	if ($iCount > 0)
	{		
?>
        <div style="padding: 2px; font-size: 12px; font-weight: bold;">Download Pdf Files</div>                           
	<div>
	  <ul>	
<?
		for ($i = 0; $i < $iCount; $i ++)
		{
			$iPicture = $objDb->getField($i, "id");
                        $sPicture = $objDb->getField($i, "document");
                        $exts = explode('.', $sPicture);
                        $extension = end($exts);
                        if($extension == 'pdf')
                        {
?>
	    <li>
                  <a href="<?= (SITE_URL.SORS_DOC_DIR.$sPicture) ?>" target="_blank"><?= substr($sPicture, strlen("{$iSorId}-")) ?></a>
<?
			if ($sUserRights["Edit"] == "Y" && @strpos($_SERVER['REQUEST_URI'], "edit-") !== FALSE)
			{
?>
		  &nbsp; (<a href="<?= $sCurDir ?>/delete-sor-document.php?SorId=<?= $iSorId ?>&SectionId=<?= $iSectionId ?>&PictureId=<?= $iPicture ?>&Picture=<?= $sPicture ?>"><b>x</b></a>)
<?
			}
?>
		</li>
<?
                        }
		}
?>
	  </ul>
	</div>	
<?
	}
?>
            </div>
            <div class="br10"></div>
</div>
