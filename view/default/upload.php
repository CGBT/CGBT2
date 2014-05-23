<?php
include 'header.php';
$have_date = false;
?>
<div id="wp" class="wp">
<div id="ct" class="ct2_a wp cl box-inside">


<ul class="nav">
<?php if ($data['action'] == 'edit'): ?>

<li<?php if (empty($data['current_category'])) {?> class="active"<?php }?>><a href="#" style='font-weight:bold'>修改种子:</a></li>
<?php foreach($data['all_category'] as $category):?>
<li<?php if (!empty($data['current_category']) && $data['current_category']['name_en'] == $category['name_en']) {?> class="active"<?php }?>><a href="/torrents/<?=$data['tid']?>/edit?c=<?=$category['name_en']?>"><?=$category['name']?></a></li>
<?php endforeach;?>

<?php else: ?>

<li<?php if (empty($data['current_category'])) {?> class="active"<?php }?>><a href="/upload">注意事项</a></li>
<?php foreach($data['all_category'] as $category):?>
<li<?php if (!empty($data['current_category']) && $data['current_category']['name_en'] == $category['name_en']) {?> class="active"<?php }?>><a href="/upload/<?=$category['name_en']?>"><?=$category['name']?></a></li>
<?php endforeach;?>

<?php endif; ?>
</ul>
<?php if(!empty($data['current_category'])):?>
<div class="category-rule">
	<div class="clearfix rule-title">
		<a href="javascript:;" class="pull-right button button-blue" id="category-rule-resize">展开</a>
		<h2>查看版规</h2>
	</div>
	<div id="rule-detail">
		<?=funcs::ubb2html($data['current_category']['rules'])?>
	</div>
	<script>
	$(function(){
		$("#category-rule-resize").click(function(){
			var a = $("#rule-detail");
			a.toggle();
			$(this).text()=="展开"?$(this).text("收缩"):$(this).text("展开");
		})
	})
	</script>
</div>
<?php endif; ?>
<?php if ($data['action'] == 'edit'): ?>
<ul class="tb cl">
<li class="a">
<table>
<?php if (!empty($data['current_torrent']['oldname'])): ?>
<tr><td>旧版标题：<?=$data['current_torrent']['oldname']?></td><td></td></tr>
<?php endif;?>
<tr id="t<?=$data['current_torrent']['id']?>">
<td>新版标题：<span id="new-title"><a name="title" class="new-torrent-title"><?=$data['current_torrent']['title']?></a></span></td>
<td>
<input type="button" class="delete_link button button-blue" value="删除" tid="<?=$data['current_torrent']['id']?>">
<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
<input type="button" class="audit_link button button-blue" value="审核" tid="<?=$data['current_torrent']['id']?>">
<?php endif;?>
</td></tr>
</table>
<br>
发种人：<?=$data['current_torrent']['username']?>
</li>
</ul>
<?php endif; ?>

<?php if(empty($data['current_category'])):?>
<table cellspacing="0" cellpadding="0" class="tfm">
<tr>
<td>
<?=$data['setting']['upload_note']?>
<?php if (isset($data['privileges_info']) && !$data['privileges_info']['have_privileges']): ?>
<br /><span style='font-weight:bold;color:red;font-size:16px;'><?=$data['privileges_info']['msg']?></span>
<?php endif;?>

</td>
</tr>
</table>
<?php endif;?>


<?php if(!empty($data['current_category'])):?>
<script type="text/javascript" src="/static/xheditor/xheditor.min.js"></script>
<script type="text/javascript" src="/static/xheditor/xheditor_plugins/ubb.min.js"></script>
<script type="text/javascript" src="/static/xheditor/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript" src="/static/js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/datepicker.css" />

<?php if ( $data['action']=='upload' && isset($data['privileges_info']) && !$data['privileges_info']['have_privileges']): ?>
<br /><span style='font-weight:bold;color:red;font-size:16px;'><?=$data['privileges_info']['msg']?></span>
<?php endif;?>

<form action="/upload/<?php if ($data['action']=='upload') echo 'insert'; else echo 'update';?>" method="post" enctype="multipart/form-data" id="frm_upload">
<table cellspacing="0" cellpadding="0" class="tfm">
	<?php if ($data['action']=='upload'):?>
	<tr>
	<th>种子文件</th>
	<td>
	<input type='file' id="torrent_file" name="torrent_file" style="size:100px;"></input>
	</td>
	<td>
	</td>
	</tr>

	<tr style="display:none">
	<th>字幕文件</th>
	<td>
	<input type='file' id="subtitles_file" name="subtitles_file" style="size:100px;"></input>
	</td>
	<td>
	</td>
	</tr>
	<?php endif;?>

	<?php 
	foreach($data['current_category']['options'] as $key => $option):
		if ($option['status']=='0')
		{
			continue;
		}	
	?>
	<tr>
	<th><?=$option['title']?> </th>
	<td>
		<?php if ($option['type'] == 'year'): ?>
			<select id="<?=$option['variable']?>" name="<?=$option['variable']?>">
			<option value="">=请选择=</option>
			<?php for ($i = date("Y")+1; $i > 1900; $i--): ?>
			<option value="<?=$i?>"<?php if ($data['action']=='edit' && $data['current_torrent'][$option['bind_field']]==$i) echo " selected='selected'"?>><?=$i?></option>
			<?php endfor; ?>
			</select> <?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?><br />
		<?php elseif ($option['type'] == 'date'): ?>
			<?php $have_date = true ?>
			<input id="thedate" value="<?php if ($data['action']=='edit') echo $data['current_torrent'][$option['bind_field']];?>" class="px" name="<?=$option['variable']?>"> <?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?>
		<?php elseif ($option['type'] == 'text'): ?>
			<input id="<?=$option['variable']?>" type="text" name="<?=$option['variable']?>" value="<?php if ($data['action']=='edit') { 
				if ($option['variable']=='imdb') {echo $data['current_torrent']['imdb_link']; }else {echo $data['current_torrent'][$option['bind_field']];}}?>" class="px"></input><?php if ($option['variable']=='imdb') echo "<input type='button' value='自动填写' class='button button-blue imdbinfo-get'>"; ?> <?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?><br />
		<?php elseif ($option['type'] == 'select'):?>
			<select id="<?=$option['variable']?>" name="<?=$option['variable']?>">
			<option value="">=请选择=</option>
			<?php foreach (explode("\n", $option['options']) as $value): ?>
			<option value="<?=$value?>"<?php if ($data['action']=='edit' && $data['current_torrent'][$option['bind_field']]==$value) echo " selected='selected'"?>><?=$value?></option>
			<?php endforeach; ?>
			</select> <?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?><br />
		<?php elseif ($option['type'] == 'select_input'):?>
			<input id="<?=$option['variable']?>" type="text" name="<?=$option['variable']?>" value="<?php if ($data['action']=='edit') {echo $data['current_torrent'][$option['bind_field']];}?>" class="px"></input>
			<select id="<?=$option['variable']?>_select" name="<?=$option['variable']?>_select" onchange="this.form.<?=$option['variable']?>.value=this.value">
			<option value="">=请选择=</option>
			<?php foreach (explode("\n", $option['options']) as $value): ?>
			<option value="<?=$value?>"<?php if ($data['action']=='edit' && $data['current_torrent'][$option['bind_field']]==$value) echo " selected='selected'"?>><?=$value?></option>
			<?php endforeach; ?>
			</select> <?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?><br />
		<?php elseif ($option['type'] == 'selects'):?>
			<?php 
			if ($data['action']=='edit')
			{
				$arr = funcs::explode($data['current_torrent'][$option['bind_field']], '/');
			}
			foreach (funcs::explode($option['options']) as $key => $value): 
			?>
				<label><input id="<?=$option['variable'].$key?>" type="checkbox" name="<?=$option['variable']?>[]" value="<?=$value?>"<?php if ($data['action']=='edit' && in_array($value, $arr)) echo "checked='checked'" ?>></input><?=$value?></label>
			<?php endforeach; ?>
			<?php if ($option['required']) echo "<span style='color:red'>*</span>";?> <?=$option['tip']?>
			<br/>
		<?php endif;?>
	</td>
	<td></td>
	</tr>

	<?php endforeach;?>

	<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
	<tr>
	<th>其他属性</th>
	<td>
	<label><input value='1' type='checkbox' id="iscollection" name="iscollection"<?php if ($data['action']=='edit' && $data['current_torrent']['iscollection']) echo " checked='checked'"; ?>>合集</input></label>
	<label><input value='1' type='checkbox' id="is0day" name="is0day"<?php if ($data['action']=='edit' && $data['current_torrent']['is0day']) echo " checked='checked'"; ?>>0day</input></label>
	<label><input value='1' type='checkbox' id="istop" name="istop"<?php if ($data['action']=='edit' && $data['current_torrent']['istop']) echo " checked='checked'"; ?>>置顶</input></label>
	<label><input value='1' type='checkbox' id="isrecommend" name="isrecommend"<?php if ($data['action']=='edit' && $data['current_torrent']['isrecommend']) echo " checked='checked'"; ?>>推荐</input></label>
	<label><input value='1' type='checkbox' id="isfree" name="isfree"<?php if ($data['action']=='edit' && $data['current_torrent']['isfree']) echo " checked='checked'"; ?>>免费</input></label>
	<label><input value='1' type='checkbox' id="is2x" name="is2x"<?php if ($data['action']=='edit' && $data['current_torrent']['is2x']) echo " checked='checked'"; ?>>2x</input></label>
	<label><input value='1' type='checkbox' id="is30p" name="is30p"<?php if ($data['action']=='edit' && $data['current_torrent']['is30p']) echo " checked='checked'"; ?>>30%</input></label>
	<label><input value='1' type='checkbox' id="ishalf" name="ishalf"<?php if ($data['action']=='edit' && $data['current_torrent']['ishalf']) echo " checked='checked'"; ?>>50%</input></label>
	<label><input value='1' type='checkbox' id="ishot" name="ishot"<?php if ($data['action']=='edit' && $data['current_torrent']['ishot']) echo " checked='checked'"; ?>>热门</input></label>
	<label><input value='1' type='checkbox' id="isft" name="isft"<?php if ($data['action']=='edit' && $data['current_torrent']['isft']) echo " checked='checked'"; ?>>禁转</input></label>
	<label><input value='1' type='checkbox' id="anonymous" name="anonymous"<?php if ($data['action']=='edit' && $data['current_torrent']['anonymous']) echo " checked='checked'"; ?>>匿名</input></label>
	
	<label><input value='1' type='checkbox' id="top_limit_time" name="top_limit_time"<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['top'])) echo " checked='checked'"; ?>>限时置顶</input></label>
	<label><input value='1' type='checkbox' id="free_limit_time" name="free_limit_time"<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['free'])) echo " checked='checked'"; ?>>限时免费</input></label>

	<br />
	<span id="span_top_limit_time" style="display:<?php if ($data['action']=='edit' && !isset($data['current_torrent']['mod']['top'])) echo "none"; ?>;">
	限时置顶：
	<label>起始时间<input id="top_start_time" type="textbox" name="top_start_time" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['top']['start_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['top']['start_time']) ?>"></input></label>
	<label>结束时间<input id="top_end_time" type="textbox" name="top_end_time" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['top']['end_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['top']['end_time']) ?>"></input></label>
	<?php if ($data['action']=='edit'):?>
	<input type="hidden" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['top']['start_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['top']['start_time']) ?>" name="old_top_start_time">
	<input type="hidden" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['top']['end_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['top']['end_time']) ?>" name="old_top_end_time">
	<?php endif;?>
	</span>
	<span id="span_free_limit_time" style="display:<?php if ($data['action']=='edit' && !isset($data['current_torrent']['mod']['free'])) echo "none"; ?>;">
	限时免费：
	<label>起始时间<input id="free_start_time" type="textbox" name="free_start_time" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['free']['start_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['free']['start_time']) ?>"></input></label>
	<label>结束时间<input id="free_end_time" type="textbox" name="free_end_time" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['free']['end_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['free']['end_time']) ?>"></input></label>
	<?php if ($data['action']=='edit'):?>
	<input type="hidden" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['free']['start_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['free']['start_time']) ?>" name="old_free_start_time">
	<input type="hidden" value="<?php if ($data['action']=='edit' && isset($data['current_torrent']['mod']['free']['end_time'])) echo date("Y-m-d H:i:s", $data['current_torrent']['mod']['free']['end_time']) ?>" name="old_free_end_time">
	<?php endif;?>
	</span>
	</td>
	<td>
	</td>
	</tr>
	<?php endif;?>

	<tr>
	<th>种子售价</th>
	<td>
		<select id="price" name="price">
			<option value="0">=免费=</option>
			<?php foreach (funcs::explode($data['setting']['torrents_price']) as $value): ?>
			<option value="<?=$value?>"<?php if ($data['action']=='edit' && $data['current_torrent']['price'] == $value) echo " selected='selected'"?>><?=$value?></option>
			<?php endforeach; ?>
		</select> 如果设置售价，前<?=$data['setting']['torrents_price_times']?>个下载人将扣除保种积分，奖励给发种人。系统扣税 <?=$data['setting']['torrents_price_tax']?>%。
	</td>
	<td>
	</td>
	</tr>

	<tr>
		<td></td>
		<td>

		<div>
			<span id="subs_upload"></span>
		</div>
		<div id="divFileProgressContainer"></div>


			<!-- <input type="button" class="button button-blue upload-btn" value="上传字幕" id="subtitles"> -->
			<!-- <input type="button" class="button button-blue upload-btn" value="上传nfo" id="nfos"> -->
			<div id="upload-field">
				<?php if ($data['action'] == 'edit'&&$data['attachments']):?>
				<?php foreach($data['attachments'] as $key => $value){?>
					<div>
						<a href="/subtitles/<?=$value['id']?>/download/"><?=$value['old_name']?></a>
						<input type="button" class="button button-red" id="attach_<?=$value['id']?>" onclick="delete_attach(<?=$value['id']?>)" value="删除">
						<span>上传者：<?=$value['username']?></span>
					</div>
				<?php } ?>
				<?php endif;?>
			</div>
		</td>
	</tr>
	<tr>
	<th>介绍</th>
	<td>
	<textarea id="descr" name="descr" rows="40" cols="100" style="width: 95%"><?php if ($data['action']=='edit'):?><?=$data['current_torrent']['descr']['descr']?><?php endif;?></textarea>
	</td>
	<td>
	</td>
	</tr>
	<tr>
	<th>&nbsp;</th>
	<td colspan="2">
	<?php if ($data['action'] == 'edit'):?>
	<input type="hidden" name="tid" value="<?=$data['current_torrent']['id']?>">
	<?php endif;?>
	<input type="hidden" name="category" value="<?=$data['current_category']['name_en']?>">
	<input type="hidden" name="guid" value="<?=$data['guid']?>">
	<button type="button" name="submitbutton" id="submit_button" value="true" class="pn pnc" /><strong>提交</strong></button>
	</td>
	</tr>


</table>
</form>
<?php endif;?>
</div>
</div><!--wp-->

<script type="text/javascript">
<?php if(!empty($data['current_category'])):?>
function get_imdblink(){
	var des = $("#descr").val();
	var des_regexp = /\[[a-z][^\]]*\]|\[\/[a-z]+\]/g;
	des = des.replace(des_regexp,'');
	var imdbregexp = /http:\/\/www.imdb.com\/title\/tt[0-9]{7}/i;
	if(des.match(imdbregexp)){
		imdb = des.match(imdbregexp)+"/";
		$("#imdb").val(imdb);
	}
	
}

$(function(){
	$(".imdbinfo-get").click(function(){
		if ($("#imdb").val().indexOf("http://www.imdb.com")>-1) {
			var imdbnum = $("#imdb").val().substr(26,9);
			$(".imdbinfo-get").val("请等待");
			$.getJSON("/api/imdb/?imdb_id="+imdbnum, function(data) {
				$("#district").val(data.country);
				$("#year").val(data.year);
				$("#name").val(data.name);
				$("#name_en").val(data.name_en);
				if (data.actors.length>2) {
					$("#actor").val(data.actors[0]+"/"+data.actors[1]+"/"+data.actors[2]);
				}
				else if(data.actors.length==2){
					$("#actor").val(data.actors[0]+"/"+data.actors[1]);
				}
				else if(data.actors.length==1){
					$("#actor").val(data.actors[0]);
				}
				for (var i = 0; i < data.genres.length; i++) {
					$("input[value='"+data.genres[i]+"']").prop("checked",true);
				};
				$(".imdbinfo-get").val("填写成功");
			})
		}
		else{
			ui.notify('错误', "请按照右侧格式填写imdb链接！").effect('slide');
		}
	});
	$("#top_limit_time,#free_limit_time").change(function(){
		var a = $(this).attr("id");
		$("#span_"+a).toggle()
	})
	<?php if ($data['action'] == 'edit'): ?>

	var in_title_field = <?php echo json_encode($data['in_title_field']); ?>;

	$("#frm_upload").change(function(){
		var torrentName=new Array();
		var result="";
		$.each(in_title_field, function(key, value){
			if (value!=="selects") {
				if ($("[name="+key+"]").val()) {
					torrentName.push("["+$("[name="+key+"]").val()+"]");
				};
			}
			else{
				var selectsArr=new Array();
				for (var i = 0; i < $("[name*="+key+"]:checked").length; i++) {
					selectsArr.push($("[name*="+key+"]:checked").eq(i).val());
				};
				str = selectsArr.join("/");
				torrentName.push("["+str+"]");
			}
			result = torrentName.join("");
			return(result);
		});
		result = result.replace(/\[其他]|\[]/g,"");
		$("#new-title").text(result);
	})

	<?php endif; //edit ?>
	var Msg = new Array();
	var editor;
	function editor_init()
	{
		var tools = 'Fontface,FontSize,Bold,Italic,Underline,Strikethrough,|,FontColor,BackColor,|,SelectAll,Removeformat,|,Align,List,Outdent,Indent,|,Link,Unlink,Img,Hr,Table,Source,Preview,About,newupload';
		var upload = '';
		var allPlugin={
		newupload:{c:'testClassName',t:'上传文件',s:'ctrl+2',h:1,e:function(){
				var _this=this;
				var jTest=$('<div>上传文件</div><div><label for="xheLinkUrl">文件: </label><input type="text" id="xheLinkUrl" value="http://" class="xheText" /></div><div style="text-align:right;"><input type="button" id="xheSave" value="确定" /></div>');
				var jUrl=$('#xheLinkUrl',jTest),jSave=$('#xheSave',jTest);
				var type = $(".testClassName").attr("type");
				_this.uploadInit(jUrl,'/upload/receive_attach/?guid=<?=$data['guid']?>&type='+type,'srt,idx,sub,txt,ssa,ass,rar,nfo');
				jSave.click(function(){
					_this.loadBookmark();
					html = "<div><a href='/subtitles/"+Msg[0].attach_id+"/download/'>"+jUrl.val()+"</a> <input type='button' class='button button-red' id='attach_"+Msg[0].attach_id+"' onclick='delete_attach("+Msg[0].attach_id+")' value='删除'></div>"
					$("#upload-field").append(html);
					_this.hidePanel();
					return false;	
				});
				_this.saveBookmark();
				_this.showDialog(jTest);
				$('.xheFile').click();
				_this.hidePanel();
				}
			}
		};
		editor = $('#descr').xheditor({plugins:allPlugin,tools:tools,forcePtag:false,onUpload:editor_upload,beforeSetSource:ubb2html,beforeGetSource:html2ubb,shortcuts:{'ctrl+enter':submit_upload},blur:get_imdblink,focus:get_imdblink,upImgUrl:"/upload/receive_images/?guid=<?=$data['guid']?>",upImgExt:"jpg,jpeg,gif,png",html5Upload:true});
		
		function editor_upload(arrMsg){ 
			Msg = [];
			Msg = arrMsg;
        	return Msg;
    	} 
	}
	editor_init();
	$('.upload-btn').click(function(){
		var e = $(this).attr("id");
		$(".testClassName").attr("type",e);
		editor.exec('newupload');
	});
	<?php if ($have_date): ?>
	$('#thedate').datepicker({
		format: 'yyyy-mm-dd'
	});
	<?php endif; ?>
	$('#top_start_time,#top_end_time,#free_start_time,#free_end_time').datepicker({
		format: 'yyyy-mm-dd',
		timeSwitch: 1
	});
	$('#submit_button').click(function(){
		submit_upload();
	});

})


var action = '<?=$data['action']?>';
var form_url = 'ajax_'+(action=='upload'?'insert':'update')+'_check';

function submit_upload()
{
	$.post("/upload/"+form_url, $("#frm_upload").serialize(),
		function(data){
			if (data.error)
			{
				ui.notify('错误', data.msg).effect('slide');
				if (data.field_type=='selects')
				{
					$("#"+data.field+'0').focus();
				}
				else if(data.field_type=='textarea')
				{
					editor.focus();
				}
				else
				{
					$("#"+data.field).focus();
				}
			}
			else
			{
				result = check_torrent_file();
				if (result)
				{
					$('#frm_upload').submit();
				}
			}
		}, "json");
}

function check_torrent_file()
{
	if (action=='edit')
	{
		return true;
	}
	var torrent_file = $('#torrent_file');
	if (torrent_file.val()=='')
	{
		ui.notify('错误', "请选择种子文件。").effect('slide');
		torrent_file.focus();
		return false;
	}
	var re = new RegExp("^.*\.torrent$", "ig");
	if (!re.test(torrent_file.val())) {
		ui.notify('错误', "您选择的不是种子文件。").effect('slide');
		torrent_file.focus();
		return false;
	}
	return true;
}


<?php endif; //current_category ?>
</script>

<?php if ($data['action'] == 'edit'): ?>
<?php if ($data['user']['is_moderator']||$data['user']['is_admin']):?>
<?php include "include_auditbox.php"; ?>
<?php endif; ?>

<?php include "include_deletebox.php"; ?>
<?php endif; ?>

<?php include "include_subtitles_upload.php"; ?>

<?php
include 'footer.php';
?>