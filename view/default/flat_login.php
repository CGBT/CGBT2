<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>登入 - <?=$data['setting']['site_name']?></title>
    <link href="/static/css/flat-ui.css" rel="stylesheet">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <style>
    body{
    	margin: 0;
    	background: #f1f5f8 url(/static/images/bg2.png);
		color: #fff;
		text-shadow: 0 1px 1px rgba(0,0,0,.15);
    }
	body,body a,.login-form input,button{
		font-family: "microsoft jhenghei","microsoft yahei",tahoma;
	}
	.container{
		width: 940px;
		margin: 0 auto;
	}
	.ip-tips{
		margin-top:-2em;
		color:red;
	}
	.login-tips{
		margin-top: 12em;
		margin-left: 10px;
		line-height: 24px;
	}
	input:focus{
		outline:none;
	}
	.login-form:before{
		top:25px;
	}
	.login-icon {
		top: 50px;
		width: 150px;
		left: 0px;
	}
	.login-screen {
		width: 360px;
		margin: 0 auto;
		padding: 50px 0 0 0;
		background: none;
		min-height: 260px;
		float: right;
	}
	.btn-block{
		width: 100%;
		margin-bottom: 20px;
	}
	.hack-placeholder{
		display: none;
	}
	.wrap{
		width: 100%;
		margin:0 auto;
	}
	.top{
		min-height: 280px;
		position: relative;
		z-index: 0;
		box-shadow: inset 0 -1px 6px rgba(0,0,0,.1);
		min-height: 300px;
		background: #20aae5;
		padding-bottom: 50px;
	}
	.in-wrap{
		width: 740px;
		margin: 0 auto;
		overflow: hidden;
		position: relative;
	}
	.slogan{
		width: 320px;
		height: 85px;
		position: absolute;
		background: url(/static/images/slogan.png) no-repeat;
		left: 10px;
		top: 150px;
	}
	.btn.btn-primary {
		background-color: #20aae5;
		cursor: pointer;
	}
	.btn.btn-primary:hover,.btn.btn-primary:focus {
		background-color: #00B3FF;
	}
	a{color: #20aae5;}
	a:hover{color: #00B3FF;}
	input[type="text"]:focus,input[type="password"]:focus{border-color: #20aae5;}
	.login-form .login-field:focus + .login-field-icon {color: #20aae5;}
	.stories {
	    margin: 50px 0;
	}
	.reps {
	    position: relative;
	    width: 435px;
		margin: 0 auto;
	}
	.rep,.single-story {
	    background: #fff;
	    border-radius: 2px;
	    box-shadow: 0 1px 1px rgba(100,100,100,.15);
	}
	.rep {
	    width: 62px;
	    height: 62px;
	    margin: 0 10px 10px 0;
	    float: left;
	    position: relative;
	    border-radius: 32px;
	    cursor: pointer;
	}
	.rep .info-card {
	    display: none;
	}
	.rep img {
	    width: 36px;
	    height: 36px;
	    margin: 13px;
	    border: 0;
	    -webkit-transition: .05s all ease-in-out;
	    transition: .05s all ease-in-out;
	}
	.rep:hover{
		background: #99CBE0;
	}
	.rep:hover img{
		-moz-transform: scale(1) rotate(-360deg) translate(0px);
		-webkit-transform: scale(1) rotate(-360deg) translate(0px);
		-o-transform: scale(1) rotate(-360deg) translate(0px);
		transform: scale(1) rotate(-360deg) translate(0px);
		-moz-transition: all 1s ease;
		-webkit-transition: all 1s ease;
		-o-transition: all 1s ease;
		transition: all 1s ease;
	}
	.rep.current{
	    border-color: transparent;
	}
	.rep.current img{
	    width: 64px;
	    height: 64px;
	    border-radius: 33px;
	    margin: -1px;
	}
	.footer{
		text-align: center;
		color:#aaa;
	}
	.footer a{text-decoration: none;}
	</style>
	<!--[if lt IE 10]>
	<style>
	.hack-placeholder{
		right: auto;
		left: 10px;
		display: block;
	}
	</style>
	<script>
	$(function(){
		$("input[placeholder]").focus(function(){
			$(this).parent().find(".hack-placeholder").hide();
		})
		$("input[placeholder]").blur(function(){
			if(!$(this).val()){
				$(this).parent().find(".hack-placeholder").show();
			}
		})
	})
	</script>
	<![endif]-->
</head>
<body>
<div class="wrap">
	<?php if (!empty($data['user'])):?>
	<script>
	location.href = "/"
	</script>
	<?php endif;?>
	<div class="top">
		<div class="in-wrap">
			<div class="login-screen">
				<div class="login-icon">
					<img src="http://zhixing.bjtu.edu.cn/static/image/common/logo/logo-115.png" width="150" height="64" alt="Welcome to ZXBT">
					<?php if ($data['remain_login_fail_count'] < 10): ?>
					<p class="login-tips"><?=$data['setting']['login_fail_time']?>分钟内连续<?=$data['setting']['login_fail_count']?>次登录失败将会被封禁IP。还有<span style="font-weight:bold;color:red;font-size:14px;"> <?=$data['remain_login_fail_count']?> </span>次机会。 </p>
					<?php endif; ?>
				</div>
				<div class="slogan"></div>
				<div class="login-form">
					<form method="post" name="login" id="loginform" class="cl" onsubmit="" action="/user/login">
						<input type="hidden" name="formhash" value="fab5aaa7" />
						<div class="control-group">
							<input type="text" class="login-field" name="username" id="username" placeholder="用户名" onblur="check_user_exist();">
							<label class="login-field-icon fui-user" for="username"></label>
							<label class="login-field-icon hack-placeholder" for="username">用户名</label>
						</div>
						<div class="control-group">
							<input type="password" id="password" name="password" class="login-field" value="" placeholder="密码" autocomplete="off">
							<label class="login-field-icon fui-lock" for="password"></label>
							<label class="login-field-icon hack-placeholder" for="password">密码</label>
						</div>
						<?php if ($data['remain_login_fail_count'] != 10): ?>
						<div class="control-group">
							<input type="text" id="captcha" name="captcha" class="login-field" placeholder="验证码" autocomplete="off">
							<label class="login-field-icon" for="captcha"><img onclick="refresh_captcha();" id="captcha_img" src="" style="margin-top:-1px;" /></label>
							<label class="login-field-icon hack-placeholder" for="captcha">验证码</label>
						</div>
						<?php endif; ?>
						<div class="control-group" id="invite_tr" style="display:none">
							<input type="text" class="login-field" value="" placeholder="邀请码" id="invitecode" name="invitecode" autocomplete="off">
							<label class="login-field-icon fui-check-inverted" for="invitecode"></label>
							<label class="login-field-icon hack-placeholder" for="invitecode">邀请码</label>
						</div>
						<button class="btn btn-primary btn-large btn-block" type="submit" name="submit" value="submit" tabindex="1"><strong>Login</strong></button>
						<a title="忘记密码" href="/user/lostpassword/" style="float:right;">Get lost?</a>
						<a title="注册帐号" href="/user/register/">注册</a>
					</form>
				</div>
				<?php if ($data['remain_login_fail_count'] == 0): ?>
				<p style="ip-tips">
				您的ip  <?=$data['ip']?> 在 <?=$data['setting']['login_fail_time']?> 分钟内连续 <?=$data['setting']['login_fail_count']?> 次登录失败，已被封禁登录权限，请<?=$data['setting']['login_fail_time']?> 分钟后再试。
				</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="bottom">
		<div class="in-wrap">
			<div class="stories zg-clear">
				<div class="reps clearfix">
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">游戏</span>
						<img src="/static/images/catpic/game.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">纪录片</span>
						<img src="/static/images/catpic/documentary.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">动漫</span>
						<img src="/static/images/catpic/comic.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">学习</span>
						<img src="/static/images/catpic/study.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">体育</span>
						<img src="/static/images/catpic/sports.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">软件</span>
						<img src="/static/images/catpic/software.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">剧集</span>
						<img src="/static/images/catpic/tv.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">综艺</span>
						<img src="/static/images/catpic/zongyi.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">电影</span>
						<img src="/static/images/catpic/movie.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">音乐</span>
						<img src="/static/images/catpic/music.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">其它</span>
						<img src="/static/images/catpic/other.png">
					</a>
					<a href="javascrip:void(0);" class="rep">
						<span class="info-card">One</span>
						<img src="/static/images/One.png">
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="footer">
	<p>Powered by <strong><a href="http://cgbt.org/">CGBTSource v2.0 Beta</a></strong> </p>
</div>





<script type="text/javascript">
function refresh_captcha()
{
	var captcha_img = $('#captcha_img');
	now = new Date();
	captcha_img.attr('src', "/user/captcha?t=" + now.getTime());
}
$(function(){
	refresh_captcha();
})



function check_user_exist()
{
	<?php if (!$data['setting']['check_invite_code']): ?>
	return false;
	<?php endif;?>		

	var username = $("#username").val();
	if(username == '' )
	{
		return false;
	}
	$.post("/api/user/exists",{
		username: username
		},function(data){
			if (data == '0')
			{
				need_invite = true;
				$("#invite_tr").show();
			}
			else
			{
				need_invite = false;
				$("#invite_tr").hide();
			}
		});
}
</script>

</body>
</html>