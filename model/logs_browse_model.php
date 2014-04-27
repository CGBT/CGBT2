<?php
class logs_browse_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_browse');
		$this->pk = 'id';
	}


	/**
	 *
	 * @return logs_browse_model
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
		$sql = "select tid from $this->table where $where order by id desc limit 100";
		return $this->db()->get_cols($sql);
	}

	public function get_browse_count_by_uid($uid)
	{
		$uid = intval($uid);
		$cache_key = 'browse_count_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 30);
	}

	public function _get_browse_count_by_uid($uid)
	{
		$uid = intval($uid);
		$starttime = $this->timestamp - 3600;
		$sql = "select count(1) from $this->table where createtime >= '$starttime' and uid = '$uid'";
		return $this->db()->get_count($sql);
	}
}