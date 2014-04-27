<?php
class torrents_attachments_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_attachments');
	}

	/**
	 *
	 * @return torrents_attachments_model
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



	public function get_subtitles($page, $pagesize)
	{
		$page = intval($page) <= 0 ? 1 : intval($page);
		$start = ($page - 1) * $pagesize;
		$sql = "select * from $this->table where type='subtitles' order by id desc limit $start, $pagesize";
		return $this->db()->get_rows($sql);
	}

	public function get_by_tid($tid)
	{
		$tid = intval($tid);
		$sql = "select * from $this->table where tid = '$tid'";
		return $this->db()->get_rows($sql);
	}

	public function update_tid_by_guid($tid, $guid)
	{
		$guid = $this->db()->real_escape_string($guid);
		if (strlen($guid) != 36)
		{
			return;
		}
		$sql = "update $this->table set tid = '$tid' where guid='$guid'";
		$this->db()->query($sql);
	}
}