<?php
class users_group_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('users_group');
	}

	/**
	 *
	 * @return users_group_model
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

	public function get_all_users_group()
	{
		$cache_key = 'users_group';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	protected function _get_all_users_group()
	{
		$sql = "select * from $this->table order by orderid";
		return $this->db()->get_rows($sql);
	}
}