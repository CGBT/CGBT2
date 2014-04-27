<?php
class logs_modgroup_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_modgroup');
	}

	/**
	 *
	 * @return logs_modgroup_model
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
