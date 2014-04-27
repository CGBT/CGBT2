<?php
class logs_loginfail_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_loginfail');
	}

	/**
	 *
	 * @return logs_loginfail_model
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

	public function get_fail_count($login_fail_time, $ip, $username = '')
	{
		$dt = $this->timestamp - $login_fail_time * 60;
		if (empty($username))
		{
			$sql = "select count(1) from $this->table where ip = '$ip' and createtime > '$dt' and status = 0";
		}
		else
		{
			$sql = "select count(1) from $this->table where username = '$username' and createtime > '$dt'";
		}
		return $this->db()->get_count($sql);
	}
}