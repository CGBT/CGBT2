<?php
class cg_controller
{
	public $get, $post, $cookie, $puts;
	public $params;
	public $extension;
	public $autoroute = TRUE;
	public $routematch;
	public $data, $template_engine, $template_dir;
	protected $start_time, $end_time;
	public $use_cache = true;
	protected $cache_type = '';
	protected $controller_name, $action_name, $module_name;
	public $timestamp, $ip;
	public $is_windows = false;

	public function __construct()
	{
		$this->start_time = $this->microtime_float();
		$this->cache_type = cg::config()->config['cache']['cache_type'];
		if (DIRECTORY_SEPARATOR == '\\')
		{
			$this->is_windows = true;
			$this->cache_type = 'memcache';
		}
		cg::load_core('cg_model');
		$this->get = &$_GET;
		$this->post = &$_POST;
		$this->cookie = &$_COOKIE;
		$this->cache = cg::cache();
		$this->timestamp = time();
		$this->ip = $this->get_ip();
	}

	/**
	 * 取外部参数，支持post&get, php version >= 5.20
	 * @param  string $key     key
	 * @param  const $filter  php filter const
	 * @param  const $input   php input const
	 * @param  string $default if no request data, default value
	 * @return return value
	 */
	public function request($key = '', $filter = FILTER_DEFAULT, $input = INPUT_POST, $default = '')
	{
		$data = $input == INPUT_POST ? $_POST : $_GET;
		//
		if (filter_has_var($input, $key))
		{
			$options = array(
				'options' => array(
					'default' => $default
				)
			);
			return filter_var($data[$key], $filter, $options);
		}
		//
		return $default;
	}

	public function init_put_vars()
	{
		parse_str(file_get_contents('php://input'), $this->puts);
	}

	/**
	 * This will be called before the actual action is executed
	 */
	public function beforeRun($resource, $action, $module_name = '')
	{
		$this->controller_name = $resource;
		$this->action_name = $action;
		$this->module_name = $module_name;
		$this->data['controller_name'] = $resource;
		$this->data['action_name'] = $action;
		$this->data['module_name'] = $module_name;
	}

	/**
	 *
	 * @param string $cache_type
	 * return cg_cache_memcache
	 */
	final public function cache($cache_type = '')
	{
		if (empty($cache_type))
		{
			$cache_type = $this->cache_type;
		}
		return cg::cache($cache_type);
	}

	/**
	 * This will be called if the action method returns null or success status(200 to 299 not including 204) after the actual action is executed
	 * @param mixed $routeResult The result returned by an action
	 */
	public function afterRun($routeResult)
	{
	}

	/**
	 * @return cg_view
	 */
	public function view()
	{
		return cg::view($this->template_engine, $this->template_dir);
	}

	public function lang($words, $args = '')
	{
		return $this->view()->lang($words, $args);
	}

	/**
	 * 获取缓存数据
	 *
	 * @param string	$method         获取数据的函数名
	 * @param string	$cache_key	           缓存key
	 * @param array     $params         函数参数
	 * @param int		$cache_time	          缓存时间
	 * @param string    $cache_type     缓存类型
	 * @return mixed
	 */
	final public function get_cache_data($method, $cache_key, $params = '', $cache_time = 0, $cache_type = '')
	{
		$data = false;
		$get_data_ing = '__GET_DATA_ING__';
		if (empty($cache_type))
		{
			$cache_type = $this->cache_type;
		}
		if ($this->use_cache)
		{
			$data = $this->cache($cache_type)->get($cache_key);
			if ($data === $get_data_ing)
			{
				$got_data = 0;
				for($i = 0; $i < 3; $i++)
				{
					usleep(200000);
					$data = $this->cache($cache_type)->get($cache_key);
					if ($data !== $get_data_ing)
					{
						$got_data = 1;
						break;
					}
				}
				if (!$got_data)
				{
					$data = false;
				}
			}
		}

		if ($data === false || is_null($data))
		{
			if ($this->use_cache)
			{
				$this->cache($cache_type)->set($cache_key, $get_data_ing, 1);
			}
			$data = $this->$method($params);
			if (empty($data))
			{
				$this->cache($cache_type)->set($cache_key, $data, 2);
			}
			else
			{

				$this->cache($cache_type)->set($cache_key, $data, $cache_time);
			}
		}
		return $data;
	}

	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	public function get_execute_time()
	{
		$this->end_time = $this->microtime_float();
		return sprintf('%.3f', 1000 * ($this->end_time - $this->start_time)) . 'ms';
	}

	public function get_ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
}
