<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css">

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div style="clear: both"></div>
				<div class="pagecontent">
					<ul class="nav pull-left">
						<li class="active"><a href="###">我的积分日志</a></li>
					</ul>
					<div class="pager">
							<?php if (!empty($data['pager'])){echo $data['pager']->output;} ?>
					</div>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable"
					style="margin-bottom: 20px;">
					<tr>						
						<th>用户名</th>
						<th>时间</th>
						<th>积分类型</th>
						<th>积分数量</th>
						<th>操作类型</th>
						<th>操作人</th>
					</tr>
					<?php foreach ($data['rows_credits'] as $key => $row): ?>
					<tr>
						<td><?=$row['username']?></td>
						<td><?php echo date("Y-m-d H:i:s",$row['createtime']); ?></td>
						<td><?=$row['field']?></td>
						<td><?=$row['count']?></td>
						<td><?=$row['action']?></td>
						<td><?=$row['operator_username']?></td>
					</tr>
					<?php endforeach; ?>
				</table>
				<div class="pagecontent">
					<div class="pager">
						<?php if (!empty($data['pager'])){echo $data['pager']->output;} ?>
					</div>
				</div>
			</div>
			<!-- end #mainContent -->


		</div>
	</div>
</div>
<!--wp-->


<?php
include 'footer.php';
?>