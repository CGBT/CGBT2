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
						<li class="active"><a>用户排行</a></li>
						<li><a href="/top/users/total_credits/">总积分排行</a></li>
						<li><a href="/top/users/uploaded/">上传排行</a></li>
						<li><a href="/top/users/downloaded/">下载排行</a></li>
						<li><a href="/top/users/extcredits1/">保种积分排行</a></li>
						<li><a href="/top/users/extcredits2/">土豪金排行</a></li>
					</ul>
					<div class="pager">						
					</div>
				</div>
				<div class="clearfix top-user-box">
					<?php foreach ($data['users'] as $key => $user): ?>
					<div class="user-details-tabs pull-left" style="border-top: 5px solid <?=$user['group_color']?>;">
						<div>
							<i class="sort-i" title="排名"><?=$key+1?></i>
							<div class="img-tx pull-right">
								<a data-uid="<?=$user['uid']?>" href="/user/<?=$user['uid']?>"><img src="<?=$data['setting']['forums_url']?>uc_server/avatar.php?uid=<?=$user['forums_uid']?>&size=large"></a>
								<h2><a href="/user/<?=$user['uid']?>"><?=$user['username']?></a></h2>
								<p>UID:<?=$user['uid']?></p>
							</div>
							<div class="sort-info pull-left">
								<p>上传: <?=$user['uploaded_text']?></p>
								<p>下载: <?=$user['downloaded_text']?></p>
								<p>总积分：<?=$user['total_credits']?></p>
								<?php if (!empty($user['title'])):?>
								<span class="button-group">
									<a class="button active" style="background:<?=$user['group_color']?>;">昵称</a>
									<a class="button nick-area" title="<?=$user['title']?>"><?=$user['title']?></a>
								</span>
								<?php endif;?>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<!-- end #mainContent -->
		</div>
	</div>
</div>
<!--wp-->
<!--[if !IE]><!-->
<script>
$(document).ready(function (){
	var vheight = $(window).height();
	$(window).resize(function() {
		vheight = $(window).height();
	});
 
	$(window).scroll(function() {
	  $('.user-details-tabs').each(function(i, item){
		var tmppos = $(this).offset();
		if(tmppos.top > $(window).scrollTop() + vheight - 100){
		  $(this).css({'visibility':'hidden'}).removeClass("animated bounceIn");
		}else{
		  $(this).css({'visibility':'visible'}).addClass("animated bounceIn");
		}
	  });
	});
});
</script>
<!--<![endif]-->
<?php
include 'footer.php';
?>