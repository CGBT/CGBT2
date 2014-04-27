<?php
if ('127.0.0.1' == $_SERVER['SERVER_ADDR'])
{
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE | E_STRICT);
}

date_default_timezone_set('Asia/Shanghai');
include 'config/config.php';
config::init();

include config::$config['system']['SYS_PATH'] . 'cg_config.php';
include config::$config['system']['SYS_PATH'] . 'cg.php';

cg::config()->init(config::$config);
cg::app()->route = config::$config['router'];
cg::app()->run();

?>