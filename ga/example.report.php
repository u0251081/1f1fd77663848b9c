<?php  
require_once('gapi.class.php');  
  
  define('ga_profile_id','165654126');
/*建立與帳戶的連結*/  
$ga = new gapi('ga-api@ga-test-198616.iam.gserviceaccount.com','GA TEST-97cdd47a9441.p12');  
  
  $ga->requestReportData(ga_profile_id,array('browser','browserVersion'),array('pageviews','visits'));

foreach($ga->getResults() as $result)
{
  echo '<strong>'.$result.'</strong><br />';
  echo 'Pageviews: ' . $result->getPageviews() . ' ';
  echo 'Visits: ' . $result->getVisits() . '<br />';
}

echo '<p>Total pageviews: ' . $ga->getPageviews() . ' total visits: ' . $ga->getVisits() . '</p>';