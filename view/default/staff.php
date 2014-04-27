<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="mainContent">
				<!--search-->
				<?php foreach ($data['users'] as $gkey => $guser){ ?>
				<div class="pagecontent clearfix">
					<ul class="nav pull-left">
						<li class="active"><a><?=$gkey?></a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<table cellpadding="0" cellspacing="0" class="torrenttable">
					<tr>
						<th>No.</th>
						<th>UID</th>
						<th>用户名</th>
						<th>上传流量</th>
						<th>下载流量</th>
						<th>注册时间</th>
						<th>最后访问时间</th>
						<th>用户组</th>
						<th>职责</th>
						<th>在线</th>
					</tr>
				<?php  foreach ($guser as $ikey => $iuser):?>
				<tr>
				<td><?=$ikey+1?></td>
				<td><?=$iuser['uid']?></td>
				<td><a href="/user/<?=$iuser['uid']?>/" class="bluelink" target="_blank"><?=$iuser['username']?></a></td>
				<td class='r'><?=$iuser['uploaded_text']?></td>
				<td class='r'><?=$iuser['downloaded_text']?></td>
				<td><?=date("Y-m-d H:i:s" ,$iuser['createtime'])?></td>
				<td><?=date("Y-m-d H:i:s" ,$iuser['last_access'])?></td>
				<td><?=$iuser['group_name']?></td>
				<td><?=$iuser['duty']?></td>
				<?php if(!$iuser['is_online']) { ?>
				<td><a href='/user/<?=$iuser['uid']?>/'><img src='/static/images/offline.png' alt='不在线' /></a></td>
				<?php } elseif ($iuser['is_online']) {?>
				<td><a href='/user/<?=$iuser['uid']?>/'><img src='/static/images/online.png' alt='在线' /></a></td>
				<?php }?>
				</tr>
				<?php  endforeach;  ?>
				</table>

<?php } ?>
</div>
<!-- end #mainContent -->
</div>
</div><!--wp-->


<?php
include 'footer.php';
?>