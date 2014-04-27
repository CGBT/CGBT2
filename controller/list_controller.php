<?php
class list_controller extends base_controller
{
	private $pagesize = 100;
	private $page = 1;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
	}

	public function price_mod_action()
	{
		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$this->data['rows'] = $torrents_price_mod_model->get_all();
		$this->show('price_mod.php');
	}

	public function soft_action()
	{
		cg::load_model('soft_model');
		$soft_model = soft_model::get_instance();
		$this->data['rows_soft'] = $soft_model->get_rows('', 'id', '');


		$dict_black_peer_id = funcs::explode($this->setting['tracker_black_peer_id']);
		foreach ($dict_black_peer_id as $value)
		{
			list($peer_id, $name) = funcs::explode($value, ':');
			$black_agent[] = $name;
		}
		$black_agent = array_unique($black_agent);
		$black_agent = array_merge($black_agent, funcs::explode($this->setting['tracker_black_agent']));
		$this->data['black_agent'] = $black_agent;
		$this->show('soft.php');
	}

	public function softdown_action()
	{
		//$this->check_login();
		$id = isset($this->get['id']) ? intval($this->get['id']) : '0';
		if (empty($id))
		{
			$this->showmessage('参数错误', true);
		}
		cg::load_model('soft_model');
		$soft_model = soft_model::get_instance();
		$row = $soft_model->find($id, false);
		$arr_fields = array(
			'download' => $row['download'] + 1
		);
		$soft_model->update($arr_fields, $row['id']);
		$this->redirect($row['link']);
	}

	public function dupe_action()
	{
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$this->data['dupe_torrents'] = $torrents_module->get_dupe_torrents();
		$this->show('dupe.php');
	}

	public function sitelog_action($uid = '', $sitelog = 'sitelog')
	{
		cg::load_core('cg_pager');
		$this->data['selected_nav'] = 'sitelog';
		cg::load_model('logs_actions_model');
		$logs_actions_model = logs_actions_model::get_instance();
		$total = $logs_actions_model->count($uid);
		if ($total == 0)
		{
			$this->data['rows'] = array();
			$this->data['pager'] = null;
		}
		else
		{
			$pager = new cg_pager('/list/' . $sitelog . '/$page', $total, $this->pagesize, 10);
			$this->page = isset($this->params['page']) ? intval($this->params['page']) : 1;
			$pager->paginate($this->page);
			$limit = $pager->limit;
			$this->data['rows'] = $logs_actions_model->get_rows($uid, 'id desc', $limit);
			$this->data['pager'] = &$pager;
		}
		$dict_actions = array(
			'api_insert_torrent' => '播种机发种',
			'audit_nopass' => '审核未通过',
			'audit_pass' => '审核通过',
			'delete_torrent' => '删除种子',
			'insert_torrent' => '发布种子',
			'update_torrent' => '修改种子',
			'update_bot_torrent' => '修改播种机种子',
			'delete_price_mod_top' => '删除竞价置顶'
		);
		$dict_fields = array(
			'tid' => '种子ID',
			'reason' => '原因',
			'torrent_title' => '种子标题',
			'price_mod_id' => '竞价ID'
		);
		foreach ($this->data['rows'] as $key => $row)
		{
			$this->data['rows'][$key]['action'] = $dict_actions[$row['action']];

			$arr_details = json_decode($row['details'], true);
			$s = '';
			foreach ($arr_details as $k => $v)
			{
				$k = $dict_fields[$k];
				if (!empty($v))
				{
					$s .= $k . ': ' . $v . '<br />';
				}
			}
			$this->data['rows'][$key]['details'] = $s;
		}
		$this->show('sitelog.php');
	}

	public function mysitelog_action()
	{
		$this->check_login();
		$this->sitelog_action("uid='$this->uid'", 'mysitelog');
	}

	public function develop_action()
	{
		$this->data['selected_nav'] = 'develop';
		cg::load_model('logs_develop_model');
		$logs_develop_model = logs_develop_model::get_instance();
		$where = 'enabled=1';
		$this->data['rows'] = $logs_develop_model->get_rows($where, '`status`, cdatetime desc', '');
		foreach ($this->data['rows'] as $key => $row)
		{
			$status = '未开始';
			if ($row['status'] == '0')
			{
				$status = '未开始';
			}
			elseif ($row['status'] == '1')
			{
				$status = '进行中';
			}
			elseif ($row['status'] == '2')
			{
				$status = '已完成';
			}

			$this->data['rows'][$key]['status'] = $status;
		}
		$this->show('develop.php');
	}

	public function works_action()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('您没有权限访问此页面', true);
		}
		cg::load_model('users_model');
		$start = isset($this->get['start']) ? $this->get['start'] : '';
		$end = isset($this->get['end']) ? $this->get['end'] : '';
		$username = isset($this->get['username']) ? $this->get['username'] : '';
		if (!empty($username))
		{
			$users_model = users_model::get_instance();
			$username = $users_model->get_uid_by_username($username) ? $username : '';
		}
		cg::load_model('logs_actions_model');
		$logs_actions_model = logs_actions_model::get_instance();
		$data['moderator'] = $logs_actions_model->get_log_rows($start, $end, $username, '(is_moderator = 1 or is_admin = 1)', '');
		$data['user'] = $logs_actions_model->get_log_rows($start, $end, $username, 'is_moderator = 0 and is_admin = 0', '  having  total > 5');
		$dict_actions = array(
			'api_insert_torrent',
			'audit_nopass',
			'audit_pass',
			'delete_torrent',
			'insert_torrent',
			'update_torrent',
			'update_bot_torrent'
		);
		$users = array();
		$normalusers = array();
		foreach ($data['moderator'] as $key => $user)
		{
			$users[$user['username']][$user['action']] = $user['total'];
		}
		foreach ($data['user'] as $nkey => $nuser)
		{
			$normalusers[$nuser['username']][$nuser['action']] = $nuser['total'];
		}
		foreach ($normalusers as $nkey => $nuser)
		{
			$total = 0;
			foreach ($dict_actions as $key => $action)
			{
				if (!isset($nuser[$action]))
				{
					$normalusers[$nkey][$action] = 0;
				}
				$total = $normalusers[$nkey][$action] + $total;
			}
			if (($normalusers[$nkey]['api_insert_torrent'] + $normalusers[$nkey]['insert_torrent']) < 5)
			{
				unset($normalusers[$nkey]);
			}
			else
			{
				$normalusers[$nkey]['score'] = $total;
			}
		}
		$this->users_module = new users_module();
		$staff_uids = $this->users_module->users_stat_model->get_staff_uids();
		$staff_users = $this->data['staff_users'] = $this->users_module->get_by_uids($staff_uids);
		$arr_users = array();
		foreach ($staff_users as $jkey => $juser)
		{
			foreach ($users as $ikey => $iuser)
			{
				if (($juser['is_moderator'] || $juser['is_admin']) && $ikey == $juser['username'])
				{
					$arr_users[$juser['groupid']][$juser['username']] = $iuser;
				}
				else if (!isset($arr_users[$juser['groupid']][$juser['username']]))
				{
					$arr_users[$juser['groupid']][$juser['username']] = array();
				}
			}
		}
		foreach ($arr_users as $key => $user)
		{
			foreach ($user as $ikey => $iuser)
			{
				foreach ($dict_actions as $nkey => $action)
				{
					if (!isset($iuser[$action]))
					{
						$arr_users[$key][$ikey][$action] = 0;
					}
				}
			}
		}
		foreach ($arr_users as $ikey => $iuser)
		{
			if ($ikey == 28 || $ikey == 100)
			{
				foreach ($iuser as $jkey => $juser)
				{
					if (15 < $juser['audit_pass'] && $juser['audit_pass'] < 80)
					{
						$audit_pass = $juser['audit_pass'] / 80.0 * 1.5;
					}
					else if ($juser['audit_pass'] >= 80)
					{
						$audit_pass = 1.5;
					}
					else if ($juser['audit_pass'] <= 15)
					{
						$audit_pass = 0.0;
					}
					if (($juser['api_insert_torrent'] + $juser['insert_torrent']) > 0 && ($juser['api_insert_torrent'] + $juser['insert_torrent']) < 10)
					{
						$insert_torrent = 0.5;
					}
					else if (($juser['api_insert_torrent'] + $juser['insert_torrent']) >= 45)
					{
						$insert_torrent = 3.0;
					}
					else
					{
						$insert_torrent = ($juser['api_insert_torrent'] + $juser['insert_torrent']) / 15.0;
					}
					$audit_nopass = $juser['audit_nopass'] < 80 ? ($juser['audit_nopass'] / 80.0) * 0.5 : 0.5;
					$delete_torrent = $juser['delete_torrent'] < 100 ? $juser['delete_torrent'] * 0.01 : 1.0;
					$update_torrent = $juser['update_torrent'] < 140 ? $juser['update_torrent'] / 70.0 : 2.0;
					$update_bot_torrent = $juser['update_bot_torrent'] < 160 ? $juser['update_bot_torrent'] / 80.0 : 2.0;
					$arr_users[$ikey][$jkey]['score'] = round(($delete_torrent + $insert_torrent + $audit_nopass + $audit_pass + $update_torrent + $update_bot_torrent), 4);
				}
			}
			if ($ikey == 24 || $ikey == 26)
			{
				foreach ($iuser as $jkey => $juser)
				{
					if (($juser['api_insert_torrent'] + $juser['insert_torrent']) < 5)
					{
						$insert_torrent = 0.0;
					}
					else if (($juser['api_insert_torrent'] + $juser['insert_torrent']) >= 5 && ($juser['api_insert_torrent'] + $juser['insert_torrent']) <= 15)
					{
						$insert_torrent = 0.5 + ($juser['api_insert_torrent'] + $juser['insert_torrent']) / 15.0;
					}
					else if (($juser['api_insert_torrent'] + $juser['insert_torrent']) > 96)
					{
						$insert_torrent = 5.0;
					}
					else
					{
						$insert_torrent = (0.35 + (($juser['api_insert_torrent'] + $juser['insert_torrent']) - 15) / 90.0) * 4;
					}
					$audit_nopass = $juser['audit_nopass'] < 60 ? ($juser['audit_nopass'] / 60.0) * 0.5 : 0.5;
					$audit_pass = $juser['audit_pass'] < 40 ? ($juser['audit_pass'] / 40.0) * 0.5 : 0.5;
					$delete_torrent = $juser['delete_torrent'] < 100 ? $juser['delete_torrent'] * 0.01 : 1.0;
					$update_torrent = $juser['update_torrent'] < 150 ? $juser['update_torrent'] / 150.0 : 1.0;
					$update_bot_torrent = $juser['update_bot_torrent'] < 160 ? $juser['update_bot_torrent'] / 80.0 : 2.0;
					$arr_users[$ikey][$jkey]['score'] = round(($delete_torrent + $insert_torrent + $audit_nopass + $audit_pass + $update_torrent + $update_bot_torrent), 4);
				}
			}
			if ($ikey == 22)
			{
				foreach ($iuser as $jkey => $juser)
				{
					$audit_nopass = $juser['audit_nopass'] < 200 ? $juser['audit_nopass'] / 100.0 : 2.0;
					$audit_pass = $juser['audit_pass'] < 150 ? $juser['audit_pass'] / 150.0 : 1.0;
					$update_torrent = $juser['update_torrent'] < 200 ? $juser['update_torrent'] / 100.0 : 2.0;
					$delete_torrent = $juser['delete_torrent'] < 200 ? $juser['delete_torrent'] / 200.0 : 1.0;
					$arr_users[$ikey][$jkey]['score'] = round(($delete_torrent + $audit_nopass + $audit_pass + $update_torrent), 4);
				}
			}
		}
		krsort($arr_users);
		unset($users);
		$data = array();
		foreach ($arr_users as $groupid => $users)
		{
			ksort($users);
			if ($groupid == '100')
			{
				$data['管理员'] = $users;
			}
			else if ($groupid == '28')
			{
				$data['版主'] = $users;
			}
			else if ($groupid == '26' || $groupid == '24')
			{
				$data['播种员'] = $users;
			}
			else if ($groupid == '22')
			{
				$data['资源审核员'] = $users;
			}
		}
		krsort($arr_users);
		ksort($normalusers);
		$data['普通用户'] = $normalusers;
		$this->data['log_action'] = $data;
		$this->show('works.php');
	}

	public function logslogin_action()
	{
		cg::load_model('logs_login_model');
		cg::load_core('cg_pager');
		$logs_login_model = logs_login_model::get_instance();
		$itemPerPage = 50;
		$total = $logs_login_model->count('uid =' . $this->user['uid']);
		$pager = new cg_pager('/list/logslogin/?p=$page', $total, $itemPerPage, 10);
		$this->page = isset($this->get['p']) ? intval($this->get['p']) : 1;
		$pager->paginate($this->page);
		$limit = $pager->limit;
		$this->data['logs_login'] = $logs_login_model->get_rows('uid =' . $this->user['uid'], 'createtime desc', $limit);
		$this->data['pager'] = &$pager;
		$this->show('logs_login.php');
	}

	public function credits_action()
	{
		$dict_action = array(
			'money2uploaded2_money' => '论坛金币兑换虚拟上传流量-扣除金币',
			'money2uploaded2_uploaded2' => '论坛金币兑换虚拟上传流量-增加流量',
			'extcretids12invite' => '保种积分兑换邀请码',
			'extcretids12download' => '下载种子扣除保种积分',
			'delete_torrent' => '删除种子',
			'update_user_title' => '修改个人昵称',
			'download2extcredits1' => '种子售价，扣除下载人保种积分',
			'extcredits12download' => '种子售价，奖励发种人保种积分',
			'download_softsite' => '软件站软件售价',
			'money2uploaded2' => '金币兑换虚拟上传流量',
			'extcredits12uploaded2' => '保种积分兑换虚拟上传流量',
			'torrnets_award' => '种子奖励保种积分',
			'req_seed' => '请求补种',
			'admin' => '管理操作',
			'price_mod_top' => '竞价置顶',
			'upload_sub_extcredits1' => '上传字幕奖励保种积分',
			'post_comments' => '发布评论',
			'torrents_rate' => '审核区顶踩',
			'upload_subtitles' => '上传字幕',
			'invite_award' => '邀请奖励'
		);
		$dict_credits = array(
			'uploaded' => '上传流量',
			'downloaded' => '下载流量',
			'extcredits1' => '保种积分',
			'extcredits2' => '土豪金',
			'uploaded2' => '虚拟上传流量',
			'downloaded2' => '虚拟下载流量',
			'money' => '论坛金币'
		);

		cg::load_model('logs_credits_model');
		cg::load_core('cg_pager');
		$itemPerPage = 50;
		$logs_credits_model = logs_credits_model::get_instance();
		$total = $logs_credits_model->count('uid =' . $this->user['uid']);
		$pager = new cg_pager('/list/credits/?p=$page', $total, $itemPerPage, 10);
		$this->page = isset($this->get['p']) ? intval($this->get['p']) : 1;
		$pager->paginate($this->page);
		$limit = $pager->limit;
		$this->data['rows_credits'] = $logs_credits_model->get_rows('uid =' . $this->user['uid'], 'createtime desc', $limit);

		foreach ($this->data['rows_credits'] as $key => $row)
		{
			$this->data['rows_credits'][$key]['action'] = $dict_action[$row['action']];
			$this->data['rows_credits'][$key]['field'] = $dict_credits[$row['field']];
		}
		$this->data['pager'] = &$pager;
		$this->template_file = 'credits.php';
		$this->show();
	}

	public function topaward_action()
	{
		$dict_action = array(
			'money2uploaded2_money' => '论坛金币兑换虚拟上传流量-扣除金币',
			'money2uploaded2_uploaded2' => '论坛金币兑换虚拟上传流量-增加流量',
			'extcretids12invite' => '保种积分兑换邀请码',
			'extcretids12download' => '下载种子扣除保种积分',
			'delete_torrent' => '删除种子',
			'update_user_title' => '修改个人昵称',
			'download2extcredits1' => '种子售价，扣除下载人保种积分',
			'extcredits12download' => '种子售价，奖励发种人保种积分',
			'download_softsite' => '软件站软件售价',
			'money2uploaded2' => '金币兑换虚拟上传流量',
			'extcredits12uploaded2' => '保种积分兑换虚拟上传流量',
			'torrnets_award' => '种子奖励保种积分'
		);
		$dict_credits = array(
			'uploaded' => '上传流量',
			'downloaded' => '下载流量',
			'extcredits1' => '保种积分',
			'uploaded2' => '虚拟上传流量',
			'downloaded2' => '虚拟下载流量',
			'money' => '论坛金币'
		);

		cg::load_model('logs_credits_model');
		$logs_credits_model = logs_credits_model::get_instance();
		$start = isset($this->get['start']) ? $this->get['start'] : '';
		$end = isset($this->get['end']) ? $this->get['end'] : '';
		$this->data['rows_credits'] = $logs_credits_model->get_credits_rows($start, $end, 'operator_username', ' < 0');
		foreach ($this->data['rows_credits'] as $key => $row)
		{
			$this->data['rows_credits'][$key]['action'] = $dict_action[$row['action']];
			$this->data['rows_credits'][$key]['field'] = $dict_credits[$row['field']];
		}
		$this->template_file = 'topaward.php';
		$this->show();
	}

	public function topawarded_action()
	{
		$dict_action = array(
			'money2uploaded2_money' => '论坛金币兑换虚拟上传流量-扣除金币',
			'money2uploaded2_uploaded2' => '论坛金币兑换虚拟上传流量-增加流量',
			'extcretids12invite' => '保种积分兑换邀请码',
			'extcretids12download' => '下载种子扣除保种积分',
			'delete_torrent' => '删除种子',
			'update_user_title' => '修改个人昵称',
			'download2extcredits1' => '种子售价，扣除下载人保种积分',
			'extcredits12download' => '种子售价，奖励发种人保种积分',
			'download_softsite' => '软件站软件售价',
			'money2uploaded2' => '金币兑换虚拟上传流量',
			'extcredits12uploaded2' => '保种积分兑换虚拟上传流量',
			'torrnets_award' => '种子奖励保种积分'
		);
		$dict_credits = array(
			'uploaded' => '上传流量',
			'downloaded' => '下载流量',
			'extcredits1' => '保种积分',
			'uploaded2' => '虚拟上传流量',
			'downloaded2' => '虚拟下载流量',
			'money' => '论坛金币'
		);

		cg::load_model('logs_credits_model');
		$logs_credits_model = logs_credits_model::get_instance();
		$start = isset($this->get['start']) ? $this->get['start'] : '';
		$end = isset($this->get['end']) ? $this->get['end'] : '';
		$this->data['rows_credits'] = $logs_credits_model->get_credits_rows($start, $end, 'username', ' > 0', 'desc');
		foreach ($this->data['rows_credits'] as $key => $row)
		{
			$this->data['rows_credits'][$key]['action'] = $dict_action[$row['action']];
			$this->data['rows_credits'][$key]['field'] = $dict_credits[$row['field']];
		}
		$this->template_file = 'topawarded.php';
		$this->show();
	}
}