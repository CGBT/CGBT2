<?php
class users_stat_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('users_stat');
		$this->pk = 'uid';
	}

	/**
	 *
	 * @return users_stat_model
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

	public function update_last_access($uid, $ip)
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "update_last_access_$uid";
		$params = array(
			$uid,
			$ip
		);
		return $this->get_cache_data($function_name, $cache_key, $params, 1700);
	}

	public function _update_last_access($params)
	{
		list($uid, $ip) = $params;
		$arr_fields = array();
		$arr_fields['last_access_both'] = $this->timestamp;
		if (stripos($ip, ':') !== false)
		{
			$arr_fields['last_access_ipv6'] = $this->timestamp;
			$arr_fields['last_ipv6'] = $ip;
		}
		else
		{
			$arr_fields['last_access'] = $this->timestamp;
			$arr_fields['last_ip'] = $ip;
		}
		$this->update($arr_fields, $uid);
	}

	public function get_user_group_count()
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "user_group_count";
		return $this->get_cache_data($function_name, $cache_key, '', 7200);
	}

	public function _get_user_group_count()
	{
		$sql = "select class,count(1) as count from $this->table group by class";
		return $this->db()->get_rows($sql);
	}

	public function get_staff_uids()
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "staff_uids";
		return $this->get_cache_data($function_name, $cache_key, '', 7200);
	}

	public function _get_staff_uids()
	{
		$sql = "select uid from $this->table where class > 10 order by class desc, uid";
		return $this->db()->get_cols($sql);
	}

	public function add_credits($uid, $count, $field)
	{
		$sql = "update $this->table set $field = $field + $count where uid = '$uid'";
		$this->db()->query($sql);

		$this->push_cache_status();
		$this->db()->push_slave_status();
		$this->find($uid);
		$this->db()->pop_slave_status();
		$this->pop_cache_status();
	}

	public function fail_newbie_task_uids($starttime, $endtime, $uploaded, $extcredits1)
	{
		$sql = "select uid from $this->table where createtime > '$starttime' and createtime < '$endtime' and extcredits1 < '$extcredits1' and uploaded < '$uploaded'";
		return $this->db()->get_cols($sql);
	}

	public function get_pass_kaohe_users($uids, $uploaded, $downloaded, $extcredits1)
	{
		$G = 1024 * 1024 * 1024;
		$uploaded = $uploaded * $G;
		$downloaded = $downloaded * $G;
		$sql = "select uid,username,createtime, case when uploaded > '$uploaded' and downloaded > '$downloaded' and extcredits1 > '$extcredits1'
		          then 1 else 0 end as all_pass from $this->table where uid in ($uids)";
		return $this->db()->get_rows($sql);
	}

	public function tracker_update($set, $uid)
	{
		$sql = "update $this->table set $set where uid='$uid'";
		$this->db()->query($sql);

		$this->push_cache_status();
		$this->db()->push_slave_status();
		$this->find($uid);
		$this->db()->pop_slave_status();
		$this->pop_cache_status();
	}

	public function update_total_credits()
	{
		$sql = "update $this->table set total_credits =
		(uploaded/1024/1024/1024)/(ln((uploaded/1024/1024/1024)+2)+6) -
		(downloaded/1024/1024/1024)/155*ln((downloaded/1024/1024/1024)+1) +
		total_upload_times*1.3 +
		total_torrent_size/1024/1024/1024/4 +
		5000*(1-exp(-(extcredits1)/30000))";
		$this->db()->query($sql);
	}

	public function top_ratio_uids()
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "top_ratio_uids";
		return $this->get_cache_data($function_name, $cache_key, '', 7100);
	}

	public function _top_ratio_uids()
	{
		$sql = "select uid from $this->table where downloaded>20*1024*1024*1024 order by (uploaded+uploaded2)/downloaded desc limit 10000";
		return $this->db()->get_cols($sql);
	}

	public function top_uids($type)
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "top_uids_$type";
		return $this->get_cache_data($function_name, $cache_key, $type, 7100);
	}

	public function _top_uids($type)
	{
		$sql = "select uid from $this->table order by $type desc limit 10000";
		return $this->db()->get_cols($sql);
	}

	public function clear_hour_uploaded_downloaded()
	{
		$hour = date("H", time() - 3600);
		$date = date("Ymd", time() - 3600);
		$sql = "select uid from $this->table order by hour_uploaded desc limit 100";
		$uids1 = $this->db()->get_cols($sql);
		$sql = "select uid from $this->table order by hour_downloaded desc limit 100";
		$uids2 = $this->db()->get_cols($sql);

		$uids = array_merge($uids1, $uids2);
		$uids = array_unique($uids);
		$str_uids = implode(',', $uids);

		$sql = "insert into cgbt_logs_hour_stat (uid,date,hour,createtime,uploaded,downloaded,uploaded2,downloaded2)
		select uid,'$date','$hour','$this->timestamp',hour_uploaded,hour_downloaded,hour_uploaded2,hour_downloaded2 from $this->table
		where uid in ($str_uids)";
		$this->db()->query($sql);

		$sql = "update $this->table set hour_uploaded = 0, hour_uploaded2 = 0, hour_downloaded = 0, hour_downloaded2 = 0";
		$this->db()->query($sql);
	}

	public function clear_today_uploaded_downloaded()
	{
		$date = date("Ymd", time() - 86400);
		$sql = "insert into cgbt_logs_day_stat (uid,date,createtime,uploaded,downloaded,uploaded2,downloaded2)
		select uid,'$date','$this->timestamp',today_uploaded,today_downloaded,today_uploaded2,today_downloaded2 from $this->table
		where today_uploaded+today_downloaded+today_uploaded2+today_downloaded2>0";
		$this->db()->query($sql);
		$sql = "update $this->table set today_uploaded = 0, today_uploaded2 = 0, today_downloaded = 0, today_downloaded2 = 0";
		$this->db()->query($sql);
	}
}