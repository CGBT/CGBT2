<?php
class logs_rate_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_rate');
	}

	/**
	 *
	 * @return logs_rate_model
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

	public function exists_uid_tid($tid, $uid)
	{
		$sql = "select count(1) from $this->table where tid='$tid' and uid='$uid'";
		return $this->db()->get_value($sql);
	}
}