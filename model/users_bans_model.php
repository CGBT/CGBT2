<?php
class users_bans_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('users_bans');
	}

	/**
	 *
	 * @return users_bans_model
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

	public function get_users_bans($uid)
	{
		$cache_key = 'users_bans_' . $uid;
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, $uid, 60);
	}

	protected function _get_users_bans($uid)
	{
		$sql = "select * from $this->table where uid = '$uid' and starttime <= '$this->timestamp' and endtime >= '$this->timestamp' and status = 1";
		return $this->db()->get_rows($sql);
	}

	public function get_all_bans()
	{
		$sql = "select * from $this->table order by status desc, id desc";
		return $this->db()->get_rows($sql);
	}

	public function clear_bans_status()
	{
		$sql = "update $this->table set status = 0 where endtime < $this->timestamp";
		$this->db()->query($sql);
		$cache_key = 'all_enabled_bans';
		$this->cache()->delete($cache_key);
	}

	public function get_all_enabled_bans()
	{
		$cache_key = 'all_enabled_bans';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	public function _get_all_enabled_bans()
	{
		$sql = "select * from $this->table where status = '1' order by id desc";
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $row)
		{
			$data[$row['uid']][$row['privileges_name']] = $row;
		}
		return $data;
	}

	public function get_ban($uid, $privileges)
	{
		$sql = "select * from $this->table where uid = '$uid' and starttime < '$this->timestamp'
		and endtime > '$this->timestamp' and status = '1' and privileges_name = '$privileges'
		order by id desc";
		return $this->db()->get_row($sql);
	}
}