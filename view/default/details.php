<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css" >
<div class="breadcrumbs-fixed">
	<a href="javascript:;" class="ml10 toggle-control-btn active" type="torrent-detail-descr">查看详情</a>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-file">文件：<?=$data['current_torrent']['files']?></a>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-seed">种子：<?=$data['current_torrent']['seeder']?></a>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-download">下载：<?=$data['current_torrent']['leecher']?></a>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-done">完成：<?=$data['current_torrent']['complete']?></a>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-attachments">附件：<?=$data['subtitles_count']?></a>
	<?php if (!empty($data['current_torrent']['imdb'])):?>
	<a href="javascript:;" class="ml10 toggle-control-btn" type="torrent-detail-versions">其他版本</a>
	<?php endif;?>
	<a href="/torrents/<?=$data['current_torrent']['id']?>/download/" class="ml10" onclick="return alert_price(0)">种子下载</a> 
</div>
<div id="wp" class="wp"
><div id="ct" class="ct2_a wp cl box-inside-detail">
<ul class="breadcrumbs">
    <li><a href="/search/" class="breadcrumb">种子列表</a></li>
    <li><a href="/search/<?=$data['current_torrent']['category']?>/" class="breadcrumb"><?=$data['current_torrent']['category_name']?></a></li>
    <li><span class="breadcrumb current"><?=$data['current_torrent']['title']?></span></li>
</ul>
<?php if (empty($data['current_torrent']['seeder'])):?>
<div class="alert-none">
	暂时没有种子！<button class="button button-red" id="req-seed">☁ 请求补种</button>
</div> 
<?php endif; ?>
<div class="torrent-detail-box">
	<div id="threadstamp">
		<?php if (!empty($data['current_torrent']['stamps'])):?>
		<img src="/static/images/stamps/<?=$data['current_torrent']['stamps']?>">
		<?php endif; ?>
	</div>
	<div class="torrent-head-l">
		<?php if ($data['current_torrent']['anonymous']): ?>
			<img src="<?=$data['setting']['forums_url']?>uc_server/avatar.php?uid=0&size=middle" alt="发种人头像">
		<?php else: ?>
			<a href="/user/<?=$data['torrent_user']['uid']?>/" data-uid="<?=$data['torrent_user']['uid']?>">
			<img src="<?=$data['setting']['forums_url']?>uc_server/avatar.php?uid=<?=$data['torrent_user']['forums_uid']?>&size=middle" alt="发种人头像">
			</a>
		<?php endif;?>
	</div>
	<div class="torrent-head-r">
		<div class="torrent-title info">
			<h1>种子名称：<?=$data['current_torrent']['title']?></h1>
		</div>
		<div class="info">
			<span class="torrent-seeder"><img src="/static/images/users.png">发种人：
				<?php if ($data['current_torrent']['anonymous']): ?>				
					匿名				
				<?php else: ?>
				<a href="/user/<?=$data['torrent_user']['uid']?>/" data-uid="<?=$data['torrent_user']['uid']?>" title="<?php if ( !empty($data['torrent_user']['title']) && $data['torrent_user']['title'] != $data['torrent_user']['username'] ):?><?=$data['torrent_user']['title']?><?php endif;?>">
					<?=$data['current_torrent']['username']?>
					(<?=$data['torrent_user']['group_name']?>)
				</a>
				<?php endif;?>
			</span>
			<span>
				<a href="/torrents/<?=$data['current_torrent']['id']?>/download/" class="button button-blue" onclick="return alert_price(<?=$data['current_torrent']['price']?>)">▼ 下载种子</a>
				<a tid="<?=$data['current_torrent']['id']?>" href="javascript:void(0);" class="fav_link button button-red"><?php if (empty($data['favorite_status'])){echo "❤ 收藏";}else{echo "❤ 已收藏";}?></a>
				<a href="javascript:void(0);" title="已收到<?=$data['sum_award']?>保种积分！" class="button button-red" id="thumbup">
					✯ 奖励保种积分<?php if (!empty($data['sum_award'])):?><i>+<?=$data['sum_award']?></i><?php endif;?>
				</a>
				<?php if ($data['user']['is_moderator']||$data['user']['is_admin']||$data['uid']==$data['current_torrent']['uid']):?>
				<a href="/torrents/<?=$data['current_torrent']['id']?>/edit/" class="button button-green">✒ 修改</a>
				<?php endif;?>
				<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
				<a href="javascript:void(0);" class="delete_link button button-green" tid="<?=$data['current_torrent']['id']?>">✖ 删除</a>
				<a href="javascript:void(0);" class="audit_link button button-green" tid="<?=$data['current_torrent']['id']?>">✔ 审核</a>
				<?php endif;?>
				<a href="/torrents/<?=$data['current_torrent']['id']?>/play/" target="_blank" class="button button-green">► 网页播放</a>
				<?php if ( empty($data['current_torrent']['anonymous'])): ?>
				<a href="<?=$data['setting']['forums_url']?>home.php?mod=spacecp&ac=pm&op=showmsg&touid=<?=$data['torrent_user']['forums_uid']?>" target="_blank" class="button button-red">✉ 私信</a>
        		<?php endif;?>
        	</span>
		</div>
		<div class="info">
			<span class="tag">大小：<?=$data['current_torrent']['size_text']?></span>
			<span class="tag">回复/查看：<?=$data['current_torrent']['comment_count']?>/<?=$data['current_torrent']['view']?></span>
			<span class="tag">发布时间：<?=$data['current_torrent']['simple_createtime']?></span>
			<span class="tag">最后做种时间：<?=$data['current_torrent']['simple_last_action']?></span>
			<span class="tag">种子ID：<?=$data['current_torrent']['id']?></span>
		</div>
		<div class="info">
			<span class="tag toggle-control-btn active" type="torrent-detail-descr">查看详情</span>
			<span class="tag toggle-control-btn" type="torrent-detail-file">文件：<?=$data['current_torrent']['files']?></span>
			<span class="tag toggle-control-btn" type="torrent-detail-seed">种子：<?=$data['current_torrent']['seeder']?></span>
			<span class="tag toggle-control-btn" type="torrent-detail-download">下载：<?=$data['current_torrent']['leecher']?></span>
			<span class="tag toggle-control-btn" type="torrent-detail-done">完成：<?=$data['current_torrent']['complete']?></span>
			<span class="tag toggle-control-btn" type="torrent-detail-attachments">附件：<span<?php if (!empty($data['subtitles_count'])): ?> style="color:red;font-weight:bold;"<?php endif;?>><?=$data['subtitles_count']?></span></span>
			<?php if (!empty($data['current_torrent']['imdb'])):?>
			<span class="tag toggle-control-btn" type="torrent-detail-versions">其他版本</span>
			<?php endif;?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="tab-control-item" id="torrent-detail-descr" style="display:block;">
	<?=$data['current_torrent']['descr']['descr']?>
</div>
<div class="tab-control-item" id="torrent-detail-file">
	<table cellspacing="0" cellpadding="0" class="torrenttable">
		<tbody>
			<tr>
				<th class="l">文件</th>
				<th width="150">大小</th>
			</tr>	
		</tbody>
	</table>
</div>
<div class="tab-control-item" id="torrent-detail-seed">
	<table cellspacing="0" cellpadding="0" class="torrenttable">
		<tbody>
			<tr>
				<th width="100">用户名</th>
				<th width="100">用户组</th>
				<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
				<th width="150">IP</th>
				<?php endif; ?>
				<th width="100">已上传</th>
				<th width="100">上传速度</th>
				<th width="100">已下载</th>
				<th width="100">下载速度</th>
				<th width="150">最近事件</th>
				<th width="150">最后活动时间</th>
				<th width="150">创建时间</th>
				<th width="150">完成时间</th>
				<th width="150">客户端</th>
			</tr>	
		</tbody>
	</table>
</div>
<div class="tab-control-item" id="torrent-detail-download">
	<table cellspacing="0" cellpadding="0" class="torrenttable">
		<tbody>
			<tr>
				<th width="100">用户名</th>
				<th width="100">用户组</th>
				<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
				<th width="150">IP</th>
				<?php endif; ?>
				<th width="100">已上传</th>
				<th width="100">上传速度</th>
				<th width="100">已下载</th>
				<th width="100">下载速度</th>
				<th width="150">最近事件</th>
				<th width="150">最后活动时间</th>
				<th width="150">创建时间</th>
				<th width="150">客户端</th>
			</tr>	
		</tbody>
	</table>
</div>
<div class="tab-control-item" id="torrent-detail-done">
	<table cellspacing="0" cellpadding="0" class="torrenttable">
		<tbody>
			<tr>
				<th width="150">用户名</th>
				<th width="150">用户组</th>
				<th width="150">用户UID</th>
				<th width="150">完成时间</th>
			</tr>	
		</tbody>
	</table>
</div>
<div class="tab-control-item" id="torrent-detail-attachments">
	<div class="margin-field">
		<span id="subs_upload"></span>
		<div id="divFileProgressContainer"></div>
		<div id="upload-field"></div>
	</div>
	<table cellspacing="0" cellpadding="0" class="torrenttable">
		<tbody>
			<tr>
				<th width="150">文件名</th>
				<th width="150">上传者</th>
				<th width="150">上传时间</th>
				<th width="150">下载次数</th>
				<th width="150">操作</th>
			</tr>	
		</tbody>
	</table>
</div>
<div class="tab-control-item" id="torrent-detail-versions"></div>
<div class="comment-list">
	<div class="comment-item" id="comments-header">
		<span class="button-group pull-right">
		    <a href="javascript:void(0);" class="button button-icon icon-prev">&lt;</a>
		    <a href="javascript:void(0);" class="button button-icon icon-next">&gt;</a>
		</span>
		<span class="pull-left header">种子评论</span>
		<div class="clearfix"></div>
	</div>
	<div id="comments">
		
	</div>
	<div class="comment-item">
		<span class="button-group pull-right">
		    <a href="javascript:void(0);" class="button button-icon icon-prev">&lt;</a>
		    <a href="javascript:void(0);" class="button button-icon icon-next">&gt;</a>
		</span>
		<div class="clearfix"></div>
	</div>
	<div class="comment-item">
		<input type="button" class="button button-blue mb10" value="表情" id="emo-display"><br>
		<?php include "include_emo.php"; ?>
		<textarea id="message" placeholder="至少回复5个字符，按ctrl+回车直接回复"></textarea>
		<br>
		<input type="button" value="发表回复" class="button button-blue" id="post-message">
		<input type="button" value="清空" class="button" id="reset-message">
	</div>
</div>
</div>
</div><!--wp-->
<script>
$(function(){
	function getdatashow(obj){
		if (obj=="torrent-detail-file") {
			$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/files/",function(data)
			{
				if ($("#"+obj+" table tbody tr").length<2) {
					$.each(data,function(index,value){
						var html = "<tr><td class='l'>"+value.filename+"</td><td>"+value.size_text+"</td></tr>"
						$("#"+obj+" table tbody").append(html);
					});
				}
			}
			);
		}
		else if (obj=="torrent-detail-seed") {
			$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/seeders/",function(data)
			{
				if ($("#"+obj+" table tbody tr").length<2) {
					$.each(data,function(index,value){
						var html = "<tr><td><a href='/user/"+value.uid+"/' class='bluelink' target='_blank'>"+value.username+"</a></td>";
						html = html +"<td>"+value.group_name+"</td>";
						<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
						html = html +"<td>"+value.ip;
						if (value.ipv6 != value.ip)
						{
							html = html +"<br>"+value.ipv6;
						}
						html = html + "</td>";
						<?php endif; ?>
						html = html +"<td>"+value.uploaded_text+"</td>";
						html = html +"<td>"+value.upload_speed+"</td>";
						html = html +"<td>"+value.downloaded_text+"</td>";
						html = html +"<td>"+value.download_speed+"</td>";
						html = html +"<td>"+value.last_event+"<br>已完成："+value.finished+"</td>";
						html = html +"<td>"+value.last_action_text+"</td>";
						html = html +"<td>"+value.createtime_text+"</td>";
						html = html +"<td>"+value.completed_time_text+"</td>";
						html = html +"<td>"+value.agent+"<br>(端口："+value.port+")</td></tr>";
						$("#"+obj+" table tbody").append(html);	
					});
				}
			}
			);
		}
		else if (obj=="torrent-detail-download") {
			$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/leechers/",function(data)
			{
				if ($("#"+obj+" table tbody tr").length<2) {
					$.each(data,function(index,value){
						var html = "<tr><td><a href='/user/"+value.uid+"/' class='bluelink' target='_blank'>"+value.username+"</a></td>";
						html = html +"<td>"+value.group_name+"</td>";
						<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
						html = html +"<td>"+value.ip;
						if (value.ipv6 != value.ip)
						{
							html = html +"<br>"+value.ipv6;
						}
						html = html + "</td>";
						<?php endif; ?>
						html = html +"<td>"+value.uploaded_text+"</td>";
						html = html +"<td>"+value.upload_speed+"</td>";
						html = html +"<td>"+value.downloaded_text+"</td>";
						html = html +"<td>"+value.download_speed+"</td>";
						html = html +"<td>"+value.last_event+"<br>已完成："+value.finished+"</td>";
						html = html +"<td>"+value.last_action_text+"</td>";
						html = html +"<td>"+value.createtime_text+"</td>";
						html = html +"<td>"+value.agent+"<br>(端口："+value.port+")</td></tr>";
						$("#"+obj+" table tbody").append(html);	
					});
				}
			}
			);
		}
		else if (obj=="torrent-detail-done") {
			$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/completed_users/",function(data)
			{
				if ($("#"+obj+" table tbody tr").length<2) {
					$.each(data,function(index,value){
						var html = "<tr><td><a href='/user/"+value.uid+"/' class='bluelink' target='_blank'>"+value.username+"</a></td><td>"+value.group_name+"</td><td>"+value.uid+"</td><td>"+value.createtime_text+"</td></tr>"
						$("#"+obj+" table tbody").append(html);
					});
				}
			}
			);
		}
		else if (obj=="torrent-detail-attachments") {
			$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/subtitles/",function(data)
			{
				if ($("#"+obj+" table tbody tr").length<2) {
					$.each(data,function(index,value){
						var html = "<tr><td>"+value.old_name+"</td><td>"+value.username+"</td><td>"+value.createtime_text+"</td>";
						html = html +"<td>"+value.download+"</td><td><a class='button button-green' href='/subtitles/"+value.id+"/download/'>下载</a></td></tr>"
						$("#"+obj+" table tbody").append(html);
					});
				}
			}
			);
		}
		else if (obj=="torrent-detail-versions") {
			$("#"+obj).load("/torrents/<?=$data['current_torrent']['id']?>/versions/");
		}
		$("#"+obj).show();
	}
	$(".toggle-control-btn").click(function(){
		$(".toggle-control-btn").removeClass("active");
		var type = $(this).attr("type");
		$(".toggle-control-btn[type='"+type+"']").addClass("active");
		var count = $(this).index()+1;
		$(".torrent-detail-box").removeAttr("id")
		$(".tab-control-item").hide();
		$(".torrent-detail-box").attr("id","tag-"+count);
		getdatashow(type);
	});
	function get_comments(page){
		$.getJSON("/torrents/<?=$data['current_torrent']['id']?>/comments/?page="+page,function(data){
			if (data!="") {
				$("#comments").html("");
				$.each(data,function(index,value){
					index = parseInt(index)+(page-1)*20+1;
					var html = '<div class="comment-item">';
					html = html +'<table cellpadding="0" cellspacing="0" border="0" width="100%">';
				    html = html +'<tbody>';
				    html = html +'<tr>';
				    html = html +'<td width="48" valign="top" align="center"><a href="/users/'+value.author+'" target="_blank"><img src="<?=$data['setting']['forums_url']?>uc_server/avatar.php?uid='+value.authorid+'&size=small" class="avatar"></a></td>';
				    html = html +'<td width="15" valign="top"></td>';
				    html = html +'<td width="auto" valign="top" align="left"><div class="pull-right"> <a href="javascript:;" class="replyTo">回复</a> &nbsp;&nbsp; <span class="no">'+index+'</span></div>';
				    html = html +'<div class="sep3"></div>';
				    html = html +'<strong><a href="/users/'+value.author+'" target="_blank">'+value.author+'</a></strong>&nbsp; &nbsp;<span class="fade small">'+value.createtime+'</span>';
				    html = html +'<div class="sep5"></div>';
				    html = html +'<div class="comment-content">'+value.message+'</div>';
				    html = html +'</td>';
				    html = html +'</tr>';
				    html = html +'</tbody>';
				    html = html +'</table>';
					html = html +'</div>';
					$("#comments").append(html);
				})
			}
			else{
				$("#comments").html("<div class='alert-none'>暂无评论</div>");
			}
		});
	};
	var page = 1;
	if (location.hash!="") {
		page = location.hash.slice(2)
	};
	get_comments(page);
	$(".icon-prev,.icon-next").click(function(){
		if ($("#comments .alert-none").html()!="暂无评论"||page>1) {
			if ($(this).attr("class").indexOf("icon-prev")>-1) {
				page = page-1;
			}
			else{
				page = page+1;
			}
			get_comments(page);
			$("html,body").animate({scrollTop: $("#comments-header").offset().top-40}, 500);
			return page;
		}
		else{ui.notify('提示',"没有更多评论了！").effect('slide');}
	});
	function replyTo(username){
    	replyContent = $("#message");
		oldContent = replyContent.val();
		prefix = "@" + username + " ";
		newContent = ''
		if(oldContent.length > 0){
		    if (oldContent != prefix) {
		        newContent = oldContent + "\n" + prefix;
		    }
		} else {
		    newContent = prefix
		}
		replyContent.focus();
		replyContent.val(newContent);
		$("html,body").animate({scrollTop: $("#message").offset().middle}, 500);
	}
	$(".replyTo").live("click",function(){
		username = $(this).parent().parent().find("strong a").text();
		replyTo(username);
	});
	function reset_comment(){
		$("#message").val("");
	}
	function post_comment(){
		var comment = $("#message").val();
		if($("#comments .alert-none").html()=="暂无评论"){
			$("#comments").html("");
		}
		if (comment.length>4) {
			$.post("/torrents/<?=$data['current_torrent']['id']?>/post_comments/",{message:comment,inajax:1},function(result){
				if (result.error)
				{
					ui.notify('错误', result.msg).effect('slide');
					$(this).attr("disabled",false);
				}
				else
				{
					reset_comment();
					get_comments(page);
					$(this).attr("disabled",false);
				}
  			},"json");
		}
		else
		{
			ui.notify('错误', "请回复至少5个字符！").effect('slide');
		}
	}
	$("#message").keydown(function(e) {
        if ((e.ctrlKey || e.metaKey) && e.which === 13) {
            e.preventDefault();
            post_comment();
        }
    });
	$("#reset-message").click(function(){
		reset_comment();
	});
	$("#post-message").click(function(){
		$(this).attr("disabled",true);
  		post_comment();
	});
	$('.fav_link').click(function(){
		var tid = $(this).attr("tid");
		var url="/torrents/"+tid+"/favorite";
		$.post(url,{},function(data){
			if (data==="收藏成功!") {ui.notify('提示', data+"再次点击取消收藏").effect('slide');$(".fav_link").text("❤ 已收藏");}
			else if (data==="取消收藏成功!") {$(".fav_link").text("❤ 收藏");};
		});
	});
	$("#fastpostsmilie td img").click(function(){
		url = $(this).attr("src");
		old = $("#message").val();
		$("#message").val(old+"[img]"+url+"[/img]");
	});
	$("#emo-display").click(function(){
		$("#fastpostsmilie td img").each(function(){
			$(this).attr("src",$(this).attr("lsrc"));
		});
		$("#fastpostsmilie").toggle();
	});
	$("#req-seed").click(function(){
		var msg = "请求补种将给最近100个下载过该种子的用户发短消息提醒补种。同时扣除您<?=$data['setting']['req_seed_extcredits1']?> 个保种积分。";
		if(confirm(msg) ){
   			$.post("/torrents/<?=$data['current_torrent']['id']?>/req_seed/",{inajax:1},function(data){ui.notify('提示',data.msg).effect('slide');},"json")
		}
	});
})
//显示顶部切换栏
$(window).scroll(function() {
	if ($(this).scrollTop() > 360) {
		$('.breadcrumbs-fixed').fadeIn(500);
	} else {
		$('.breadcrumbs-fixed').fadeOut(300);
	}
});

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
</script>
<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
<?php include "include_auditbox.php"; ?>
<?php endif; ?>
<?php include "include_floatbox.php"; ?>
<?php include "include_deletebox.php"; ?>
<?php include "include_subtitles_upload.php"; ?>

<?php
include 'footer.php';
?>