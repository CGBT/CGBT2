<?php
class peers_connectable_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('peers_connectable');
	}

	/**
	 *
	 * @return peers_connectable_model
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

	public function get_to_check_ips()
	{
		$sql = "select id, ip, ipv6, port from $this->table where connectable = -1 order by id limit 100";
		return $this->db()->get_rows($sql);
	}

	public function delete_no_used_peers_ips()
	{
		//删除无用的数据
		$sql = "select a.id from cgbt_peers_connectable a
        		left join cgbt_peers b on a.ip = b.ip and a.ipv6=b.ipv6 and a.port = b.port
				where b.id is null";
		$ids = $this->db()->get_cols($sql);
		if (!empty($ids))
		{
			$str_ids = implode(',', $ids);
			$sql = "delete from cgbt_peers_connectable where id in ($str_ids)";
			$this->db()->query($sql);
		}
	}

	public function import_peers_ips()
	{
		//导入新数据
		$sql = "insert into cgbt_peers_connectable (ip, ipv6, port,createtime,checktime)
				select distinct a.ip, a.ipv6, a.port,unix_timestamp(),unix_timestamp() - 7200
				from cgbt_peers a left join cgbt_peers_connectable b on a.ip = b.ip and a.ipv6 = b.ipv6 and a.port = b.port
				where b.port is null";
		$this->db()->query($sql);
	}

	public function reset_connectable()
	{
		$time = $this->timestamp - 86400;
		$sql = "update $this->table set connectable = -1 where checktime < '$time'";
		$this->db()->query($sql);
	}

	public function set_connect($data)
	{
		if (empty($data))
		{
			return;
		}
		$time = time();
		foreach ($data as $connectable => $arr_ids)
		{
			$str_ids = implode(',', $arr_ids);
			$sql = "update $this->table set connectable = '$connectable', checktime = '$time' where id in ($str_ids)";
			$this->db()->query($sql);
		}
	}
}