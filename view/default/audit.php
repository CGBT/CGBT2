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
						<li<?php if($data['current_category']=='all'):?> class="active"<?php endif;?>><a href="/audit/">全部</a></li>
						<?php foreach($data['all_category'] as $category):?>
						<li<?php if ($data['current_category'] == $category['name_en']) {?> class="active"<?php }?>><a href="/audit/<?=$category['name_en']?>/"><?=$category['name']?></a></li>
					<?php endforeach;?>
					</ul>
					<div class="pager">						
						<?php echo $data['pager']->output; ?>
					</div>
				</div>
				<span style="font-size:18px;">审核区顶踩规则: 当顶的数量减去踩的数量大于10，则系统自动通过审核。版主也可以手工通过审核。顶踩操作后，数量显示约1分钟后更新。</span>
				<?php include "include_torrenttable.php"; ?>

				<div class="pagecontent">
					<div class="pager">
						<?php echo $data['pager']->output; ?>
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