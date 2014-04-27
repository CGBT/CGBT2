<?php
include 'header.php';
?>
<div id="wp" class="wp"><div id="ct" class="ptm wp w cl">

<div class="mn" id="main_message">
	<div class="bm">
		<div class="bm_h bbs">
			<span class="y"><a href="/user/register/" class="xi2">没有帐号？注册</a></span>
			<h3 class="xs2">登录</h3>
		</div>
		<div>
			<div id="main_messaqge_layer">
				<div id="layer_login">
					<form method="post" name="login" id="loginform" class="cl" onsubmit="" action="/user/login">
						<div class="c cl">
							<input type="hidden" name="formhash" value="fab5aaa7" />

							<div class="rfm">
							<table>
							<tr>
							<th><label for="username">说明:</label></th>
							<td>
							<?php if (!empty($data['user'])):?>
							<span style='color:red;font-weight:bold;'><?=$data['username']?>: 您处于登录状态，不需要再次登录。</span><br />
							<?php endif;?>
							<?=$data['setting']['login_fail_time']?>分钟内连续<?=$data['setting']['login_fail_count']?>次登录失败将会被封禁ip不能登录。<br />您还有<span style="font-weight:bold;color:red;font-size:14px;"> <?=$data['remain_login_fail_count']?> </span>次登录机会。 </td>
							<td class="tipcol"></td>
							</tr>
							</table>
							</div>
							<?php if ($data['remain_login_fail_count'] == 0): ?>
							<div class="rfm" style="font-size:14px;color:red">
							您的ip  <?=$data['ip']?> 在 <?=$data['setting']['login_fail_time']?> 分钟内连续 <?=$data['setting']['login_fail_count']?> 次登录失败，已被封禁登录权限，请<?=$data['setting']['login_fail_time']?> 分钟后再试。
							</div>

							<?php else: ?>

							<div class="rfm">
							<table>
							<tr>
							<th><label for="username">帐号:</label></th>
							<td><input type="text" name="username" id="username" size="30" class="px p_fre" tabindex="1" value="" autofocus onblur="check_user_exist();"/></td>
							<td class="tipcol"><a target='_blank' href="/user/register/">注册</a></td>
							</tr>
							</table>
							</div>

							<div class="rfm">
							<table>
							<tr>
							<th><label for="password">密码:</label></th>
							<td><input type="password" id="password" name="password" onkeypress="detectCapsLock(event);" autocomplete="off" size="30" class="px p_fre" tabindex="1" /></td>
							<td class="tipcol"><a href="/user/lostpassword/" target="_blank" title="找回密码">找回密码</a><span style ="color:red;margin-left:10px;visibility:hidden" id="capStatus">Caps Lock键正处于启用状态，启用它可能导致密码输入错误。</span></td>
							</tr>
							</table>
							</div>

							<div class="rfm" style="display:none" id='invite_tr'>
							<table>
							<tr>
							<th><label for="invitecode">邀请码:</label></th>
							<td><input type="text" id="invitecode" name="invitecode" size="30" class="px p_fre" autocomplete="off" tabindex="1" /></td>
							<td class="tipcol">请确认你的帐号输入正确。如果是第一次登录，您必须输入邀请码。</td>
							</tr>
							</table>
							</div>

							<?php if ($data['remain_login_fail_count'] != 10): ?>
							<div class="rfm">
							<table>
							<tr>
							<th><label for="captcha">验证码:</label></th>
							<td><input type="captcha" id="captcha" name="captcha" size="30" class="px p_fre" autocomplete="off" tabindex="1" /></td>
							<td class="tipcol"><a href="javascript:void()" onclick="refresh_captcha();"><img id="captcha_img" src="" /></a></td>
							</tr>
							</table>
							</div>
							<?php endif; ?>

							<div class="rfm mbw bw0">
							<table width="100%">
							<tr>
							<th>&nbsp;</th>
							<td>
							<button class="pn pnc" type="submit" name="submit" value="submit" tabindex="1"><strong>登录</strong></button>
							</td>
							<td></td>
							</tr>
							</table>
							</div>
							<?php endif; ?>

						</div>
					</form>
				</div><!--layer_login-->
			</div><!--main_message_layer-->
		</div><!--main_message-->
	</div>
</div>
</div>
</div>
<!--wp-->

<script type="text/javascript">
function refresh_captcha()
{
	var captcha_img = $('#captcha_img');
	now = new Date();
	captcha_img.attr('src', "/user/captcha?t=" + now.getTime());
}
function detectCapsLock(e){
    valueCapsLock  =  e.keyCode ? e.keyCode:e.which;
    valueShift  =  e.shiftKey ? e.shiftKey:((valueCapsLock  ==   16 ) ? true : false ); 

     if (((valueCapsLock  >=   65   &&  valueCapsLock  <=   90 )  &&   ! valueShift)
     || ((valueCapsLock  >=   97   &&  valueCapsLock  <=   122 )  &&  valueShift))
        document.getElementById('capStatus').style.visibility  =  'visible';
     else 
        document.getElementById('capStatus').style.visibility  =  'hidden';
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

<?php
include 'footer.php';
?>