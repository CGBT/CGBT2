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
						<li class="active"><a>客户端统计</a></li>
					</ul>
				</div>
				<table cellpadding="0" cellspacing="0" class="torrenttable">
					<tr>
						<th>序号</th>
						<th>客户端</th>
					</tr>
				<?php  foreach ($data['agentinfo'] as $key => $value):?>
					<tr>
						<td><?=$key+1?></td>
						<td><?=$value?></td>
					</tr>
				<?php  endforeach;  ?>
				</table>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
	<!--wp-->

<?php
include 'footer.php';
?>