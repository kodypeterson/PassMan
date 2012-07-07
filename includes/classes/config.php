<?php
//MYSQL USERNAME
$CONFIG['mysql_credentials'][0]['mysql_user'] = '';
//MYSQL PASSWORD
$CONFIG['mysql_credentials'][0]['mysql_pass'] = '';
//MYSQL HOST
$CONFIG['mysql_credentials'][0]['mysql_host'] = '';
//MYSQL DATABASE
$CONFIG['mysql_credentials'][0]['mysql_database'] = '';
//MYSQL PORT
$CONFIG['mysql_credentials'][0]['mysql_port'] = 3306;
//MYSQL PERSISTENT
$CONFIG['mysql_credentials'][0]['mysql_use_persistent_connection'] = false;

$CONFIG['current_mysql_credentials'] = $CONFIG['mysql_credentials'][0];
require_once('db.php');
session_start();
?>