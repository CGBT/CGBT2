<?php
class logs_actions_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_actions');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return logs_actions_model
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

	public function get_log_rows($start = '', $end = '', $username = '' ,$is_moderator='',$having='')
	{
		$sql = "select username , action ,count(action) as total from $this->table ";
		$start = strtotime($start);
		$end = strtotime($end);
		$start = !empty($start) ? $start : strtotime(date("Y-m-1"));
		$firstdaystr = date("Y-m-01");
		$end = !empty($end) ? $end : strtotime(date('Y-m-d 00:00:00', strtotime("$firstdaystr +1 month ")));
		if (!empty($username))
		{
			$sql .= " where  username='$username'  and ";
		}
		else
		{
			$sql .= 'where';
		}
		$sql .= " '$start' < createtime and createtime < '$end' and $is_moderator  group by username , action";
		if (!empty($having))
		{
			$sql.= $having;
		}
		return $this->db()->get_rows($sql);
	}
}
