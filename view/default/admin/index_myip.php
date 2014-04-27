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
		<div class="form_title w100">用户日访问量</div>		
		<div class="form_box w1020">

			<div style="font-size:14px;padding:20px;">   总大小: <?=$data['sumsize']?>    总数量: <?=$data['sumcount']?>  <br /></div>
			<table class="table table-bordered table-hover">
			<tr>
				<td>IP</td>
				<td>IPv6</td>
				<td>uTorrent</td>
				<td>端口</td>
				<td>做种</td>
				<td>种子数</td>
				<td>大小</td>
				<td>用户名</td>
				<td>uid</td>
			 </tr>
			
			<?php foreach ($data['myip_stat_rows'] as $row): ?>
			<tr>
				<td><?=$row['ip']?></td>
				<td><?=$row['ipv6']?></td>
				<td><?=$row['agent']?></td>
				<td><?=$row['port']?></td>
				<td><?php echo $row['is_seeder']?'是':'否'; ?></td>
				<td><?=$row['torrents_count']?></td>
				<td><?=$row['size']?></td>
				<td><?=$row['username']?></td>
				<td><?=$row['uid']?></td>
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
