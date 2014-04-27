<?php
class users_module extends base_module
{
	public $users_model;
	public $users_stat_model;
	private $torrents_index_model;
	private $logs_completed_model;
	private $peers_model;
	public $users_group_model;
	public $privileges_model;
	public $all_users_group;
	public $all_privileges;
	public $all_admin_privileges;

	public function __construct()
	{
		parent::__construct();
		cg::load_model('users_model');
		cg::load_model('users_stat_model');
		cg::load_model('torrents_index_model');
		cg::load_model('logs_completed_model');
		cg::load_model('peers_model');
		cg::load_model('users_group_model');
		cg::load_model('privileges_model');

		$this->users_model = users_model::get_instance();
		$this->users_stat_model = users_stat_model::get_instance();
		$this->torrents_index_model = torrents_index_model::get_instance();
		$this->logs_completed_model = logs_completed_model::get_instance();
		$this->peers_model = peers_model::get_instance();
		$this->users_group_model = users_group_model::get_instance();
		$this->privileges_model = privileges_model::get_instance();

		$this->all_privileges = $this->privileges_model->get_all_privileges();
		$this->all_admin_privileges = $this->get_all_admin_privileges();
		$this->all_users_group = $this->get_all_users_group();
	}

	/**
	 *
	 * @return users_module
	 */
	public static function get_instance()
	{
		static $instance;
		$name = __CLASS__;
		if (!isset($instance[$name]))
		{
			$instance[$name] = new $name();
		}
		return $instance[$name];
	}

	public function get_by_uids($uids)
	{
		$users = array();
		foreach ($uids as $uid)
		{
			$users[] = $this->get_by_uid($uid);
		}
		return $users;
	}

	private function get_all_admin_privileges()
	{
		return cg::config()->config['admin_privileges'];
	}

	public function delete_cache($uid)
	{
		$this->users_model->delete_cache($uid);
		$this->users_stat_model->delete_cache($uid);
	}

	public function get_by_uid($uid)
	{
		$users = $this->users_model->find($uid);
		if (empty($users))
		{
			return array();
		}
		$users_stat = $this->users_stat_model->find($uid);

		if ($users_stat['downloaded'] - $users_stat['downloaded2'] > 20 * 1024 * 1024 * 1024)
		{
			$users_stat['ratio'] = sprintf("%.2f", ($users_stat['uploaded'] + $users_stat['uploaded2']) / ($users_stat['downloaded'] - $users_stat['downloaded2']));
		}
		else
		{
			$users_stat['ratio'] = 0;
		}

		$users_stat['total_upload_size_text'] = funcs::mksize($users_stat['total_upload_size']);
		$users_stat['total_torrent_size_text'] = funcs::mksize($users_stat['total_torrent_size']);
		$users_stat['uploaded_sum_text'] = funcs::mksize($users_stat['uploaded'] + $users_stat['uploaded2']);

		$users_stat['uploaded_text'] = funcs::mksize($users_stat['uploaded']);
		$users_stat['uploaded2_text'] = funcs::mksize($users_stat['uploaded2']);
		$users_stat['downloaded_text'] = funcs::mksize($users_stat['downloaded']);
		$users_stat['downloaded2_text'] = funcs::mksize($users_stat['downloaded2']);

		$users_stat['today_uploaded_text'] = funcs::mksize($users_stat['today_uploaded']);
		$users_stat['today_uploaded2_text'] = funcs::mksize($users_stat['today_uploaded2']);
		$users_stat['today_downloaded_text'] = funcs::mksize($users_stat['today_downloaded']);
		$users_stat['today_downloaded2_text'] = funcs::mksize($users_stat['today_downloaded2']);

		$users_stat['hour_uploaded_text'] = funcs::mksize($users_stat['hour_uploaded']);
		$users_stat['hour_uploaded2_text'] = funcs::mksize($users_stat['hour_uploaded2']);
		$users_stat['hour_downloaded_text'] = funcs::mksize($users_stat['hour_downloaded']);
		$users_stat['hour_downloaded2_text'] = funcs::mksize($users_stat['hour_downloaded2']);

		$users_stat['extcredits1_text'] = sprintf("%.1f", $users_stat['extcredits1']);
		$users_stat['total_credits'] = $this->get_total_credits($users_stat);
		$groupid = $this->get_groupid($users_stat);
		$users_stat['groupid'] = $groupid;
		$users_stat['class'] = $groupid;
		$group = $this->get_group($groupid);
		$users['group_color'] = $group['color'];
		$users['group_name'] = $group['name'];
		$users['privileges'] = $groupid < 20 ? $group['privileges'] : array();
		$users['admin_privileges'] = $groupid > 20 ? $group['admin_privileges'] : array();

		//管理权限暂时与group无关，用户权限于用户级别(1-10)有关
		$users['is_limited_user'] = $groupid == 0;
		$users['is_user'] = $groupid > 0 && $groupid <= 10;
		$users['is_moderator'] = $groupid > 20 && $groupid <= 30;
		$users['is_admin'] = $groupid > 30;
		$users['is_online'] = time() - $users_stat['last_access'] < $this->setting['online_time'] * 60 ||
		 time() - $users_stat['last_access_ipv6'] < $this->setting['online_time'] * 60;

		return array_merge((array)$users, (array)$users_stat);
	}

	public function get_user_stats_ext($uid)
	{
		$user['total_torrent_count'] = $this->torrents_index_model->get_count_by_uid($uid);
		//$user['total_completed_count'] = $this->logs_completed_model->get_count_by_uid($uid);
		//$user['seed_count'] = $this->peers_model->get_seed_count_by_user($uid);
		//$user['leech_count'] = $this->peers_model->get_leech_count_by_user($uid);
		return $user;
	}

	public function get_user_current_torrent_stat($uid, $include_size = false)
	{
		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$rows = $peers_model->get_tid_and_is_seeder_by_uid($uid);

		$data = array(
			'total_torrent_count' => 0,
			'seed_count' => 0,
			'seed_size' => 0,
			'seed_size_text' => '0',
			'leech_count' => 0,
			'leech_size' => 0,
			'leech_size_text' => '0',
			'extcredits1_speed' => 0
		);
		$seed_tids = array();
		$leech_tids = array();
		foreach ($rows as $row)
		{
			if ($row['is_seeder'])
			{
				$data['seed_count']++;
				$seed_tids[] = $row['tid'];
			}
			else
			{
				$data['leech_count']++;
				$leech_tids[] = $row['tid'];
			}
		}

		cg::load_model('torrents_index_model');
		$torrents_index_model = torrents_index_model::get_instance();
		$data['total_torrent_count'] = $this->torrents_index_model->get_count_by_uid($uid);

		if ($include_size && (!empty($seed_tids) || !empty($leech_tids)))
		{
			$row = $torrents_index_model->get_user_current_torrent_stat($seed_tids, $leech_tids, $uid);
			if (!empty($row))
			{
				$data['seed_size'] = $row['seed_size'];
				$data['seed_size_text'] = funcs::mksize($row['seed_size']);
				$data['leech_size'] = $row['leech_size'];
				$data['leech_size_text'] = funcs::mksize($row['leech_size']);
				$data['extcredits1_speed'] = sprintf("%.2f", $row['extcredits1_speed']);
			}
		}
		return $data;
	}

	private function get_total_credits($user)
	{
		$G = 1024 * 1024 * 1024;
		$e = M_E;
		$uploaded = floatval($user['uploaded']);
		$downloaded = floatval($user['downloaded']);
		$total_torrent_size = floatval($user['total_torrent_size']);
		$total_upload_times = $user['total_upload_times'];
		$extcredits1 = floatval($user['extcredits1']);

		$total_credits = ($uploaded / $G) / (log(($uploaded / $G) + 2, $e) + 6);
		$total_credits -= ($downloaded / $G) / 155 * log(($downloaded / $G) + 1, $e);
		$total_credits += $total_upload_times * 1.3 + $total_torrent_size / $G / 4;
		$total_credits += 5000 * (1 - exp(-($extcredits1) / 30000));
		$total_credits = intval($total_credits);
		return $total_credits;
	}

	private function get_groupid($user)
	{
		$groupid = 0;
		if ($user['class'] > 10)
		{
			$groupid = $user['class'];
		}
		else
		{
			foreach ($this->all_users_group as $group)
			{
				if ($user['total_credits'] > $group['min_credits'] && $user['total_credits'] <= $group['max_credits'])
				{
					$groupid = $group['groupid'];
					break;
				}
			}
		}
		return $groupid;
	}

	public function get_group($groupid)
	{
		if (!isset($this->all_users_group[$groupid]))
		{
			$groupid = 0;
		}
		return $this->all_users_group[$groupid];
	}

	public function get_by_username($username)
	{
		$uid = $this->users_model->get_uid_by_username($username);
		if ($uid > 0)
		{
			return $this->get_by_uid($uid);
		}
		return array();
	}

	public function insert_user($arr_fields)
	{
		$default_fields = array(
			'status' => '1',
			'parked' => '0',
			'enabled' => '1',
			'title' => '',
			'createtime' => time(),
			'last_check' => time(),
			'regip' => '',
			'passkey' => '',
			'pktime' => '',
			'gender' => '',
			'need_audit' => '1'
		);
		$arr_fields = array_merge($default_fields, $arr_fields);
		$arr_fields['passkey'] = $this->create_passkey($arr_fields);
		$uid = $this->users_model->insert($arr_fields);
		$arr_fields['uid'] = $uid;
		$this->users_stat_model->insert($arr_fields);
		$this->users_model->cache()->set("username2uid_" . $arr_fields['username'], $uid, 0);
		$this->users_model->cache()->set("passkey2uid_" . $arr_fields['passkey'], $uid, 0);
		return $uid;
	}

	public function create_passkey($user)
	{
		return md5($user['username'] . $user['salt'] . funcs::guid());
	}

	public function import_user($arr_fields)
	{
		$uid = $this->users_model->insert($arr_fields);
		if ($uid > 0)
		{
			$this->users_stat_model->insert($arr_fields);
		}
		return $uid;
	}

	/**
	 *
	 * @param int    $uid         uid
	 * @param int    $count       增加的数量，如果是流量单位为G，可以为负值
	 * @param string $field       字段名: uploaded,downloaded,uploaded2,downloaded2,extcredits1
	 * @param array  $logs_fields 记录日志表内容
	 */
	public function add_credits($uid, $count, $field, $logs_fields = array())
	{
		if ($count == 0)
		{
			return;
		}
		if (!in_array($field, array(
			'uploaded',
			'downloaded',
			'uploaded2',
			'downloaded2',
			'extcredits1',
			'extcredits2',
			'money'
		)))
		{
			return false;
		}
		if ($field != 'extcredits1' && $field != 'extcredits2' && $field != 'money')
		{
			$count = $count * 1024 * 1024 * 1024;
		}
		$this->users_stat_model->add_credits($uid, $count, $field);

		if (!empty($logs_fields))
		{
			cg::load_model('logs_credits_model');
			$logs_credits_model = logs_credits_model::get_instance();
			$logs_credits_model->insert($logs_fields);
		}
	}

	public function get_all_users_group()
	{
		$dict_group_type = array(
			'user' => '普通用户组',
			'vip' => '特殊用户组',
			'admin' => '管理组'
		);

		$rows_users_group = $this->users_group_model->get_all_users_group();
		$new_group = array();
		foreach ($rows_users_group as $key => $row_group)
		{
			$groupid = $row_group['groupid'];
			if ($row_group['type'] == 'user')
			{
				$group_privileges = json_decode($row_group['privileges'], true);
				foreach ($this->all_privileges as $row_privileges)
				{
					if (!isset($group_privileges[$row_privileges['name_en']]))
					{
						$group_privileges[$row_privileges['name_en']] = $row_privileges['default_value'];
					}
				}
			}
			else
			{
				$group_privileges = array();
			}
			$new_group[$groupid] = $row_group;
			$new_group[$groupid]['type_text'] = $dict_group_type[$row_group['type']];
			$new_group[$groupid]['privileges'] = $group_privileges;

			if ($row_group['type'] == 'admin')
			{
				$admin_privileges = json_decode($row_group['admin_privileges'], true);
				foreach ($this->all_admin_privileges as $top_name => $rows)
				{
					foreach ($rows as $key => $row)
					{
						list($side_name, $controller, $action, $subnav_name, $display) = explode('|', $row);
						$p = $controller . '/' . $action;
						if (!isset($admin_privileges[$p]))
						{
							$admin_privileges[$p] = 0;
						}
						if ($groupid == 100)
						{
							$admin_privileges[$p] = 1;
						}
					}
				}
				$new_group[$row_group['groupid']]['admin_privileges'] = $admin_privileges;
			}
			else
			{
				$new_group[$row_group['groupid']]['admin_privileges'] = array();
			}
		}
		return $new_group;
	}
}
