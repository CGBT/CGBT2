<?php
/**
 * include:
 * system_config
 * router_config
 * db_config
 *
 */
class config
{
	public static $config;
	public static $ERROR_404_DOCUMENT = '404.php';
	public static $ERROR_404_ROUTE = '/error/404';
	private static $inited;
	private static $framework_dir = 'system';
	private static $application_name = 'cgbt'; //缓存key前缀，可以为空，多个cgbt共用memcached则需要区分
	private static $system_salt_key = ''; //系统唯一key
	private static $system_founder = 'admin'; //创始人，可以登录后台

	public static function init()
	{
		if (self::$inited)
		{
			return;
		}

		self::$inited = true;
		self::system_config();
		self::router_config();
		self::db_config();
		self::cache_config();
		self::other_config();
		self::cookie_config();
		self::admin_privileges_config();
	}

	public static function cookie_config()
	{
		self::$config['cookie'] = array(
			'cookie_prefix' => 'cgbt_',
			'salt' => true,
			'salt_key' => self::$system_salt_key,
			'salt_agent' => false,
			'salt_ip' => false,
			'path' => '/',
			'secure' => false,
			'httponly' => true
		);
		//多个域名公用同一个cookie域
		self::$config['dict_cookie_domain'] = array(
			'cgbt.cn' => '.cgbt.cn',
			'ipv4.cgbt.cn' => '.cgbt.cn',
			'ipv6.cgbt.cn' => '.cgbt.cn'
		);
		$cookie_domain = '';
		if (isset(self::$config['dict_cookie_domain'][$_SERVER['HTTP_HOST']]))
		{
			$cookie_domain = self::$config['dict_cookie_domain'][$_SERVER['HTTP_HOST']];
		}
		self::$config['cookie']['domain'] = $cookie_domain;
	}

	public static function db_config()
	{
		self::$config['db'] = include 'db.config.php';
	}

	public static function cache_config()
	{
		$cache_config = array();
		$cache_config['cache_type'] = 'memcache'; //memcache/memcached/redis
		$cache_config['file'] = array(
			'cache_dir' => 'cache'
		);
		$cache_config['memcache'] = array(
			'key_prefix' => self::$application_name,
			'server' => array(
				0 => array(
					"host" => "127.0.0.1",
					"port" => 11211,
					"weight" => 1,
					"status" => true
				),
				1 => array(
					"host" => "127.0.0.1",
					"port" => 11212,
					"weight" => 1,
					"status" => false
				)
			)
		);
		$cache_config['redis'] = array(
			'key_prefix' => self::$application_name,
			'server' => array(
				0 => array(
					"host" => "127.0.0.1",
					"port" => 6379,
					"status" => true
				)
			)
		);
		self::$config['cache'] = $cache_config;
	}

	public static function router_config()
	{
		self::$config['router'] = include 'router.config.php';
	}

	public static function admin_privileges_config()
	{
		self::$config['admin_privileges'] = include 'admin.privileges.php';
	}

	public static function system_config()
	{
		$system_config['APP_PATH'] = self::fix_dir(substr(dirname(__FILE__), 0, -7) . DIRECTORY_SEPARATOR);
		$system_config['APP_SUBDIR'] = str_replace(self::fix_dir($_SERVER['DOCUMENT_ROOT']), '', $system_config['APP_PATH']);
		$protocol = self::is_ssl() ? 'https' : 'http';
		$system_config['APP_URL'] = $protocol . '://' . $_SERVER['HTTP_HOST'] . $system_config['APP_SUBDIR'];
		if (substr($system_config['APP_URL'], -1) != '/')
		{
			$system_config['APP_URL'] .= '/';
		}
		$system_config['SYS_PATH'] = self::fix_dir($system_config['APP_PATH'] . self::$framework_dir . DIRECTORY_SEPARATOR);
		$system_config['ERROR_404_DOCUMENT'] = self::$ERROR_404_DOCUMENT;
		$system_config['ERROR_404_ROUTE'] = self::$ERROR_404_ROUTE;
		$system_config['AUTO_ROUTE'] = true;
		self::$config['system'] = $system_config;
	}

	public static function other_config()
	{
		self::$config['system_salt_key'] = self::$system_salt_key;
		self::$config['system_founder'] = self::$system_founder;

		self::$config['lang'] = array(
			'default' => 'cn',
			'multi_lang' => true,
			'all' => array(
				'en' => 'en-us',
				'cn' => 'zh-cn',
				'tw' => 'zh-tw'
			)
		);
		self::$config['view'] = array(
			'default_template' => 'default', //dir
			'all_templates' => array(
				//dir => array();
				'default' => array(
					'name' => '默认',
					'engine' => 'cg'
				),
				'cgbt' => array(
					'name' => 'cgbt',
					'engine' => 'cg'
				),
				'smarty' => array(
					'name' => 'smarty',
					'engine' => 'smarty'
				),
				'dz' => array(
					'name' => 'dz',
					'engine' => 'dz'
				)
			)
		);

		self::$config['forums'] = array(
			'db_config_name' => 'newcgbtdiscuzx',
			'uc_config' => array(
				'db_config_name' => 'newcgbtdiscuzx',
				'uc_api' => '',
				'uc_key' => '',
				'uc_appid' => '',
				'uc_ip' => ''
			)
		);

		self::$config['MODULES'] = array(
			'admin',
			'api',
			'upgrade'
		);
	}

	private static function fix_dir($dir)
	{
		return str_replace('\\', '/', $dir);
	}

	private static function is_ssl()
	{
		//sometimes, php can't detect https
		if (isset($_SERVER['HTTPS']))
		{
			if ($_SERVER['HTTPS'] === 1)
			{
				return TRUE;
			}
			elseif ($_SERVER['HTTPS'] === 'on')
			{
				return TRUE;
			}
		}

		if ($_SERVER['SERVER_PORT'] == 443)
		{
			return TRUE;
		}
		return FALSE;
	}
}

