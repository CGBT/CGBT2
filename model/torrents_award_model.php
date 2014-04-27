<?php
class torrents_award_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_award');
	}

	/**
	 *
	 * @return torrents_award_model
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

	public function sum_award($tid)
	{
		$sql = "select sum(count) from $this->table where tid = '$tid'";
		return $this->db()->get_count($sql);
	}

	public function check_awarded($uid, $tid)
	{
		$sql = "select count(1) from $this->table where tid = '$tid' and uid = '$uid'";
		return $this->db()->get_count($sql);
	}

	public function insert_award($arr_fields)
	{
		$tid = $arr_fields['tid'];
		$this->cache()->delete('award_' . $tid);
		$this->insert($arr_fields);
	}

	public function get_award_by_tid($tid)
	{
		$cache_key = 'award_' . $tid;
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, $tid);
	}

	public function _get_award_by_tid($tid)
	{
		$sql = "select * from $this->table where tid = '$tid' ";
		return $this->db()->get_rows($sql);
	}
}