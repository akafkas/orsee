<?php
// part of orsee. see orsee.org
ob_start();

$target='participant_login.php';
$query=array();
if (isset($_REQUEST['logout']) && $_REQUEST['logout']) $query[]='logout=true';
if (isset($_REQUEST['requested_url']) && $_REQUEST['requested_url']) $query[]='requested_url='.urlencode($_REQUEST['requested_url']);

if (file_exists("../config/settings.php")) include ("../config/settings.php");
elseif (file_exists("../config/settings-docker.php")) include ("../config/settings-docker.php");
include ("../config/system.php");
include ("../config/requires.php");

site__database_config();
$settings=load_settings();

if (isset($settings['subject_authentication']) && $settings['subject_authentication']=='token') {
    $target='index.php';
}

if (count($query)>0) $target.='?'.implode('&',$query);
header("Location: ".$target);
exit;
?>
