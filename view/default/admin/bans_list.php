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
		<div class="form_title w150">封禁用户权限列表</div>		
		
		<div class="blank_box20"></div>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>ID</th>
				<th>用户名</th>
				<th>权限名</th>
				<th>权限值</th>
				<th>起始时间</th>
				<th>结束时间</th>
				<th>原因</th>
				<th>操作人</th>
				<th>状态</th>
				<th>操作</th>
			</tr>
			<?php foreach ($data['all_bans'] as $key => $row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?=$row['username']?></td>
				<td><?=$row['privileges_name_cn']?></td>
				<td><?=$row['privileges_value']?></td>
				<td><?=date("Y-m-d H:i:s", $row['starttime'])?></td>
				<td><?=date("Y-m-d H:i:s", $row['endtime'])?></td>
				<td><?=$row['reason']?></td>
				<td><?=$row['operator']?></td>
				<td><?=$row['status']?></td>
				<td>
					<a href="/admin/bans/edit?id=<?=$row['id']?>">修改</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>

	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>
