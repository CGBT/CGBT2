<?php
class logs_download_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_download');
	}


	/**
	 *
	 * @return logs_download_model
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

	public function get_download_tids_by_uid($uid)
	{
		$uid = intval($uid);
		$starttime = strtotime(date("Y-m-d"));
		$sql = "select tid from $this->table where createtime >= '$starttime' and uid = '$uid'";
		$tids = $this->db()->get_cols($sql);
		return array_unique($tids);
	}

	public function get_download_count_by_uid($uid)
	{
		$uid = intval($uid);
		$starttime = strtotime(date("Y-m-d"));
		$sql = "select tid from $this->table where createtime >= '$starttime' and uid = '$uid'";
		$tids = $this->db()->get_cols($sql);
		$tids = array_unique($tids);
		return count($tids);
	}

	public function get_ids_range($where)
	{
		$sql = "select tid from $this->table where $where";
		return $this->db()->get_cols($sql);
	}
}