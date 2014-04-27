<?php
$db_config = array();
$db_config['newcgbt'] = array(
	'name' => 'newcgbt',
	'charset' => 'utf8',
	'use_slave' => false,
	'table_prefix' => 'cgbt_',
	'error_log' => '',
	'master' => array(
		'host' => '192.168.1.1',
		'username' => 'user',
		'passwd' => 'passwd',
		'dbname' => 'newcgbt',
		'port' => 3306
	)
);

$db_config['oldcgbt'] = array(
	'name' => 'oldcgbt',
	'charset' => 'utf8',
	'use_slave' => false,
	'table_prefix' => '',
	'error_log' => '',
	'master' => array(
		'host' => '192.168.1.1',
		'username' => 'user',
		'passwd' => 'passwd',
		'dbname' => 'cgbt',
		'port' => 3306
	)
);

$db_config['newcgbtdiscuzx'] = array(
	'name' => 'newcgbtdiscuzx',
	'charset' => 'utf8',
	'use_slave' => false,
	'table_prefix' => 'pre_',
	'error_log' => '',
	'master' => array(
		'host' => '192.168.1.1',
		'username' => 'user',
		'passwd' => 'passwd',
		'dbname' => 'discuzx',
		'port' => 3306
	)
);

return $db_config;