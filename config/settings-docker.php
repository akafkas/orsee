<?php
error_reporting(E_ALL & ~E_NOTICE);

// SERVER SETTINGS
$settings__root_to_server="/var/www/html";
$settings__root_directory="";
$settings__server_url="127.0.0.1:8080";
$settings__server_protocol="http://";

// DATABASE CONFIGURATION
$site__database_host="db";
$site__database_database="orsee_db";
$site__database_admin_username="orsee_user";
$site__database_admin_password="orsee_pw";
$site__database_type="mysql";
$site__database_table_prefix="or_";

$site__database_use_ssl=false;
$site__database_ssl_key='/etc/mysql/ssl/client-key.pem';
$site__database_ssl_cert='/etc/mysql/ssl/client-cert.pem';
$site__database_ssl_ca='/etc/mysql/ssl/ca-cert.pem';

date_default_timezone_set("Europe/Athens");

// OUTGOING EMAIL
$settings__sendmail_path="/usr/sbin/sendmail";

// INCOMING EMAIL MODULE
$settings__email_server_type="pop3";
$settings__email_server_name="mail.foobar.edu";
$settings__email_server_port="";
$settings__email_username="orsee@foobar.edu";
$settings__email_password="orseefoorbar_pw";
$settings__email_ssl=FALSE;

// SECURITY SETTINGS
session_set_cookie_params(array('httponly'=>true,'samesite'=>'Strict'));

// STOP SITE, TRACKING, DEBUGGING
$settings__stop_admin_site="n";
$settings__disable_orsee_tracking="n";
$settings__time_debugging_enabled="n";
$settings__query_debugging_enabled="n";

ini_set("include_path",ini_get("include_path").":./tagsets:./../tagsets:./../../tagsets");

?>
