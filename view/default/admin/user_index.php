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
		<div class="form_title w100">用户查询</div>		
		<div class="form_box w1020">
			<div class="blank_box20"></div>
			<form class="form-inline ml15" method="get">
		    <select name="search_type" id="search_type">
		    	<? foreach($data['search_type_arr'] as $key => $value): ?>
				<option value="<?=$key?>" <?=($data['search_type'] == $key) ? 'selected' : '' ?> ><?=$value?></option>
				<? endforeach; ?>
			</select>
		    <input id="search_value" name="search_value" type="text" class="input-small" value="<?=!empty($data['search_value']) ? $data['search_value'] : ''?>">
		    <button type="submit" class="btn">查询</button>
		    </form>		
		</div><!--form_box-->
		<div class="blank_box20"></div>
		<? if(!empty($data['user_list'])): ?>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>UID</th>				
				<th>用户名</th>
				<th>title</th>
				<th>Email</th>
				<th>性别</th>
				<th>注册ip</th>
			</tr>
			<?php foreach ($data['user_list'] as $key =>$user): ?>
			<tr>
				<td><?=$user['uid']?></td>
				<td><?=$user['status'] ? $user['username'] : '<s>'.$user['username'].'</s>'?></td>
				<td><?=$user['title']?></td>				
				<td><?=$user['email']?></td>
				<td><?=$user['gender']?></td>
				<td><?=$user['regip']?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<? endif; ?>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<script>
	$(function(){
		$("#search_type").change(function(){
			$("#search_value").val('');
		});
	});
</script>
<?php
include 'footer.php';
?>
