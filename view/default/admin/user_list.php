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
		<table class="table table-bordered table-hover">
			<tr>
				<th>UID</th>				
				<th>用户名</th>
				<th>title</th>
				<th>Email</th>
				<th>性别</th>
				<th>注册ip</th>
				<th>操作</th>
			</tr>
			<?php foreach ($data['user_list'] as $key =>$user): ?>
			<tr>
				<td><?=$user['uid']?></td>
				<td><?=$user['status'] ? $user['username'] : '<s>'.$user['username'].'</s>'?></td>
				<td><?=$user['title']?></td>				
				<td><?=$user['email']?></td>
				<td><?=$user['gender']?></td>
				<td><?=$user['regip']?></td>
				<td><a href="javascript:void(0);">修改</a></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<div class="pagination pagination-large pagination-centered"><?=$data['pager']->output?></div>
	</div><!--content-->
	<div style="clear:both"></div>	
</div><!--container-->
<?php
include 'footer.php';
?>
