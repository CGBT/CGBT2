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
		<div class="form_title w100">种子分类管理</div>		
		<div class="form_box w1020">
			<?php if ($data['action_name']=='edit_action'): ?>
			<form action="/admin/category/update" method="post" name='form1' id="form1">
			<input type="hidden" name="editid" value="<?=$data['current_row']['id'];?>">
			<?php else: ?>
			<form action="/admin/category/insert" method="post" name='form1' id="form1">
			<?php endif; ?>
			<table class="table_form">
				<tr>
					<th>应用</th>
					<td>					
					<select name="app" id="app">
							<option value="torrents"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['app'] == 'torrents' ): ?>selected='selected'<?php endif;?>>种子</option>
							<option value="book"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['app'] == 'book' ): ?>selected='selected'<?php endif;?>>二手书</option>
							<option value="softsite"<?php if ($data['action_name'] == 'edit_action' && $data['current_row']['app'] == 'softsite' ): ?>selected='selected'<?php endif;?>>软件站</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>英文标识</th>
					<td><input type="text" id="name" name="name_en" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['name_en'];?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类名称</th>
					<td><input type="text" id="name" name="name" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['name'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类图标</th>
					<td><input type="text" id="name" name="icon" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['icon'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>对应论坛板块(fid)</th>
					<td><input type="text" id="forums_fid" name="forums_fid" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['forums_fid'];?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>分类版主</th>
					<td><textarea id="admins" name="admins"  class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['admins'];?></textarea></td>
					<td>可修改删除置顶种子的用户名，一行一条记录</td>
				</tr>
				<tr>
					<th>热门关键词</th>
					<td><textarea id="hot_keywords" name="hot_keywords"  class="textarea"><?php if ($data['action_name']=='edit_action') echo $data['current_row']['hot_keywords'];?></textarea></td>
					<td>如果为空则由系统自动生成，一行一条记录</td>
				</tr>
				<tr>
					<th>热门关键词数量</th>
					<td><input type="text" id="hot_keywords_count" name="hot_keywords_count" value="<?php if ($data['action_name']=='edit_action') echo $data['current_row']['hot_keywords_count'];?>" class="input" /></td>
					<td>如果系统自动生成，显示多少条记录</td>
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
				<th>英文标识</th>
				<th>分类名称</th>				
				<th>分类图标</th>
				<th>对应论坛板块(fid)</th>
				<th>热门关键词数量</th>
				<th>操作</th>
			</tr>
			<?php foreach ($data['all_category'] as $key =>$category): ?>
			<tr>
				<td><?=$category['id']?></td>
				<td><?=$category['name_en']?></td>
				<td><?=$category['name']?></td>
				<td><?=$category['icon']?></td>
				<td><?=$category['forums_fid']?></td>
				<td><?=$category['hot_keywords_count']?></td>
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
