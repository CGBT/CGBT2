<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css">

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<div class="pagecontent clearfix">
					<ul class="nav pull-left">
						<li <?php if ($data['action_name'] == 'sitelog_action') echo 'class="active"'; ?>><a href="/list/sitelog/">站点日志</a></li>
						<?php if (!empty($data['uid'])):?>
						<li <?php if ($data['action_name'] == 'mysitelog_action') echo 'class="active"'; ?>><a href="/list/mysitelog/">我的操作日志</a></li>
						<?php endif;?>
					</ul>
					<div class="pager">
							<?php if (!empty($data['pager'])){echo $data['pager']->output;} ?>
					</div>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable" style="margin-bottom:20px;">
					<tr>
						<th width="20">ID</th>
						<th width="60">时间</th>
						<th width="60">操作人</th>
						<th width="80">操作类型</th>
						<th class="l">内容</th>
					</tr>
					<?php foreach ($data['rows'] as $key => $row): ?>
					<tr>
						<td><?=$key+1?></td>
						<td><?=date("Y-m-d H:i:s", $row['createtime'])?></td>
						<td><?=$row['username']?></td>
						<td><?=$row['action']?></td>
						<td class="l"><? print_r($row['details'])?></td>

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