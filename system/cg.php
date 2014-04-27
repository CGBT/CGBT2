<?php
class cg
{
	protected static $_app;
	protected static $_config;
	protected static $_db;
	protected static $_cache;
	protected static $_router;
	protected static $_view;

	/**
	 * @return cg_config
	 */
	public static function config()
	{
		if (self::$_config === NULL)
		{
			self::$_config = new cg_config();
		}
		return self::$_config;
	}

	/**
	 * @return cg_router
	 */
	public static function router()
	{
		if (self::$_router === null)
		{
			self::load_core('cg_router');
			self::$_router = new cg_router();
		}
		return self::$_router;
	}

	/**
	 * @return cg_app
	 */
	public static function app()
	{
		if (self::$_app === null)
		{
			self::load_core('cg_app');
			self::$_app = new cg_app();
		}
		return self::$_app;
	}

	/**
	 * @return cg_db
	 */
	public static function db($db_config_id = 'main')
	{
		if (empty(self::$_db))
		{
			self::load_core('cg_db');
		}
		$db_config = self::config()->config['db'][$db_config_id];
		if (!isset(self::$_db[$db_config['name']]))
		{
			self::$_db[$db_config['name']] = new cg_db($db_config);
		}
		return self::$_db[$db_config['name']];
	}

	/**
	 * @return cg_cache_memcache
	 */
	public static function cache($cache_type = '')
	{
		if (empty($cache_type))
		{
			$cache_type = self::config()->config['cache']['cache_type'];
		}
		if (DIRECTORY_SEPARATOR == '\\')
		{
			$cache_type = 'memcache';
		}
		if ($cache_type == 'memcached')
		{
			if (isset(self::$_cache['memcached']))
			{
				return self::$_cache['memcached'];
			}
			self::load_core('cg_cache_memcached');
			self::$_cache['memcached'] = new cg_cache_memcached(self::config()->config['cache']['memcache']); //note: config array key is memcache
			return self::$_cache['memcached'];
		}
		elseif ($cache_type == 'redis')
		{
			if (isset(self::$_cache['redis']))
			{
				return self::$_cache['redis'];
			}
			self::load_core('cg_cache_redis');
			self::$_cache['redis'] = new cg_cache_memcached(self::config()->config['cache']['redis']);
			return self::$_cache['redis'];
		}
		else //默认memcache
		{
			if (isset(self::$_cache['memcache']))
			{
				return self::$_cache['memcache'];
			}
			self::load_core('cg_cache_memcache');
			self::$_cache['memcache'] = new cg_cache_memcache(self::config()->config['cache']['memcache']);
			return self::$_cache['memcache'];
		}
		return false;
	}

	/**
	 * @return cg_view
	 */
	public static function view($template_engine = 'php', $template_dir = '')
	{
		if ($template_engine == 'smarty')
		{
			if (isset(self::$_view['smarty']))
			{
				return self::$_view['smarty'];
			}
			self::load_core('cg_view_smarty');
			self::$_view['smarty'] = new cg_view_smarty($template_dir);
			return self::$_view['smarty'];
		}
		else
		{
			if (isset(self::$_view['php']))
			{
				return self::$_view['php'];
			}
			self::load_core('cg_view');
			self::$_view['php'] = new cg_view($template_dir);
			return self::$_view['php'];
		}
		return false;
	}

	protected static function load($class_name, $path, $create_object = false)
	{
		if (is_string($class_name))
		{
			require_once $path . "$class_name.php";
			if ($create_object)
			{
				return new $class_name();
			}
		}
		else
		{
			if ($create_object)
			{
				$obj = array();
			}
			foreach ($class_name as $one)
			{
				require_once $path . "$one.php";
				if ($create_object)
				{
					$obj[] = new $one();
				}
			}
			if ($create_object)
			{
				return $obj;
			}
		}
	}

	public static function load_file($file)
	{
		$file = self::config()->APP_PATH . "class/" . $file;		
		require_once ($file);
	}

	public static function load_class($class_name, $create_object = false)
	{
		return self::load($class_name, self::config()->APP_PATH . "class/", $create_object);
	}

	public static function load_controller($class_name, $create_object = false)
	{
		return self::load($class_name, self::config()->CONTROLLER_PATH, $create_object);
	}

	public static function load_model($class_name, $create_object = false)
	{
		return self::load($class_name, self::config()->MODEL_PATH, $create_object);
	}

	public static function load_core($class_name, $create_object = false)
	{
		return self::load($class_name, self::config()->SYS_PATH, $create_object);
	}

	public static function load_module($class_name, $create_object = false)
	{
		return self::load($class_name, self::config()->MODULE_PATH, $create_object);
	}
}

function __autoload($class_name)
{
	$class_info = explode("_", $class_name);
	$end_info = end($class_info);
	if ($class_info[0] == 'cg')
	{
		cg::load_core($class_name);
	}
	elseif ($end_info == 'model')
	{
		cg::load_model($class_name);
	}
	elseif ($end_info == 'module')
	{
		cg::load_module($class_name);
	}
	elseif ($end_info == 'controller')
	{
		if (count($class_info) == 3)
		{
			cg::load_controller($class_info[0] . '/' . $class_name);
		}
		else
		{
			cg::load_controller($class_name);
		}
	}
	else
	{
		cg::load_file($class_name . '.php');
	}
}