<?php
class logs_memcached_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_memcached');
		$this->pk = 'id';
	}


	/**
	 *
	 * @return logs_memcached_model
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
}