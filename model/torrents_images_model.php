<?php
class torrents_images_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_images');
		$this->pk = 'aid';
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

	/**
	 *
	 * @return torrents_images_model
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

	public function delete_no_tid_images()
	{
		$sql = "delete from  $this->table where tid=0";
		$this->db()->query($sql);
	}
}