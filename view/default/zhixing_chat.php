<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
</head>
<body>

	<div id="chat" style="width: 1000px; height: 100px; border: 1px solid #ccc; overflow: scroll; overflow-x: hidden; margin: 0 auto; padding: 2px;"></div>
	<div style="width: 1006px; margin: 0 auto; margin-top: 5px;">
	<span onclick="clear_chat();" style="cursor: pointer;">清空记录</span>
	&nbsp;&nbsp;&nbsp;<input type="checkbox" name="scroll" checked="checked" id="autoscroll"> 自动滚屏
	&nbsp;&nbsp;&nbsp;<span id="span_private" style="display:none"><input type="checkbox" name="private" checked="checked" id="private">
	跟他私聊(选中则只有被你@的人能看到本条消息)</span>

	<br />
	<input type="text" name="txt" class="frminput" style="width: 945px;" id="txt" maxlength="250">
	<input type="hidden" name="start" id="start">
	<input type="hidden" name="reply_uid" id="reply_uid">
	<input type="button" value="发 言" class="button button-blue" onclick="sendchat();" id="btnsubmit">
	<br />
	<br />
	<br />
	<br />
	</div>


<?php
include 'include_chat_js.php';
?>
</body>
</html>