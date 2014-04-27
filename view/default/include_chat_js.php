
<script type="text/javascript">
var $chat = $("#chat");
var $txt = $("#txt");
var $start = $("#start");
var emptytimes = 1;
var sendtimes = 0;
var timeout = 0;
var interval_time = 5000;
var room = '<?=$data['room']?>';
var room_key = '<?=$data['room_key']?>';
var usertitle = '<?=empty($data['user']['title']) ? $data['user']['username'] : $data['user']['title']?>';
var first_time = 1;

$txt.keypress(function(e){
	if (e.which==13)
	{
		sendchat();
	}
});
function sendchat()
{
	if($txt.val()=='')
	{
		alert('请输入内容!');
		return false;
	}
	clearTimeout(timeout);
    $('#btnsubmit').prop('disabled', true);
	if (!$("#private").prop("checked"))
	{
		reply_uid = 0;
	}
	else
	{
		reply_uid = $("#reply_uid").val();
	}
	$.post('/chat/say/', { start: $start.val(), txt: $txt.val(), room: room, room_key: room_key, reply_uid: reply_uid}, display_chat, 'json');
	$txt.val('');
	$("#span_private").hide();
	sendtimes++;
}
function getchat()
{
	var reply_me = $("#reply_me").prop("checked") ? 1 : 0;
	var my_chat = $("#my_chat").prop("checked") ? 1 : 0;
	$.post('/chat/get/', { start: $start.val(), room: room, room_key: room_key, reply_me : reply_me, my_chat: my_chat}, display_chat, 'json');	
}
function clear_chat()
{
	if(confirm("确定要清空聊天记录?"))
	{
		$chat.html('');
	}
}
function clear_and_reget()
{
	$chat.html('');
	$start.val('1');		
	getchat();
}
function display_chat(data)
{
	var txt = data.txt;
	if(data.action == "ban")
	{
		$txt.val('你已经被封禁发言权限!');
		$txt.attr('readonly', true);
		$('#btnsubmit').hide();
		return;
	}
	
	$('#btnsubmit').prop('disabled', false);
	
	if (data.action == "refresh")
	{
		$chat.html('');
	}
	if (txt == '')
	{		
		clearTimeout(timeout);
		emptytimes ++;
		if(emptytimes > 6)
		{
			emptytimes = 6;
		}
		timeout = setTimeout(getchat, interval_time*emptytimes);		
	}
	else
	{
		clearTimeout(timeout);
		emptytimes --;
		emptytimes --;
		if(emptytimes < 2)
		{
			emptytimes = 1;
		}
		timeout = setTimeout(getchat, interval_time*emptytimes);		
	}	
	if (data.txt != '')
	{
		$chat.html($chat.html() + data.txt);
	}
	$start.val(data.start);
	if ($("#autoscroll").attr("checked"))
	{	
		var h=0;
		$("#chat li").each(function(){h = h +$(this).height()+10;})
		$chat.scrollTop(h);
	}
	if (data.txt.indexOf('@'+usertitle)>0 && first_time == 0)
	{
		alert("你有新的消息");
	}	
	first_time = 0;
}
function insert_smilies($smilies)
{
	$txt.val($txt.val() + $smilies);
}
function reply_user($username, reply_uid)
{
	$newtxt = $txt.val() + '@' + $username + ': ';
	$txt.val($newtxt);
	$("#span_private").show();
	$("#reply_uid").val(reply_uid);
}
<?php if ($data['is_room_admin']) : ?>
function del(i)
{
	if(confirm('确定删除？'))
	{
		$.post('/chat/del/', { id: i, room: room, room_key: room_key }, display_chat, 'json');
	}
}
function ban(username)
{
	if(confirm('确定封禁用户'+username+'?'))
	{
		$.post('/chat/ban/', { ban_user: username, room: room, room_key: room_key }, display_chat, 'json');
	}
}
<?php endif; ?>

getchat();
timeout = setTimeout(getchat, interval_time);


</script>
