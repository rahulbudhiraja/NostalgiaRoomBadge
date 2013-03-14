<?php
include_once "fbaccess.php";
 
$logoutUrl = $facebook->getLogoutUrl(array(
  'next' => $site_url));
 
$facebook->destroySession();
 
header("Location: $logoutUrl");
exit;
?>