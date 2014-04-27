<?php
class invite_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('invite');
	}

	public function get_row_by_invitecode($invitecode)
	{
		$invitecode = $this->db()->real_escape_string($invitecode);
		$sql = "select * from $this->table where code='$invitecode' limit 1";
		return $this->db()->get_row($sql);
	}

	/**
	 *
	 * @return invite_model
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

	public function get_award_uid()
	{
		$sql = "select uid, username, count(1) as c from $this->table
		where createtime >= 1387900800 and used_uid > 0 and createtime < FROM_UNIXTIME('2014-01-02')
		group by uid, username";
		return $this->db()->get_rows($sql);
	}
}