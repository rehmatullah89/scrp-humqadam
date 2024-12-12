<?
        $sSQL = "SELECT * FROM tbl_sor_section_d WHERE sor_id='$iSorId'";
	$objDb->query($sSQL);

	$sFloorHeight  = $objDb->getField(0, "floor_height");
        $sOther        = $objDb->getField(0, "other");
        $sComments     = $objDb->getField(0, "comments");
?>            
	<h3>SECTION D - OTHER</h3>

        <div class="grid" style="padding: 20px;">

            <span><b>Agreed New Height Of Buildings? </b><input type="radio" name="floor_height" value="Y" <?= ($sFloorHeight == 'Y'?'checked':'')?>> Yes &nbsp;  <input type="radio" name="floor_height" value="N" <?= ($sFloorHeight == 'N'?'checked':'')?>> No</span><br/><br/>
            <span><b>Other: </b><input type="text" class="textbox" width="80%" size="50" name="other" value="<?=$sOther?>"></span><br/><br/>
            <span><b>Comments</b><br><textarea style="padding: 10px;" name="comments" rows="4" cols="70"><?=$sComments?></textarea></span>
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
                </div><div class="br10"></div>
                <!--  <a id="uploadFile" section="<? //$iSectionId?>" sor="<? //$iSorId?>">Upload Document</a> -->
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
