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
		<div class="form_title w100">清理缓存</div>		
		<div class="form_box w1020">		

<?php if (isset($data['cache'])): ?>
<pre>
<?php
if (empty($data['cache']))
{
	var_dump($data['cache']);
}
else
{
	print_r($data['cache']);
}
?>
</pre>
<?php else: ?>

		<form action="/admin/index/clearcache?exec" method="post" name='form1' id="form1">			
		<table class="table_form">
			<tr>
				<th>缓存key</th>
				<td><input type="text" id="cache_key" name="cache_key" value="" class="input"  /></td>
				<td>不需要key前缀</td>
			</tr>
			<tr>
				<th>ID范围,从 </th>
				<td>
					<input type="text" id="id_from" name="id_from" value="" class="input"  />  					
				</td>
				<td>数字范围，用于批量清理缓存</td>
			</tr>
			<tr>
				<th>ID范围,到 </th>
				<td>					
  					<input type="text" id="id_to" name="id_to" value="" class="input"  />
				</td>
				<td>数字范围，用于批量清理缓存</td>
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
