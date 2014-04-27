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
						<li class="active"><a>重复种子</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
<table cellspacing="0" cellpadding="0" class="torrenttable dupe">
	<tr>
		<th width="50">种子ID</th>
		<th width="120">发布时间</th>
		<th class="l">名称</th>
		<th width="150">审核结果</th>
		<th width="50">审核</th>
		<th width="50">种子</th>
		<th width="50">种子id</th>
		<th width="120">发布时间</th>
		<th class="l">名称</th>
		<th width="50">审核</th>
		<th width="50">种子</th>
	</tr>
	<?php foreach ($data['dupe_torrents'] as $key => $row): ?>
	<tr>
		<td><a href='/torrents/<?=$row['a']['id']?>/edit/' style='color:blue'><?=$row['a']['id']?></a></td>
		<td><?=date("Y-m-d H:i:s", $row['a']['createtime'])?></td>
		<td><a href='/torrents/<?=$row['a']['id']?>/details/' target='_blank'><?=$row['a']['name']?>/<?=$row['a']['name_en']?></a></td>
		<td><?=$row['a']['audit_note']?></td>
		<td><?php echo $row['a']['status']==0 ? '否':'是'; ?></td>
		<td><?=$row['a']['seeder']?></td>

		<td><a href='/torrents/<?=$row['b']['id']?>/edit/' style='color:blue'><?=$row['b']['id']?></a></td>
		<td><?=date("Y-m-d H:i:s", $row['b']['createtime'])?></td>
		<td><a href='/torrents/<?=$row['b']['id']?>/details/' target='_blank'><?=$row['b']['name']?>/<?=$row['b']['name_en']?></a></td>
		<td><?php echo $row['b']['status']==0 ? '否':'是'; ?></td>
		<td><?=$row['b']['seeder']?></td>

	</tr>
	<?php endforeach; ?>
</table>
			</div>
			<!-- end #mainContent -->

		
		</div>
	</div>
	<!--wp-->


<?php
include 'footer.php';
?>