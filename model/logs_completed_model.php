<?php
class logs_completed_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_completed');
		$this->pk = 'id';
	}


	/**
	 *
	 * @return logs_completed_model
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

	public function get_uids_by_tid($tid)
	{
		$tid = intval($tid);
		$cache_key = 'completed_users_' . $tid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $tid, 120);
	}

	public function _get_uids_by_tid($tid)
	{
		$tid = intval($tid);
		$sql = "select uid, createtime from $this->table where tid = '$tid' order by id desc limit 100";
		return $this->db()->get_rows($sql);
	}

	public function get_count_by_uid($uid)
	{
		$cache_key = 'completed_count_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 1800);
	}

	public function _get_count_by_uid($uid)
	{
		$uid = intval($uid);
		$sql = "select count(1) from $this->table where uid = '$uid'";
		return $this->db()->get_count($sql);
	}

	public function get_ids_range($where)
	{
		$sql = "select tid from $this->table where $where order by id desc";
		return $this->db()->get_cols($sql);
	}
}