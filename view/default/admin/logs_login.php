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
	</div>
		<!--sidebar-->
		<div id="content">
		<?php include 'menu_subnav.php'; ?>
			<div class="form_title w100">登陆日志</div>
			<div class="form_box w1020">
				<div class="blank_box20"></div>
				<form class="form-inline ml15" action="/admin/index/logslogin">
					用户名
				    <input type="text" name="username" maxlength="30">
					<input type="submit" class="btn btn-blue" value="查询">
				</form>
				<table class="table table-bordered table-hover">
					<tr>
						<th>时间</th>
						<th>IP地址</th>
					</tr>
			<?php foreach ($data['logs_login'] as $key => $value): ?>
			<tr>
						<td><?php echo date("Y-m-d H:i:s",$value['createtime']); ?></td>
						<td><?=$value['ip']?></td>
					</tr>
			<?php endforeach; ?>
			</table>
			</div>
			<!--form_box-->
			<div class="blank_box20"></div>
		</div>
		<!--content-->
		<div style="clear: both"></div>
	</div>
	<!--container-->
<?php
include 'footer.php';
?>