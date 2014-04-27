<?php
class url_queue_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('images_url_queue');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return url_queue_model
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

	public function url_md5_exists($url_md5)
	{
		$sql = "select count(1) from $this->table where url_md5 = '$url_md5'";
		return $this->db()->get_count($sql);
	}

	public function get_queue_ids($pagesize)
	{
		$pagesize = intval($pagesize);
		$sql = "select id from $this->table where fetched = 0 and fetched_times < 3 order by id desc limit $pagesize";
		return $this->db()->get_cols($sql);
	}

	public function get_tobereplaced_ids($pagesize)
	{
		$pagesize = intval($pagesize);
		$sql = "select id from $this->table where fetched = 1 and replaced = 0 order by id desc limit $pagesize";
		return $this->db()->get_cols($sql);
	}

	public function set_replaced($ids)
	{
		$str_ids = implode(',', $ids);
		$sql = "update $this->table set replaced = 1 where id in ($str_ids)";
		$this->db()->query($sql);
	}

	public function set_fetched_timeout()
	{
		$dt = $this->timestamp - 300;
		$sql = "update $this->table set fetched = 0 where fetched = 2 and last_fetch_time < '$dt'";
		$this->db()->query($sql);
	}

	public function set_not_fetched($id)
	{
		$arr_fields = array(
			'fetched' => 0
		);
		$this->update($arr_fields, $id);
	}

	public function set_fetched($id)
	{
		$arr_fields = array(
			'fetched' => 1
		);
		$this->update($arr_fields, $id);
	}

	public function set_fetching($ids)
	{
		$str_ids = implode(',', $ids);
		$sql = "update $this->table set fetched = 2, fetched_times = fetched_times + 1, last_fetch_time = '$this->timestamp' where id in ($str_ids)";
		$this->db()->query($sql);
	}
}
