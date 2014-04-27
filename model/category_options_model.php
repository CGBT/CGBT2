<?php
class category_options_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('category_options');
	}

	/**
	 *
	 * @return category_options_model
	 */
	public static function get_instance($name)
	{
		static $instance;
		$name = __CLASS__;
		if (!isset($instance[$name]))
		{
			$instance[$name] = new $name();
		}
		return $instance[$name];
	}

	public function get_all_category_options()
	{
		$cache_key = 'category_options';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	protected function _get_all_category_options()
	{
		$sql = "select * from $this->table order by category,orderid";
		return $this->db()->get_rows($sql);
	}
}