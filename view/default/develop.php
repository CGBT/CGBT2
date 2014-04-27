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
						<li class="active"><a>晨光BT开发日志 (版本：CGBTSource v1.0 Alpha) (欢迎各位用户提建议提需求)</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable">
					<tr>
						<th width="50">ID</th>
						<th class="l">标题</th>
						<th width="80">状态</th>
						<th width="150">时间</th>
					</tr>
					<?php foreach ($data['rows'] as $key => $row): ?>
					<tr>
						<td><?=$key+1?></td>
						<td class="l"><?=$row['title']?></td>
						<td><?=$row['status']?></td>
						<td><?=$row['cdatetime']?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
</div>
	<!--wp-->


<?php
include 'footer.php';
?>