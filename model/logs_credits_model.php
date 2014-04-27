<?php
class logs_credits_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_credits');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return logs_credits_model
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

	public function last_money2uploaded_time($uid)
	{
		$sql = "select createtime from $this->table where uid = '$uid' and action = 'money2uploaded2' order by id desc limit 1";
		return $this->db()->get_value($sql);
	}

	public function last_extcredits12uploaded_time($uid)
	{
		$sql = "select createtime from $this->table where uid = '$uid' and action = 'extcredits12uploaded2' order by id desc limit 1";
		return $this->db()->get_value($sql);
	}
	public function get_latest_rows()
	{
		$sql = "select * from $this->table order by id desc limit 100";
		return $this->db()->get_rows($sql);
	}

	public function get_credits_rows($start = '', $end = '', $username = '', $count = '', $order = '')
	{
		$sql = "select $username ,createtime, field ,sum(count) as total from $this->table ";
		$start = strtotime($start);
		$end = strtotime($end);
		$start = !empty($start) ? $start : strtotime(date("Y-m-1"));
		$firstdaystr = date("Y-m-01");
		$end = !empty($end) ? $end : strtotime(date('Y-m-d 00:00:00', strtotime("$firstdaystr +1 month ")));
		$sql .= " where  '$start' < createtime and createtime < '$end'  and count $count and field = 'extcredits1' and  action= 'torrnets_award' group by $username order by total $order limit 100";
		return $this->db()->get_rows($sql);
	}
}
