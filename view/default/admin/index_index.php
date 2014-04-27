<?php
include 'header.php';
?>
<body>
<?php
include 'top.php';
?>
<!--top-->
<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>		
		<div class="form_title w100">站点数据</div>		
		<div class="form_box w1020">			
			<table class="table table-bordered table-hover">
			<tr>
				<th>id</th>
				<th>时间</th>
				<th>活动种子数</th>
				<th>下载人数</th>
				<th>做种人数</th>
				<th>peer总人数</th>
				<th>下载peer</th>
				<th>上传peer</th>
				<th>活动种子大小</th>
				<th>在线用户</th>
			</tr>
			<?php foreach ($data['site_stat_rows'] as $key =>$row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?php echo date("Y-m-d H:i:s",$row['createtime']); ?></td>
				<td><?=$row['active_torrent_count']?></td>
				<td><?=$row['leecher_count']?></td>
				<td><?=$row['seeder_count']?></td>
				<td><?=$row['peer_user_count']?></td>
				<td><?=$row['leech_peer_count']?></td>
				<td><?=$row['seed_peer_count']?></td>
				<td><?=$row['active_size_text']?></td>
				<td><?=$row['online_user']?></td>
			</tr>
			<?php endforeach; ?>
			</table>
			
		</div><!--form_box-->
		<div class="blank_box20"></div>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>
