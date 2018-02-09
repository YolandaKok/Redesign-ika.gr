<?php
//Get Heroku ClearDB connection information
$cleardb_url      = parse_url(getenv("CLEARDB_DATABASE_URL"));
$hn   = $cleardb_url["host"];
$un = $cleardb_url["user"];
$pw = $cleardb_url["pass"];
$db = substr($cleardb_url["path"],1);

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'   => '',
	'hostname' => $hn,
	'username' => $un,
	'password' => $pw,
	'database' => $db,
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
?>
