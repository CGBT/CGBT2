<?php
include 'header.php';
?>
<link rel="stylesheet" type="text/css" href="/static/css/search.css?version=20131012" >
<div class="breadcrumbs-fixed">
	<?php
	$i = 0;
	foreach ($data['search_items'] as $category => $item):
	$i++;
	?>
	<a href="/search/<?php echo $category == 'all'? '' : $category . '/' ?>" class="ml10 toggle-control-btn <?php if ($data['current_category'] == $category) {echo "active";} ?>"><?=$item['name']?></a>
	<?php endforeach; ?>
</div>
<div id="wp" class="wp">
	<div id="ct" class="ct2_a wp cl">
		<div id="container">
			<div id="sidebar">
				<div class="boxtitle">
					<a href="/user/<?=$data['uid']?>/">
						<img src="/static/images/users.png">
						<?=$data['username']?>
					</a>
					<a href="/user/logout" title="注销"><img src="/static/images/exit.png" class="pull-right" alt="注销"></a>
				</div>
				<div class="box-field">
					<img src="/static/images/arrow-up.png" align="absmiddle"> 上传 <?=$data['user']['uploaded_text']?>
					<img src="/static/images/arrow-up-s.png" width="8" align="absmiddle" title="我发布的种子总数">
					<span title="我发布的种子总数"><?=$data['user']['total_torrent_count']?></span>
					<br />

					<img src="/static/images/arrow-down.png" align="absmiddle"> 下载 <?=$data['user']['downloaded_text']?>
					<img src="/static/images/arrow-down-s.png" width="8" align="absmiddle" title="我下载过的种子总数">
					<span title="我下载过的种子总数"><?=$data['user']['total_completed_count']?></span>
					<br />

					<img src="/static/images/tab.png" align="absmiddle"> 共享率 <?=$data['user']['ratio']?>
					<img src="/static/images/arrow-up-s.png" width="8" align="absmiddle" title="我正在做种的种子总数">
					<span title="我正在做种的种子总数"><?=$data['user']['seed_count']?></span>
					<img src="/static/images/arrow-down-s.png" width="8" align="absmiddle" title="我正在下载的种子总数">
					<span title="我正在下载的种子总数"><?=$data['user']['leech_count']?></span>
					<br />


					<img src="/static/images/thumbs-up.png" align="absmiddle"> 保种积分 <?=$data['user']['extcredits1_text']?>
					<br />
					<img src="/static/images/thumbs-up.png" align="absmiddle"> 虚拟上传 <?=$data['user']['uploaded2_text']?>
					<br />
					<img src="/static/images/thumbs-up.png" align="absmiddle"> 虚拟下载 <?=$data['user']['downloaded2_text']?>
					<br />
					<img src="/static/images/thumbs-up.png" align="absmiddle"> 土豪金 <?=$data['user']['extcredits2']?>
					<br />
					<img src="/static/images/thumbs-up.png" align="absmiddle"> 总积分 <?=$data['user']['total_credits']?>
					<br />
					<img src="/static/images/thumbs-up.png" align="absmiddle"> 用户组： <?=$data['user']['group_name']?>
					<br />
					<div class="mt10">
						昵称：<input id="user_title" type="text" name="user_title" value="<?=$data['user']['title']?>">
						<input type="button" value="修改" class="button button-blue" id="modify_user_title">
					</div>
					<?php if ($data['user']['is_moderator'] || $data['user']['is_admin'] || $data['user']['class'] == '12'): ?>
					<div class="mt10 mb10">
						职责：<input id="user_duty" type="text" name="user_duty" value="<?=$data['user']['duty']?>">
						<input type="button" value="修改" class="button button-blue" onclick="modify_user_duty();">
					</div>
					<?php endif;?>
				</div>
			</div>
			<div id="select" class="m" clstag="thirdtype|keycount|thirdtype|select">
				<div id="tabContainer">
					<ul class="nav">
						<?php
						$i = 0;
						foreach ($data['search_items'] as $category => $item):
						$i++;
						?>
						<li id="tab<?=$i?>"><a onmouseover="switchTab('tab<?=$i?>','con<?=$i?>')"  href="/search/<?php echo $category == 'all'? '' : $category . '/' ?>" class="<?php if ($data['current_category'] == $category) {echo "on";} ?>"><?=$item['name']?></a></li>
						<?php endforeach; ?>
					</ul>
					<div style="clear: both"></div>
				</div>
<?php
$i = 0;
foreach ($data['search_items'] as $category => $item):
$i++;
?>
				<div id="con<?=$i?>" <?php if ( $data['current_category'] != $category) {echo "style='display:none'";} ?>>
					<div class="mt">
						<h2><?=$item['name']?>&nbsp;-&nbsp;条件筛选<?php if($data['today_upload_count'][$category]>0):?><i title="今日新增种子数"><a href="/search/<?=$category?>/">+<?=$data['today_upload_count'][$category]?></a></i><?php endif;?>
						<?php if ($category!='all'): ?>
						版主:
						<?php foreach( funcs::explode($data['all_category'][$category]['admins']) as $admin) : ?>
						<a href='/users/<?=$admin?>/'><?=$admin?></a>
						<?php endforeach; ?>
						<?php endif;?>
						</h2>
					</div>
					<?php foreach ($item['search_items'] as $key => $value): ?>
					<dl class="fore">
						<dt><?=$value['name']?>：</dt>
						<dd>
							<?php foreach ($value['options'] as $index => $option): ?>
							<div rel="0"><a <?php if ($category == 'all'): ?>style="color:blue"<?php endif;?> id="0" href="/search/<?php echo $category == 'all' ? '' : $category.'/'; ?><?php echo empty($option['link']) ? '' : $option['link'] . '/' ?>" <?php if (isset($option['checked']) && $option['checked']): ?>class="curr" <?php endif ?>><?=$option['title']?></a></div>
							<?php endforeach; ?>
						</dd>
					</dl>
					<?php endforeach; ?>
					<dl class="fore">
						<dt>热门搜索：</dt>
						<dd>
							<?php foreach (funcs::explode($item['hot_keywords']) as $keyword): ?>
							<div rel="0"><a id="0" href="/search/<?php echo $category == 'all' ? '' : $category.'/'; ?>x<?=$keyword?>/"><?=$keyword?></a></div>
							<?php endforeach; ?>
						</dd>
					</dl>
					<?php if ( $i ==1 )://这是广告啊 ?>
					<dl class="fore">
						<dt>公告：</dt>
						<dd>
							<div>
								<?=$data['setting']['search_page_announce']?>
							</div>
    					</dd>
					</dl>
					<?php endif; ?>
				</div>
<?php endforeach; ?>
			</div>
			<div id="mainContent">

				<!--search-->
				<div class="pagecontent clearfix">
<table style="float:left">
		<tr>
<td><input onclick="check(this);" <?php if (in_array('my', $data['dict_search_params'])) echo "checked='checked'"; ?>  type="checkbox" value="my" id="cb_my" name="cb_my"><label for="cb_my">我的种子</label></td>
<td><input onclick="check(this);" <?php if (in_array('subs', $data['dict_search_params'])) echo "checked='checked'"; ?> type="checkbox" value="subs" id="cb_subs" name="cb_subs"><label for="cb_subs">有字幕</label></td>
<td><input onclick="check(this);" <?php if (in_array('collection', $data['dict_search_params'])) echo "checked='checked'"; ?> type="checkbox" value="collection" id="cb_collection" name="cb_collection"><label for="cb_collection">合集</label></td>
<td>
<div class="select">
	<select id="cb_include" onchange="check(this);" type="select-one">
		<option value="all" <?php if (in_array('all', $data['dict_search_params'])) echo "selected='selected'"; ?>>=全部=</option>
		<option value="dead" <?php if (in_array('dead', $data['dict_search_params'])) echo "selected='selected'"; ?>>仅死种</option>
		<option value="" <?php if ( !in_array('dead', $data['dict_search_params']) && !in_array('all', $data['dict_search_params'])) echo "selected='selected'"; ?>>仅活种</option>
	</select>
</div>
</td>
<td>
<div class="select">
	<select id="cb_off" onchange="check(this);" type="select-one">
		<option value="">=优惠=</option>
		<option value="free" <?php if (in_array('free', $data['dict_search_params'])) echo "selected='selected'"; ?>>免费</option>
		<option value="30p" <?php if (in_array('30p', $data['dict_search_params'])) echo "selected='selected'"; ?>>30%</option>
		<option value="half" <?php if (in_array('half', $data['dict_search_params'])) echo "selected='selected'"; ?>>50%</option>
	</select>
</div>
</td>
<td>
<div class="select">
	<select id="cb_format" onchange="check(this);" type="select-one">
		<option value="">=标签=</option>
		<option value="top"<?php if (in_array('top', $data['dict_search_params'])) echo "selected='selected'"; ?>>置顶</option>
		<option value="recmd"<?php if (in_array('recmd', $data['dict_search_params'])) echo "selected='selected'"; ?>>推荐</option>
	</select>
</div>
</td>
<td>
<div class="select">
	<select id="cb_format" onchange="check(this);" type="select-one">
		<option value="">=质量=</option>
		<option value="hd" <?php if (in_array('hd', $data['dict_search_params'])) echo "selected='selected'"; ?>>高清</option>
		<option value="sd" <?php if (in_array('sd', $data['dict_search_params'])) echo "selected='selected'"; ?>>标清</option>
	</select>
</div>
</td>
<td>
<div class="select">
	<select id="cb_orderby" onchange="check(this);" type="select-one">
		<option value="">=排序=</option>
		<?php foreach ($data['search_item_orderby'] as $k=>$o): ?>
		<option value="<?=$o?>" <?php if (in_array($o, $data['dict_search_params'])) echo "selected='selected'"; ?>><?=$data['search_item_orderby_text'][$o]?></option>
		<?php endforeach; ?>
	</select>
</div>
</td>
<td>
	<div class="select">
		<select id="cb_orderby2" onchange="check(this);" type="select-one">
			<option value="">倒序</option>
			<option value="asc" <?php if (in_array('asc', $data['dict_search_params'])) echo "selected='selected'"; ?>>正序</option>
		</select>
	</div>
</td>
		</tr>
</table>
<table style="float:right">
		<tr>
			<td>
				<form action="" onsubmit="search();return false;">
				<input type="text" name="keyword" maxlength="30" id="keyword" value="<?=$data['keyword']?>" x-webkit-speech autocomplete="off" placeholder="请输入搜索词">
				<input type="button" class="searchbutton button button-blue" value="搜索" onclick="search()">
				</form>
			</td>
		</tr>
</table>
				</div>
				<div class="pagecontent clearfix">
					<div class="pager">
						<a href="/rss/<?php echo empty($data['current_category'])||$data['current_category']=='all' ? '' : $data['current_category'] . '/'; ?><?php echo (empty($data['search_params']) ? '' : $data['search_params'] . '/') . $data['user']['passkey'] . '/'; ?>" class="button button-blue pull-left">RSS</a>
						<a href="javascript:void(0);" class="button button-blue pull-left ml10" id="update_last_browse_time">清空new标记</a>
						<?php echo $data['pager']->output; ?>
					</div>
				</div>

<?php include "include_torrenttable.php"; ?>


				<div class="pagecontent clearfix">
					<div class="pager">
						<a href="/rss/<?php echo empty($data['current_category'])||$data['current_category']=='all' ? '' : $data['current_category'] . '/'; ?><?php echo (empty($data['search_params']) ? '' : $data['search_params'] . '/') . $data['user']['passkey'] . '/'; ?>" class="button button-blue pull-left">RSS</a>
						<?php echo $data['pager']->output; ?>
					</div>
				</div>


			</div>
			<!-- end #mainContent -->


		</div>
	</div>
</div>
<!--wp-->

<script type="text/javascript" src="/static/js/jquery.suggest.js"></script>
<script type="text/javascript">
	function switchTab(ProTag, ProBox) {
		for (i = 1; i < 13; i++) {
			if ("tab" + i == ProTag) {
				document.getElementById(ProTag).getElementsByTagName("a")[0].className = "on";
			} else {
				document.getElementById("tab" + i).getElementsByTagName("a")[0].className = "";
			}
			if ("con" + i == ProBox) {
				document.getElementById(ProBox).style.display = "";
			} else {
				document.getElementById("con" + i).style.display = "none";
			}
		}
	}

	var oldurl = "<?=$data['url']?>";
	var dict_search_params = <?php echo json_encode($data['dict_search_params']); ?>;
	var search_params = "<?php echo $data['search_params']; ?>";
	var oldkeyword = "<?=$data['keyword'] ?>";
	Array.prototype.indexof = function (str) {
	for (var i = 0; i < this.length; i++) {
		if (str == this[i]) {
			return i;
		}
	}
		return -1;
	}
	function toggle_params(add_params, remove_params)
	{
		var newurl = oldurl.replace(search_params, '');
		newurl = newurl.replace('//','/');
		var dupe_dict_search_params = dict_search_params;

		if (remove_params != '')
		{
			for (i in remove_params)
			{
				index = dupe_dict_search_params.indexof(remove_params[i]);
				if (index >= 0)
				{
					dupe_dict_search_params.splice(index,1)
				}
			}
		}
		if (add_params != '')
		{
			dupe_dict_search_params.push(add_params);
		}
		newurl = newurl + dupe_dict_search_params.join('-') + '/';
		self.location.href = newurl;
	}

	function search()
	{
		var keyword = $("#keyword").val();
		keyword = keyword.replace(/-/g, ' ');
		keyword = keyword.replace(/\//g, ' ');
		keyword = keyword.replace(/\./g, ' ');
		var param = "x"+keyword;
		var page;
		for (var i = 0; i < dict_search_params.length; i++) {
			if (dict_search_params[i].slice(0,1)=="p") {
				page = dict_search_params[i];
			};
		};
		if (oldkeyword != '')
		{
			if (oldkeyword != keyword)
			{
				toggle_params(param, new Array('x'+oldkeyword,page));
			}
		}
		else
		{
			toggle_params(param,new Array(page));
		}
	}
	function check(obj)
	{
		var o = $(obj);
		var type = o.attr('type');
		var v = o.val();

		if (type=='checkbox')
		{
			if (o.is(":checked"))
			{
				if (v == 'my')
				{
					toggle_params('my-all', '');
				}
				else
				{
					toggle_params(v, '');
				}
			}
			else
			{
				if (v == 'my')
				{
					toggle_params('', new Array('my','all'));
				}
				else
				{
					toggle_params('', new Array(v));
				}
			}
		}
		else if (type=='select-one')
		{
			add_params = '';
			remove_params = new Array();
			for (i=0; i<o.children().length; i++)
			{
				option_value = o.children()[i].value;
				if (option_value != '' && dict_search_params.indexof(option_value) != -1)
				{
					remove_params.push(option_value);
				}
			}
			if (v != '')
			{
				add_params = v;
			}
			toggle_params(v, remove_params);
		}
		else if(type=='link'){
			var o_href = location.pathname;
			var o_array = new Array('o1','o2','o3','o4','o5','o6','o7','asc');
			var o_val = o.attr('val');
			if (o_href.indexOf(o_val)>0&&o_href.indexOf("asc")<0) {
				toggle_params('asc','');
			}
			else if (o_href.indexOf(o_val)>0&&o_href.indexOf("asc")>0) {
				toggle_params('',new Array('asc'));
			}
			else if (o_href.indexOf(o_val)<0&&o_href.indexOf("o")>0) {
				toggle_params(o_val,o_array);
			}
			else{
				toggle_params(o_val,'');
			}
		}
	}



	<?php if ($data['user']['is_moderator'] || $data['user']['is_admin'] || $data['user']['class'] == '12'): ?>
	function modify_user_duty()
	{
		var user_duty = $('#user_duty').val();
		$.post("/user/update_duty/",{user_duty:user_duty},function(data){
			ui.notify('提示', data).effect('slide');
		});
		$("#user_duty").val(user_duty);

	}
	<?php endif; ?>

$(window).load(function(){

	keyword = $("#keyword");
	keyword.suggest("/api/search_suggest/",{
		onSelect: function() {
			keyword.val(this.value);
		}
	});
	function modify_user_title()
	{
		var title = $('#user_title').val();
		var extcredits1 = <?=$data['setting']['modify_title_need_extcredits1']?>;
		var c_title = "修改昵称",c_msg = "修改昵称需要花费您"+extcredits1+"个保种积分，确认修改？";
		var c_post = function(){
			$.post("/user/update_title/",{title:title},function(data){
				ui.notify('提示', data).effect('slide');
			})
		}
		dConfirm(c_title,c_msg,c_post);
	}
	$("#modify_user_title").click(function(){
		modify_user_title();
	})
	$("#update_last_browse_time").click(function(){
		$.post("/user/update_last_browse_time/",null,function(data){
			$(".new_flag").hide();
			ui.notify('提示', data).effect('slide');
		});
	});
});
//显示顶部切换栏
$(window).scroll(function() {
	if ($(this).scrollTop() > 450) {
		$('.breadcrumbs-fixed').fadeIn(500);
	} else {
		$('.breadcrumbs-fixed').fadeOut(300);
	}
});
</script>



<?php
include 'footer.php';
?>