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
		<div class="form_title w100">积分日志</div>
		<div class="form_box w1020">
			<table class="table table-bordered table-hover">
			<tr>
				<th>id</th>
				<th>时间</th>
				<th>用户</th>
				<th>积分类型</th>
				<th>积分</th>
				<th>类型</th>
				<th>操作人</th>
			</tr>
			<?php foreach ($data['rows_credits'] as $key =>$row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?php echo date("Y-m-d H:i:s",$row['createtime']); ?></td>
				<td><?=$row['username']?></td>
				<td><?=$row['field']?></td>
				<td><?=$row['count']?></td>
				<td><?=$row['action']?></td>
				<td><?=$row['operator_username']?></td>
			</tr>
			<?php endforeach; ?>
			</table>
        <div class="pagination pagination-large pagination-centered"><?=$data['pager']->output?></div>
		</div><!--form_box-->
		<div class="blank_box20"></div>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>