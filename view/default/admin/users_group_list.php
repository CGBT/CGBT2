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
		<div class="form_title w100">用户组管理</div>		
		
		<div class="blank_box20"></div>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>ID</th>
				<th>组id</th>
				<th>排序</th>
				<th>组类型</th>
				<th>组名称</th>
				<th>组颜色</th>
				<th>积分介于</th>
				<th>操作</th>
			</tr>
			<?php foreach ($data['all_users_group'] as $key => $row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?=$row['groupid']?></td>
				<td><?=$row['orderid']?></td>
				<td><?=$row['type_text']?></td>
				<td><?=$row['color']?></td>
				<td><?=$row['name']?></td>
				<td><?=$row['min_credits']?>-<?=$row['max_credits']?></td>
				<td>
					<a href="/admin/users_group/edit?id=<?=$row['id']?>">前台权限</a>
					<?php if ($row['type'] == 'admin'): ?>
					<a href="/admin/users_group/editadmin?id=<?=$row['id']?>">后台权限</a>
					<?php endif; ?>
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
