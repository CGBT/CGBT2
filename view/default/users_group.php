<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css">

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl box-inside-detail">

		<ul class="nav ptl20">
			<li class="active"><a href="javascript:void(0);">用户组及权限列表、限制下载规则</a></li>
		</ul>
		<div class="clearfix">
				<div class="tab-box info">
					<div class="tabs-group">
						<div class="user-details-tabs">
							<div>
								<h2>共享率限制标准</h2>
								<?=$data['ratio_limit_msg']?>
							</div>
						</div>
						<div class="user-details-tabs purple">
							<div>
								<h2>总积分计算公式：</h2>
								<br />
				(上传量下载量发种容量单位为G，发种数和发种容量暂时没有更新)<br />
				总积分=(上传量)/(ln((上传量)+2)+6)-(下载量)/155*ln((下载量)+1)+ (发种数)*1.3+(发种容量)/4+5000*(1-e^(-(保种积分)/(10^4*3)))<br />
				<br /><br />
							</div>
						</div>
					</div>
					<div class="tabs-group">
						<div class="user-details-tabs orange">
							<div>
								<h2>保种容量限制标准<?php if (!$data['setting']['enable_seed_size_limit']):?> (未启用)<?php endif;?></h2>
								<?=$data['seed_size_limit_msg']?>
							</div>
						</div>
						<div class="user-details-tabs green">
							<div>
								<h2>保种数量限制标准<?php if (!$data['setting']['enable_seed_count_limit']):?> (未启用)<?php endif;?></h2>
								<?=$data['seed_count_limit_msg']?>
							</div>
						</div>
					</div>
				</div>

				<table cellspacing="0" cellpadding="0" class="torrenttable">
				<?php foreach ($data['users_group'] as $key => $row): ?>
					<tr>
						<?php foreach ($row as $k => $v): ?>
						<td>
						<?php if ($v=='yes') : ?>
						<img src="/static/images/yes.gif">
						<?php elseif ($v=='no') : ?>
						<img src="/static/images/no.gif">
						<?php else: ?>
						<?=$v?>
						<?php endif;?>
						</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</table>
		</div>
		<!-- end #mainContent -->


	</div>
</div>
<!--wp-->


<?php
include 'footer.php';
?>