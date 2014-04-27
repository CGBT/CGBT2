<?php
class cg_config
{
	public $SYS_PATH;
	public $APP_PATH;
	public $APP_SUBDIR;
	public $APP_URL;
	public $AUTO_ROUTE;
	public $ERROR_404_DOCUMENT;
	public $ERROR_404_ROUTE;
	public $CONTROLLER_PATH;
	public $MODEL_PATH;
	public $CLASS_PATH;
	public $MODULE_PATH;
	public $config;

	static public function get($config_type)
	{
		return $this->config[$config_type];
	}

	/**
	 * @param config object $config
	 */
	public function init($config)
	{
		$this->config = $config;
		foreach ($config['system'] as $k => $v)
		{
			$this->{$k} = $v;
		}
		unset($this->config['system']);

		if ($this->APP_SUBDIR == NULL)
		{
			$this->APP_SUBDIR = '/';
		}
		if ($this->AUTO_ROUTE == NULL)
		{
			$this->AUTO_ROUTE = FALSE;
		}
		if ($this->MODEL_PATH == NULL)
		{
			$this->MODEL_PATH = $this->APP_PATH . 'model/';
		}
		if ($this->CONTROLLER_PATH == NULL)
		{
			$this->CONTROLLER_PATH = $this->APP_PATH . 'controller/';
		}
		if ($this->MODULE_PATH == NULL)
		{
			$this->MODULE_PATH = $this->APP_PATH . 'module/';
		}
		if ($this->CLASS_PATH == NULL)
		{
			$this->CLASS_PATH = $this->APP_PATH . 'class/';
		}
	}

	/**
	 * 判断调试模式
	 * @return boolean [description]
	 */
	public static function is_debug()
	{
		return ($_SERVER['SERVER_ADDR'] == '127.0.0.1');
	}
}
