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
						<li class="active"><a>常用软件</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<div class="download-list pull-left">
					<?php foreach ($data['rows_soft'] as $key => $row): ?>
					<div class="download-item">
						<div class="pull-left info">
							<h3><a href='/list/softdown/?id=<?=$row['id']?>' target="_blank"><?=$row['title']?></a></h3>
							<p>
								<?=$row['memo']?>
							</p>
							<p>
								更新时间：<?=$row['updatetime']?>|下载次数：<?=$row['download']?>
							</p>
						</div>
						<div class="pull-right">
							<?php if (!empty($row['link2'])): ?>
							<a href='<?=$row['link2']?>' class="button button-blue" target="_blank">百度网盘下载</a>
							<?php endif;?>
							<a href='/list/softdown/?id=<?=$row['id']?>' class="button button-blue" target="_blank">下载</a>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<div class="pull-left wp30">
					<table cellspacing="0" cellpadding="0" class="torrenttable" style="width:90%;margin-left:20px;">
						<tr>
							<th>晨光不支持的客户端列表</th>
						</tr>
						<?php foreach ($data['black_agent'] as $key => $agent): ?>
						<tr>
							<td><?=$agent?></td>		
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
				<div class="clearfix"></div>
			</div>
			<!-- end #mainContent -->

		
		</div>
	</div>
	<!--wp-->


<?php
include 'footer.php';
?>