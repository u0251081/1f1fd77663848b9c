<?php
require 'gapi.class.php';
 define('ga_profile_id','165654126');

$ga = new gapi('ga-api@ga-test-198616.iam.gserviceaccount.com','GA TEST-97cdd47a9441.p12');  

/**
 * Note: OR || operators are calculated first, before AND &&.
 * There are no brackets () for precedence and no quotes are
 * required around parameters.
 * 
 * Do not use brackets () for precedence, these are only valid for 
 * use in regular expressions operators!
 * 
 * The below filter represented in normal PHP logic would be:
 * country == 'United States' && ( browser == 'Firefox || browser == 'Chrome')
 */
$filter=null;
$start_date=date("2018-03-21");
$end_date=date("Y-m-d");
$filter=null;
$ga->requestReportData(ga_profile_id,array('pagePath'),array('timeOnPage'),"-timeOnPage",$filter,$start_date,$end_date);
?>
<table>

<?php
foreach($ga->getResults() as $result):
  
?>
<tr>
  <td><?php echo $result ?></td>
  <td><?php echo $result->gettimeOnPage(); ?></td>
  
  

</tr>
<?php
endforeach
?>
</table>

