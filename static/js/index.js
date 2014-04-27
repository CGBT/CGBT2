
function submit_form(form_id, post_string, fail_callback, success_callback)
{
	var form = $('#'+form_id);
	var	action = form.prop('action');
	post_string = form.serialize()+"&inajax=1&"+post_string;
	$.post(action, post_string, 
		function(data){
			if (data.error == true)
			{
				alert(data.msg);
				if (fail_callback != '')
				{
					fail_callback();
				}
			}
			else if (data.error == 'default')
			{
				alert(data.msg);
				if (success_callback != '')
				{
					success_callback();
				}
			}
			else if (data.error == false)
			{
				form.submit();
			}
		}, "json");
}

function SetCookie(name,value){
    var Days = 7;
    var exp  = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path=/;domain="+cookie_domain;
    window.location.href = location.href;
}

function loadcss(link_id, css_url)
{
	if (loaded_css[link_id])
	{
		var $link = $("#" + link_id);
		$link.attr({
			"href": css_url
		});
	}
	else
	{
		var $link = $("<link/>");
		$link.attr({
			"id" : link_id,
			"rel":"stylesheet",
			"type":"text/css",
			"href": css_url
		});
		$link.appendTo('head');
		loaded_css[link_id] = 1;
	}
}

function showMenu(obj, showid, pos)
{
	var timeoutid = 0;
	var x = $(obj).offset().left;
    var y = $(obj).offset().top;
	showobj = $('#'+showid);
	var w = showobj.width();
	showobj.css({"position" : 'absolute', "left": x+20, "top": y+17}).show();
	clearTimeout(timeoutid);
}

$(function(){
	$("#sslct").powerFloat({
		target: "#sslct_menu"
	});

	$("#qmenu").powerFloat({
		target: "#qmenu_menu",
		position: "3-2"
	});
	
	$("#mn_N6a3e").powerFloat({
		target: "#mn_N6a3e_menu",
		position: "4-1"
	});

	$(window).scroll(function() {
		if ($(this).scrollTop() > 150) {
			$('.back-to-top').fadeIn(100);
		} else {
			$('.back-to-top').fadeOut(100);
		}
	});
	
	$('.back-to-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, 500);
	})
	$("body").live("powerfloat",function(){
		$("a[data-uid]").powerFloat({
			position: "2-3",
			showDelay: 500,
			hideDelay: 300,
			showCall:function(){
						var uid = $(this).attr("data-uid");
						if (uid>0) {
							$.getJSON("/user/"+uid+"/",{inajax:1},function(data){
								push_tipbox(data.uid,data.forums_uid,data.username,data.group_color);
							})
						};		
					},
			hideCall:function(){$("#box-tipbox").html("");},
			target: "#box-tipbox"
		});
	})
	$("body").trigger("powerfloat");
	$("body").ajaxStart(function(){
		var html = '<div id="loading"><div class="spinner large" role="progressbar"></div></div>';
		$("body").append(html);
	})
	$("body").ajaxComplete(function(){
		$("#loading").remove();
	})
	var iconPop = function(){
		var category = {"game":"游戏","music":"音乐","comic":"动漫","zongyi":"综艺","documentary":"纪录片","movie":"电影","tv":"剧集","other":"其它","software":"软件","sports":"体育","study":"学习"}
		var msg = [
			"怎么你连这个都没下过吗？",
			"再点它你就要被扣流量了！",
			"反正你点100次我也不会给你送流量的！",
			"听说点了1314次的人会遇到自己的心上人，你信吗？"
		];
		var i = parseInt(Math.random()*3+1);
		$(".icon-td img").click(function(){
			if (i>=4) {
				i=0;
			};
			var c = $(this).attr("src").split("/")[4].split(".")[0];
			ui.notify('提示', "这是"+category[c]+"分类，"+msg[i]).effect('scale');
			i++;
		})
	}
	iconPop();
})

var push_tipbox = function(uid,fuid,username,color){
	$("#box-tipbox").html("");
	var template = '<div class="box-header">'
	template = template+'<img src="'+bbs_link+'uc_server/avatar.php?uid='+fuid+'&size=middle"></div>'
	template = template+'<div class="box-content">'
	template = template+'<h2 id="box-username" style="color:'+color+'">'+username+'</h2>'
	template = template+'<span class="button-group">'
	template = template+'<a target="_blank" href="/user/'+uid+'/" class="button">用户信息</a>'
	template = template+'<a target="_blank" href="'+bbs_link+'home.php?mod=space&uid='+fuid+'&do=profile" class="button">论坛空间</a>'
	template = template+'<a target="_blank" href="/chat/room/'+username+'/" class="button">和他聊天</a>'
	template = template+'</span>'
	template = template+'</div>'
	$("#box-tipbox").append(template);
}

function dConfirm(h,m,F){
	new ui.Confirmation({ title: h, message: m })
	.overlay()
	.effect('slide')
	.ok('确定')
    .cancel('取消')
    .show(function(ok){
      if (ok){
      		F();
      }
    });
}