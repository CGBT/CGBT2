<div class="box box-tipbox flipInX" id="box-tipbox">
</div>
<div id="ft" class="wp cl">
	<div id="flk" class="y">
	<p>
	<a href="/user/group/" class="bluelink">用户组权限表</a>
	<span class="pipe">|</span>
	<a href="/user/staff/" >管理组成员 (申请版主播种请加群155469703)</a>
	<span class="pipe">|</span>
	<a href="/user/dashi/" >驻知行PT大使 (申请大使请加群174560703)</a>
	<span class="pipe">|</span>
    <?=$data['setting']['site_qq_qun_name']?>：<?=$data['setting']['site_qq_qun']?>
	</p>
	<p class="xs0">GMT+8, <?=date("Y-m-d H:i:s")?><span id="debuginfo"></span></p>
	</div>
	<div id="frt">
		<p>Powered by <strong><a href='http://cgbtorg.taobao.com'>CGBTSource v2.0 Beta</a></strong> </p>
		<p class="xs0">&copy; 2004-2013 <a href="http://cgbt.cn" target="_blank">cgbt.cn</a></p>
	</div>
	<div id="execute_time"><?=$this->lang('page_execute_time')?>:<?=$data['page_execute_time']?>
	&nbsp; 服务器负载:<?=$data['server_load']?>
	&nbsp; 
	<?php if ($data['is_ipv6']): ?>
	您正在使用IPv6地址 <?=$data['ip']?> 访问本站!
	<?php else: ?>
	<span style='color:red;font-weight:bold;'>提示：您的ip为 <?=$data['ip']?> , 您没有正确安装IPv6网络。</span>
	<?php endif; ?>

	<span style='font-weight:bold;'><a href='/list/soft/' target='_blank' class="bluelink">优特(uTorrent)软件下载</a></span><br />	
	
	</div>
</div>
<?php
if ($data['is_developer'])
{
	echo "sql count: ".count($data['all_sql'])."<br />\n";
	echo "cache set count: ".count($data['all_cache_keys']['set'])."<br />\n";
	echo "cache get count: ".count($data['all_cache_keys']['get'])."<br />\n";
	echo "sleep times : " . $data['sleep_times'] . "<br />\n";
	echo "cache conn time: ".($data['all_cache_keys']['time'] * 1000)."<br />\n";

	echo "query_time";
	print_r($data['db_stat']['query_time']);	
	echo "connectttime";
	print_r($data['db_stat']['connect_time']);

	print_r($data['all_sql']);
	//print_r($data['all_cache_keys']['get_keys_time']);
	//print_r($data['all_cache_keys']['set']);
}
?>
<a href="javascript:void(0);" class="back-to-top button button-blue" title="回到顶部">▲</a>
</body>
</html>
