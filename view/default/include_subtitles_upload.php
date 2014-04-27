<script type="text/javascript" src="/static/xheditor/xheditor_plugins/multiupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/static/js/handlers.js"></script>
<script type="text/javascript">
var delete_attach = function(id){
	$.post("/subtitles/"+id+"/delete/",function(e){
		ui.notify('提示', e).effect('slide');
		if (e=="删除成功") {
			$("#attach_"+id).parent().remove();
		};
	});
}
var swfu;
<?php
if (!isset($data['current_torrent']['id']))
{
	$data['current_torrent']['id'] = 0;
}
?>
window.onload = function() {
	var settings = {
		flash_url : "/static/xheditor/xheditor_plugins/multiupload/swfupload/swfupload.swf",
		upload_url: "/upload/receive_attach/?swfupload=1&type=subtitles&tid=<?=$data['current_torrent']['id']?>&guid=<?=$data['guid']?>",
		post_params: {},
		file_size_limit : "100 MB",
		file_types : "*.srt;*.idx;*.sub;*.txt;*.ssa;*.nfo;*.ass;*.rar",
		file_types_description : "字幕文件",
		file_upload_limit : 100,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,

		// Button settings
		button_image_url: "/static/images/upload_sub.png",
		button_width: "63",
		button_height: "28",
		button_placeholder_id: "subs_upload",
		button_cursor : SWFUpload.CURSOR.HAND,
		// button_text: '<span class="theFont">上传字幕<span>',
		// button_text_style: ".theFont { color:#ffffff;}.theFont:hover { color:#000000;}",
		button_text_left_padding: 6,
		button_text_top_padding: 4,
		
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,

		custom_settings : {
			upload_target : "divFileProgressContainer"
		},

	};

	swfu = new SWFUpload(settings);
};
</script>