<script type="text/javascript">
$(function(){
	$(".audit_link").powerFloat({
		position: "2-3",
		eventType: "click",
		showCall:function(){
				var tid = $(this).attr("tid");
				var action="/torrents/"+tid+"/audit/";
				$("#frm_audit").attr("action", action);
				$("select[name=selectreason] option:first-child").prop("selected",true);
				$("input[name=reason]").val('');
				$("#tid").val(tid);	
				var tr = $("#t"+tid);
				var text = tr.find('a[name=title]').html()||$(".torrent-title h1").text();
				$('#audit_torrent_name').text(text);				
			},
		target: "#audit_box"
	});

	$('#audit_nopass_button').click(function(){
		$("#audit_status").val('0');
		submit_form('frm_audit', '', '', audit_success);
	});

	$('#audit_pass_button').click(function(){
		$("#audit_status").val('1');
		submit_form('frm_audit', '', '', audit_success);
	});

	$('#audit_close_button').click(function(){
		$.powerFloat.hide();
	});

	function audit_success()
	{
		var tid = $("#tid").val();
		if ($("#audit_status").val() == '0')
		{
			if ($("input[name=reason]").eq(0).val()) {
				$("#t"+tid).find(".audit-result").text($("input[name=reason]").eq(0).val());
				$("input[name=reason]").eq(0).val("");
			};
		}
		else if($("#audit_status").val()==='1')
		{
			$("#t"+tid).remove();
		}
		$.powerFloat.hide();
		$("select[name=selectreason] option:first-child").prop("selected",true);
		$("input[name=reason]").val('');
	}
})
</script>

<div id="audit_box" class="shadow target_box dn flipInX">
	<form action="" method="post" enctype="multipart/form-data" id="frm_audit">
		<div class="target_list">
			<span style="color:red;font-weight:bold">审核</span>种子名称：<span id="audit_torrent_name" style="display:block;width:320px;"></span>
		</div>
		<div class="target_list">
	    	操作原因
	    	<input type="text" name="reason" value="">
	    	<select name="selectreason" style="width:100px;" onchange="this.form.reason.value=this.value">
				<option value="">自定义</option>
				<option value="">--------</option>
				<?php foreach (funcs::explode($data['setting']['audit_torrents_reasons']) as $reason): ?>
				<option value="<?=$reason?>"><?=$reason?></option>
				<?php endforeach;?>
			</select>
	    </div>
	    <div class="target_list">
	        短消息通知
	    	<label><input type="checkbox">发短消息通知发种人</label>
	    </div>
	    <div class="target_list" style="border-bottom:none;">
	        <input id="tid" type="hidden" value="">
			<?php if (isset($data['action']) && ($data['action'] == 'edit'||$data['action'] == 'details')): ?>
			<input type="hidden" value="" name="edit_page">
			<?php endif; ?>
	        <input id="audit_status" type="hidden" value="" name="audit_status">
	        <button id="audit_nopass_button" type="button" class="pn pnc"><strong>审核不通过</strong></button>
			<button id="audit_pass_button" type="button" class="pn pnc"><strong>审核通过</strong></button>
			<button id="audit_close_button" type="button" class="pn pnc"><strong>关闭</strong></button>
	    </div>
	</form>
</div>
