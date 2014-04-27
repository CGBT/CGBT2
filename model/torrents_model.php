<?php
class torrents_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents');
	}

	/**
	 *
	 * @return torrents_model
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

	public function delete_torrent($tid, $info_hash)
	{
		$cache_key = "infohash2tid_" . $info_hash;
		$this->cache()->delete($cache_key);
		$this->delete($tid);
	}

	public function check_bt_infohash_exists($bt_info_hash)
	{
		$sql = "select id from $this->table where bt_info_hash = '$bt_info_hash'";
		return $this->db()->get_value($sql);
	}

	public function check_infohash_exists($info_hash)
	{
		$sql = "select id from $this->table where info_hash = '$info_hash'";
		return $this->db()->get_value($sql);
	}

	public function check_id_exists($id)
	{
		$sql = "select count(1) from $this->table where id = '$id'";
		return $this->db()->get_count($sql);
	}

	public function get_ids_by_sql($where, $orderby, $start, $limit)
	{
		$cache_key = 'torrents_ids' . md5($where . $orderby . $start . $limit);
		$params = array(
			$where,
			$orderby,
			$start,
			$limit
		);
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $params, 60);
	}

	public function get_torrents_by_sql($where, $orderby, $start, $limit)
	{
		$cache_key = 'torrents_rows' . md5($where . $orderby . $start . $limit);
		$params = array(
			$where,
			$orderby,
			$start,
			$limit
		);
		$ids = $this->get_cache_data('_get_ids_by_sql', $cache_key, $params, 60);
		return $this->get_rows_by_ids($ids);
	}

	public function get_tid_by_info_hash($info_hash)
	{
		$cache_key = 'infohash2tid_' . $info_hash;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $info_hash, 0);
	}

	protected function _get_tid_by_info_hash($info_hash)
	{
		$sql = "select id from $this->table where info_hash='$info_hash' limit 1";
		return $this->db()->get_value($sql);
	}

	protected function _get_ids_by_sql($params)
	{
		list($where, $orderby, $start, $limit) = $params;
		if (!empty($where))
		{
			$where = 'where ' . $where;
		}
		if (!empty($orderby))
		{
			$orderby = 'order by ' . $orderby;
		}
		$sql = "select id from $this->table $where $orderby limit $start, $limit";
		$rows = $this->db()->get_rows($sql);

		$ids = array();
		foreach ($rows as $row)
		{
			$ids[] = $row['id'];
		}
		return $ids;
	}

	public function get_category_is_bozhongji_torrents()
	{
		$start_time = $this->timestamp - 43200;
		$sql = "select id,info_hash,name,username from $this->table where category='other' and type='播种机' and status = 0 and createtime < '$start_time'";
		return $this->db()->get_rows($sql);
	}

	public function get_torrents_count_by_sql($where)
	{
		$cache_key = 'torrents_count' . md5($where);
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $where, 60);
	}

	protected function _get_torrents_count_by_sql($where)
	{
		if (!empty($where))
		{
			$where = 'where ' . $where;
		}
		$sql = "select count(1) from $this->table $where";
		return $this->db()->get_count($sql);
	}

	public function get_myip_stats()
	{
		$sql = "select a.ip, a.ipv6, a.port, a.is_seeder, a.uid, count(1) as torrents_count, b.username, a.agent, sum(a.size) as size
		from cgbt_peers a left join cgbt_users b on a.uid = b.uid
		where a.ip in (
		'202.112.155.50',
		'202.112.155.49',
		'202.112.155.142',
		'202.112.155.154',
		'202.112.155.152',
		'202.112.159.234',
		'202.112.159.245',
		'202.112.159.246',
		'202.112.159.247',
		'202.112.159.248',
		'202.112.159.249',
		'202.112.159.250'
		)
		group by a.ip, a.ipv6, a.port, a.is_seeder, a.uid, b.username, a.agent
		order by a.ip, port";
		return $this->db()->get_rows($sql);
	}

	public function get_dupe_torrents($startdate)
	{
		$startdate = strtotime($startdate);

		$sql = "select a.id aid, b.id bid
                from cgbt_torrents a, cgbt_torrents b
                where a.size = b.size and a.id > b.id
                and a.createtime > '$startdate'
                order by a.createtime desc
                limit 100";
		return $this->db()->get_rows($sql);
	}

	public function bt_info_hash_is_empty_torrents()
	{
		$sql = "select id, filename from $this->table where bt_info_hash = '' order by id desc limit 200";
		return $this->db()->get_rows($sql);
	}
}