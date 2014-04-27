<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div style="clear:both"></div>
				
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a>当前做种/下载</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<table cellpadding="0" cellspacing="0" class="torrenttable">
					<tr>
						<th>No.</th>
						<th>用户名</th>
						<th>种子id</th>
						<th>种子大小</th>
						<th>ip</th>
						<th>已上传</th>
						<th>上传速度</th>
						<th>已下载</th>
						<th>下载速度</th>
						<th>最近事件</th>
						<th>最后活动时间</th>
						<th>创建时间</th>
						<th>完成时间</th>
						<th>客户端</th>
					</tr>
				<?php  foreach ($data['peers'] as $key => $peer):?>
				<tr>
				<td><?=$key+1?></td>
				<td><?=$peer['username']?></td>
				<td><a href="/torrents/<?=$peer['tid']?>/" class="bluelink" target="_blank"><?=$peer['tid']?></a></td>
				<td><?=funcs::mksize($peer['size'])?></td>
				<td class='l'><?=$peer['ip']?><br /><?=$peer['ipv6']?></td>
				<td><?=$peer['uploaded_text']?></td>
				<td><?=$peer['upload_speed']?></td>
				<td><?=$peer['downloaded_text']?></td>
				<td><?=$peer['download_speed']?></td>
				<td><?=$peer['last_event']?></td>
				<td><?=$peer['last_action_period']?>s ago<br /><?=$peer['last_action_text']?></td>
				<td><?=$peer['createtime_text']?></td>
				<td><?=$peer['completed_time_text']?></td>
				<td><?=$peer['agent']?><br />(<?=$peer['port']?>)</td>
				</tr>
				<?php  endforeach;  ?>
				</table>


</div>
<!-- end #mainContent -->
</div>
</div><!--wp-->


<?php
include 'footer.php';
?>