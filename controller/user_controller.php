<?php
class user_controller extends base_controller
{

	/**
	 *
	 * @var users_module
	 */
	private $users_module;
	private $auth_type;
	private $invite_id;

	public function __construct()
	{
		parent::__construct();

		cg::load_module('users_module');
		$this->users_module = new users_module();
		$this->auth_type = 'discuzx'; //todo in setting;
	}

	public function details_action()
	{
		$this->check_login();
		$uid = intval($this->params['uid']);
		if ($uid <= 0)
		{
			$this->showmessage('参数错误', true);
		}
		if ($uid == $this->uid)
		{
			$this->data['u'] = $this->user;
		}
		else
		{
			$this->data['u'] = $this->users_module->get_by_uid($uid);
		}
		if (empty($this->data['u']))
		{
			$this->showmessage('参数错误', true);
		}
		$this->data['title'] = $this->data['u']['username'] . '的个人信息-';

		if (!$this->user['is_admin'] && $uid != $this->uid)
		{
			$this->data['u']['last_ip'] = funcs::create_hidden_ip($this->data['u']['last_ip']);
			$this->data['u']['last_ipv6'] = funcs::create_hidden_ip($this->data['u']['last_ipv6']);
		}
		if ($this->inajax)
		{
			$user_info = array();
			$display_fields = array(
				'uid',
				'username',
				'forums_uid',
				'title',
				'group_name',
				'group_color'
			);
			foreach ($display_fields as $f)
			{
				$user_info[$f] = isset($this->data['u'][$f]) ? $this->data['u'][$f] : '';
			}
			die(json_encode($user_info));
		}
		else
		{
			$users_stat_ext1 = $this->users_module->get_user_current_torrent_stat($uid, true);
			$users_top = $this->get_top_uids($uid);
			$this->data['u'] = array_merge($this->data['u'], $users_stat_ext1, $users_top);
			$this->show('user_details.php');
		}
	}

	private function get_top_uids($uid)
	{
		$uploaded_uids = $this->users_module->users_stat_model->top_uids('uploaded');
		$downloaded_uids = $this->users_module->users_stat_model->top_uids('downloaded');
		$extcredits1_uids = $this->users_module->users_stat_model->top_uids('extcredits1');
		$total_credits_uids = $this->users_module->users_stat_model->top_uids('total_credits');
		$ratio_uids = $this->users_module->users_stat_model->top_ratio_uids();

		$data = array();
		$seq = array_search($uid, $uploaded_uids);
		$data['uploaded_seq'] = $seq ? $seq + 1 : "10000+";
		$seq = array_search($uid, $downloaded_uids);
		$data['downloaded_seq'] = $seq ? $seq + 1 : "10000+";
		$seq = array_search($uid, $extcredits1_uids);
		$data['extcredits1_seq'] = $seq ? $seq + 1 : "10000+";
		$seq = array_search($uid, $total_credits_uids);
		$data['total_credits_seq'] = $seq ? $seq + 1 : "10000+";
		$seq = array_search($uid, $ratio_uids);
		$data['ratio_seq'] = $seq ? $seq + 1 : "10000+";
		return $data;
	}

	public function peers_action()
	{
		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$ids = $peers_model->get_ids_by_where("uid='$this->uid'", '500', 'last_action asc');
		$peers = $peers_model->get_peers_by_ids($ids);

		foreach ($peers as $peer)
		{
			$this->data['peers'][] = array_merge($peer, $this->convert_peer_data($peer));
		}

		$this->show('user_peers.php');
	}

	private function convert_peer_data($row)
	{
		$data = array();
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
		$data['last_action_period'] = $this->timestamp - $row['last_action'];
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

	public function other_details_action()
	{
		$this->check_login();
		$uid = intval($this->params['uid']);
		if ($uid <= 0)
		{
			$this->showmessage('参数错误', true);
		}
		//$u = $this->users_module->get_by_uid($uid);
		$dict_action = array(
			'uploaded',
			'seeding',
			'leeching',
			'completed'
		);
		$action = isset($this->params['action']) ? $this->params['action'] : '';
		if (!in_array($action, $dict_action))
		{
			$this->showmessage('参数错误', true);
		}
		$page_params = isset($this->params['page']) ? $this->params['page'] : '';
		$page = intval(str_replace('p', '', $page_params));
		$page = $page <= 0 ? 1 : $page;

		$where = '';
		$table = '';
		switch ($action)
		{
			case 'uploaded':
				$table = 'torrents_index';
				$where = " uid = '$uid'";
				break;
			case 'leeching':
				$table = 'peers';
				$where = " uid = '$uid' and is_seeder=0 ";
				break;
			case 'seeding':
				$table = 'peers';
				$where = " uid = '$uid' and is_seeder=1 ";
				break;
			case 'completed':
				$table = 'logs_completed';
				$where = " uid = '$uid' ";
				break;
			default:
				$where = '';
				$table = '';
				break;
		}
		$ids = array();
		if ($table != '')
		{
			$model = cg::load_model($table . '_model', true);
			$ids = $model->get_ids_range($where);
		}
		$pagesize = 20;
		$start = ($page - 1) * $pagesize;
		$ids = array_slice($ids, $start, $pagesize);
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$this->data['torrents'] = $torrents_module->get_torrents($ids);
		$this->data['torrents_count'] = count($this->data['torrents']);
		$this->view()->assign('data', $this->data);
		$data = $this->view()->fetch('include_torrenttable.php');
		echo $data;
	}

	public function login_action()
	{
		header('Cache-Control: no-cache, must-revalidate');
		$this->data['selected_nav'] = 'index';
		$this->data['random'] = mt_rand(100000, 999999);
		if (isset($this->post['submit']))
		{
			$this->login_submit();
		}
		else
		{
			cg::load_model('logs_loginfail_model');
			$logs_loginfail_model = logs_loginfail_model::get_instance();
			$login_fail_count = $logs_loginfail_model->get_fail_count($this->setting['login_fail_time'], $this->ip);
			$this->data['remain_login_fail_count'] = $this->setting['login_fail_count'] - $login_fail_count;
			$this->template_file = 'flat_login.php'; //old template 'login.php'
			$this->show();
		}
	}

	public function dashi_action()
	{
		$staff_uids = $this->users_module->users_stat_model->get_staff_uids();
		$users = $this->data['staff_users'] = $this->users_module->get_by_uids($staff_uids);
		$arr_users = array();
		foreach ($users as $key => $user)
		{
			if ($user['class'] == 12)
			{
				$arr_users[$user['group_name']][] = $user;
			}
		}
		$this->data['users'] = $arr_users;
		$this->template_file = 'dashi.php';
		$this->show();
	}

	public function staff_action()
	{
		$this->check_login();
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('您没有权限访问此页面', true);
		}
		$staff_uids = $this->users_module->users_stat_model->get_staff_uids();
		$users = $this->data['staff_users'] = $this->users_module->get_by_uids($staff_uids);
		$arr_users = array();

		foreach ($users as $key => $user)
		{
			if ($this->user['is_admin'] || $this->user['is_moderator'])
			{
				$arr_users[$user['groupid']][] = $user;
			}
			else
			{
				if ($user['is_admin'] || $user['is_moderator'])
				{
					$arr_users[$user['groupid']][] = $user;
				}
			}
		}
		krsort($arr_users);
		unset($users);
		$data = array();
		foreach ($arr_users as $groupid => $users)
		{
			foreach ($users as $user)
			{
				$data[$user['group_name']][] = $user;
			}
		}
		unset($arr_users);

		$this->data['users'] = $data;
		$this->template_file = 'staff.php';
		$this->show();
	}

	public function group_action()
	{
		$this->check_login();
		$rows_privileges = $this->users_module->all_privileges;
		$rows_users_group = $this->users_module->get_all_users_group();

		$dict_title = array(
			'groupid' => '用户级别',
			'name' => '用户组名称',
			'credits' => '总积分介于'
		);
		$data = array();
		$i = 0;
		foreach ($dict_title as $key => $value)
		{
			$data[$i][] = $value;
			foreach ($rows_users_group as $groupid => $group)
			{
				if ($groupid > 10)
				{
					continue;
				}
				if ($key == 'credits')
				{
					$data[$i][] = '(' . $group['min_credits'] . ',' . $group['max_credits'] . ']';
				}
				else
				{
					$data[$i][] = $group[$key];
				}
			}
			$i++;
		}
		$i = count($dict_title);
		foreach ($rows_privileges as $privileges)
		{
			if (empty($privileges['status']) || !$privileges['is_front'])
			{
				continue;
			}
			$data[$i][] = $privileges['name'];
			foreach ($rows_users_group as $groupid => $group)
			{
				if ($groupid > 10)
				{
					continue;
				}
				$val = $group['privileges'][$privileges['name_en']];
				if ($privileges['type'] == 'yes_no')
				{
					$data[$i][] = $val == '1' ? 'yes' : 'no';
				}
				else
				{
					$data[$i][] = $val;
				}
			}
			$i++;
		}
		$this->data['ratio_limit_msg'] = $this->get_ratio_limit_msg();
		$this->data['seed_count_limit_msg'] = $this->get_seed_count_limit();
		$this->data['seed_size_limit_msg'] = $this->get_seed_size_limit();
		$this->data['users_group'] = $data;
		$this->show('users_group.php');
	}

	private function get_seed_size_limit()
	{
		if (!$this->setting['enable_seed_size_limit'])
		{
			return '';
		}
		$rows = funcs::explode($this->setting['seed_size_limit']);
		$msg = '';
		foreach ($rows as $row)
		{
			list($key, $limit) = funcs::explode($row, ':');
			$msg .= "当你的保种容量小于 $key G时，你的下载额度是 $limit 。<br />";
		}
		$msg .= "自己发布的种子，置顶、推荐、免费的种子没有限制<br />";

		return $msg;
	}

	private function get_seed_count_limit()
	{
		if (!$this->setting['enable_seed_count_limit'])
		{
			return '';
		}
		$rows = funcs::explode($this->setting['seed_count_limit']);
		$msg = '';
		foreach ($rows as $row)
		{
			list($key, $limit) = funcs::explode($row, ':');
			$msg .= "当你的保种数量小于 $key 时，你的下载额度是 $limit 。<br />";
		}
		$msg .= "自己发布的种子，置顶、推荐、免费的种子没有限制<br />";
		return $msg;
	}

	private function get_ratio_limit_msg()
	{
		$dict_ratio_limit = array();
		$rows = funcs::explode($this->setting['ratio_limit']);
		$msg = '';
		foreach ($rows as $row)
		{
			list($key, $limit) = funcs::explode($row, ':');
			if ($key > 0)
			{
				$msg .= "当你的下载流量大于 $key G时，你的共享率必须大于 $limit 。<br />";
			}
		}
		$msg .= "自己发布的种子，置顶、推荐、免费的种子没有限制<br />";
		$msg .= "<br />";
		$msg .= "<br />";
		return $msg;
	}

	public function update_title_action()
	{
		$this->check_login();
		$extcredits1 = intval($this->setting['modify_title_need_extcredits1']);
		$title = $this->post['title'];
		$title = htmlspecialchars($title, ENT_QUOTES);
		$arr_fields = array(
			'title' => $title
		);
		$this->users_module->users_model->update($arr_fields, $this->uid);
		if ($extcredits1 > 0)
		{
			$logs_fields = array(
				'count' => -1 * $extcredits1,
				'field' => 'extcredits1',
				'action' => 'update_user_title'
			);
			$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
			$this->users_module->add_credits($this->uid, -1 * $extcredits1, 'extcredits1', $logs_fields);
		}
		die('修改成功!');
	}

	public function update_duty_action()
	{
		$this->check_login();
		$duty = $this->post['user_duty'];
		$arr_fields = array(
			'duty' => $duty
		);
		$this->users_module->users_model->update($arr_fields, $this->uid);
		die('修改成功!');
	}

	public function update_last_browse_time_action()
	{
		$this->check_login();
		$arr_fields = array(
			'last_browse' => $this->timestamp
		);
		$this->users_module->users_stat_model->update($arr_fields, $this->uid);
		die('更新成功! 当前时间之前发布的种子的new标记已经清空。');
	}

	public function logout_action()
	{
		$this->set_logout_cookie();
		cg::load_class('user_auth');
		$auth_type = 'discuzx'; //@todo in setting
		$user_auth = new user_auth($auth_type);
		echo $auth_result = $user_auth->syn_logout_discuzx();
		echo "<script type='text/javascript'>location.href='/index/index';</script>";
		die();
	}

	public function captcha_action()
	{
		cg::load_class('captcha');
		captcha::createAuthcode();
	}

	public function register_action()
	{
		$this->redirect($this->setting['forums_register_url']);
	}

	public function lostpassword_action()
	{
		$this->redirect($this->setting['forums_lost_password_url']);
	}

	private function check_user_exists($username)
	{
		$uid = $this->users_module->users_model->get_uid_by_username($username, false);
		if (empty($uid))
		{
			return false;
		}
		return $uid;
	}

	private function get_fourms_user_info($username)
	{
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$user_info = $forums_discuzx_model->get_user_info($username);
		return $user_info;
	}

	private function check_forums_user_valid($username)
	{
		if (!$this->setting['check_forums_user_valid'])
		{
			return;
		}
		$user_info = $this->get_fourms_user_info($username);
		if ($user_info['emailstatus'] == '0')
		{
			$this->showmessage('您必须在论坛邮箱验证通过之后才能访问PT。如果收不到验证邮件，请到设置-密码安全里面重发验证邮件，直到收到为止。');
		}
		/*
		if ($user_info['groupid'] == '57' && stripos($user_info['email'], 'edu.cn') === false)
		{
			$this->showmessage('您必须在论坛进行实名认证通过之后才能访问PT。请到论坛填写实名认证表单，并等待管理人员审核。');
		}

		if ($this->timestamp - $user_info['regdate'] < 86400 * 3)
		{
			$this->showmessage('您必须注册3天后才能访问知行PT，在此期间请阅读论坛的公告，熟悉PT的规则。');
		}
		*/
	}

	private function check_invite_code($username)
	{
		if (!$this->setting['check_invite_code'])
		{
			return;
		}
		$invitecode = trim($this->post['invitecode']);
		if (!preg_match('/[a-f0-9]{32}/i', $invitecode))
		{
			$this->showmessage('邀请码错误，请填写正确的邀请码。');
		}
		cg::load_model('invite_model');
		$invite_model = invite_model::get_instance();
		$row_invite = $invite_model->get_row_by_invitecode($invitecode);
		if (empty($row_invite))
		{
			$this->showmessage('邀请码错误，请填写正确的邀请码。');
		}
		if ($row_invite['expiretime'] < $this->timestamp)
		{
			$this->showmessage('您输入的邀请码已过期。');
		}
		if ($row_invite['used_username'] != '')
		{
			$this->showmessage('您输入的邀请码已经使用过了，不能重复使用。');
		}
		$this->invite_id = $row_invite['id']; //after auth, udpate invite table
	}

	private function login_log($uid)
	{
		$arr_fields = array(
			'uid' => $uid,
			'ip' => $this->ip,
			'createtime' => $this->timestamp
		);
		cg::load_model('logs_login_model');
		$logs_login_model = logs_login_model::get_instance();
		$logs_login_model->insert($arr_fields);
	}

	private function login_fail_log($username = '')
	{
		$arr_fields = array(
			'username' => $username,
			'ip' => $this->ip,
			'createtime' => $this->timestamp
		);
		cg::load_model('logs_loginfail_model');
		$logs_loginfail_model = logs_loginfail_model::get_instance();
		$logs_loginfail_model->insert($arr_fields);
		/*
		$login_fail_count = $logs_loginfail_model->get_fail_count($this->setting['login_fail_time'], $this->ip, $username);
		if ($login_fail_count >= $this->setting['login_fail_count'])
		{
			$msg = "您的帐号 $username 在 {$this->setting['login_fail_time']} 分钟内多次登录失败，请查看工具箱登录日志，确认您的登录是否正常。	请保护好您的个人信息(包括密码和email地址)不要泄露给他人。";
			$this->send_pm('', $username, '', $msg);
		}
		*/
	}

	private function login_submit()
	{
		cg::load_model('logs_loginfail_model');
		$logs_loginfail_model = logs_loginfail_model::get_instance();
		$login_fail_count = $logs_loginfail_model->get_fail_count($this->setting['login_fail_time'], $this->ip, '');
		if ($login_fail_count > 0)
		{
			$msg = $this->lang('captcha_error');
			if (!isset($this->post['captcha']) || !isset($_SESSION['captcha']))
			{
				$this->login_fail_log();
				$this->showmessage($msg);
			}
			$captcha = $this->post['captcha'];
			$session_captcha = $_SESSION['captcha'];
			$_SESSION['captcha'] = '';
			if (strtolower($captcha) != strtolower($session_captcha))
			{
				$this->login_fail_log();
				$this->showmessage($msg);
			}
		}
		$username = $this->post['username'];
		$password = $this->post['password'];

		//从论坛验证先
		cg::load_class('user_auth');
		$user_auth = new user_auth($this->auth_type);
		$auth_result = $user_auth->check_login($username, $password);
		if ($auth_result['result'] != '0')
		{
			$msg = "用户名或密码错误!";
			$this->login_fail_log($username);
			$this->showmessage($msg);
		}
		else
		{
			//通过验证, 则检查bt数据库是否存在该用户,不存在则检查用户是否激活，邀请码
			$uid = $this->check_user_exists($username);
			if ($uid === false)
			{
				$this->check_forums_user_valid($username);
				$this->check_invite_code($username);
			}
			$auth_result['username'] = $username;
			$auth_result['password'] = $password;

			// auto reg, setcookie
			$this->after_user_auth($this->auth_type, $auth_result);

			//syn login
			echo $user_auth->syn_login_discuzx($auth_result['uid']);
			$this->login_redirect();
		}
	}

	private function after_user_auth($auth_type, $auth_result)
	{
		$password_md5 = '';
		if ($auth_type == 'internal')
		{
			$uid = $auth_result['uid'];
			if ($this->setting['forums_type'] == 'discuz' || $this->setting['forums_type'] == 'discuzx')
			{
				//@todo: auto register user into discuz/ucenter
			}
		}
		elseif ($auth_type == 'discuz' || $auth_type == 'discuzx')
		{
			//auto register user into internal users table
			$arr_fields = array();
			$arr_fields['username'] = $auth_result['username'];
			$arr_fields['salt'] = $auth_result['salt'];
			$arr_fields['password'] = md5(md5($auth_result['password']) . $auth_result['salt']);
			$password_md5 = $arr_fields['password'];
			$arr_fields['forums_uid'] = $auth_result['uid'];
			$arr_fields['email'] = $auth_result['email'];

			//check exists
			$uid = $this->users_module->users_model->get_uid_by_username($auth_result['username'], false);

			if (empty($uid))
			{
				$uid = $this->users_module->insert_user($arr_fields);
				if ($this->invite_id > 0)
				{
					$invite_fields = array(
						'updatetime' => $this->timestamp,
						'used_username' => $arr_fields['username'],
						'used_uid' => $uid
					);
					$invite_model = invite_model::get_instance();
					$invite_model->update($invite_fields, $this->invite_id);
				}
			}
			else
			{
				$this->users_module->users_model->update($arr_fields, $uid);
			}
			$this->login_log($uid);
			$this->users_module->delete_cache($uid);
		}
		elseif ($auth_type == 'phpwind')
		{
		}
		elseif ($auth_type == 'kbs')
		{
		}

		$cookie_days = intval($this->setting['site_cookie_expire']) == 0 ? 1 : intval($this->setting['site_cookie_expire']);
		$cookie_time = $cookie_days * 86400;
		$this->set_login_cookie($uid, $password_md5, $cookie_time);
	}

	private function login_redirect()
	{
		//header("Location: /index/test");
		//if syn_login discuz, must use javascript
		echo "<script type='text/javascript'>location.href='/index/index';</script>";
		die();
	}

	private function set_login_cookie($uid, $password, $cookie_time)
	{
		$_SESSION['cgbt_uid'] = $uid; //@todo session in many servers
		//cg::load_core('cg_cookie'); //load in base_controller
		//cg::load_core('cg_encrypt');
		$expires = time() + $cookie_time;
		$uid = cg_enctypt::encrypt($uid);
		$password = cg_enctypt::encrypt($password);
		cg_cookie::set("uid", $uid, $expires);
		cg_cookie::set("password", $password, $expires);
	}

	public function reset_passkey_action()
	{
		$password = isset($this->post['password']) ? $this->post['password'] : '';
		if (empty($password))
		{
			$this->showmessage('请输入密码', true);
		}

		cg::load_class('user_auth');
		$user_auth = new user_auth('discuzx');
		$result = $user_auth->check_login($this->username, $password);
		if ($result['result'] != '0')
		{
			$this->showmessage('密码错误', true);
		}
		$old_passkey = $this->user['passkey'];
		$passkey = $this->users_module->create_passkey($this->user);
		$arr_fields = array(
			'passkey' => $passkey
		);
		$this->users_module->users_model->update($arr_fields, $this->uid);
		cg::load_model('logs_passkey_model');
		$logs_passkey_model = logs_passkey_model::get_instance();
		$arr_fields = array(
			'createtime' => $this->timestamp,
			'uid' => $this->uid,
			'username' => $this->username,
			'passkey' => $passkey
		);
		$logs_passkey_model->insert($arr_fields);

		$cache_key = "passkey2uid_$old_passkey";
		$this->cache()->delete($cache_key);

		$this->showmessage('重置用户识别码操作成功', false);
	}

	private function set_logout_cookie()
	{
		$_SESSION['cgbt_uid'] = '0';
		//cg::load_core('cg_cookie');
		cg_cookie::delete("uid");
		cg_cookie::delete("password");
	}
}