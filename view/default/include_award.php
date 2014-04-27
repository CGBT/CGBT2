<div class="box box-float flipInX" id="thumbup-box">
	<div class="box-header">
		<span class="box-title">用自己的保种积分奖励发种人</span>
		<ul class="box-toolbar">
			<li class="close-this"><a href="javascript:void(0);">X</a></li>
		</ul>
	</div>
	<div class="box-content">
		<p>已奖励的用户：</p>
		<p id="award-user-list"></p>
		<div class="form-actions">
			<div class="mb10">奖励
			<select name="count" id="count">
			<?php foreach (funcs::explode($data['setting']['torrents_award']) as $extcredits1): ?>
				<option value="<?=$extcredits1?>"><?=$extcredits1?></option>
			<?php endforeach; ?>
			</select>
			保种积分
			</div>
		 	<button id="postaward" class="button button-blue">提交</button>
		 	<input type="button" class="button close-this" value="取消">
		</div>
	</div>
</div>
<script>
$(function(){
	var tableFloatbox = function(){
		var tid,obj;
		$(".thumbs-up").click(function(){
			obj = $(this);
			tid = obj.parents("tr").attr("id").substring(1);
			$("#award-user-list").html("");
			get_award(tid);
			$("#thumbup-box").show();
		});
		$(".close-this").click(function(){
			$(this).parent().parent().parent().hide();
		});
		$("#postaward").click(function(){
			post_award(tid);
		})
		var post_award = function(tid){
			$.post("/torrents/"+tid+"/award/",{count:$("#count").val(),inajax:1},function(data){
				ui.notify('提示', data.msg).effect('slide');
				$(".close-this").click();
			},"json")
		}
		var get_award = function(tid){
			$.getJSON("/torrents/"+tid+"/award_cloud/",function(data){
				$.each(data,function(index,value){
					var html = "<a href='/user/"+value.uid+"/' title='"+value.username+"奖励给发种人"+value.count+"保种积分' style='font-size:"+value.fontsize+"px;color:"+value.color+"' target='_blank'>"+value.user_title+"</a>|";
					$("#award-user-list").append(html);	
				});
			})
		}
	}
	tableFloatbox();
})
</script>