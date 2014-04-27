<?php
class files_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_files');
	}

	/**
	 *
	 * @return files_model
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

	public function get_files_by_torrent($tid, $start, $limit)
	{
		$cache_key = 'torrents_files_' . $tid;
		$params = array(
			$tid,
			$start,
			$limit
		);
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $params);
	}

	public function _get_files_by_torrent($params)
	{
		list($tid, $start, $limit) = $params;
		$sql = "select * from $this->table where tid='$tid' order by id desc limit $start, $limit";
		return $this->db()->get_rows($sql);
	}

	public function insert_files($arr_files, $tid)
	{
		$sql = "insert into $this->table (tid, filename, size) values ";
		foreach ($arr_files as $file)
		{
			$sql .= "('$tid', '" . $this->db()->real_escape_string($file['filename']) . "', '$file[length]'),";
		}
		$sql = trim($sql, ',');
		$this->db()->query($sql);
	}
}