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

		<div class="form_title w150">添加前台权限</div>
		<div class="form_box w1020">
			<?php if ($data['action_name']=='edit_action'): ?>
			<form action="/admin/privileges/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/privileges/insert" method="post" name='form1' id="form1">
			<?php endif; ?>
			<table class="table_form">
				<tr>
					<th>分类</th>
					<td>
						<select name="is_front" id="is_front" onchange="change_type();">
							<option value="1"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['is_front']=='1'): ?> selected='selected'<?php endif;?>>前台权限</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>显示顺序</th>
					<td>
					<input type="text" id="orderid" name="orderid" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['orderid'];?>" class="input" /> 数字
					</td>
					<td></td>
				</tr>
				<tr>
					<th>权限名称</th>
					<td><input type="text" id="name" name="name" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['name'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="is_front">
					<th>权限名称英文</th>
					<td><input type="text" id="name_en" name="name_en" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['name_en'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="not_is_front">
					<th>Controller</th>
					<td><input type="text" id="controller" name="controller" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['controller'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="not_is_front">
					<th>Action</th>
					<td><input type="text" id="action" name="action" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['action'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="is_front">
					<th>类型</th>
					<td>
						<select name="type" id="type">
							<option value="">=请选择=</option>
							<option value="yes_no"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='yes_no'): ?> selected='selected'<?php endif;?>>是/否</option>
							<option value="text"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='text'): ?> selected='selected'<?php endif;?>>文本</option>
						</select>
					</td>
					<td></td>
				</tr>
				<!-- <tr>
					<th>单选/多选选项</th>
					<td><textarea id="options" name="options" class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['options'];?></textarea> 每行一个选项，格式为"名称"或"名称:值"</td>
					<td></td>
				</tr> -->
				<tr class="is_front">
					<th>普通用户组默认值</th>
					<td><input type="text" id="default_value" name="default_value" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['default_value'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="is_front">
					<th>特殊用户组默认值</th>
					<td><input type="text" id="vip_default_value" name="vip_default_value" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['vip_default_value'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr class="is_front">
					<th>管理组默认值</th>
					<td><input type="text" id="admin_default_value" name="admin_default_value" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['admin_default_value'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>说明</th>
					<td><textarea id="tip" name="tip" class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['tip'];?></textarea></td></td>
					<td></td>
				</tr>
				<tr class="is_front">
					<th>可以封禁本权限</th>
					<td>
						<select name="can_ban" id="can_ban">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['can_ban']=='1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['can_ban']=='0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
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
		

	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->

<script type="text/javascript">
function change_type()
{
	if ($("#is_front").val() == '1')
	{
		$(".is_front").show();
		$(".not_is_front").hide();
	}
	else
	{
		$(".is_front").hide();
		$(".not_is_front").show();
	}
}
change_type();
</script>

<?php
include 'footer.php';
?>
