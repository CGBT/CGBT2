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
		<div class="form_title w100">修改用户级别</div>		
		<div class="form_box w1020">
			<form action="/admin/user/update_group" method="post" name='form1' id="form1">
			<table class="table_form">
				<tr>
					<th>用户名</th>
					<td><input type="text" id="username" name="username" value="" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>原因</th>
					<td><input type="text" id="reason" name="reason" value="" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>修改用户组</th>
					<td>
						<select name="groupid">
						<?php foreach($data['all_users_group'] as $key => $row): 
						if ($key == '1' || $key > 10 ):
						?>
						<option value="<?=$row['groupid']?>"><?=$row['name']?></option>
						<?php
						endif;
						endforeach; ?>
						</select>					
					</td>
					<td></td>
				</tr>
				<tr>
					<th></th>
					<td>
					<a class="btn btn-success" id="submit" href="javascript:document.form1.submit()">保存</a>
					</td>
					<td></td>
				</tr>				
			</table>
			</form>
		</div><!--form_box-->
		<div class="blank_box20"></div>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>ID</th>
				<th>用户名</th>
				<th>原用户组</th>				
				<th>新用户组</th>
				<th>操作人</th>
				<th>原因</th>
				<th>时间</th>
			</tr>
			<?php foreach ($data['rows_logs'] as $key => $row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?=$row['username']?></td>
				<td><?=$row['old_groupname']?></td>
				<td><?=$row['new_groupname']?></td>
				<td><?=$row['operator']?></td>
				<td><?=$row['reason']?></td>
				<td><?=date("Y-m-d H:i", $row['createtime'])?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div><!--container-->
<?php
include 'footer.php';
?>
