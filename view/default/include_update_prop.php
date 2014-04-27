<div class="box box-float flipInX" id="update_prop_box">
	<div class="box-header">
		<span class="box-title">修改种子属性</span>
		<ul class="box-toolbar">
			<li class="close_prop_box"><a href="javascript:void(0);">X</a></li>
		</ul>
	</div>
	<div class="box-content">
			<form id="update_prop_form" action="#" method="post">
			<label><input value='1' type='checkbox' id="iscollection" name="iscollection">合集</input></label>
			<label><input value='1' type='checkbox' id="is0day" name="is0day">0day</input></label>
			<label><input value='1' type='checkbox' id="istop" name="istop">置顶</input></label>
			<label><input value='1' type='checkbox' id="isrecommend" name="isrecommend">推荐</input></label>
			<label><input value='1' type='checkbox' id="isfree" name="isfree">免费</input></label>
			<br />
			<label><input value='1' type='checkbox' id="is2x" name="is2x">2x</input></label>
			<label><input value='1' type='checkbox' id="is30p" name="is30p">30%</input></label>
			<label><input value='1' type='checkbox' id="ishalf" name="ishalf">50%</input></label>
			<label><input value='1' type='checkbox' id="ishot" name="ishot">热门</input></label>
			<label><input value='1' type='checkbox' id="isft" name="isft">禁转</input></label>
			<br />
		 	<input type="button" id="post_update_prop" class="button button-blue" value="提交">
		 	<input type="button" class="button close_prop_box" value="取消">
			</form>
	</div>
</div>
<script>
$(function(){
	var update_prop_float = function(){
		var tid,obj;
		$(".prop_link").click(function(){
			obj = $(this);
			tid = obj.parents("tr").attr("id").substring(1);
			get_prop(tid);
		});
		$(".close_prop_box").click(function(){
			$('#update_prop_box').hide();
		});
		$("#post_update_prop").click(function(){
			post_update_prop(tid);
		})
		var post_update_prop = function(tid){
			$.post("/torrents/"+tid+"/update_prop/",$("#update_prop_form").serialize()+"&inajax=1",function(data){
				ui.notify('提示', data.msg).effect('slide');
				$(".close-this").click();
			},"json")
		}
		var get_prop = function(tid){
			$.getJSON("/torrents/"+tid+"/get_prop/",function(data){
				if (data.iscollection=='1') {$("#iscollection").prop("checked", 'checked');} else {$("#iscollection").prop("checked", '');}
				if (data.is0day=='1')       {$("#is0day").prop("checked", 'checked');} else {$("#is0day").prop("checked", '');}
				if (data.istop=='1')        {$("#istop").prop("checked", 'checked');} else {$("#istop").prop("checked", '');}
				if (data.isrecommend=='1')  {$("#isrecommend").prop("checked", 'checked');} else {$("#isrecommend").prop("checked", '');}
				if (data.isfree=='1')       {$("#isfree").prop("checked", 'checked');} else {$("#isfree").prop("checked", '');}
				if (data.is2x=='1')         {$("#is2x").prop("checked", 'checked');} else {$("#is2x").prop("checked", '');}
				if (data.is30p=='1')        {$("#is30p").prop("checked", 'checked');} else {$("#is30p").prop("checked", '');}
				if (data.ishalf=='1')       {$("#ishalf").prop("checked", 'checked');} else {$("#ishalf").prop("checked", '');}
				if (data.ishot=='1')        {$("#ishot").prop("checked", 'checked');} else {$("#ishot").prop("checked", '');}
				if (data.isft=='1')         {$("#isft").prop("checked", 'checked');} else {$("#isft").prop("checked", '');}
				$("#update_prop_box").show();
			})
		}
	}
	update_prop_float();
})
</script>