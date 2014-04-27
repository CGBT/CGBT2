<?php
class favorite_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('favorite');
	}

	/**
	 *
	 * @return favorite_model
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

	public function get_ids_range($where)
	{
		$sql = "select tid from $this->table where $where";
		return $this->db()->get_cols($sql);
	}

	public function get_favorite($tid, $uid)
	{
		$sql = "select * from $this->table where uid = '$uid' and tid = '$tid' limit 1";
		return $this->db()->get_row($sql);
	}

	public function get_favorite_count($uid)
	{
		$sql = "select count(1) from $this->table where uid = '$uid'";
		return $this->db()->get_count($sql);
	}
}