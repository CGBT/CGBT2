<?php
class base_controller extends cg_controller
{
	public $template_file;
	public $data = array();
	public $setting;
	public $user, $uid, $username, $passkey;
	public $all_category;
	public $inajax;
	public $logs_credits_fields;
	public $site_url;
	/**
	 *
	 * @var setting_model;
	 */
	private $setting_model;

	public function __construct()
	{
		parent::__construct();

		session_cache_limiter('private, must-revalidate');
		session_start();
		header('Cache-control:private, must-revalidate');

		cg::load_class('funcs');
		cg::load_model('base_model');
		cg::load_module('base_module');

		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			$_SERVER['HTTP_USER_AGENT'] = '';
		}
		$this->check_ie6();
	}

	private function check_ie6()
	{
		if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
			header("content-type:text/html;charset=utf-8");
			$msg = "不支持IE6.0版本，请升级您的浏览器：<a href='http://windows.microsoft.com/zh-cn/windows/upgrade-your-browser'>点击此处</a>";
			die($msg);
		}
	}

	/**
	 * This will be called before the actual action is executed
	 */
	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);

		$this->init_config(); //init cookie,encrypt
		$this->template_engine = 'php';
		$this->template_dir = 'default';
		$this->get_setting();
		$this->user = array();
		$this->get_current_user();
		$this->data['selected_nav'] = '';
		$this->data['title'] = '';
		$this->data['ip'] = $this->ip;
		$this->data['is_ipv6'] = stripos($this->ip, ':');
		$this->data['cookie_domain'] = cg::config()->config['cookie']['domain'];
		$this->site_url = cg::config()->APP_URL;
		$this->data['site_url'] = $this->site_url;

		$this->get_style();
		if (!empty($this->user))
		{
			if (empty($this->user['enabled']))
			{
				$this->check_ban($this->user['uid']);
			}
			$this->uid = $this->user['uid'];
			$this->username = $this->user['username'];
			$this->passkey = $this->user['passkey'];
			if ($this->is_developer())
			{
				$this->user['is_admin'] = true;
			}
			$this->data['user'] = $this->user;
			$this->data['uid'] = $this->uid;
			$this->data['username'] = $this->username;
			$this->new_msg_and_notification();
		}
		$this->inajax = isset($this->post['inajax']) || isset($this->get['inajax']);
		//记录日志时默认值
		$this->logs_credits_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'operator' => $this->uid,
			'operator_username' => $this->username,
			'ip' => $this->ip
		);
		$this->get_header_background_pic();
	}

	private function check_ban($uid)
	{
		$reason = $this->get_ban_reason($uid);
		if (empty($reason))
		{
			$this->showmessage("该用户因未通过新手考核或其他原因已被封禁，不能登录。");
		}
		$this->showmessage($reason);
	}

	private function get_ban_reason($uid)
	{
		cg::load_model('users_bans_model');
		$users_bans_model = users_bans_model::get_instance();
		$rows = $users_bans_model->get_users_bans($uid);
		$msg = '';
		foreach ($rows as $row)
		{
			if ($row['privileges_name'] == 'login')
			{
				if ($row['endtime'] - $row['starttime'] > 180 * 86400)
				{
					$ban_time = '永久';
				}
				else
				{
					$ban_time = date("Y-m-d H:i", $row[starttime]) . ' 至 ' . date("Y-m-d H:i", $row[endtime]);
				}
				$msg = "你已经被封禁登录权限。<br /> 封禁原因: $row[reason] <br />封禁时间： $ban_time </br>";
				break;
			}
		}
		return $msg;
	}

	private function new_msg_and_notification()
	{
		$forums_uid = $this->user['forums_uid'];
		$cache_key = 'new_msg_' . $forums_uid;
		$data = $this->cache()->get($cache_key);
		if (empty($data))
		{
			cg::load_model('forums_discuzx_model');
			$forums_discuzx_model = forums_discuzx_model::get_instance();
			$data['new_msg'] = $forums_discuzx_model->new_msg($forums_uid);
			$data['new_notification'] = $forums_discuzx_model->new_notification($forums_uid);
			$this->cache()->set($cache_key, $data, 30);
		}
		$this->data['new_msg'] = $data['new_msg'];
		$this->data['new_notification'] = $data['new_notification'];
	}

	private function get_header_background_pic()
	{
		if (isset($_COOKIE['display_header_background']) && !$_COOKIE['display_header_background'])
		{
			$this->data['header_background_pic'] = '';
			return;
		}
		if (empty($this->setting['header_background_pic']))
		{
			$this->data['header_background_pic'] = '';
			return;
		}

		$lines = funcs::explode($this->setting['header_background_pic']);
		$key = array_rand($lines);
		$line = $lines[$key];
		list($url, $padding, $link) = explode("|||", $line);
		$this->data['header_background_pic'] = $url;
		$this->data['header_background_pic_padding'] = $padding;
		$this->data['header_background_pic_link'] = $link;
	}

	public function show_no_error_ajax_message()
	{
		if ($this->inajax)
		{
			$this->showmessage('', false);
		}
	}

	public function get_style()
	{
		cg::load_model('discuzx_style_model');
		$discuz_style_model = new discuzx_style_model();
		$styleid = $discuz_style_model->get_styleid();
		$extstyle = $discuz_style_model->get_extstyle($styleid);
		$this->data['styleid'] = $styleid;
		$this->data['extstyle'] = $extstyle['extstyle'];
		$this->data['verhash'] = $extstyle['verhash'];
		$this->data['extstyle_detail'] = $this->get_style_detail($this->data['extstyle']);

		$default_style = cg_cookie::get('default_style');
		if (empty($default_style))
		{
			$this->data['default_style'] = $extstyle['default_style'];
		}
		else
		{
			$this->data['default_style'] = $default_style;
		}
	}

	public function get_style_detail($extstyle)
	{
		$cache_key = 'discuzx_style_detail';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, $extstyle, 1800);
	}

	public function _get_style_detail($extstyle)
	{
		if (empty($this->setting['forums_template_dir']))
		{
			$style_dir = cg::config()->APP_PATH . '/template/default/style/';
		}
		else
		{
			$style_dir = $this->setting['forums_template_dir'] . '/default/style/';
		}

		$data = array();
		foreach ($extstyle as $sub_dir)
		{
			$css_file = $style_dir . $sub_dir . '/style.css';
			if (!file_exists($css_file))
			{
				$data[$sub_dir]['name'] = '';
				$data[$sub_dir]['color'] = '';
				continue;
			}
			$content = file_get_contents($css_file);
			if (preg_match('/\[name\](.+?)\[\/name\]/i', $content, $r1) && preg_match('/\[iconbgcolor](.+?)\[\/iconbgcolor]/i', $content, $r2))
			{
				$data[$sub_dir]['name'] = $r1[1];
				$data[$sub_dir]['color'] = $r2[1];
			}
		}
		return $data;
	}

	public function check_login()
	{
		if (empty($this->user))
		{
			$this->redirect('/user/login');
		}
	}

	public function is_developer()
	{
		if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'])
		{
			return true;
		}
		if (!empty($this->username) && in_array($this->username, funcs::explode($this->setting['admins_developer'])))
		{
			return true;
		}
		if (in_array($this->ip, funcs::explode($this->setting['admins_trust_ips'])))
		{
			return true;
		}
		return false;
	}

	public function get_current_user()
	{
		cg::load_module('users_module');
		$users_module = users_module::get_instance();

		if (isset($_SESSION['cgbt_uid']) && intval($_SESSION['cgbt_uid']) > 0)
		{
			$this->user = $users_module->get_by_uid($_SESSION['cgbt_uid']);
		}
		else
		{
			$uid = cg_cookie::get('uid');
			$pwd = cg_cookie::get('password');
			if (empty($uid) || empty($pwd))
			{
				$dict_allow_controller = array(
					'rss_controller',
					'torrents_controller',
					'upload_controller',
					'subtitles_controller'
				);

				if (in_array($this->controller_name, $dict_allow_controller))
				{
					$this->get_current_user_by_passkey();
				}
			}
			$uid = cg_enctypt::decrypt($uid);
			$pwd = cg_enctypt::decrypt($pwd);

			if (intval($uid) <= 0 || empty($pwd))
			{
				return;
			}
			$row = $users_module->get_by_uid($uid);
			if (empty($row))
			{
				return;
			}
			else
			{
				if ($row['password'] != $pwd)
				{
					return;
				}
				else
				{
					$this->user = $row;
					$_SESSION['cgbt_uid'] = $row['uid'];
					unset($row);
				}
			}
		}
		if (!empty($this->user))
		{
			$users_module->users_stat_model->update_last_access($this->user['uid'], $this->ip);
		}
	}

	public function check_have_privileges($privileges, $exit = false)
	{
		$return_info = array();
		if ($this->user['groupid'] > 10)
		{
			$return_info['have_privileges'] = true;
			return $return_info;
		}
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$all_privileges = $users_module->all_privileges;
		$privileges_name = $all_privileges[$privileges]['name'];
		if ($this->user['groupid'] < 10 && empty($this->user['privileges'][$privileges]))
		{
			$return_info['have_privileges'] = false;
			$return_info['msg'] = "您所在的用户组没有 $privileges_name 权限";
			if ($exit)
			{
				$this->showmessage($return_info['msg'], true);
			}
			return $return_info;
		}
		else
		{
			cg::load_model('users_bans_model');
			$users_bans_model = users_bans_model::get_instance();
			$data = $users_bans_model->get_all_enabled_bans();
			if (isset($data[$this->uid][$privileges]))
			{
				$row = $data[$this->uid][$privileges];
				if ($row['starttime'] < $this->timestamp && $row['endtime'] > $this->timestamp)
				{
					$return_info['have_privileges'] = false;
					$msg = '您被封禁 ' . $privileges_name . ' 权限。<br />';
					$msg .= '时间:' . date("Y-m-d H:i", $row['starttime']) . ' 至 ' . date("Y-m-d H:i", $row['endtime']) . '<br />';
					$msg .= '原因:' . $row['reason'];
					$return_info['msg'] = $msg;
					if ($exit)
					{
						$this->showmessage($return_info['msg']);
					}
				}
				else
				{
					$return_info['have_privileges'] = true;
				}
			}
			else
			{
				$return_info['have_privileges'] = true;
			}
		}
		return $return_info;
	}


	//@todo 暂时未用到，可以用在rss页面和接口页面
	public function get_current_user_by_passkey()
	{
		$passkey = '';
		if (isset($this->params['passkey']))
		{
			$passkey = $this->params['passkey'];
		}
		elseif (isset($this->get['passkey']))
		{
			$passkey = $this->get['passkey'];
		}
		elseif (isset($this->post['passkey']))
		{
			$passkey = $this->post['passkey'];
		}

		if (preg_match('/[a-f0-9]{32}/i', $passkey))
		{
			cg::load_module('users_module');
			$users_module = new users_module();
			$uid = $users_module->users_model->get_uid_by_passkey($passkey);
			$this->user = $users_module->get_by_uid($uid);
		}
	}

	public function get_category()
	{
		cg::load_model('category_model');
		$this->category_model = new category_model();
		$this->all_category = $this->category_model->get_all();
	}

	public function get_setting()
	{
		cg::load_model('setting_model');
		$this->setting_model = new setting_model();
		$this->setting = $this->setting_model->get_all();
		$this->data['setting'] = &$this->setting;
	}

	public function redirect($url)
	{
		header('Location: ' . $url);
		die();
	}

	public function get_all_sql()
	{
		return $this->setting_model->get_all_sql();
	}

	public function get_sql_count()
	{
		return $this->setting_model->get_sql_count();
	}

	public function get_cache_keys()
	{
		return $this->cache()->keys();
	}

	public function show($template_file = '')
	{
		if (empty($template_file))
		{
			$template_file = $this->template_file;
		}
		if (empty($template_file))
		{
			die('template file empty!');
		}
		if (DIRECTORY_SEPARATOR != '\\')
		{
			$this->data['server_load'] = exec('uptime');
		}
		else
		{
			$this->data['server_load'] = '';
		}
		$this->data['page_execute_time'] = $this->get_execute_time();
		$this->data['all_sql'] = $this->get_all_sql();
		$this->data['all_sql_count'] = $this->get_sql_count();
		$this->data['all_cache_keys'] = $this->get_cache_keys();
		$this->data['is_developer'] = $this->is_developer();
		$this->data['sleep_times'] = $this->setting_model->get_sleep_times();
		$this->data['db_stat'] = $this->setting_model->get_db_stat_data();
		$this->view()->assign('data', $this->data);
		$this->view()->display($template_file);
	}

	/**
	 *
	 * @param array $msg_data
	 * @example:
	 * $msg_data = array(
	 * 		'error' => true,false,'default'
	 * 		'msg' => 'params error',
	 * 		'return_url' => false, '/search/', 'default'   //default: history.back(-1)
	 * );
	 */
	public function showmessage($msg_data = '', $error = 'default')
	{
		if (is_string($msg_data))
		{
			$msg_data = array(
				'error' => $error,
				'msg' => $msg_data
			);
		}

		if (isset($this->post['inajax']) || isset($msg_data['inajax']) || isset($this->get['inajax']))
		{
			die(json_encode($msg_data));
		}
		else
		{
			$return_type = isset($this->get['return_type']) ? $this->get['return_type'] : '';
			if (empty($return_type))
			{
				$return_type = isset($this->post['return_type']) ? $this->post['return_type'] : '';
			}
			if ($return_type == 'text')
			{
				if ($msg_data['error'] !== false)
				{
					die('error: ' . $msg_data['msg']);
				}
				else
				{
					die($msg_data['msg']);
				}
			}
			$this->data['server_load'] = exec('uptime');
			$this->data['page_execute_time'] = $this->get_execute_time();
			$this->data['all_sql'] = $this->get_all_sql();
			$this->data['all_sql_count'] = $this->get_sql_count();
			$this->data['all_cache_keys'] = $this->get_cache_keys();
			$this->data['is_developer'] = $this->is_developer();
			$this->data['sleep_times'] = $this->setting_model->get_sleep_times();
			$this->data['db_stat'] = $this->setting_model->get_db_stat_data();

			$this->view()->assign('msg_data', $msg_data);
			$this->view()->assign('data', $this->data);
			$this->view()->display('message.php');
			exit();
		}
	}

	public function send_note($from_user, $to_uid, $note)
	{
		$from_forums_uid = $from_user['forums_uid'];
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$forums_discuzx_model->notice_send($from_forums_uid, $from_user['username'], $to_uid, $note);
	}

	public function send_pm($from_forums_uid, $msgto, $subject, $message, $isusername = 1)
	{
		if (empty($from_forums_uid))
		{
			$from_username = $this->setting['admins_deliver'];
			cg::load_module('users_module');
			$users_module = new users_module();
			$from_user = $users_module->get_by_username($from_username);
			if (!empty($from_user))
			{
				$from_forums_uid = $from_user['forums_uid'];
			}
		}
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$forums_discuzx_model->pm_send($from_forums_uid, $msgto, $subject, $message, $isusername);
	}

	private function init_config()
	{
		cg::load_core('cg_cookie');
		cg::load_core('cg_encrypt');
		cg_cookie::init(cg::config()->config['cookie']);
		cg_enctypt::init(cg::config()->config['system_salt_key']);
	}

	private function get_server_load()
	{
		$this->data['server_load'] = exec('uptime');
	}

	public function check_ext_loaded($ext)
	{
		$all_exts = get_loaded_extensions();
		if (in_array($ext, $all_exts) !== false)
		{
			return true;
		}
		return false;
	}

	public function logs_debug($txt, $logtype = '')
	{
		$arr_fields = array(
			'createtime' => $this->timestamp,
			'logtype' => $logtype,
			'txt' => $txt
		);
		cg::load_model('logs_debug_model');
		$logs_debug_model = logs_debug_model::get_instance();
		$logs_debug_model->insert($arr_fields);
	}
}