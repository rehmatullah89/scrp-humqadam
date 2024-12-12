<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr valign="top">
        <td><h2 style="height:18px;">&nbsp;</h2></td>
	  <td width="1200">
	 
	   	<br />
		<h2 style="padding-left:18px; margin-top: -18px;">Inspections Failure Reasons Report</h2>
		
	    <div id="InspectionFailureReasonChart">Loading Graph...</div>

		<script type="text/javascript">
		<!--
			var objChart2 = new FusionCharts("scripts/FusionCharts/charts/Column3D.swf", "InspectionFailureReasons", "100%", "500", "0", "1");

			objChart2.setXMLData("<chart caption='' bgcolor='ffffff' canvasBgColor='ffffff' numDivLines='10' formatNumberScale='0' showValues='1' showLabels='1' decimals='0' numberSuffix='' chartBottomMargin='5' plotFillAlpha='95' labelDisplay='AUTO' exportEnabled='1' exportShowMenuItem='1' exportAtClient='0' exportHandler='scripts/FusionCharts/PHP/FCExporter.php' exportAction='download' exportFileName='inspection-failure-reasons'>" +
<?
	$sSQL = "SELECT count(1) as _FrCount, fr.reason as _Reason  FROM tbl_inspections i, tbl_failure_reasons fr WHERE i.failure_reason_id = fr.id AND i.failure_reason_id != '0' AND (i.date BETWEEN '$sFromDate' AND '$sToDate') Group By _Reason";
	$objDb->query($sSQL);
	
	$iPreSelection = $objDb->getCount( );
        
        for($i=0; $i < $iPreSelection; $i++){
            
            $iFailureReasonCount  = $objDb->getField($i, "_FrCount");
            $sFailureReason       = $objDb->getField($i, "_Reason");
?>            
        "<set tooltext='' label='<?= $sFailureReason ?>' value='<?= $iFailureReasonCount ?>' link='' />" +
            
<?      } ?>
								
	"</chart>");


			objChart2.render("InspectionFailureReasonChart");


			$(document).ready(function( )
			{
				
				$("#Dashboard #frmSearch #BtnSearch, #Dashboard #frmSearch #SearchButton .fa").click(function( )
				{
					$.post("ajax/get-inspection-failure-reasons-graph.php",
						$("#Dashboard #frmSearch").serialize( ),

						function (sResponse)
						{
							var sData = sResponse;
							
							objChart2.setXMLData(sData);
							objChart2.render("InspectionFailureReasonChart");
						},

						"text");
				});


			});
		-->
		</script>		
	  </td>
          <td><h2 style="height:18px;">&nbsp;</h2></td>
    </tr>
  </table>
