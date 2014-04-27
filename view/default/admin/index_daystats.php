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
			<table class="table table-bordered table-hover">
			<tr>
				<th>日期</th>
				<th>访问用户数</th>
				<th>搜索次数</th>
				<th>下载完成数</th>
				<th>浏览次数</th>
				<th>下载次数</th>
				<th>登录次数</th>
			</tr>
			<?php foreach ($data['today_stats'] as $row): ?>
			<tr>
				<td><?=$row['thedate']?></td>
				<td><?=$row['users_count']?></td>
				<td><?=$row['search_count']?></td>
				<td><?=$row['completed_count']?></td>
				<td><?=$row['browse_count']?></td>
				<td><?=$row['download_count']?></td>
				<td><?=$row['login_count']?></td>
			</tr>
			<?php endforeach; ?>
			<?php foreach ($data['daystats'] as $row): ?>
			<tr>
				<td><?=$row['thedate']?></td>
				<td><?=$row['users_count']?></td>
				<td><?=$row['search_count']?></td>
				<td><?=$row['completed_count']?></td>
				<td><?=$row['browse_count']?></td>
				<td><?=$row['download_count']?></td>
				<td><?=$row['login_count']?></td>
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
