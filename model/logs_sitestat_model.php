<?php
class logs_sitestat_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_sitestat');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return logs_sitestat_model
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

	public function get_index_site_stat()
	{
		$cache_key = 'index_site_stat';
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, '', 600);
	}

	public function _get_index_site_stat()
	{
		$sql = "select * from $this->table order by id desc limit 1";
		$row = $this->db()->get_row($sql);
		if (!empty($row))
		{
			$row = $this->convert_row($row);
		}
		return $row;
	}

	public function get_admin_index_data($type = 'same_hour')
	{
		$starttime = time() - 15 * 86400;
		$hour = date("H");
		if ($type == 'same_hour')
		{
			$sql = "select * from cgbt_logs_sitestat
			where createtime > $starttime and DATE_FORMAT(FROM_UNIXTIME(createtime),'%H') = $hour
			order by id desc";
		}
		else
		{
			$sql = "select * from $this->table order by id desc limit 200";
		}
		$rows = $this->db()->get_rows($sql);
		foreach ($rows as $key => $row)
		{
			$rows[$key] = $this->convert_row($row);
		}
		return $rows;
	}

	private function convert_row($row)
	{
		$row['total_size_text'] = funcs::mksize($row['total_size']);
		$row['active_size_text'] = funcs::mksize($row['active_size']);
		return $row;
	}
}
