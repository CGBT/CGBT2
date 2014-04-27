<?php
include 'header.php';
?>
<body>
<?php
include 'top.php';
?>
<!--top-->
<div id="container">
	<div id="sidebar">
		<?php
			include 'menu_sidebar.php';
		?>
	</div><!--sidebar-->
	<div id="content">
		<?php include 'menu_subnav.php'; ?>		
		<div class="form_title w100">系统设置</div>		
		<div class="form_box w1020">
			<form action="/admin/setting/<?=$data['action']?>/" method="post" name='form1' id="form1">			
			<table class="table_form">
				<?php if ($data['action']=='index'): ?>
				<tr>
					<th>站点名称</th>
					<td><input type="text" id="site_name" name="site_name" value="<?=$data['setting']['site_name']?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>站点地址</th>
					<td><input type="text" id="site_domain" name="site_domain" value="<?=$data['setting']['site_domain']?>" class="input"  /></td>
					<td>如 http://cgbt.cn 无斜杠结尾</td>
				</tr>
				<tr>
					<th>静态url前缀</th>
					<td><input type="text" id="static_prefix" name="static_prefix" value="<?=$data['setting']['static_prefix']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>论坛类型</th>
					<td><select name="forums_type">
						<option<?php if ($data['setting']['forums_type']=='discuz x2') echo " selected='selected'";?> value="discuz x2">discuz x2</option>
						<option<?php if ($data['setting']['forums_type']=='discuz 7.2') echo " selected='selected'";?> value="discuz 7.2">discuz 7.2</option>
						<option<?php if ($data['setting']['forums_type']=='discuz x2.5') echo " selected='selected'";?> value="discuz x2.5">discuz x2.5</option>
						<option<?php if ($data['setting']['forums_type']=='internal') echo " selected='selected'";?> value="internal">internal</option>
						</select> </td>
					<td>目前仅支持discuz x2</td>
				</tr>
				<tr>
					<th>论坛地址</th>
					<td><input type="text" id="forums_url" name="forums_url" value="<?=$data['setting']['forums_url']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>QQ群</th>
					<td><input type="text" id="site_qq_qun" name="site_qq_qun" value="<?=$data['setting']['site_qq_qun']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>QQ群名称</th>
					<td><input type="text" id="site_qq_qun_name" name="site_qq_qun_name" value="<?=$data['setting']['site_qq_qun_name']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>Cookie超时时间</th>
					<td><input type="text" id="site_cookie_expire" name="site_cookie_expire" value="<?=$data['setting']['site_cookie_expire']?>" class="input" /></td>
					<td>天</td>
				</tr>
				<tr>
					<th>用户在线时间</th>
					<td><input type="text" id="online_time" name="online_time" value="<?=$data['setting']['online_time']?>" class="input" /></td>
					<td>分钟</td>
				</tr>
				<tr>
					<th>检查登录失败次数</th>
					<td><input type="text" id="login_fail_count" name="login_fail_count" value="<?=$data['setting']['login_fail_count']?>" class="input" /></td>
					<td>在一定时间内登录失败超过一定次数则不能登录</td>
				</tr>
				<tr>
					<th>检查登录失败时间</th>
					<td><input type="text" id="login_fail_time" name="login_fail_time" value="<?=$data['setting']['login_fail_time']?>" class="input" /></td>
					<td>分钟, 在一定时间内登录失败超过一定次数则不能登录</td>
				</tr>
				<tr>
					<th>验证邀请码</th>
					<td>
						<select name="check_invite_code" id="check_invite_code">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['check_invite_code'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['check_invite_code'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td>第一次登录PT时需要输入邀请码</td>
				</tr>
				<tr>
					<th>验证论坛邮箱激活</th>
					<td>
						<select name="check_forums_user_valid" id="check_forums_user_valid">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['check_forums_user_valid'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['check_forums_user_valid'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td>第一次登录PT时验证论坛邮箱是否激活</td>
				</tr>
				<tr>
					<th>页头背景图</th>
					<td>
					<textarea id="header_background_pic" name="header_background_pic" style="width:210px;height:100px;" ><?=$data['setting']['header_background_pic']?></textarea>
					</td>
					<td>格式为：url|||padding|||link<br />
					一行一条记录，三个竖线分割，多条记录随机显示</td>
				</tr>
				<tr>
					<th>搜索页公告</th>
					<td>
					<textarea id="search_page_announce" name="search_page_announce" style="width:210px;height:100px;" ><?=$data['setting']['search_page_announce']?></textarea>
					</td>
					<td>UBB格式</td>
				</tr>
				<?php elseif ($data['action']=='forums'): ?>				
				<tr>
					<th>论坛地址</th>
					<td><input type="text" id="forums_url" name="forums_url" value="<?=$data['setting']['forums_url']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>论坛template目录</th>
					<td><input type="text" id="forums_template_dir" name="forums_template_dir" value="<?=$data['setting']['forums_template_dir']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>注册页面地址</th>
					<td><input type="text" id="forums_register_url" name="forums_register_url" value="<?=$data['setting']['forums_register_url']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>取回密码页面地址</th>
					<td><input type="text" id="forums_lost_password_url" name="forums_lost_password_url" value="<?=$data['setting']['forums_lost_password_url']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>修改密码页面地址</th>
					<td><input type="text" id="forums_modify_password_url" name="forums_modify_password_url" value="<?=$data['setting']['forums_modify_password_url']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>论坛帖子url模板</th>
					<td><input type="text" id="forums_thread_url" name="forums_thread_url" value="<?=$data['setting']['forums_thread_url']?>" class="input" /></td>
					<td>注意{$tid}</td>
				</tr>
				<?php elseif ($data['action']=='credits'): ?>	
				<tr>
					<th>1下载额度需要多少保种积分</th>
					<td><input type="text" id="modify_title_need_extcredits1" name="modify_title_need_extcredits1" value="<?=$data['setting']['modify_title_need_extcredits1']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>修改昵称需要多少保种积分</th>
					<td><input type="text" id="download_need_extcredits1" name="download_need_extcredits1" value="<?=$data['setting']['download_need_extcredits1']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th style="width:160px;">保种积分：单种最大值</th>
					<td><input type="text" id="extcredits1_max" name="extcredits1_max" value="<?=$data['setting']['extcredits1_max']?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>保种积分：单种最小值</th>
					<td><input type="text" id="extcredits1_min" name="extcredits1_min" value="<?=$data['setting']['extcredits1_min']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>保种积分：种子大小因数</th>
					<td><input type="text" id="extcredits1_size" name="extcredits1_size" value="<?=$data['setting']['extcredits1_size']?>" class="input" /></td>
					<td>G</td>
				</tr>
				<tr>
					<th>保种积分：种子数量因数</th>
					<td><input type="text" id="extcredits1_seeders" name="extcredits1_seeders" value="<?=$data['setting']['extcredits1_seeders']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>保种积分：发布时间因数</th>
					<td><input type="text" id="extcredits1_weeks" name="extcredits1_weeks" value="<?=$data['setting']['extcredits1_weeks']?>" class="input" /></td>
					<td>周</td>
				</tr>

				<tr>
					<th>论坛积分：金币字段名</th>
					<td><input type="text" id="forums_money_field" name="forums_money_field" value="<?=$data['setting']['forums_money_field']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>金币兑换上传流量比例</th>
					<td><input type="text" id="money2uploaded_need_money" name="money2uploaded_need_money" value="<?=$data['setting']['money2uploaded_need_money']?>" class="input" /></td>
					<td>多少金币兑换1G上传流量，整数，0则表示不开通兑换功能</td>
				</tr>
				<tr>
					<th>金币兑换上传流量时间间隔</th>
					<td><input type="text" id="money2uploaded_days_interval" name="money2uploaded_days_interval" value="<?=$data['setting']['money2uploaded_days_interval']?>" class="input" /></td>
					<td>多少天内只能兑换一次，0则表示不限制</td>
				</tr>
				<tr>
					<th>金币兑换上传流量上限</th>
					<td><input type="text" id="money2uploaded_max" name="money2uploaded_max" value="<?=$data['setting']['money2uploaded_max']?>" class="input" /></td>
					<td>最多可以兑换多少金币</td>
				</tr>

				<tr>
					<th>保种积分兑换上传流量比例</th>
					<td><input type="text" id="extcredits12uploaded_need_extcredits1" name="extcredits12uploaded_need_extcredits1" value="<?=$data['setting']['extcredits12uploaded_need_extcredits1']?>" class="input" /></td>
					<td>多少保重积分兑换1G上传流量，整数，0则表示不开通兑换功能</td>
				</tr>
				<tr>
					<th>保种积分兑换上传流量时间间隔</th>
					<td><input type="text" id="extcredits12uploaded_days_interval" name="extcredits12uploaded_days_interval" value="<?=$data['setting']['extcredits12uploaded_days_interval']?>" class="input" /></td>
					<td>多少天内只能兑换一次，0则表示不限制</td>
				</tr>
				<tr>
					<th>保种积分兑换上传流量上限</th>
					<td><input type="text" id="extcredits12uploaded_max" name="extcredits12uploaded_max" value="<?=$data['setting']['extcredits12uploaded_max']?>" class="input" /></td>
					<td>最多可以兑换多少保种积分</td>
				</tr>
				<tr>
					<th>种子可奖励积分数</th>
					<td>
					<textarea id="torrents_award" name="torrents_award" style="width:210px;height:100px;" ><?=$data['setting']['torrents_award']?></textarea>
					</td>
					<td>一行一条记录</td>
				</tr>
				<tr>
					<th>请求补种扣除保种积分数</th>
					<td><input type="text" id="req_seed_extcredits1" name="req_seed_extcredits1" value="<?=$data['setting']['req_seed_extcredits1']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>竞价置顶最低出价</th>
					<td><input type="text" id="mod_price_min" name="mod_price_min" value="<?=$data['setting']['mod_price_min']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>上传字幕奖励保种积分</th>
					<td><input type="text" id="upload_sub_extcredits1" name="upload_sub_extcredits1" value="<?=$data['setting']['upload_sub_extcredits1']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>上传字幕奖励土豪金</th>
					<td><input type="text" id="upload_sub_extcredits2" name="upload_sub_extcredits2" value="<?=$data['setting']['upload_sub_extcredits2']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>审核区顶踩奖励土豪金</th>
					<td><input type="text" id="torrents_rate_extcredits2" name="torrents_rate_extcredits2" value="<?=$data['setting']['torrents_rate_extcredits2']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>感谢发种人奖励土豪金</th>
					<td><input type="text" id="torrents_award_extcredits2" name="torrents_award_extcredits2" value="<?=$data['setting']['torrents_award_extcredits2']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>发布种子评论奖励土豪金</th>
					<td><input type="text" id="torrents_comments_extcredits2" name="torrents_comments_extcredits2" value="<?=$data['setting']['torrents_comments_extcredits2']?>" class="input" /></td>
					<td></td>
				</tr>
				<?php elseif ($data['action']=='tracker'): ?>	
				<tr>
					<th>announce url</th>
					<td><input type="text" id="tracker_url" name="tracker_url" value="<?=$data['setting']['tracker_url']?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>announce interval</th>
					<td><input type="text" id="tracker_announce_interval" name="tracker_announce_interval" value="<?=$data['setting']['tracker_announce_interval']?>" class="input"  /></td>
					<td></td>
				</tr>
				<tr>
					<th>min interval</th>
					<td><input type="text" id="tracker_min_interval" name="tracker_min_interval" value="<?=$data['setting']['tracker_min_interval']?>" class="input" /></td>
					<td>Tracker返回状态正常时，客户端的最小连接时间间隔，uTorrent默认为60秒。</td>
				</tr>
				<tr>
					<th>min interval limit</th>
					<td><input type="text" id="tracker_min_interval_limit" name="tracker_min_interval_limit" value="<?=$data['setting']['tracker_min_interval_limit']?>" class="input" /></td>
					<td>如果客户端在此时间内多次连接Tracker则不记录上传下载等。</td>
				</tr>
				<tr>
					<th>peer清理时间</th>
					<td><input type="text" id="tracker_peer_clean_time" name="tracker_peer_clean_time" value="<?=$data['setting']['tracker_peer_clean_time']?>" class="input" /></td>
					<td>peer不活动时间，超过此时间则清理。应该大于announce interval。</td>
				</tr>
				<tr>
					<th>peer强制清理时间</th>
					<td><input type="text" id="tracker_peer_force_clean_time" name="tracker_peer_force_clean_time" value="<?=$data['setting']['tracker_peer_force_clean_time']?>" class="input" /></td>
					<td>最后活动时间超过此值则强制delete。</td>
				</tr>
				<tr>
					<th>上传速度</th>
					<td><input type="text" id="tracker_log_speed" name="tracker_log_speed" value="<?=$data['setting']['tracker_log_speed']?>" class="input" /></td>
					<td>大于该速度的则记录日志，单位M</td>
				</tr>
				<tr>
					<th style="width:160px;">可以下载未审核种子的用户名</th>
					<td><input type="text" id="tracker_download_unaudited_user" name="tracker_download_unaudited_user" value="<?=$data['setting']['tracker_download_unaudited_user']?>" class="input" /></td>
					<td>逗号隔开，区分大小写</td>
				</tr>
				<tr>
					<th>peer_id黑名单</th>
					<td>
					<textarea id="tracker_black_peer_id" name="tracker_black_peer_id" style="width:210px;height:100px;" ><?=$data['setting']['tracker_black_peer_id']?></textarea>
					</td>
					<td>一行一条记录，包含则ban</td>
				</tr>
				<tr>
					<th>agent黑名单</th>
					<td><textarea id="tracker_black_agent" name="tracker_black_agent" style="width:210px;height:100px;" ><?=$data['setting']['tracker_black_agent']?></textarea></td>
					<td>一行一条记录，包含则ban</td>
				</tr>
				<tr>
					<th>IP地址黑名单(封禁IP)</th>
					<td><textarea id="tracker_black_ips" name="tracker_black_ips" style="width:210px;height:100px;" ><?=$data['setting']['tracker_black_ips']?></textarea></td>
					<td>一行一条记录，ip范围仅支持ipv4，ipv6仅支持单个ip</td>
				</tr>
				<?php elseif ($data['action']=='admins'): ?>
				<tr>
					<th>后台设置管理员</th>
					<td><input type="text" id="admins_admins" name="admins_admins" value="<?=$data['setting']['admins_admins']?>" class="input" /></td>
					<td>能进入本后台的人员，多个用逗号分隔，区分大小写</td>
				</tr>
				<tr>
					<th>开发人员</th>
					<td><input type="text" id="admins_developer" name="admins_developer" value="<?=$data['setting']['admins_developer']?>" class="input" /></td>
					<td>开发人员可以访问调试页面，增加调试参数，多个用逗号分隔，区分大小写</td>
				</tr>
				<tr>
					<th>系统消息发信人</th>
					<td><input type="text" id="admins_deliver" name="admins_deliver" value="<?=$data['setting']['admins_deliver']?>" class="input" /></td>
					<td>使用本账号给用户发送系统消息</td>
				</tr>
				<tr>
					<th>信任ip列表</th>
					<td><input type="text" id="admins_trust_ips" name="admins_trust_ips" value="<?=$data['setting']['admins_trust_ips']?>" class="input" /></td>
					<td>比如部署cron的服务器IP，用来测试的用户ip等。默认仅包含服务器本机。</td>
				</tr>
				<?php elseif ($data['action']=='upload'): ?>
				<tr>
					<th>发布种子页面注意事项</th>
					<td><textarea id="upload_note" name="upload_note" style="width:210px;height:200px;" ><?=$data['setting']['upload_note']?></textarea></td>
					<td>支持ubb标签</td>
				</tr>
				<tr>
					<th>种子售价</th>
					<td><textarea id="torrents_price" name="torrents_price" style="width:210px;height:100px;" ><?=$data['setting']['torrents_price']?></textarea></td>
					<td>保种积分。发种人设置售价，用户下载扣分，系统扣税</td>
				</tr>
				<tr>
					<th>种子售价扣税比率</th>
					<td><input type="text" id="torrents_price_tax" name="torrents_price_tax" value="<?=$data['setting']['torrents_price_tax']?>" class="input" /></td>
					<td>% 发种人设置售价，用户下载扣分，系统扣税</td>
				</tr>
				<tr>
					<th>种子售价次数</th>
					<td><input type="text" id="torrents_price_times" name="torrents_price_times" value="<?=$data['setting']['torrents_price_times']?>" class="input" /></td>
					<td>下载次数超过比如20次之后不再扣保种积分。</td>
				</tr>
				<tr>
					<th>种子保存路径</th>
					<td><input type="text" id="torrents_save_path" name="torrents_save_path" value="<?=$data['setting']['torrents_save_path']?>" class="input" /></td>
					<td>斜杠/结尾，留空则默认在attachments/torrents 目录</td>
				</tr>
				<tr>
					<th>图片保存路径</th>
					<td><input type="text" id="images_save_path" name="images_save_path" value="<?=$data['setting']['images_save_path']?>" class="input" /></td>
					<td>斜杠/结尾，留空则默认在attachments/images 目录，另外要在webserver上配置为/attachments虚拟目录</td>
				</tr>
				<tr>
					<th>字幕保存路径</th>
					<td><input type="text" id="subtitles_save_path" name="subtitles_save_path" value="<?=$data['setting']['subtitles_save_path']?>" class="input" /></td>
					<td>斜杠/结尾，留空则默认在attachments/subtitles 目录</td>
				</tr>
				<tr>
					<th>nfo保存路径</th>
					<td><input type="text" id="nfos_save_path" name="nfos_save_path" value="<?=$data['setting']['nfos_save_path']?>" class="input" /></td>
					<td>斜杠/结尾，留空则默认在attachments/nfos 目录</td>
				</tr>
				<tr>
					<th>软件站保存路径</th>
					<td><input type="text" id="softsite_save_path" name="softsite_save_path" value="<?=$data['setting']['softsite_save_path']?>" class="input" /></td>
					<td>斜杠/结尾，留空则默认在attachments/softsite 目录</td>
				</tr>

				<tr>
					<th>图片独立域名前缀</th>
					<td><input type="text" id="images_domain" name="images_domain" value="<?=$data['setting']['images_domain']?>" class="input" /></td>
					<td>斜杠/结尾，如 http://n.cgbt.cn/attachments/images/，留空则默认与网站域名一致 </td>
				</tr>
				<tr>
					<th>种子Source</th>
					<td><input type="text" id="torrents_source" name="torrents_source" value="<?=$data['setting']['torrents_source']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>种子大小限制</th>
					<td><input type="text" id="torrents_size_limit" name="torrents_size_limit" value="<?=$data['setting']['torrents_size_limit']?>" class="input" /></td>
					<td>M</td>
				</tr>
				<tr>
					<th>图片大小限制</th>
					<td><input type="text" id="images_size_limit" name="images_size_limit" value="<?=$data['setting']['images_size_limit']?>" class="input" /></td>
					<td>M</td>
				</tr>
				<tr>
					<th>字幕大小限制</th>
					<td><input type="text" id="subtitles_size_limit" name="subtitles_size_limit" value="<?=$data['setting']['subtitles_size_limit']?>" class="input" /></td>
					<td>M</td>
				</tr>
				<tr>
					<th>nfo大小限制</th>
					<td><input type="text" id="nfos_size_limit" name="nfos_size_limit" value="<?=$data['setting']['nfos_size_limit']?>" class="input" /></td>
					<td>M</td>
				</tr>
				<tr>
					<th>软件大小限制</th>
					<td><input type="text" id="softsite_size_limit" name="softsite_size_limit" value="<?=$data['setting']['softsite_size_limit']?>" class="input" /></td>
					<td>M</td>
				</tr>
				<tr>
					<th>下载种子文件名前缀</th>
					<td><input type="text" id="download_torrents_name_prefix" name="download_torrents_name_prefix" value="<?=$data['setting']['download_torrents_name_prefix']?>" class="input" /></td>
					<td></td>
				</tr>
				<tr>
					<th>删种原因</th>
					<td><textarea id="delete_torrents_reasons" name="delete_torrents_reasons" style="width:210px;height:200px;" ><?=$data['setting']['delete_torrents_reasons']?></textarea></td>
					<td>一行一条记录</td>
				</tr>
				<tr>
					<th>审种不过原因</th>
					<td><textarea id="audit_torrents_reasons" name="audit_torrents_reasons" style="width:210px;height:200px;" ><?=$data['setting']['audit_torrents_reasons']?></textarea></td>
					<td>一行一条记录</td>
				</tr>
				<?php elseif ($data['action']=='rule'): ?>
				<tr>
					<th>全站免费</th>
					<td>
						<select name="all_free" id="all_free">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['all_free'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['all_free'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>全站2x</th>
					<td>
						<select name="all_2x" id="all_2x">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['all_2x'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['all_2x'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>热门种子保种数限制</th>
					<td>
						<input type="text" id="hot_torrents_seed_count_limit" name="hot_torrents_seed_count_limit" value="<?=$data['setting']['hot_torrents_seed_count_limit']?>" class="input" />
					</td>
					<td>保种数大于此值才能下载热门种子</td>
				</tr>
				<tr>
					<th>热门种子保种容量限制</th>
					<td>
						<input type="text" id="hot_torrents_seed_size_limit" name="hot_torrents_seed_size_limit" value="<?=$data['setting']['hot_torrents_seed_size_limit']?>" class="input" />
					</td>
					<td>G，保种容量大于此值才能下载热门种子</td>
				</tr>
				<tr>
					<th>小于xG的种子无优惠</th>
					<td>
						<input type="text" id="torrents_free_min_size" name="torrents_free_min_size" value="<?=$data['setting']['torrents_free_min_size']?>" class="input" />
					</td>
					<td>G</td>
				</tr>
				<tr>
					<th>新发布种子免费时间</th>
					<td>
						<input type="text" id="new_torrents_free_time" name="new_torrents_free_time" value="<?=$data['setting']['new_torrents_free_time']?>" class="input" />
					</td>
					<td>小时，比如1小时内免费</td>
				</tr>
				<tr>
					<th>新发布种子30%时间</th>
					<td>
						<input type="text" id="new_torrents_30p_time" name="new_torrents_30p_time" value="<?=$data['setting']['new_torrents_30p_time']?>" class="input" />
					</td>
					<td>小时，比如3小时内30%，该时间实际上会减去免费的时间</td>
				</tr>
				<tr>
					<th>新发布种子50%时间</th>
					<td>
						<input type="text" id="new_torrents_half_time" name="new_torrents_half_time" value="<?=$data['setting']['new_torrents_half_time']?>" class="input" />
					</td>
					<td>小时，比如5小时内50%，该时间实际上会减去免费和30%的时间</td>
				</tr>
				<tr>
					<th>启用共享率限制</th>
					<td>
						<select name="enable_ratio_limit" id="enable_ratio_limit">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['enable_ratio_limit'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['enable_ratio_limit'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>共享率限制</th>
					<td><textarea id="ratio_limit" name="ratio_limit" style="width:210px;height:120px;" ><?=$data['setting']['ratio_limit']?></textarea></td>
					<td>
					一行一条记录<br />
					比如：<br />
					下载流量大于50G时，共享率必须大于0.8<br />
					下载流量大于20G时，共享率必须大于0.6<br />
					下载流量大于0G时，共享率必须大于0<br />
					填写：<br />
					50:0.8<br />
					20:0.6<br />
					0:0
					</td>
				</tr>
				<tr>
					<th>启用保种数量限制</th>
					<td>
						<select name="enable_seed_count_limit" id="enable_seed_count_limit">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['enable_seed_count_limit'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['enable_seed_count_limit'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>保种数量限制</th>
					<td><textarea id="seed_count_limit" name="seed_count_limit" style="width:210px;height:120px;" ><?=$data['setting']['seed_count_limit']?></textarea></td>
					<td>
					一行一条记录<br />
					格式：保种数量,下载数量<br />
					比如：保种数量小于5时，下载数量为1<br />
					填写：5:1
					</td>
				</tr>
				<tr>
					<th>启用保种容量限制</th>
					<td>
						<select name="enable_seed_size_limit" id="enable_seed_size_limit">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['enable_seed_size_limit'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['enable_seed_size_limit'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>保种容量限制</th>
					<td><textarea id="seed_size_limit" name="seed_size_limit" style="width:210px;height:120px;" ><?=$data['setting']['seed_size_limit']?></textarea></td>
					<td>
					一行一条记录<br />
					格式：保种容量,下载数量<br />
					比如：保种容量小于5G时，下载数量为1<br />
					填写：5:1
					</td>
				</tr>
				<tr>
					<th>启用上传加倍规则</th>
					<td>
						<select name="enable_upload_factor" id="enable_upload_factor">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['enable_upload_factor'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['enable_upload_factor'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>上传加倍规则链接</th>
					<td>
						<input type="text" id="upload_factor_link" name="upload_factor_link" value="<?=$data['setting']['upload_factor_link']?>" class="input" />
					</td>
					<td></td>
				</tr>
				<?php elseif ($data['action']=='newbie'): ?>
				<tr>
					<th>启用新手考核</th>
					<td>
						<select name="newbie_enable" id="newbie_enable">
							<option value="">=请选择=</option>
							<option value="1"<?php if ($data['setting']['newbie_enable'] == '1'): ?>selected='selected'<?php endif;?>>是</option>
							<option value="0"<?php if ($data['setting']['newbie_enable'] == '0'): ?>selected='selected'<?php endif;?>>否</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>起始时间</th>
					<td>
						<input type="text" id="newbie_startdate" name="newbie_startdate" value="<?=$data['setting']['newbie_startdate']?>" class="input" />
					</td>
					<td>格式 2013-01-01，从该日期起注册的用户显示新手考核</td>
				</tr>

				<tr>
					<th>考核时间</th>
					<td>
						<input type="text" id="newbie_days" name="newbie_days" value="<?=$data['setting']['newbie_days']?>" class="input" />
					</td>
					<td>天，注册多少天以内必须满足下面条件</td>
				</tr>
				<tr>
					<th>考核条件, 上传量要求</th>
					<td>
						<input type="text" id="newbie_uploaded" name="newbie_uploaded" value="<?=$data['setting']['newbie_uploaded']?>" class="input" />
					</td>
					<td>G</td>
				</tr>
				<tr>
					<th>考核条件, 下载量要求</th>
					<td>
						<input type="text" id="newbie_downloaded" name="newbie_downloaded" value="<?=$data['setting']['newbie_downloaded']?>" class="input" />
					</td>
					<td>G</td>
				</tr>
				<tr>
					<th style="width:160px;">考核条件, 保种积分要求</th>
					<td>
						<input type="text" id="newbie_extcredits1" name="newbie_extcredits1" value="<?=$data['setting']['newbie_extcredits1']?>" class="input" />
					</td>
					<td></td>
				</tr>
				<?php endif ?>
				<tr>
					<th></th>
					<td>
					<a class="btn btn-success" id="submit" href="javascript:document.form1.submit()">保存</a>
					<input type='hidden' name='submitbtn'>
					</td>
					<td></td>
				</tr>				
			</table>
			</form>
		</div><!--form_box-->
		<div class="blank_box20"></div>
	</div><!--content-->
	<div style="clear:both"></div>
</div><!--container-->
<?php
include 'footer.php';
?>
