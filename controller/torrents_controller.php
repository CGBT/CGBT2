<?php
class torrents_controller extends base_controller
{
	private $tid, $mod;
	private $users_module, $torrents_module;
	private $torrent;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);

		$this->check_login();

		cg::load_module('users_module');
		cg::load_module('torrents_module');

		$this->users_module = new users_module();
		$this->torrents_module = new torrents_module();

		$this->get_setting();
		$this->get_category();
		foreach ($this->all_category as $key => $category)
		{
			if ($category['app'] != 'torrents')
			{
				unset($this->all_category[$key]);
			}
		}
	}

	public function index_action()
	{
		$this->tid = empty($this->params['tid']) ? 0 : intval($this->params['tid']);
		$this->mod = empty($this->params['mod']) ? '' : $this->params['mod'];

		if ($this->tid <= 0)
		{
			$this->showmessage('种子不存在');
		}

		//@todo miss descr
		$this->torrent = $this->torrents_module->get_torrent($this->tid);
		if (empty($this->torrent))
		{
			$this->showmessage('种子不存在或已被删除!');
		}
		$category_admins = $this->all_category[$this->torrent['category']]['admins'];
		$this->user['is_category_admins'] = in_array($this->username, funcs::explode($category_admins));

		if (empty($this->mod))
		{
			$this->index();
			die();
		}

		$dict_mod = array(
			'details',
			'download',
			'edit',
			'delete',
			'audit',
			'favorite',
			'unfavorite',
			'forums',
			'files',
			'leechers',
			'seeders',
			'completed_users',
			'comments',
			'post_comments',
			'subtitles',
			'versions',
			'play',
			'award',
			'award_cloud',
			'req_seed',
			'rate',
			'get_prop',
			'update_prop'
		);
		if (!in_array($this->mod, $dict_mod))
		{
			$this->redirect('/search/');
		}
		$this->{$this->mod}();
		die();
	}

	private function req_seed()
	{
		cg::load_model('logs_completed_model');
		$logs_completed_model = logs_completed_model::get_instance();
		$rows = $logs_completed_model->get_uids_by_tid($this->tid);
		if (empty($rows))
		{
			$this->showmessage('无人下载完成!');
		}
		$data = array();
		$title = $this->torrent['title'];
		$msg = "您下载过种子：$title 。\n";
		$msg .= "该种子现在无人保种，麻烦您能来补个种，感谢！";
		foreach ($rows as $key => $row)
		{
			$user = $this->users_module->get_by_uid($row['uid']);
			if (!empty($user))
			{
				$this->send_pm($this->user['forums_uid'], $user['forums_uid'], '请求补种', $msg, 0);
			}
		}

		$extcredits1 = intval($this->setting['req_seed_extcredits1']);
		$logs_fields = array(
			'count' => -1 * $extcredits1,
			'field' => 'extcredits1',
			'action' => 'req_seed'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$this->users_module->add_credits($this->uid, -1 * $extcredits1, 'extcredits1', $logs_fields);

		$this->showmessage('操作成功！');
	}

	private function versions()
	{
		$imdb = $this->torrent['imdb'];
		if (empty($imdb))
		{
			die('');
		}
		$ids = $this->torrents_module->torrents_index_model->get_ids_by_imdb($imdb);
		$this->data['torrents'] = $this->torrents_module->get_torrents($ids);
		$this->data['torrents_count'] = count($this->data['torrents']);
		$this->view()->assign('data', $this->data);
		$data = $this->view()->fetch('include_torrenttable.php');
		echo $data;
	}

	private function comments()
	{
		$page = isset($this->get['page']) ? intval($this->get['page']) : '1';
		$forums_tid = $this->torrent['forums_tid'];
		cg::load_model("forums_discuzx_model");
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$rows = $forums_discuzx_model->get_posts($forums_tid, $page);
		if ($page == '1')
		{
			unset($rows[0]);
		}
		$data = array();
		foreach ($rows as $key => $row)
		{
			$rows[$key]['createtime'] = date("Y-m-d H:i:s", $row['dateline']);
			$message = funcs::ubb2html($row['message']);

			$message = str_replace('forum.php?mod=redirect', $this->setting['forums_url'] . 'forums.php?mod=redirect', $message);
			$rows[$key]['message'] = str_replace('static/image/common/back.gif', $this->setting['forums_url'] . 'static/image/common/back.gif', $message);
		}
		echo json_encode($rows);
	}

	private function get_forums_attach($message)
	{
		//@todo 未完成
		//[attach]232275[/attach]
		preg_match_all('/\[attach\]([0-9]+)[\/attach]/i', $message, $matches);
		$aids = $matches[1];
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		foreach ($aids as $aid)
		{
			$row = $forums_discuzx_model->get_attach($aid);
			str_replace("[attach]{$aid}[/attach]", "<a href=>", $subject);
		}
	}

	private function post_comments()
	{
		$this->check_have_privileges('comment', true);
		$fid = $this->all_category[$this->torrent['category']]['forums_fid'];
		$tid = $this->torrent['forums_tid'];
		$uid = $this->user['forums_uid'];
		$username = $this->username;
		$subject = '';
		$message = isset($this->post['message']) ? trim($this->post['message']) : '';
		if (empty($message))
		{
			$this->showmessage('评论不能为空', true);
		}
		$message = htmlspecialchars($message, ENT_QUOTES);
		cg::load_model("forums_discuzx_model");
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$forums_discuzx_model->new_post($fid, $tid, $uid, $username, $subject, $message, true, 0);

		if ($this->uid != $this->torrent['uid'])
		{
			$note = "<a href='{$this->site_url}user/{$this->uid}/' target='_blank'>$username</a> 评价了您的种子
			<a target='_blank' href='{$this->site_url}torrents/{$this->torrent['id']}/'>{$this->torrent['title']}</a>
			<a target='_blank' href='{$this->site_url}torrents/{$this->torrent['id']}/'>点击查看</a>";
			$to_user = $this->users_module->get_by_uid($this->torrent['uid']);
			$this->send_note($this->user, $to_user['forums_uid'], $note);
		}
		$note = "<a href='{$this->site_url}user/{$this->uid}/' target='_blank'>$username</a> 在种子评价中提到了您  <a target='_blank' href='{$this->site_url}torrents/{$this->torrent['id']}/'>点击查看</a>";
		$matches = array();
		preg_match_all('/@([a-z][a-z0-9_]+)/i', $message, $matches);
		$i = 0;
		foreach ($matches[1] as $username)
		{
			if ($i < 10)
			{
				$to_user = $this->users_module->get_by_username($username);
				if (!empty($to_user))
				{
					$this->send_note($this->user, $to_user['forums_uid'], $note);
					$i++;
				}
			}
		}

		//奖励1个土豪金
		$torrents_comments_extcredits2 = intval($this->setting['torrents_comments_extcredits2']);
		$logs_fields = array(
			'count' => $torrents_comments_extcredits2,
			'field' => 'extcredits2',
			'action' => 'post_comments'
		);
		$logs_fields = array_merge($this->logs_credits_fields, $logs_fields);
		$this->users_module->add_credits($this->uid, $torrents_comments_extcredits2, 'extcredits2', $logs_fields);

		$this->showmessage('发布成功', false);
	}

	private function forums()
	{
		$this->log_browse();
		$url = $this->torrent['forums_url'];
		if (stripos($this->ip, ':') !== false)
		{
			$url = str_replace('http://', 'http://ipv6.', $url);
		}
		$this->redirect($url);
	}

	private function log_browse()
	{
		$arr_fields = array(
			'tid' => $this->tid,
			'uid' => $this->uid,
			'createtime' => $this->timestamp
		);
		cg::load_model('logs_browse_model');
		$logs_browse_model = logs_browse_model::get_instance();
		$logs_browse_model->insert($arr_fields);

		$count = $logs_browse_model->get_browse_count_by_uid($this->uid);
		if ($count > 50)
		{
			$this->showmessage('您访问的页面过多，系统怀疑你是机器人！');
		}
	}

	private function index()
	{
		$this->details();
		die();
		$url = $this->torrent['forums_url'];
		if (stripos($this->ip, ':') !== false)
		{
			$url = str_replace('http://', 'http://ipv6.', $url);
		}
		$this->redirect($url);
	}

	private function subtitles()
	{
		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$rows = $torrents_attachments_model->get_by_tid($this->tid);
		$data = array();
		foreach ($rows as $key => $row)
		{
			$data[$key]['id'] = $row['id'];
			$data[$key]['download'] = $row['download'];
			$data[$key]['username'] = $row['username'];
			$data[$key]['old_name'] = $row['old_name'];
			$data[$key]['createtime_text'] = date("Y-m-d H:i:s", $row['createtime']);
		}
		die(json_encode($data));
	}

	private function award()
	{
		$count = isset($this->post['count']) ? intval($this->post['count']) : 0;
		if ($count <= 0)
		{
			$this->showmessage('奖励积分错误');
		}
		$dict_award = funcs::explode($this->setting['torrents_award']);
		if (!in_array($count, $dict_award))
		{
			$this->showmessage('奖励积分错误');
		}
		if ($this->uid == $this->torrent['uid'])
		{
			$this->showmessage('不能自己给自己奖励!');
		}
		cg::load_model('torrents_award_model');
		$torrents_award_model = torrents_award_model::get_instance();
		$awarded = $torrents_award_model->check_awarded($this->uid, $this->tid);
		if ($awarded > 0)
		{
			$this->showmessage('您已经奖励过了，不能重复奖励!');
		}

		$arr_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'count' => $count,
			'user_title' => $this->user['title'],
			'createtime' => $this->timestamp,
			'tid' => $this->tid
		);
		$torrents_award_model->insert_award($arr_fields);

		$logs_fields = array(
			'count' => -1 * $count,
			'field' => 'extcredits1',
			'action' => 'torrnets_award'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$this->users_module->add_credits($this->uid, -1 * $count, 'extcredits1', $logs_fields);

		$logs_fields = array(
			'count' => $count,
			'field' => 'extcredits1',
			'action' => 'torrnets_award',
			'uid' => $this->torrent['uid'],
			'username' => $this->torrent['username']
		);
		$logs_fields = array_merge($this->logs_credits_fields, $logs_fields);
		$this->users_module->add_credits($this->torrent['uid'], $count, 'extcredits1', $logs_fields);

		$msg = "感谢您奖励种子  $count 保种积分。 \n种子名称为 {$this->torrent['title']} ";
		$this->send_pm('', $this->username, '', $msg);

		$msg = "您的种子被 $this->username 奖励  $count 保种积分。 \n种子名称为 {$this->torrent['title']} ";
		$this->send_pm('', $this->torrent['username'], '', $msg);

		//奖励他人积分的同时，给自己奖励2个土豪金
		$torrents_award_extcredits2 = intval($this->setting['torrents_award_extcredits2']);
		$logs_fields = array(
			'count' => $torrents_award_extcredits2,
			'field' => 'extcredits2',
			'action' => 'torrnets_award'
		);
		$logs_fields = array_merge($this->logs_credits_fields, $logs_fields);
		$this->users_module->add_credits($this->uid, $torrents_award_extcredits2, 'extcredits2', $logs_fields);

		$this->showmessage('操作成功');
	}

	private function award_cloud()
	{
		cg::load_model('torrents_award_model');
		$torrents_award_model = torrents_award_model::get_instance();
		$rows = $torrents_award_model->get_award_by_tid($this->tid);

		$fontsize_min = 14;
		$fontsize_max = 36;
		$color = array(
			"#FF0000",
			"#00CCFF",
			"#FF9900",
			"#0099FF",
			"#999999"
		);
		$count_max = 0;
		foreach ($rows as $row)
		{
			$count_max = max($count_max, $row['count']);
		}
		$count_min = $count_max;
		foreach ($rows as $row)
		{
			$count_min = min($count_min, $row['count']);
		}
		foreach ($rows as $key => $row)
		{
			$rows[$key]['extcredits1'] = $row['count'];
			$rows[$key]['count'] = $row['count'] - $count_min;
		}
		$period = $count_max - $count_min;
		$data = array();
		foreach ($rows as $key => $row)
		{
			if ($period > 0)
			{
				$data[$key]["fontsize"] = round($fontsize_min + $row['count'] * ($fontsize_max - $fontsize_min) / $period, 1);
			}
			else
			{
				$data[$key]["fontsize"] = $fontsize_min;
			}
			$data[$key]["color"] = $color[array_rand($color)];
			if (empty($row['user_title']))
			{
				$data[$key]['user_title'] = $row['username'];
			}
			else
			{
				$data[$key]['user_title'] = $row['user_title'];
			}
			$data[$key]['uid'] = $row['uid'];
			$data[$key]['count'] = $row['extcredits1'];
			$data[$key]['username'] = $row['username'];
		}
		shuffle($data);
		die(json_encode($data));
	}

	private function details()
	{
		$this->log_browse();
		$this->data['action'] = 'details'; //删除种子，页面跳转的判断
		$this->data['title'] = $this->torrent['title'] . '-种子下载-';
		$this->data['guid'] = funcs::guid();

		if ($this->torrent['isfree'] || $this->torrent['auto_isfree'])
		{
			$this->torrent['stamps'] = $this->torrent['istop'] ? 'topfree.gif' : ($this->torrent['isrecommend'] ? 'recfree.gif' : 'free.gif');
		}
		else
		{
			$this->torrent['stamps'] = $this->torrent['istop'] ? 'top.gif' : ($this->torrent['isrecommend'] ? 'rec.gif' : '');
		}

		$this->data['current_torrent'] = $this->torrent;
		$this->data['current_torrent']['descr'] = $this->torrents_module->get_descr($this->tid);
		$this->data['current_torrent']['descr']['descr'] = funcs::ubb2html($this->data['current_torrent']['descr']['descr']);
		$this->data['torrent_user'] = $this->users_module->get_by_uid($this->torrent['uid']);

		cg::load_model('torrents_award_model');
		$torrents_award_model = torrents_award_model::get_instance();
		$this->data['sum_award'] = $torrents_award_model->sum_award($this->tid);

		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$rows_subtitles = $torrents_attachments_model->get_by_tid($this->tid);
		$this->data['subtitles_count'] = count($rows_subtitles);
		$this->update_view_times();
		$this->get_favorite_status();
		$this->template_file = 'details.php';
		$this->show();
	}

	private function completed_users()
	{
		cg::load_model('logs_completed_model');
		$logs_completed_model = logs_completed_model::get_instance();
		$rows = $logs_completed_model->get_uids_by_tid($this->tid);
		$data = array();
		foreach ($rows as $key => $row)
		{
			$data[$key]['createtime_text'] = date("Y-m-d H:i:s", $row['createtime']);
			$user = $this->users_module->get_by_uid($row['uid']);
			if (!empty($user)) //@todo
			{
				$data[$key]['uid'] = $user['uid'];
				$data[$key]['username'] = $user['username'];
				$data[$key]['group_name'] = $user['group_name'];
			}
			else
			{
				$data[$key]['uid'] = '0';
				$data[$key]['username'] = '';
				$data[$key]['group_name'] = '';
			}
		}
		echo json_encode($data);
	}

	private function files()
	{
		cg::load_model('files_model');
		$files_model = files_model::get_instance();
		$files = $files_model->get_files_by_torrent($this->tid, 0, 500);
		$data = array();
		foreach ($files as $key => $file)
		{
			$data[$key]['size_text'] = funcs::mksize($file['size']);
			$data[$key]['filename'] = $file['filename'];
		}
		echo json_encode($data);
	}

	private function leechers()
	{
		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$peers_ids = $peers_model->get_ids_by_torrent($this->tid);
		$peers_rows = $peers_model->get_peers_by_ids($peers_ids);
		$data = array();
		foreach ($peers_rows as $key => $row)
		{
			if (empty($row) || $row['is_seeder'])
			{
				continue;
			}
			$data[] = $this->convert_peer_data($row);
		}
		echo json_encode($data);
	}

	private function seeders()
	{
		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$peers_ids = $peers_model->get_ids_by_torrent($this->tid);
		$peers_rows = $peers_model->get_peers_by_ids($peers_ids);
		$data = array();
		foreach ($peers_rows as $key => $row)
		{
			if (empty($row) || !$row['is_seeder'])
			{
				continue;
			}
			$data[] = $this->convert_peer_data($row);
		}
		echo json_encode($data);
	}

	private function convert_peer_data($row)
	{
		$data = array();
		$user = $this->users_module->get_by_uid($row['uid']);
		if ($this->torrent['anonymous'] && $row['uid'] == $this->torrent['uid'])
		{
			$data['uid'] = 0;
			$data['username'] = '匿名';
			$data['group_name'] = '匿名';
		}
		else
		{
			$data['uid'] = $user['uid'];
			$data['username'] = $user['username'];
			$data['group_name'] = $user['group_name'];
		}
		if ($this->user['is_admin'] || $this->uid == $row['uid'])
		{
			$data['ip'] = $row['ip'];
			$data['ipv6'] = $row['ipv6'];
		}
		else
		{
			$data['ip'] = '';
			$data['ipv6'] = '';
		}
		$data['uploaded_text'] = funcs::mksize($row['uploaded']);
		$data['downloaded_text'] = funcs::mksize($row['downloaded']);
		$endtime = $row['completed_time'] > 0 ? $row['completed_time'] : $row['last_action'];
		if ($row['last_action'] - $row['createtime'] != 0)
		{
			$data['upload_speed'] = funcs::mksize($row['uploaded'] / ($row['last_action'] - $row['createtime'])) . '/s';
		}
		else
		{
			$data['upload_speed'] = 0;
		}
		if ($endtime - $row['createtime'] != 0)
		{
			$data['download_speed'] = funcs::mksize($row['downloaded'] / ($endtime - $row['createtime'])) . '/s';
		}
		else
		{
			$data['download_speed'] = 0;
		}
		$data['last_action_text'] = date("Y-m-d H:i:s", $row['last_action']);
		$data['ratio'] = $row['downloaded'] == '0' ? '0' : $row['uploaded'] / $row['downloaded'];
		$data['finished'] = sprintf('%.1f%%', 100 * ($row['size'] - $row['left']) / $row['size']);
		$data['createtime_text'] = date("Y-m-d H:i:s", $row['createtime']);
		$data['completed_time_text'] = $row['completed_time'] == '0' ? '' : date("Y-m-d H:i:s", $row['completed_time']);
		$data['port'] = $row['port'];
		$data['agent'] = $row['agent'];
		$data['last_event'] = $row['last_event'];
		return $data;
	}

	private function get_favorite_status()
	{
		cg::load_model('favorite_model');
		$favorite_model = favorite_model::get_instance();
		$this->data['favorite_status'] = $favorite_model->get_favorite($this->tid, $this->uid);
	}

	private function favorite()
	{
		$this->get_favorite_status();
		$favorite_model = favorite_model::get_instance();
		if (empty($this->data['favorite_status']))
		{
			if (isset($this->user['privileges']['favorite_count']))
			{
				$favorite_count = $favorite_model->get_favorite_count($this->uid);
				if ($favorite_count > $this->user['privileges']['favorite_count'])
				{
					$msg = "你收藏的种子数过多，你所在的用户组只能收藏  {$this->user['privileges']['favorite_count']} 个种子.";
					die($msg);
				}
			}
			$arr_fields = array(
				'tid' => $this->tid,
				'uid' => $this->uid
			);
			$favorite_model->insert($arr_fields);
			die('收藏成功!');
		}
		else
		{
			$favorite_model->delete($this->data['favorite_status']['id']);
			die('取消收藏成功!');
		}
	}

	private function audit()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('您没有权限审核本种子', true);
		}
		$status = $this->post['audit_status'] == "1" ? '1' : '0';

		if ($status == '0')
		{
			if (empty($this->post['reason']))
			{
				$this->showmessage('请填写原因', true);
			}
			$this->post['reason'] = htmlspecialchars($this->post['reason'], ENT_QUOTES);
			$subject = '您发布的种子没有通过审核';
			$message = "您发布的种子没有通过审核: {$this->torrent['title']} \n";
			$message .= "原因：{$this->post['reason']} 。\n";
			$message .= "如果因介绍不完整等原因未通过审核，请到种子审核区，重新修改种子，管理员会再次审核，审核通过后你的种子才会出现在种子列表中。\n";
			$message .= "如果是重复或其他情况的违规种子或审核不通过后不再修改，请到种子审核区自行删除种子，感谢合作\n";
		}
		else
		{
			if (empty($this->torrent['seeder']))
			{
				$this->showmessage('无人做种，不能审核通过', true);
			}
			$this->post['reason'] = '';
			$subject = '您发布的种子已被审核通过';
			$message = "您发布的种子已被审核通过: {$this->torrent['title']} \n";
			$message .= "请至少保种一周，有多人下载完成之后你才能停止做种！做种时间越长保种积分越多，在硬盘空间允许的情况下尽量多保种。";
		}

		//从修改页面来的，页面跳转，列表页和审核页，ajax不跳转
		if (isset($this->post['edit_page']))
		{
			$this->show_no_error_ajax_message();
		}
		//if ($this->torrent['status'] != $status)
		{
			$arr_fields = array();
			$arr_fields['status'] = $status;
			$arr_fields['audit_note'] = $this->post['reason'];
			$this->torrents_module->update_torrent($arr_fields, $this->tid);
			$this->log_audit($this->tid, $this->post['reason'], $status, $this->torrent['title']);
		}

		//发消息
		$from_username = $this->setting['admins_deliver'];
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$from_user = $users_module->get_by_username($from_username);
		if (!empty($from_user))
		{
			$from_uid = $from_user['forums_uid'];
			$msgto = $this->torrent['username'];
			$isusername = 1;
			cg::load_model('forums_discuzx_model');
			$forums_discuzx_model = forums_discuzx_model::get_instance();
			$forums_discuzx_model->pm_send($from_uid, $msgto, $subject, $message, $isusername);
		}

		$this->showmessage('操作成功');
	}

	private function log_audit($tid, $reason, $status, $torrent_title)
	{
		$details = array();
		$details['tid'] = $this->tid;
		$details['reason'] = $reason;
		$details['torrent_title'] = $torrent_title;

		$arr_fields = array();
		$arr_fields['uid'] = $this->uid;
		$arr_fields['username'] = $this->username;
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['is_moderator'] = $this->user['is_moderator'];
		$arr_fields['is_admin'] = $this->user['is_admin'];
		$arr_fields['tid'] = $tid;

		if ($status == '1')
		{
			$arr_fields['action'] = 'audit_pass';
		}
		else
		{
			$arr_fields['action'] = 'audit_nopass';
		}
		$arr_fields['details'] = json_encode($details);

		cg::load_model('logs_actions_model');
		$logs_actions_model = new logs_actions_model();
		$logs_actions_model->insert($arr_fields);
	}

	private function delete()
	{
		if (empty($this->post['reason']))
		{
			$this->showmessage('请填写删种原因', true);
		}

		if ($this->torrent['uid'] != $this->uid)
		{
			if (!$this->user['is_admin'] && !$this->user['is_moderator'])
			{
				$this->showmessage('您没有权限删除本种子', true);
			}
			else
			{
				if ($this->user['is_moderator'] && !$this->user['is_category_admins'])
				{
					$this->showmessage('您不是本分类的管理员，没有权限删除本种子', true);
				}
				if ($this->user['is_moderator'] && $this->user['groupid'] != 28)
				{
					$this->showmessage('请联系版主删除，或将种子打回审核区', true);
				}
			}
		}
		else
		{
			if ($this->torrent['istop'] || $this->torrent['isrecommend'])
			{
				$this->showmessage('置顶和推荐的种子请联系版主删除', true);
			}
			if ($this->torrent['createtime'] < $this->timestamp - 3 * 86400)
			{
				$this->showmessage('发布时间超过3天的种子请联系版主删除', true);
			}
		}
		//从修改页面来的，页面跳转，列表页和审核页，ajax不跳转
		if (isset($this->post['edit_page']))
		{
			$this->show_no_error_ajax_message();
		}

		$this->log_delete($this->tid, $this->post['reason'], $this->torrent['title']);

		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$forums_discuzx_model->delete_thread($this->torrent['forums_tid']);
		$this->torrents_module->delete_torrent($this->tid, $this->torrent['info_hash']);

		$from_forums_uid = 0;
		$from_username = $this->setting['admins_deliver'];
		cg::load_module('users_module');
		$users_module = new users_module();
		$from_user = $users_module->get_by_username($from_username);
		if (!empty($from_user))
		{
			$from_forums_uid = $from_user['forums_uid'];
		}

		$delete_uploaded = isset($this->post['uploaded']) ? intval($this->post['uploaded']) : 0;
		if ($delete_uploaded > 0)
		{
			$logs_fields = array(
				'uid' => $this->torrent['uid'],
				'username' => $this->torrent['username'],
				'createtime' => $this->timestamp,
				'count' => (-1) * $delete_uploaded,
				'field' => 'uploaded',
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'action' => 'delete_torrent'
			);
			$this->users_module->add_credits($this->torrent['uid'], $delete_uploaded, 'uploaded', $logs_fields);
		}

		//给发种人发消息
		$this->post['reason'] = htmlspecialchars($this->post['reason'], ENT_QUOTES);
		$subject = '您发布的种子被删除';
		$message = "您发布的种子被删除： {$this->torrent['title']} \n";
		$message .= "删种原因：{$this->post['reason']} \n";
		if ($delete_uploaded > 0)
		{
			$message .= "扣除您的上传流量：$delete_uploaded G。\n";
		}
		$message .= "如果是违规种子，请勿再次发布。请仔细阅读发种相关规则。\n";
		$message .= "如果您对本次删种操作有所疑议，请到论坛发帖说明。谢谢合作。\n";
		$this->send_pm($from_forums_uid, $this->torrent['username'], $subject, $message);

		//给保种人发消息
		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$peers_ids = $peers_model->get_ids_by_torrent($this->tid);
		$peers_rows = $peers_model->get_peers_by_ids($peers_ids);
		$message = "您正在保种或下载种子被删除： {$this->torrent['title']} \n";
		$message .= "删种原因：{$this->post['reason']} \n";

		foreach ($peers_rows as $row)
		{
			if (empty($row) || empty($row['username']))
			{
				continue;
			}
			$this->send_pm($from_forums_uid, $row['username'], '', $message);
		}

		$this->showmessage('删除成功');
	}

	public function log_delete($tid, $reason, $torrent_title)
	{
		$details = array();
		$details['tid'] = $tid;
		$details['reason'] = $reason;
		$details['torrent_title'] = $torrent_title;

		$arr_fields = array();
		$arr_fields['uid'] = $this->uid;
		$arr_fields['username'] = $this->username;
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['is_moderator'] = $this->user['is_moderator'];
		$arr_fields['is_admin'] = $this->user['is_admin'];
		$arr_fields['tid'] = $tid;
		$arr_fields['action'] = 'delete_torrent';
		$arr_fields['details'] = json_encode($details);

		cg::load_model('logs_actions_model');
		$logs_actions_model = new logs_actions_model();
		$logs_actions_model->insert($arr_fields);
	}

	private function play()
	{
		$this->data['title'] = '正在播放-' . $this->torrent['title'] . '-';
		$this->data['current_torrent'] = $this->torrent;
		$this->show('play.php');
	}

	private function edit()
	{
		if ($this->torrent['uid'] != $this->uid)
		{
			if (!$this->user['is_admin'] && !$this->user['is_moderator'])
			{
				$this->showmessage('您没有权限修改本种子', true);
			}
			else
			{
				if ($this->user['is_moderator'] && !$this->user['is_category_admins'])
				{
					$this->showmessage('您不是本分类的管理员，没有权限修改本种子', true);
				}
			}
		}
		$this->data['title'] = '修改种子-';
		$this->data['action'] = 'edit';
		$this->data['all_category'] = $this->all_category;
		$this->data['tid'] = $this->tid;
		$this->data['current_torrent'] = $this->torrent;
		$this->data['current_torrent']['descr'] = $this->torrents_module->get_descr($this->tid);
		$this->data['current_torrent']['descr']['descr'] = str_replace("　", "  ", $this->data['current_torrent']['descr']['descr']);
		$this->data['current_category'] = $this->all_category[$this->torrent['category']];

		$this->data['in_title_field'] = array();
		foreach ($this->data['current_category']['options'] as $option)
		{
			if ($option['intitle'])
			{
				$this->data['in_title_field'][$option['variable']] = $option['type'];
			}
		}

		if (isset($this->get['c']) && isset($this->all_category[$this->get['c']]))
		{
			$this->data['current_category'] = $this->all_category[$this->get['c']];
		}

		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$this->data['attachments'] = $torrents_attachments_model->get_by_tid($this->tid);

		$this->data['guid'] = funcs::guid();
		$this->template_file = 'upload.php';
		$this->show();
	}

	private function rate()
	{
		$type = isset($this->post['type']) ? $this->post['type'] : 'against';
		if ($type != 'support')
		{
			$type = 'against';
		}
		$arr_fields = array(
			'tid' => $this->tid,
			'uid' => $this->uid,
			'type' => $type,
			'createtime' => $this->timestamp
		);
		cg::load_model('logs_rate_model');
		$logs_rate_model = logs_rate_model::get_instance();

		if ($logs_rate_model->exists_uid_tid($this->tid, $this->uid))
		{
			$this->showmessage('您已经参与过了，不能重复提交');
		}
		$logs_rate_model->insert($arr_fields);
		if ($type == 'support')
		{
			$arr_fields = array(
				'support' => $this->torrent['support'] + 1
			);
			if ($arr_fields['support'] - $this->torrent['against'] > 10)
			{
				if ($this->torrent['seeder'] > 0)
				{
					$arr_fields2['status'] = 1;
					$this->torrents_module->update_torrent($arr_fields2, $this->tid);
				}
				else
				{
					$this->showmessage('该种子无人做种，不能通过审核');
				}
			}
		}
		else
		{
			$arr_fields = array(
				'against' => $this->torrent['against'] + 1
			);
		}
		$this->torrents_module->torrents_stat_model->update($arr_fields, $this->tid);

		//奖励1个土豪金
		$torrents_rate_extcredits2 = intval($this->setting['torrents_rate_extcredits2']);
		$logs_fields = array(
			'count' => $torrents_rate_extcredits2,
			'field' => 'extcredits2',
			'action' => 'torrents_rate'
		);
		$logs_fields = array_merge($this->logs_credits_fields, $logs_fields);
		$this->users_module->add_credits($this->uid, $torrents_rate_extcredits2, 'extcredits2', $logs_fields);

		$this->showmessage('感谢参与！');
	}

	private function download()
	{
		ini_set('display_errors', '0');
		if (empty($this->torrent))
		{
			$this->showmessage('操作失败，种子不存在');
		}
		if ($this->torrent['status'] == -2)
		{
			$this->showmessage('操作失败，种子已被删除');
		}
		if ($this->torrent['uid'] != $this->uid && $this->user['is_user'])
		{
			if ($this->torrent['status'] <= 0)
			{
				$this->check_have_privileges('download_unaudit', true);
			}

			if ($this->torrent["istop"] || $this->torrent["isrecommend"] || $this->torrent["isfree"] || $this->torrent['auto_isfree'])
			{
				$this->check_have_privileges('download_top', true);
			}
			else
			{
				$this->check_have_privileges('download', true);
				$this->check_ratio_limit();
				if (isset($this->get['ext']))
				{
					$this->check_extcredits1();
				}
				else
				{
					$this->check_seed_count();
					$this->check_price();
				}
			}
		}
		$this->write_download_log();
		if (isset($this->user['total_download_times']))
		{
			$arr_fields = array(
				'total_download_times' => $this->user['total_download_times'] + 1
			);
			$this->users_module->users_stat_model->update($arr_fields, $this->uid);
		}

		if (empty($this->setting['torrents_save_path']))
		{
			$real_filename = cg::config()->APP_PATH . 'attachments/torrents/' . $this->torrent['filename'];
		}
		else
		{
			$real_filename = $this->setting['torrents_save_path'] . '/' . $this->torrent['filename'];
		}

		$announce = str_replace('{$passkey}', $this->user['passkey'], $this->setting['tracker_url']);
		cg::load_class('cg_bcode');
		$dict = bdecode(file_get_contents($real_filename));

		$newdict = array();
		$newdict['announce'] = $announce;
		$newdict['info'] = $dict['info'];
		unset($dict);

		$save_as = $newdict['info']['name'];
		if (empty($save_as))
		{
			$save_as = $this->tid;
		}
		$save_as = $this->setting['download_torrents_name_prefix'] . $save_as;
		if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		{
			$save_as = urlencode($save_as);
		}
		$save_as = str_replace("+", " ", $save_as);

		ob_end_clean();
		header("Content-Disposition: attachment; filename=\"$save_as.torrent\"");
		header("Content-Type: application/x-bittorrent");
		//$filesize = filesize($real_filename);
		//header('Content-Length: ' . $filesize);
		echo bencode($newdict);
	}

	private function check_extcredits1()
	{
		if (!isset($this->get['ext']))
		{
			return;
		}
		$download_need_extcredits1 = intval($this->setting['download_need_extcredits1']);
		if ($download_need_extcredits1 < 0)
		{
			$download_need_extcredits1 = 200;
		}
		if ($this->user['extcredits1'] > $download_need_extcredits1)
		{
			$logs_fields = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'createtime' => $this->timestamp,
				'count' => -1 * $download_need_extcredits1,
				'field' => 'extcredits1',
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'action' => 'extcretids12download'
			);
			$this->users_module->add_credits($this->uid, -1 * $download_need_extcredits1, 'extcredits1', $logs_fields);
		}
		else
		{
			$this->showmessage('您没有足够的保种积分，因此不能下载。');
		}
	}

	private function check_price()
	{
		if ($this->torrent['download'] >= $this->setting['torrents_price_times'] || $this->torrent['status'] <= 0)
		{
			return;
		}
		$price = $this->torrent['price'];
		if ($this->user['extcredits1'] < $price)
		{
			$this->showmessage("发种人设置该种子售价为 $price 保种积分，您的保种积分不足，不能下载", true);
		}
		else
		{
			if ($price < 0)
			{
				$price = -1 * $price;
			}
			$logs_fields = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'createtime' => $this->timestamp,
				'count' => -1 * $price,
				'field' => 'extcredits1',
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'action' => 'download2extcredits1'
			);
			$this->users_module->add_credits($this->uid, -1 * $price, 'extcredits1', $logs_fields);
			$price = $price - $price * $this->setting['torrents_price_tax'] / 100;
			$logs_fields = array(
				'uid' => $this->torrent['uid'],
				'username' => $this->torrent['username'],
				'createtime' => $this->timestamp,
				'count' => $price,
				'field' => 'extcredits1',
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'action' => 'extcredits12download'
			);
			$this->users_module->add_credits($this->torrent['uid'], $price, 'extcredits1', $logs_fields);
		}
	}

	private function create_torrent_info($torrent_file, $announce)
	{
	}

	private function update_view_times()
	{
		$arr_fields = array();
		$arr_fields['view'] = $this->torrent['view'] + 1;
		$this->torrents_module->torrents_stat_model->update($arr_fields, $this->tid);
	}

	private function get_prop()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('没有权限');
		}
		$arr_fields = array();
		$arr_fields['iscollection'] = $this->torrent['iscollection'];
		$arr_fields['istop'] = $this->torrent['istop'];
		$arr_fields['is0day'] = $this->torrent['is0day'];
		$arr_fields['isfree'] = $this->torrent['isfree'];
		$arr_fields['isrecommend'] = $this->torrent['isrecommend'];
		$arr_fields['is2x'] = $this->torrent['is2x'];
		$arr_fields['is30p'] = $this->torrent['is30p'];
		$arr_fields['ishalf'] = $this->torrent['ishalf'];
		$arr_fields['ishot'] = $this->torrent['ishot'];
		$arr_fields['isft'] = $this->torrent['isft'];
		echo json_encode($arr_fields);
	}

	private function update_prop()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('没有权限');
		}
		$arr_fields = array();
		$arr_fields['iscollection'] = isset($this->post['iscollection']) ? intval($this->post['iscollection']) : '0';
		$arr_fields['istop'] = isset($this->post['istop']) ? intval($this->post['istop']) : '0';
		$arr_fields['is0day'] = isset($this->post['is0day']) ? intval($this->post['is0day']) : '0';
		$arr_fields['isfree'] = isset($this->post['isfree']) ? intval($this->post['isfree']) : '0';
		$arr_fields['isrecommend'] = isset($this->post['isrecommend']) ? intval($this->post['isrecommend']) : '0';
		$arr_fields['is2x'] = isset($this->post['is2x']) ? intval($this->post['is2x']) : '0';
		$arr_fields['is30p'] = isset($this->post['is30p']) ? intval($this->post['is30p']) : '0';
		$arr_fields['ishalf'] = isset($this->post['ishalf']) ? intval($this->post['ishalf']) : '0';
		$arr_fields['ishot'] = isset($this->post['ishot']) ? intval($this->post['ishot']) : '0';
		$arr_fields['isft'] = isset($this->post['isft']) ? intval($this->post['isft']) : '0';
		$this->torrents_module->update_torrent($arr_fields, $this->tid);
		$this->showmessage('ok');
	}

	private function write_download_log()
	{
		cg::load_model('logs_download_model');
		$logs_download_model = logs_download_model::get_instance();
		$arr_fields = array(
			'tid' => $this->tid,
			'uid' => $this->uid,
			'createtime' => $this->timestamp
		);
		$logs_download_model->insert($arr_fields);

		$arr_fields = array(
			'download' => $this->torrent['download'] + 1
		);
		$this->torrents_module->torrents_stat_model->update($arr_fields, $this->tid);
	}

	/**
	 * 工具箱里面也有
	 */
	private function check_ratio_limit()
	{
		if (!$this->setting['enable_ratio_limit'])
		{
			return;
		}
		$downloaded = $this->user['downloaded'] - $this->user['downloaded2'];
		$G = 1024 * 1024 * 1024;
		if ($downloaded < 20 * $G)
		{
			return;
		}
		$dict_ratio_limit = array();
		$rows = funcs::explode($this->setting['ratio_limit']);
		foreach ($rows as $row)
		{
			list($key, $limit) = funcs::explode($row, ':');
			$dict_ratio_limit[$key] = $limit;
		}
		$ratio = $this->user['ratio'];
		$ratio_limit = 0;
		foreach ($dict_ratio_limit as $key => $value)
		{
			if ($downloaded > $key * $G)
			{
				$ratio_limit = $value;
				break;
			}
		}

		if ($ratio < $ratio_limit)
		{
			$msg = "你的共享率过低，因此你的帐号已被限制下载，不能下载这个种子。<br />
					你只能下载自己发布的种子，或者下载置顶、推荐、免费的种子并保种，提高上传流量及共享率，达到要求后系统会自动解除限制。<br />
					<a href='/user/group/'>点击此处查看共享率标准</a>";
			$this->showmessage($msg, true);
		}
	}

	private function check_seed_count()
	{
		if (isset($this->get['ext']))
		{
			return;
		}
		if (!$this->setting['enable_seed_count_limit'] && !$this->setting['enable_seed_size_limit'])
		{
			return;
		}

		$download_need_extcredits1 = intval($this->setting['download_need_extcredits1']);
		if ($download_need_extcredits1 < 0)
		{
			$download_need_extcredits1 = 200;
		}

		cg::load_model('peers_model');
		$peers_model = new peers_model();
		$seed_count = $peers_model->get_seed_count_by_user($this->uid);
		$seed_size = $peers_model->get_seed_size_by_user($this->uid);
		$G = 1024 * 1024 * 1024;
		$seed_size_text = funcs::mksize($seed_size);
		if ($this->torrent['ishot'])
		{
			$msg = "本种子被设定为热门种子，保种数量大于 {$this->setting['hot_torrents_seed_count_limit']} 并且
			保种容量大于 {$this->setting['hot_torrents_seed_size_limit']} G才能下载。<br />
			您的优特(uTorrent)里面当前保种数量为 $seed_count,保种容量为 $seed_size_text , 因此您不能下载本种子，请提高您的保种数量和保种容量。";
			if ($seed_count < $this->setting['hot_torrents_seed_count_limit'] || $seed_size < $this->setting['hot_torrents_seed_size_limit'] * $G)
			{
				$this->showmessage($msg);
			}
		}

		$dict_count_limit = array();
		$dict_size_limit = array();

		if ($this->setting['enable_seed_count_limit'])
		{
			$rows = funcs::explode($this->setting['seed_count_limit']);
			foreach ($rows as $row)
			{
				list($key, $limit) = funcs::explode($row, ':');
				$dict_count_limit[$key] = $limit;
			}
		}

		if ($this->setting['enable_seed_size_limit'])
		{
			$rows = funcs::explode($this->setting['seed_size_limit']);
			foreach ($rows as $row)
			{
				list($key, $limit) = funcs::explode($row, ':');
				$dict_size_limit[$key] = $limit;
			}
		}
		$download_limit1 = 0;
		$download_limit2 = 0;
		$download_limit = 0;
		if (!empty($dict_count_limit))
		{
			foreach ($dict_count_limit as $key => $limit)
			{
				if ($seed_count < $key)
				{
					$download_limit1 = $limit;
					break;
				}
			}
		}
		if (!empty($dict_size_limit))
		{
			foreach ($dict_size_limit as $key => $limit)
			{
				if ($seed_size < $key * $G)
				{
					$download_limit2 = $limit;
					break;
				}
			}
		}
		if ($download_limit1 > 0 && $download_limit2 > 0)
		{
			$download_limit = min($download_limit1, $download_limit2);
		}
		elseif ($download_limit1 == 0)
		{
			$download_limit = $download_limit2;
		}
		elseif ($download_limit2 == 0)
		{
			$download_limit = $download_limit1;
		}
		else
		{
			$download_limit = 0;
		}
		$seed_size = sprintf("%.2f", $seed_size / $G);
		cg::load_model('logs_download_model');
		$logs_download_model = logs_download_model::get_instance();
		$download_tids = $logs_download_model->get_download_tids_by_uid($this->uid);
		if (in_array($this->tid, $download_tids))
		{
			return;
		}
		$download_count = count($download_tids);
		if ($download_count >= $download_limit)
		{
			$msg = "您的优特(uTorrent)里面当前种保种数量为 $seed_count ，保种容量为 $seed_size G ，因此您的<b>今日下载额度</b>为 $download_limit 。<br />
			您今天已经下载过 $download_count 个种子，因此额度已经用完。<br />
			如果您需要下载，您可以通过提高优特的保种数量和容量或者点击下面的链接用 $download_need_extcredits1 <a target='_blank' href='http://zhixing.bjtu.edu.cn/thread-11787-1-1.html'>保种积分</a>兑换1个临时下载额度。<br />
			<a target='_blank' href='/torrents/{$this->tid}/download/?ext=1'>点击此处继续下载</a>（系统会扣除您 $download_need_extcredits1 保种积分, 您当前的保种积分为 {$this->user['extcredits1']} ）。<br />
			<a href='/user/group/'>点击此处查看保种数限制规则。</a>
			";
			$this->showmessage($msg);
		}
	}
}