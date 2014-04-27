<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$data['title'].$data['setting']['site_name']?></title>
<link rel="stylesheet" type="text/css" href="<?=$data['setting']['forums_url']?>data/cache/style_<?=$data['styleid']?>_common.css?<?=$data['verhash']?>" />
<?php if ($data['default_style'] != ''): ?>
<link rel="stylesheet" id="extstyle" type="text/css" href="<?=$data['setting']['forums_url']?>template/default/style/<?=$data['default_style']?>/style.css" />
<?php endif;?>
<link rel="stylesheet" href="/static/css/normal.css?version=20140103" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/jquery-powerFloat-min.js"></script>
<script type="text/javascript" src="/static/js/index.js?v=13815"></script>
<script type="text/javascript" src="/static/js/ui.js"></script>
<script type="text/javascript">
var cookie_domain = "<?=$data['cookie_domain']?>";
</script>
</head>
<?php 
if (!empty($data['header_background_pic'])): 
?>
<style>
body{background-image:url(<?=$data['header_background_pic']?>);padding-top:<?=$data['header_background_pic_padding']?>px;}
</style>
<?php 
if (!empty($data['header_background_pic_link'])): 
?>
<a href="<?=$data['header_background_pic_link']?>" target="_blank" style="height:<?=$data['header_background_pic_padding']?>px;width:100%;top:0;position:absolute;"></a>
<?php endif; ?>
<?php endif; ?>

<body onkeydown="if(event.keyCode==27) return false;" onload="checkBrowser(deny, warn, pass);">
<script type="text/javascript" src="/static/js/checkbrowser.js"></script>
<div id="toptb" class="cl">
<div class="wp">
<div class="z"></div>
<div class="y">
	<a id="sslct" href="javascript:;">切换风格</a>
</div>
</div>
</div>

<div id="sslct_menu" class="cl p_pop" style="display: none;width:600px;">
	<?php foreach($data['extstyle'] as $style):?>
	<span class="sslct_btn" onclick="extstyle('<?=$style?>')" title="<?=$data['extstyle_detail'][$style]['name']?>"><i style='background:<?=$data['extstyle_detail'][$style]['color']?>'></i></span>
	<?php endforeach;?>
</div>


<div id="hd">
<div class="wp">
<div class="hdc cl">
<h2><a href="./" title="<?=$data['setting']['site_name']?>">
	<img src="/static/images/logo.png" alt="<?=$data['setting']['site_name']?>" border="0" style="height:66px;" />
	</a>
</h2>

<?php if (empty($data['uid'])):?>
<div class="fastlg cl"></div>
<?php else: ?>
<div id="um">
<div class="avt y">
	<a href="/user/<?=$data['uid']?>/"><img src="<?=$data['setting']['forums_url']?>uc_server/avatar.php?uid=<?=$data['user']['forums_uid']?>&size=small" /></a>
</div>
	<p>
	<strong class="vwmy"><a href="/user/<?=$data['uid']?>/"><?=$data['username']?></a></strong>
	<span class="pipe">|</span><a href="<?=$data['setting']['forums_url']?>home.php?mod=spacecp">设置</a>
	<?php 
	if (!empty($data['header_background_pic'])): 
	?>
	<span class="pipe">|</span>
	<a href="javascript:;" onclick="SetCookie('display_header_background',0)">关闭背景图</a>
	<?php endif; ?>
	<span class="pipe">|</span>
	<?php if ($data['new_msg']):?>
	<a href="<?=$data['setting']['forums_url']?>home.php?mod=space&amp;do=pm" id="pm_ntc" class="new">消息</a>
	<script>
	$(function(){
		setTimeout(function(){ui.notify('提示', "您有未读的短消息。").effect('slide')},200);
	})
	</script>
	<?php else: ?>
	<a href="<?=$data['setting']['forums_url']?>home.php?mod=space&amp;do=pm" id="pm_ntc">消息</a>
	<?php endif; ?>
	<span class="pipe">|</span>
	<?php if ($data['new_notification']):?>
	<a href="<?=$data['setting']['forums_url']?>home.php?mod=space&amp;do=notice" id="myprompt" class="new">提醒(<?=$data['new_notification']?>)</a>
	<script>
	$(function(){
		setTimeout(function(){ui.notify('提示', "您有未读的提醒。").effect('slide')},200);
	})
	</script>
	<?php else: ?>
	<a href="<?=$data['setting']['forums_url']?>home.php?mod=space&amp;do=notice" id="myprompt">提醒</a><span id="myprompt_check"></span>
	<?php endif; ?>
	<span class="pipe">|</span><a href="/search/fav/">我的收藏</a>
	<?php if (in_array($data['username'], funcs::explode($data['setting']['admins_admins']))): ?>
	<span class="pipe">|</span><a href="/admin/" target='_blank'>管理中心</a>
	<?php endif;?>
	<span class="pipe">|</span><a href="/user/logout">退出</a>
	</p>
	<div class="user">
		<h5>
			查看用户积分
			<span class="pipe">|</span>
			用户组: <span style='color:<?=$data['user']['group_color']?>'><?=$data['user']['group_name']?></span>
		</h5>
		<ul>
			<li>总积分：<?=$data['user']['total_credits']?></li>
			<li>共享率: <?=$data['user']['ratio']?></li>
			<li>上传: <?=$data['user']['uploaded_sum_text']?></li>
			<li>虚拟上传：<?=$data['user']['uploaded2_text']?></li>
			<li>下载: <?=$data['user']['downloaded_text']?></li>
			<li>虚拟下载：<?=$data['user']['downloaded2_text']?></li>
			<li>保种积分: <?=$data['user']['extcredits1_text']?></li>
			<li>土豪金：<?=$data['user']['extcredits2']?></li>
			<li>用户组：<?=$data['user']['group_name']?></li>
		</ul>
	</div>
</div>
<?php endif; ?>
</div>

<div id="pure-nv">
<ul>
<li><a href="<?=$data['setting']['forums_url']?>portal.php?mod=topic&topicid=17" hidefocus="true">公告</a></li>
<li><a href="<?=$data['setting']['forums_url']?>forum.php" hidefocus="true">论坛</a></li>
<li<?php if ($data['selected_nav']=='index' || $data['selected_nav']==''):?> class="a"<?php endif;?>><a href="/" hidefocus="true">PT</a></li>
<li<?php if ($data['selected_nav']=='book'):?> class="a"<?php endif;?>><a href="/book/search/" hidefocus="true">校园二手书</a></li>
<li<?php if ($data['selected_nav']=='softsite'):?> class="a"<?php endif;?>><a href="/softsite/search/" hidefocus="true">软件站</a></li>
<li<?php if ($data['selected_nav']=='search'):?> class="a"<?php endif;?>><a href="/search/" hidefocus="true">种子列表</a></li>
<li<?php if ($data['selected_nav']=='upload'):?> class="a"<?php endif;?>><a href="/upload/" hidefocus="true">发布种子</a></li>
<li<?php if ($data['selected_nav']=='audit'):?> class="a"<?php endif;?>><a href="/audit/" hidefocus="true">种子审核区</a></li>
<li<?php if ($data['selected_nav']=='toolbox'):?> class="a"<?php endif;?>><a href="/toolbox/" hidefocus="true">工具箱</a></li>
<li<?php if ($data['selected_nav']=='top'):?> class="a"<?php endif;?>><a href="/top/" hidefocus="true">排行榜</a></li>
<li<?php if ($data['selected_nav']=='sitelog'):?> class="a"<?php endif;?>><a href="/list/sitelog/" hidefocus="true">站点日志</a></li>
<li><a href="http://zhixing.bjtu.edu.cn/thread-429797-1-1.html" hidefocus="true" target="_blank" style="color:#08c;">加入我们</a></li>

</ul>
</div>
</div>


<script type="text/javascript">

var link_id = 'extstyle';
var loaded_css = [];
var bbs_link = "<?=$data['setting']['forums_url']?>";

<?php if ($data['default_style'] != ''): ?>
loaded_css[link_id] = 1;
<?php endif;?>

function extstyle(style)
{
	var url = "<?=$data['setting']['forums_url']?>template/default/style/" + style + "/style.css";
	loadcss(link_id, url);
	$.post("/api/setcookie/default_style",{default_style:style});
}
</script>

</div>