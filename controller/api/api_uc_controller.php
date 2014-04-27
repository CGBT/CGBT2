<?php
class api_uc_controller extends base_controller
{
	private $UC_CLIENT_VERSION = '1.6.0';
	// 定义版本
	private $UC_CLIENT_RELEASE = '20110501';
	// 开关定义
	private $API_DELETEUSER = 0; // note 用户删除 API 接口开关
	private $API_RENAMEUSER = 0; // note 用户改名 API 接口开关
	private $API_GETTAG = 0; // note 获取标签 API 接口开关
	private $API_SYNLOGIN = 1; // note 同步登录 API 接口开关
	private $API_SYNLOGOUT = 1; // note 同步登出 API 接口开关
	private $API_UPDATEPW = 1; // note 更改用户密码 开关
	private $API_UPDATEBADWORDS = 0; // note 更新敏感词 开关
	private $API_UPDATEHOSTS = 0; // note 更新域名解析缓存 开关
	private $API_UPDATEAPPS = 0; // note 更新应用列表 开关
	private $API_UPDATECLIENT = 0; // note 更新客户端缓存列表 开关
	private $API_UPDATECREDIT = 0; // note 更新用户积分 开关
	private $API_GETCREDIT = 0; // note 获取用户的某项积分 开关
	private $API_GETCREDITSETTINGS = 0; // note 向 UCenter 提供积分设置 开关
	private $API_UPDATECREDITSETTINGS = 0; // note 更新应用积分设置 开关
	private $API_ADDFEED = 0; // //note 添加动态 开关
	// 定义返回值
	private $API_RETURN_SUCCEED = '1';
	private $API_RETURN_FAILED = '-1';
	private $API_RETURN_FORBIDDEN = '1';
	//
	private $IN_API = true;
	//
	private $users_module;
	//存储从UC获取的用户信息
	private $uc_user;

	public function __construct()
	{
		parent::__construct();
		error_reporting(0);
	}

	public function index_action()
	{
		if (!defined('IN_UC'))
		{
			$uc_config = cg::config()->config['forums']['uc_config'];
			// 加载UC_Client
			cg::load_class('uc_client');
			$uc_client = new uc_client();
			// 处理数据
			$get = $post = array();
			$code = @$_GET['code'];
			parse_str($uc_client->uc_authcode($code, 'DECODE'), $get);
			// 校验签名
			if (time() - $get['time'] > 3600)
			{
				exit('Authracation has expiried');
			}
			if (empty($get))
			{
				exit('Invalid Request');
			}

			//检查开关
			$switch = 'API_' . strtoupper($get['action']);
			if (!$this->$switch && $get['action'] != 'test')
			{
				echo $this->API_RETURN_FORBIDDEN;
				die();
			}

			//检查编码
			if (strtolower(cg::config()->config['db'][$uc_config['db_config_name']]['charset']) == 'gbk')
			{
				$get['username'] = funcs::gbk2utf8($get['username']);
			}

			// test 同步登录和退出不需要XML处理 暂时放在这优先处理
			if (in_array($get['action'], array(
				'test',
				'synlogin',
				'synlogout',
				'updatepw'
			)))
			{
				if (is_callable(array(
					$this,
					$get['action']
				)))
				{
					echo $this->$get['action']($get, $post);
					die();
				}
			}
			// @TODO 其他设定需要XML的 放下面处理 D大不需要 就暂时没有实现
			$post = $uc_client->uc_xml_unserialize(file_get_contents('php://input'));
		}
		else
		{
			exit();
		}
	}

	private function test($get, $post)
	{
		return $this->API_RETURN_SUCCEED;
	}

	private function synlogin($get, $post)
	{
		//如果用户还没有登录
		if (empty($this->user['uid']))
		{
			//初始化用户数据
			$this->init_uc_user($get, $post);

			//UC通知过来的密码就是内部用户表达password hash，因为D大加了salt
			if (empty($this->uc_user['uid']))
			{
				//do Nothing;
			}
			else
			{
				header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
				$cookie_days = intval($this->setting['site_cookie_expire']) == 0 ? 1 : intval($this->setting['site_cookie_expire']);
				$cookie_time = $cookie_days * 86400;
				$this->login_log($this->uc_user['uid']);
				$this->set_login_cookie($this->uc_user['uid'], $get['password'], $cookie_time);
			}
		}
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

	private function synlogout($get, $post)
	{
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->set_logout_cookie();
	}

	private function updatepw($get, $post)
	{
		//暂未实现
		$this->API_RETURN_SUCCEED;
	}

	private function init_uc_user($get, $post)
	{
		// check exists
		cg::load_module('users_module');
		$this->users_module = users_module::get_instance();
		$this->uc_user['uid'] = $this->users_module->users_model->get_uid_by_username($get['username'], false);
	}

	//以下方法暂时抠出来，等待D大准备好重用。
	private function set_login_cookie($uid, $password, $cookie_time)
	{
		$_SESSION['cgbt_uid'] = $uid; //@todo session in many servers
		cg::load_core('cg_cookie');
		cg::load_core('cg_encrypt');
		$expires = time() + $cookie_time;
		$uid = cg_enctypt::encrypt($uid); //@todo global_unique_key
		$password = cg_enctypt::encrypt($password);
		cg_cookie::set("cgbt_uid", $uid, $expires); //@todo cookie domain
		cg_cookie::set("cgbt_password", $password, $expires);
	}

	private function set_logout_cookie()
	{
		$_SESSION['cgbt_uid'] = '0';
		cg::load_core('cg_cookie');
		cg_cookie::delete("cgbt_uid");
		cg_cookie::delete("cgbt_password");
	}
}