<?php
// part of orsee. see orsee.org
ob_start();
$suppress_html_header=true;

$query=array();
if (isset($_REQUEST['logout']) && $_REQUEST['logout']) $query[]='logout=true';
if (isset($_REQUEST['requested_url']) && $_REQUEST['requested_url']) $query[]='requested_url='.urlencode($_REQUEST['requested_url']);

$target='public/participant_login.php';
if (count($query)>0) $target.='?'.implode('&',$query);

include ("header.php");
redirect($target);
?>
