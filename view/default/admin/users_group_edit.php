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
		<div class="form_title w100">修改用户组</div>		
		<div class="form_box w1020">
			<form action="/admin/users_group/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<table class="table_form">
				<tr>
					<th>groupid</th>
					<td><?=$data['current_row']['groupid'];?></td>
					<td></td>
				</tr>
				<tr>
					<th>类型</th>
					<td>
					<select id="type" name="type">
					<option value="">=请选择=</option>
					<option value="user"<?php if ($data['current_row']['type'] == 'user') echo " selected='selected'"?>>普通用户组</option>
					<option value="vip"<?php if ($data['current_row']['type'] == 'vip') echo " selected='selected'"?>>特殊用户组</option>
					<option value="admin"<?php if ($data['current_row']['type'] == 'admin') echo " selected='selected'"?>>管理组</option>
					</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>名称</th>
					<td><input type="text" id="name" name="name" value="<?=$data['current_row']['name'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>颜色</th>
					<td><input type="text" id="color" name="color" value="<?=$data['current_row']['color'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>积分大于</th>
					<td><input type="text" id="min_credits" name="min_credits" value="<?=$data['current_row']['min_credits'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>积分小于等于</th>
					<td><input type="text" id="max_credits" name="max_credits" value="<?=$data['current_row']['max_credits'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th></th>
					<td></td>
					<td></td>
				</tr>
				<?php foreach($data['all_privileges'] as $key => $row): ?>
				<tr>
					<th><?=$row['name']?></th>
					<td>

					<?php if ($row['type'] == 'text'): ?>
					<input id="<?=$row['name_en']?>" type="text" name="<?=$row['name_en']?>" value="<?=$data['current_row']['privileges'][$row['name_en']]?>" class="input">
					<?php elseif ($row['type'] == 'yes_no'):?>
					<select id="<?=$row['name_en']?>" name="<?=$row['name_en']?>">
					<option value="">=请选择=</option>
					<option value="1"<?php if ($data['current_row']['privileges'][$row['name_en']] == '1') echo " selected='selected'"?>>是</option>
					<option value="0"<?php if ($data['current_row']['privileges'][$row['name_en']] == '0') echo " selected='selected'"?>>否</option>
					</select>
					<?php elseif ($row['type'] == 'select'):?>
					<?php
					foreach (funcs::explode($row['options']) as $option):
					list($k, $v) = funcs::explode($option, ':');
					?>
					<option value="<?=$v?>"<?php if ($data['current_row']['privileges'][$row['name_en']] == $v) echo " selected='selected'"?>><?=$k?></option>
					<?php endforeach; ?>
					</select>
					<?php endif; ?>
					</td>
					<td> <?=$row['tip']?></td>
				</tr>
				<?php endforeach; ?>
			
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
