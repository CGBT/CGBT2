<?php
class torrents_descr_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_descr');
	}

	/**
	 *
	 * @return torrents_descr_model
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
	//@todo 如果一个种子多个描述之后，需要修改cron更新图片的脚本
	public function get_first_tids_id($tid)
	{
		$sql = "select id from $this->table where tid = '$tid' order by id desc limit 1";
		return intval($this->db()->get_value($sql));
	}

	public function get_ids($start, $limit)
	{
		$sql = "select id from $this->table order by id desc limit $start, $limit";
		return $this->db()->get_cols($sql);
	}

	public function get_queue_ids($limit)
	{
		$sql = "select id from $this->table where url_queued = 0 order by id desc limit $limit";
		return $this->db()->get_cols($sql);
	}
}