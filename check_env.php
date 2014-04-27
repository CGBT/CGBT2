<?php
/**
 * 检查PHP扩展
 *
 * 必须：
 * mysqli,iconv,mcrypt,curl,mbstring,json
 * memcache,redis,memcached 三选一
 *
 * 可选：yac
 *
 * 以后可能会用到:
 * ftp,PDO,zip,pdo_mysql
 *
 */
$dict_need_exts = explode(',', 'mysqli,iconv,mcrypt,curl,mbstring,json,memcached,memcache,redis');
$all_exts = get_loaded_extensions();
$no_exts = array_diff($dict_need_exts, $all_exts);

if(empty($no_exts))
{
	echo 'all exts is ok';
	exit;
}

if(in_array('memcached', $no_exts) && in_array('memcache', $no_exts) && in_array('redis', $no_exts) )
{
	$no_exts[] = 'cache';
	//memcache/memcached/redis
}
foreach ($no_exts as $ext)
{
	echo $ext . " fail!<br />";
}
