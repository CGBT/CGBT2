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
		<div class="form_title w100">封禁用户权限</div>		
		<div class="form_box w1020">

			<?php if ($data['action_name']=='edit_action'): ?>
			<form action="/admin/bans/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/bans/insert" method="post" name='form1' id="form1">
			<?php endif; ?>

			<table class="table_form">
				<tr>
					<th>封禁用户名</th>
					<td><input type="text" id="username" name="username" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['username'];?>" class="input" /></td>
					<td></td>
				</tr>

				<tr>
					<th>起始时间</th>
					<td><input type="text" id="starttime" name="starttime" value="<?php if ($data['action_name']=='edit_action') {echo date("Y-m-d H:i:s", $data['current_row']['starttime']); } else {echo date("Y-m-d 00:00:00");}?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>结束时间</th>
					<td><input type="text" id="endtime" name="endtime" value="<?php if ($data['action_name']=='edit_action') { echo date("Y-m-d H:i:s", $data['current_row']['endtime']);}else{echo date("Y-m-d 00:00:00", time() + 86400*14);}?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>原因</th>
					<td><input type="text" id="reason" name="reason" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['reason'];?>" class="input" /></td>
					<td>该原因会在页面显示，请注意填写</td>
				</tr>
				<tr>
					<th>备注</th>
					<td><input type="text" id="memo" name="memo" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['memo'];?>" class="input" /></td>
					<td>不会在页面显示</td>
				</tr>
				<tr>
					<th>权限名</th>
					<td>
						<select name="privileges_name">
						<option value="">=请选择=</option>
						<?php foreach($data['all_privileges'] as $name_en => $name): ?>
						<option value="<?=$name_en?>"<?php if ($data['action_name']=='edit_action' && $data['current_row']['privileges_name'] == $name_en) echo " selected='selected'"?>><?=$name?></option>
						<?php endforeach; ?>
						</select>					
					</td>
					<td></td>
				</tr>
				<tr>
					<th>权限值</th>
					<td><input type="text" id="privileges_value" name="privileges_value" value="<?php if ($data['action_name']=='edit_action') { echo $data['current_row']['privileges_value'];}else{echo '0';}?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>是否启用</th>
					<td>
						<select name="status" id="status">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['status']=='1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['status']=='0'): ?>selected='selected'<?php endif;?>>否</option>
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
	</div>
</div><!--container-->
<?php
include 'footer.php';
?>
