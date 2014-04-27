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
		
		<div class="blank_box20"></div>
		<table class="datalist w1020 table-hover">
			<tr>
				<th>序号</th>
				<th>分类</th>
				<th>排序</th>
				<th>名称</th>
				<th>controller/action</th>
				<th>启用</th>
				<th>操作</th>
			</tr>
			<?php
			$i=0;
			foreach ($data['all_privileges'] as $key =>$option):
			if ($option['is_front'])
			{
				continue;
			}
			$i++;
			?>
			<tr>
				<td><?=$i?></td>
				<td><?=$option['is_front']?'前台权限':'后台权限'?></td>
				<td><?=$option['orderid']?></td>
				<td><?=$option['name']?></td>
				<td><?=$option['controller']?>/<?=$option['action']?></td>
				<td><?=$option['status']?></td>
				<td>
					<a href="/admin/privileges/edit?id=<?=$option['id']?>">修改</a>
					&nbsp;
					<a href="/admin/privileges/delete?id=<?=$option['id']?>" onclick="return confirm('您确定删除吗？');">删除</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>

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
