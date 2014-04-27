<?php

//http://cgbt.cn/browse.php?srchtxt=xx
$s = isset($_POST['srchtxt']) ? $_POST['srchtxt'] : '';

if (!empty($s))
{
	$s = str_replace(array(
		'-',
		'/',
		'.'
	), '', $s);
	header("location: /search/x$s");
}
else
{
	header("location: /");
}


