<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div class="pagecontent clearfix">
					<ul class="nav pull-left">
						<li class="active"><a>竞价置顶</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
<table cellpadding="0" cellspacing="0" class="torrenttable">
	<tr>
		<th>No.</th>
		<th>竞价ID</th>
		<th>用户ID</th>
		<th>用户名</th>
		<th>种子ID</th>
		<th>起始时间</th>
		<th>结束时间</th>
		<th>出价类型</th>
		<th>出价额度</th>
		<th>折算为保种积分</th>
		<th>有效</th>
		<th>已删除</th>
		<?php if ($data['user']['is_admin']||$data['user']['is_moderator']):?>
		<th>操作</th>
		<?php endif;?>
	</tr>
<?php foreach ($data['rows'] as $key => $row):?>
<tr>
<td><?=$key+1?></td>
<td><?=$row['id']?></td>
<td><?=$row['uid']?></td>
<td><?=$row['username']?></td>
<td><?=$row['tid']?></td>
<td><?=date("Y-m-d H:i:s" ,$row['start_time'])?></td>
<td><?=date("Y-m-d H:i:s" ,$row['end_time'])?></td>
<td><?=$row['price_type'] == 'extcredits1' ? '保种积分' : ($row['price_type'] == 'uploaded' ? '上传流量' : '虚拟上传流量')?></td>
<td><?=$row['price']?></td>
<td><?=$row['sort_price']?></td>
<td><?=$row['enabled']=='1'?'是':'否'?></td>
<td><?=$row['status']=='-1'?'是':'否'?></td>
<?php if ($data['user']['is_admin']||$data['user']['is_moderator']):?>
<td><a href='#' onclick="delete_mod(<?=$row['id']?>);">删除</a></td>
<?php endif;?>
</tr>
<?php  endforeach;  ?>
</table>
</div>
<!-- end #mainContent -->
</div>
</div><!--wp-->

<?php if ($data['user']['is_admin']||$data['user']['is_moderator']):?>
<script type="text/javascript"> 
function delete_mod(id)
{
	if (confirm("确认删除"))
	{
		$.post('/toolbox/price_mod_delete/',{id: id, inajax: 1},function(data){
			ui.notify('提示', data).effect('slide');
		});
	}
}
</script>
<?php endif;?>


<?php
include 'footer.php';
?>