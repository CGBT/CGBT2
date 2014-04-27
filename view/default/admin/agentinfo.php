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
		<div class="form_title w100">客户端统计</div>
		<div class="form_box w1020">
			<table class="table table-bordered table-hover">
			<tr>
				<th>序号</th>
						<th>客户端</th>
			</tr>
			<?php foreach ($data['agentinfo'] as $key => $value): ?>
			<tr>
				<td><?=$value['id']?></td>
			    <td><?=$value['agent']?></td>
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