<ul id="chat" class="pmb" style="width: 900px;background:#fff; height: 400px; border: 1px solid #ccc; overflow: scroll; overflow-x: hidden; margin: 0 auto; padding: 10px;"></ul>
<div class="chat-action">
    <span onclick="clear_chat();" style="cursor: pointer;">清空记录</span>
    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="scroll" checked="checked" id="autoscroll"> 自动滚屏
    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="reply_me" id="reply_me" value="1" onchange="clear_and_reget()"> 看@我的
    &nbsp;&nbsp;&nbsp;<input type="checkbox" name="my_chat"  id="my_chat" value="1" onchange="clear_and_reget()">  看我的发言
    &nbsp;&nbsp;&nbsp;<span id="span_private" style="display:none"><input type="checkbox" name="private" checked="checked" id="private">
    跟他私聊(选中则只有被你@的人能看到本条消息)</span>

    <br />
    <input type="text" name="txt" class="frminput" id="txt" maxlength="250">
    <input type="hidden" name="start" id="start">
    <input type="hidden" name="reply_uid" id="reply_uid">
	<?php if ($data['chat_use_ubb']): ?>
    <input type="button" class="button button-green" value="表 情" id="emo-display">
	<?php endif;?>
    <input type="button" value="发 言" class="button button-blue" onclick="sendchat();" id="btnsubmit">
    <div class="emo-for-chat"><?php include "include_emo.php"; ?></div>
    <script>
    $(function(){
        $("#fastpostsmilie td img").click(function(){
            url = $(this).attr("src");
            old = $("#txt").val();
            $("#txt").val(old+"[img]"+url+"[/img]");
        });
        $("#emo-display").click(function(){
            $("#fastpostsmilie td img").each(function(){
                $(this).attr("src",$(this).attr("lsrc"));
            });
            $("#fastpostsmilie").toggle();
            $("#fastpostsmilie").parent().toggle();
        });
    })
    </script>
    <br />
	    <br />    <br />    <br />    <br />
</div>
