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
						<li class="active"><a href="###">我的登陆日志</a></li>
					</ul>
					<div class="pager">
							<?php if (!empty($data['pager'])){echo $data['pager']->output;} ?>
					</div>
				</div>
				<table cellspacing="0" cellpadding="0" class="torrenttable" style="margin-bottom:20px;">
					<tr>
						<th width="60">登陆时间</th>
						<th width="80">登陆IP</th>
					</tr>
					<?php foreach ($data['logs_login'] as $key => $row): ?>
					<tr>
						<td><?=date("Y-m-d H:i:s", $row['createtime'])?></td>
						<td><?=$row['ip']?></td>
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