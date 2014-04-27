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
		<div class="form_title w100">缓存统计</div>		
		<div class="form_box w1020">		

<?php if (isset($data['cache'])): ?>
<pre>
<?php print_r($data['cache']); ?>
</pre>
<?php else: ?>

		<form action="/admin/index/memcached?exec" method="post" name='form1' id="form1">			
		<table class="table_form">
			<tr>
				<th>sleep time</th>
				<td><input type="text" id="sleep_time" name="sleep_time" value="5" class="input"  /></td>
				<td>页面执行时间为sleep_time乘以Memcached服务数量</td>
			</tr>
			<tr>
			<th></th>
			<td>
				<a class="btn btn-success" id="submit" href="javascript:document.form1.submit()">提交</a>
				<input type='hidden' name='submitbtn'>
			</td>
			<td></td>
		</tr>
		</table>
		</form>

<?php endif;?>
			
		</div><!--form_box-->
		<div class="blank_box20"></div>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>
