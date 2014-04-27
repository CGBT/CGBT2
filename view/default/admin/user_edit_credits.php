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
		<div class="form_title w100">修改用户积分</div>		
		<div class="form_box w1020">
			<form action="/admin/user/edit_credits/" method="post" name='form1' id="form1">
			<table class="table_form">
				<tr>
					<th>用户名</th>
					<td><input type="text" id="username" name="username" value="" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>积分类型</th>
					<td>
						<select name="field">	
						<option value="">=请选择=</option>
						<option value="uploaded">上传流量</option>
						<option value="downloaded">下载流量</option>
						<option value="uploaded2">虚拟上传流量</option>
						<option value="downloaded2">虚拟下载流量</option>
						<option value="extcredits1">保种积分</option>
						<option value="extcredits2">土豪金</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>积分数量</th>
					<td><input type="text" id="count" name="count" value="" class="input" /></td>
					<td>流量单位为G</td>
				</tr>
				<tr>
					<th>原因</th>
					<td><input type="text" id="reason" name="reason" value="" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th></th>
					<td>
					<input type="hidden" id="submited" name="submited" value="" />
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
				<th>积分类型</th>				
				<th>数量</th>
				<th>操作人</th>
				<th>类型</th>
				<th>详情</th>
				<th>时间</th>
			</tr>
			<?php foreach ($data['latest_rows'] as $key => $row): ?>
			<tr>
				<td><?=$row['id']?></td>
				<td><?=$row['username']?></td>
				<td><?=$row['field']?></td>
				<td><?=$row['count']?></td>
				<td><?=$row['operator_username']?></td>
				<td><?=$row['action']?></td>
				<td><?=$row['details']?></td>
				<td><?=date("Y-m-d H:i", $row['createtime'])?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div><!--container-->
<?php
include 'footer.php';
?>
