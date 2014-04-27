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
		<div class="form_title w150">种子分类表单设计</div>
		<div class="form_box w1020">
			<?php if ($data['action_name']=='edit_action'): ?>
			<form action="/admin/category_options/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/category_options/insert" method="post" name='form1' id="form1">
			<?php endif; ?>
			<table class="table_form">
				<tr>
					<th>种子分类</th>
					<td>
						<?php if ($data['action_name'] == 'edit_action'): ?>
						<select name="category" id="category">
						<?php else: ?>
						<select name="category" id="category" onchange="location.href='?category='+this.value">
						<?php endif; ?>
							<option value="">=请选择=</option>
							<?php foreach ($data['all_category'] as $category): ?>
							<option value="<?=$category['name_en']?>"<?php if (($data['current_category'] == $category['name_en']) || ($data['action_name'] == 'edit_action' && $data['current_row']['category'] == $category['name_en'])): ?>selected='selected'<?php endif;?>><?=$category['name']?></option>
							<?php endforeach;?>
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
					<th>标题</th>
					<td><input type="text" id="title" name="title" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['title'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>发布页面表单变量</th>
					<td><input type="text" id="variable" name="variable" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['variable'];?>" class="input" /> 英文</td>
					<td></td>
				</tr>
				<tr>
					<th>搜索url变量</th>
					<td><input type="text" id="variable_search" name="variable_search" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['variable_search'];?>" class="input" /> 英文(1-2个字母)</td>
					<td></td>
				</tr>
				<tr>
					<th>绑定字段</th>
					<td>
						<select name="bind_field" id="type">
							<option value="">=请选择=</option>
							<?php foreach ($data['dict_bind_field'] as $field): ?>
							<option value="<?=$field?>"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['bind_field'] == $field): ?>selected='selected'<?php endif;?>><?=$field?></option>
							<?php endforeach;?>							
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>类型</th>
					<td>
						<select name="type" id="type">
							<option value="">=请选择=</option>							
							<option value="text"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='text'): ?> selected='selected'<?php endif;?>>文本</option>
							<option value="select"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='select'): ?> selected='selected'<?php endif;?>>单选</option>
							<option value="selects"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='selects'): ?> selected='selected'<?php endif;?>>多选</option>
							<option value="select_input"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='select_input'): ?> selected='selected'<?php endif;?>>选填</option>
							<option value="year"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='year'): ?> selected='selected'<?php endif;?>>年份</option>
							<option value="date"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='date'): ?> selected='selected'<?php endif;?>>日期</option>
							<option value="range"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['type']=='range'): ?> selected='selected'<?php endif;?>>区间</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>单选/多选选项</th>
					<td><textarea id="options" name="options" class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['options'];?></textarea> 每行一个选项，格式为"名称"或"名称:值"</td>
					<td></td>
				</tr>
				<tr>
					<th>显示在</th>
					<td>
						<input type="checkbox" id="insearch_item" name="insearch_item" value="1" class="checkbox" <?php if ( $data['action_name']=='edit_action'  && $data['current_row']['insearch_item']=='1' ) echo "checked='checked'";?> /> <label for="insearch_item">搜索条件</label>
						<input type="checkbox" id="insearch_keyword" name="insearch_keyword" value="1" class="checkbox" <?php if ( $data['action_name']=='edit_action'  && $data['current_row']['insearch_keyword']=='1' ) echo "checked='checked'";?> /> <label for="insearch_keyword">搜索关键词</label>
						<input type="checkbox" id="intitle" name="intitle" value="1" class="checkbox" <?php if ( $data['action_name']=='edit_action' && $data['current_row']['intitle']=='1' ) echo "checked='checked'";?> /> <label for="intitle">标题</label>
						<input type="checkbox" id="indetail" name="indetail" value="1" class="checkbox" <?php if ( $data['action_name']=='edit_action' && $data['current_row']['indetail']=='1' ) echo "checked='checked'";?> /> <label for="indetail">详细信息</label>
						<input type="checkbox" id="intag" name="intag" value="1" class="checkbox" <?php if ( $data['action_name']=='edit_action' && $data['current_row']['intag']=='1' ) echo "checked='checked'";?> /> <label for="intag">标签</label>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>是否必填</th>
					<td>
						<select name="required" id="required">
							<option value="">=请选择=</option>							
							<option value="1"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['required']=='1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['required']=='0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>填写说明</th>
					<td><textarea id="tip" name="tip" class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['tip'];?></textarea></td></td>
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
		<div class="blank_box20"></div>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>序号</th>				
				<th>种子分类</th>
				<th>显示顺序</th>
				<th>标题</th>
				<th>绑定字段</th>
				<th>变量名</th>
				<th>类型</th>
				<th>显示在标题/搜索条件/搜索关键词/详细信息/标签</th>
				<th>必填/启用</th>
				<th>填写说明</th>				
				<th>操作</th>
			</tr>
			<?php foreach ($data['all_category_options'] as $key =>$option): ?>
			<tr>
				<td><?=$key+1?></td>
				<td><?=$option['category']?></td>
				<td><?=$option['orderid']?></td>				
				<td><?=$option['title']?></td>
				<td><?=$option['bind_field']?></td>
				<td><?=$option['variable']?>/<?=$option['variable_search']?></td>
				<td><?=$option['type']?></td>
				<td><?=$option['intitle']?>/<?=$option['insearch_item']?>/<?=$option['insearch_keyword']?>/<?=$option['indetail']?>/<?=$option['intag']?></td>
				<td><?=$option['required']?>/<?=$option['status']?></td>				
				<td><?=$option['tip']?></td>
				<td>
					<a href="/admin/category_options/edit?id=<?=$option['id']?>&category=<?=$option['category']?>">修改</a>
					&nbsp;
					<a href="/admin/category_options/delete?id=<?=$option['id']?>" onclick="return confirm('您确定删除吗？');">删除</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>

	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<script type="text/javascript">
function change_category()
{
	category = $('#category').val();
	location.href="/admin/category_options/index"
}
</script>
<?php
include 'footer.php';
?>
