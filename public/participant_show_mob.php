<?php
// part of orsee. see orsee.org
ob_start();

$query=array();
if (isset($_REQUEST['p']) && $_REQUEST['p']) $query[]='p='.urlencode($_REQUEST['p']);
if (isset($_REQUEST['s']) && $_REQUEST['s']) $query[]='s='.urlencode($_REQUEST['s']);
if (isset($_REQUEST['register']) && $_REQUEST['register']) $query[]='register=true';
if (isset($_REQUEST['cancel']) && $_REQUEST['cancel']) $query[]='cancel=true';
if (isset($_REQUEST['reallyregister']) && $_REQUEST['reallyregister']) $query[]='reallyregister=true';
if (isset($_REQUEST['reallycancel']) && $_REQUEST['reallycancel']) $query[]='reallycancel=true';
if (isset($_REQUEST['betternot']) && $_REQUEST['betternot']) $query[]='betternot=true';

$target='participant_show.php';
if (count($query)>0) $target.='?'.implode('&',$query);

header("Location: ".$target);
exit;
?>
