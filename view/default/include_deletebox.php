<script type="text/javascript">
$(function(){
	$(".delete_link").powerFloat({
		position: "2-3",
		eventType: "click",
		showCall:function(){
				var tid = $(this).attr("tid");
				var action="/torrents/"+tid+"/delete";
				$("#frm_delete").attr("action", action);
				$("select[name=selectreason] option:first-child").prop("selected",true);
				$("input[name=reason]").val('');
				$("#tid").val(tid);	
				var tr = $("#t"+tid);
				var text = tr.find('a[name=title]').html()||$(".torrent-title h1").text();
				$('#delete_torrent_name').text(text);
		},
		target: "#delete_box"
	});

	$('#delete_button').click(function(){
		submit_form('frm_delete', '', '', delete_success);
	});

	$('#delete_close_button').click(function(){
		$.powerFloat.hide();
	});

	function delete_success()
	{
		var tid = $("#tid").val();
		$("#t"+tid).remove();
		$("select[name=selectreason] option:first-child").prop("selected",true);
		$("input[name=reason]").val('');
		$.powerFloat.hide();
	}
})
</script>

<div id="delete_box" class="shadow target_box dn flipInX">
	<form action="" method="post" enctype="multipart/form-data" id="frm_delete">
		<div class="target_list">
		<span style="color:red;font-weight:bold">删除</span>种子名称：<span id="delete_torrent_name" style="display:block;width:320px;"></span>
		</div>
		<div class="target_list">
	    	操作原因
	    	<input type="text" name="reason" value="">
	    	<select name="selectreason" style="width:100px;" onchange="this.form.reason.value=this.value">
				<option value="">自定义</option>
				<option value="">--------</option>
				<?php foreach (funcs::explode($data['setting']['delete_torrents_reasons']) as $reason): ?>
				<option value="<?=$reason?>"><?=$reason?></option>
				<?php endforeach;?>
			</select>
	    </div>
		<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
	    <div class="target_list">
	    	扣除发种人上传流量
	    	<select name="uploaded" id="uploaded">
	    		<option value="0">=不扣除=</option>
	    		<option value="1">1G</option>
	    		<option value="2">2G</option>
	    		<option value="5">5G</option>
	    		<option value="10">10G</option>
	    		<option value="50">50G</option>
	    		<option value="100">100G</option>
	    	</select>
	    </div>
	    <div class="target_list" style="display:none">
	        封禁发种人发种权限
	    	<select name="" id="">
	    		<option value="1">=不封禁=</option>
	    		<option value="1">封禁</option>
	    	</select>
	    </div>
		<?php endif; ?>

	    <div class="target_list" style="border-bottom:none;">
	        <input id="tid" type="hidden" value="">
			<?php if (isset($data['action']) && ($data['action'] == 'edit'||$data['action'] == 'details')): ?>
			<input type="hidden" value="" name="edit_page">
			<?php endif; ?>
	        <button id="delete_button" type="button" class="pn pnc"><strong>提交</strong></button>
			<button id="delete_close_button" type="button" class="pn pnc"><strong>关闭</strong></button>
	    </div>
	</form>
</div>