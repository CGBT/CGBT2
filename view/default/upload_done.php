<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >

<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">				
				<div style="width:95%;font-size:16px;font-weight:bold;padding-left:20px;">
				<br />
				注意：您的种子尚未发布完成。<br />
				<span style='color:red;font-size:20px;font-weight:bold;'>
				请下载您刚发布的种子并用优特(uTorrent)打开，保存到你的电脑上资源文件所在的位置，选中跳过散列检查。</span><br />
				<a href='/torrents/<?=$data['tid']?>/download/' style='font-size:20px;color:blue;text-decoration:underline'>点击此处下载</a><br />

				<?php if (count($data['torrents']) > 0 ):?>
				<br />		
				<span style='color:red;font-size:40px;'>注意：</span><br />
				您发布的种子可能与下列种子重复，请确认。如果重复，请到审核区或种子列表删除刚刚发布种子，然后下载已有的种子进行保种！<br />			
				</div>
				<?php include "include_torrenttable.php"; ?>
				<?php else: ?>
				<br />
				<br />
				<br />
				<br />

				</div>
				<?php endif; ?>
			</div>
			<!-- end #mainContent -->

		
		</div>
	</div>
	<!--wp-->

<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
<?php include "include_auditbox.php"; ?>
<?php endif; ?>

<?php include "include_deletebox.php"; ?>

<?php
include 'footer.php';
?>