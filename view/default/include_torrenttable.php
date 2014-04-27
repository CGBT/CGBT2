<table cellspacing="0" cellpadding="0" class="torrenttable">
	<tr>
		<th width="38"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o8">类别</a></th>
		<th class="l">名称 (种子数目:<?=$data['torrents_count']?>)</th>
		<th width="75">操作</th>
		<th width="55"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o6">大小</a></th>
		<th width="30"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o7">文件</a></th>
		<?php if ($data['controller_name'] != 'audit_controller'): ?>
		<th width="60"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o4">点击</a></th>
		<?php endif;?>
		<th width="50"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o1">发布时间</a></th>
		<th width="26"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o2">种子</a></th>
		<?php if ($data['controller_name'] == 'audit_controller'): ?>
		<th width="50">审核结果</th>
		<?php endif;?>
		<?php if ($data['controller_name'] != 'audit_controller'): ?>
		<th width="26"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o3">下载</a></th>
		<th width="30"><a href="javascript:void(0);" onclick="check(this);" type="link" val="o5">完成</a></th>
		<?php endif;?>
		<th width="88" align="middle">发布者</th>
	</tr>
	<?php foreach ($data['torrents'] as $key => $torrent): ?>
	<tr<?php if (isset($torrent['is_mod_top'])): ?> class="c2"<?php elseif ($torrent['istop']): ?> class="c1"<?php endif; ?> id="t<?=$torrent['id']?>">
		<td class="icon-td"><img src="/static/images/catpic/<?=$torrent['category_icon']?>"></td>
		<td class="l">
			<?php if (isset($torrent['is_mod_top'])): ?>
			<span class="top"><img src="/static/images/price_mod_top.png"></span>
			<?php elseif ($torrent['istop']): ?>
			<span class="top"><img src="/static/images/top.gif"></span>
			<?php elseif ($torrent['isrecommend']):?>
			<span class="cmd"><img src="/static/images/rec.gif"></span>
			<?php endif;?>

			<a href='/torrents/<?=$torrent['id']?>/' target='_blank' name="title"><?=$torrent['title']?></a>

			<?php if ($torrent['iscollection']): ?>
			<span class="top"><img src="/static/images/col.png"></span>
			<?php endif;?>
			<?php if ($torrent['ishd']):?>
			<span class="cmd"><img src="/static/images/hd.png"></span>
			<?php endif;?>

			<?php if ($torrent['isft']): ?>
			<img src='/static/images/ft.png'>
			<?php endif;?>
			<?php if ($torrent['ishot']): ?>
			<img src='/static/images/hot.png'>
			<?php endif;?>
			<?php if ($torrent['createtime'] > $data['user']['last_browse']): ?>
			<img src='/static/images/btn_new.gif' class="new_flag">
			<?php endif;?>
			<?php if ($torrent['upload_factor'] > 1): ?>
			<span class="factor factor<?=$torrent['upload_factor']?>">
			<a target="_blank" href="<?=$data['setting']['upload_factor_link']?>"><?=$torrent['upload_factor']?></a></span>
			<?php endif;?>

			<?php if ($torrent['isfree'] || $torrent['auto_isfree']): ?>
			<img src="/static/images/btn_free.gif">
			<?php elseif ($torrent['is30p'] || $torrent['auto_is30p']):?>
			<img src="/static/images/btn_30p.gif">
			<?php elseif ($torrent['ishalf'] || $torrent['auto_ishalf']):?>
			<img src="/static/images/btn_50p.gif">
			<?php endif;?>

			<?php if (isset($torrent['mod']['free']['remain_time'])):?>
				<span class="clock-red"><img src="/static/images/clock.png"> <?=$torrent['mod']['free']['remain_time']?></span>
			<?php endif;?>
			<?php if ($data['controller_name'] == 'audit_controller'): ?>
			<span class="rate-group">
				<button class="button button-red"><img src="/static/images/thumbs_up.png"> 顶 ：<span><?=$torrent['support']?></span></button>
				<button class="button button-green"><img src="/static/images/thumbs_down.png"> 踩 ：<span><?=$torrent['against']?></span></button>
			</span>
			<?php endif;?>
			</td>
		<td>
			<div class="button-group group-for-drop">
				<a href="javascript:;" class="button thumbs-up">感谢</a>
				<ul class="dropdown-menu">
					<?php if (!isset($data['user']['privileges']['display_download_link']) || $data['user']['privileges']['display_download_link']): ?>
						<?php if ( $torrent['status'] <= 0 || $torrent['istop'] || $torrent['isfree'] || $torrent['auto_isfree'] || $torrent['isrecommend'] || $torrent['uid'] == $data['uid'] || $torrent['price'] == 0 || $data['user']['is_moderator'] || $data['user']['is_admin'] ): ?>
						<li><a href="/torrents/<?=$torrent['id']?>/download/" >下载</a></li>
						<?php else : ?>
						<li><a href="/torrents/<?=$torrent['id']?>/download/" onclick="return alert_price(<?=$torrent['price']?>)">下载</a></li>
						<?php endif; ?>
					<?php endif; ?>
	            
		            <?php if ($data['user']['is_moderator']||$data['user']['is_admin']||$data['uid']==$torrent['uid']):?>
					<li><a href="/torrents/<?=$torrent['id']?>/edit/">修改</a></li>
					<li><a tid="<?=$torrent['id']?>" href="javascript:void(0);" class="delete_link">删除</a></li>
					<?php endif;?>
	            	<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
		            <li><a href="javascript:;" tid="<?=$torrent['id']?>" class="prop_link">属性</a></li>
		            <li><a href="javascript:;" tid="<?=$torrent['id']?>" class="audit_link">审核</a></li>
					<?php endif;?>
		            <li><a tid="<?=$torrent['id']?>" src="targetBox-3" href="javascript:void(0);" class="fav_link">收藏</a></li>
		        	</ul>
	        	<a href="javascript:;" class="button button-for-drop"><span class="caret"></span></a>
        	</div>
		    </td>
		<td class="r"><a href='/torrents/<?=$torrent['id']?>/details/' target='_blank' class='bluelink'><?=$torrent['size_text']?></a></td>
		<td><?=$torrent['files']?></td>
		<?php if ($data['controller_name'] != 'audit_controller'): ?>
		<td><?=$torrent['view']?></td>
			<?php endif;?>
		<td><?=$torrent['simple_createtime']?></td>
		<td><?=$torrent['seeder']?></td>
		<?php if ($data['controller_name'] == 'audit_controller'): ?>
		<td class="audit-result"><?=$torrent['audit_note']?></td>
		<?php endif;?>
		<?php if ($data['controller_name'] != 'audit_controller'): ?>
		<td><?=$torrent['leecher']?></td>
		<td><?=$torrent['complete']?></td>
		<?php endif;?>
		<?php if ($torrent['anonymous']): ?>
		<td title="匿名">匿名</td>
		<?php else:?>
		<td><a href="/user/<?=$torrent['uid']?>/" data-uid="<?=$torrent['uid']?>" class="bluelink" target="_blank"><?=$torrent['user_title']?></a></td>
		<?php endif;?>
	</tr>
	<?php endforeach; ?>
</table>

<script type="text/javascript">
function alert_price(price)
{
	if (price > 0)
	{
		if (confirm('下载本种子将扣除您'+price+'保种积分,确认下载?'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
$(function(){
	$('.fav_link').click(function(){
		var tid = $(this).attr("tid");
		var url="/torrents/"+tid+"/favorite";
		$.post(url,{},function(data){
			ui.notify('提示', data).effect('slide');
		});
	});
	$(".button-for-drop").hover(function(){$(this).prev("ul").addClass("hover")})
	$(".group-for-drop").mouseleave(function(){$(this).children("ul").removeClass("hover")})
	$("body").trigger("powerfloat");
	<?php if ($data['controller_name'] == 'audit_controller'): ?>
	$(".rate-group .button").click(function(){
		var obj = $(this);
		var tid = obj.parents("tr").attr("id").substring(1);
		var type = obj.hasClass("button-red")?type="support":type="against";
		$.post("/torrents/"+tid+"/rate/",{type:type,inajax:1},function(data){
			if (data.msg==="感谢参与！") {
				obj.find("span").text(parseInt(obj.find("span").text())+1);
			};
			ui.notify('提示', data.msg).effect('slide');
		},"json")
	})
	<?php endif;?>
})

</script>
<?php include "include_award.php"; ?>
<?php include "include_update_prop.php"; ?>
<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
<?php include "include_auditbox.php"; ?>
<?php endif; ?>

<?php include "include_deletebox.php"; ?>