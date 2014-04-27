<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div class="pagecontent clearfix">
					<ul class="nav pull-left">
						<li class="active"><a>种子排行</a></li>
						<li><a href="/top/torrents/seeder/">种子数排行</a></li>
						<li><a href="/top/torrents/leecher/">下载数排行</a></li>
						<li><a href="/top/torrents/complete/">完成数排行</a></li>
					</ul>
					<div class="pager">
					</div>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable">
					<tr>
						<th width="50">排名</th>
						<th class="l">名称</th>
						<th width="80">种子</th>
						<th width="80">下载</th>
						<th width="80">完成</th>
						<th width="150">发布者</th>
					</tr>
					<?php $i=1;
					foreach ($data['torrents'] as $key => $torrent): ?>
					<tr>
						<td><?=$i;$i++?></td>
						<td class="l"><a href="/torrents/<?=$torrent['id']?>" class="bluelink"><?=$torrent['title']?></a></td>
						<td><?=$torrent['seeder']?></td>
						<td><?=$torrent['leecher']?></td>
						<td><?=$torrent['complete']?></td>
						<td><a href="/user/<?=$torrent['uid']?>" class="bluelink"><?=$torrent['user_title']?></a></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
</div>
	<!--wp-->


<?php
include 'footer.php';
?>