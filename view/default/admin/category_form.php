<?php
include 'header.php';
?>
<body>
<div id="top">
	白志鹏 退出
</div><!--top-->
<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>		
		<div class="form_title w100">种子分类管理</div>		
		<div class="form_box w1020">
			<?php if ($data['action_name']=='edit'): ?>
			<form action="/admin/category/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/category/insert" method="post" name='form1' id="form1">
			<?php endif; ?>
			<table class="table_form">
				<tr>
					<th>英文标识</th>
					<td><input type="text" id="name" name="name_en" value="<?php if ($data['action_name']=='edit') echo $data['current_row']['name_en'];?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类名称</th>
					<td><input type="text" id="name" name="name" value="<?php if ($data['action_name']=='edit') echo $data['current_row']['name'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类图标</th>
					<td><input type="text" id="name" name="icon" value="<?php if ($data['action_name']=='edit') echo $data['current_row']['icon'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类版主</th>
					<td><input type="text" id="name" name="admins" value="<?php if ($data['action_name']=='edit') echo $data['current_row']['admins'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th></th>
					<td>
					<a class="submit_button" id="submit" href="javascript:document.form1.submit()"><em>保存</em></a>
					</td>
					<td></td>
				</tr>				
			</table>
			</form>
		</div><!--form_box-->
		<div class="blank_box20"></div>
		<table class="datalist w1020">
			<tr>
				<th>ID</th>
				<th>英文标识</th>
				<th>分类名称</th>				
				<th>分类图标</th>
				<th>分类版主</th>				
				<th>操作</th>
			</tr>
			<?php foreach ($data['all_category'] as $key =>$category): ?>
			<tr>
				<td><?=$category['id']?></td>
				<td><?=$category['name_en']?></td>
				<td><?=$category['name']?></td>
				<td><?=$category['icon']?></td>
				<td><?=$category['admins']?></td>
				<td>
					<a href="/admin/category/edit?id=<?=$category['id']?>">修改</a>
					&nbsp;
					<a href="/admin/category/delete?id=<?=$category['id']?>" onclick="return confirm('您确定删除吗？');">删除</a>
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
