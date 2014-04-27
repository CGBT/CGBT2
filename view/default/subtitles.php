<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a>字幕</a></li>
					</ul>
					<div class="pager">
						<?php echo $data['pager']->output; ?>
					</div>
				</div>
				
<table cellspacing="0" cellpadding="0" class="torrenttable">
	<tr>
		<th width="30">ID</th>
		<th width="200">种子</th>
		<th width="200">字幕文件名</th>
		<th width="50">下载</th>
		<th width="50">下载次数</th>
		<th width="50">上传人</th>
		<th width="80">上传时间</th>
	</tr>
	<?php foreach ($data['rows_subs'] as $key => $row): ?>
	<tr>
		<td><?=$key+1?></td>
		<td><a href='/torrents/<?=$row['tid']?>/details/' target='_blank'><?=$row['torrent_title']?></a></td>
		<td><?=$row['old_name']?></td>
		<td><a href='/subtitles/<?=$row['id']?>/download/' target='_blank' class="bluelink">下载</a></td>
		<td><?=$row['download']?></td>
		<td><?=$row['username']?></td>
		<td><?=date("Y-m-d H:i:s", $row['createtime'])?></td>
	</tr>
	<?php endforeach; ?>
</table>

				<div class="pagecontent">
					<div class="pager">
						<?php echo $data['pager']->output; ?>
					</div>
				</div>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
	<!--wp-->

<?php
include 'footer.php';
?>