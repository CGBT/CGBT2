<?php
class peers_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('peers');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return peers_model
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

	public function get_peer($pid)
	{
		$peer = $this->find($pid);
		if (empty($peer))
		{
			return array();
		}
		//last_action字段有缓存，数据库内的值不是每次都更新的，但是每次都会写到缓存里，所以从缓存读取并覆盖从库里面取出来的值。
		/*
		$cache_key = 'peers_last_action_' . $pid;
		$last_action = $this->cache()->get($cache_key);
		if (empty($last_action))
		{
			//peer存在但是缓存key不存在，缓存key被剔除，概率较小。
			//last_action至少两个小时更新一次。
			//设为300，数值太大如果大于peer_clean_time会被清理。
			$peer['last_action'] = $this->timestamp - 300;
			$this->cache()->set($cache_key, $this->timestamp - 300);
		}
		else
		{
			//缓存里得值应该大于等于库里面的值
			if ($peer['last_action'] < $last_action)
			{
				$peer['last_action'] = $last_action;
			}
			else
			{
				//不可能
			}
		}
		*/
		return $peer;
	}

	public function get_peers_by_ids_from_db($ids)
	{
		$str_ids = implode(',', $ids);
		$sql = "select * from $this->table where id in ($str_ids)";
		return $this->db()->get_rows($sql);
	}

	public function get_peers_by_ids($ids)
	{
		if (empty($ids))
		{
			return array();
		}
		foreach ($ids as $key => $id)
		{
			if (empty($id))
			{
				unset($ids[$key]);
			}
		}
		if (count($ids) > 100)
		{
			return $this->get_peers_by_ids_from_db($ids);
		}
		$cache_keys = array();
		//$cache_keys_last_action = array();
		foreach ($ids as $pid)
		{
			$cache_keys[] = $this->table . '_' . $pid;
			//	$cache_keys_last_action[] = 'peers_last_action_' . $pid;
		}
		//@todo empty
		$rows = $this->cache()->get($cache_keys);
		//$rows_last_action = $this->cache()->get($cache_keys_last_action);
		foreach ($rows as $key => $row)
		{
			//memcache扩展返回的数组没有key
			//memcached扩展返回的数组有key，内容为空
			if (empty($row))
			{
				unset($rows[$key]);
			}
		}
		if (count($ids) != count($rows)) //缓存不存在，可能新增数据或者失效的缓存
		{
			$got_ids = array();
			foreach ((array)$rows as $row)
			{
				$got_ids[] = $row['id'];
			}
			$diff = array_diff($ids, $got_ids);
			foreach ($diff as $id)
			{
				$row = $this->_find($id);
				if (!empty($row))
				{
					$rows[$this->table . '_' . $id] = $row;
				}
			}
		}

		//last_action可能会不写库，只写缓存，所以要从缓存里取出来更新从库里面取出来的值
		//逻辑同get_peer方法
		/*
		foreach ($rows as $key => $row)
		{
			if (!isset($row['id']))
			{
				continue;
			}
			$cache_key = 'peers_last_action_' . $row['id'];
			if (isset($rows_last_action[$cache_key])) //存在则判断大小更新
			{
				if ($rows_last_action[$cache_key] > $row['last_action'])
				{
					$rows[$key]['last_action'] = $rows_last_action[$cache_key];
				}
			}
			else //不存在? 则写缓存
			{
				//与get_peer方法统一
				$rows[$key]['last_action'] = $this->timestamp - 300;
				$this->cache()->set($cache_key, $this->timestamp - 300);
			}
		}
		*/
		return $rows;
	}

	public function get_seed_size_by_user($uid)
	{
		$cache_key = 'seed_size_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 120);
	}

	protected function _get_seed_size_by_user($uid)
	{
		$sql = "select sum(size) from $this->table where uid = '$uid' and `left` = 0";
		return $this->db()->get_count($sql);
	}

	public function get_peer_user_count($is_seeder = 'default')
	{
		$cache_key = 'peer_user_count_' . $is_seeder;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $is_seeder, 300);
	}

	protected function _get_peer_user_count($is_seeder)
	{
		$sql = "select count(distinct uid) from $this->table";
		if ($is_seeder == '1')
		{
			$sql .= " where is_seeder = '1'";
		}
		elseif ($is_seeder == '0')
		{
			$sql .= " where is_seeder = '0'";
		}
		return $this->db()->get_count($sql);
	}

	public function get_seed_count_by_user($uid)
	{
		$cache_key = 'seed_count_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 120);
	}

	protected function _get_seed_count_by_user($uid)
	{
		$sql = "select count(1) from $this->table where uid = '$uid' and `left` = 0";
		return $this->db()->get_count($sql);
	}

	public function get_leech_count_by_user($uid)
	{
		$cache_key = 'leech_count_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 300);
	}

	protected function _get_leech_count_by_user($uid)
	{
		$sql = "select count(1) from $this->table where uid = '$uid' and `left` > 0";
		return $this->db()->get_count($sql);
	}

	public function get_seeders_uids_by_torrent($tid)
	{
		$cache_key = 'torrent_seeders_' . $tid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $tid, 60);
	}

	public function _get_seeders_uids_by_torrent($tid)
	{
		$sql = "select uid from $this->table where tid='$tid' and is_seeder = 1";
		return $this->db()->get_cols($sql);
	}

	public function get_leechers_uids_by_torrent($tid)
	{
		$cache_key = 'torrent_leechers_' . $tid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $tid, 60);
	}

	public function _get_leechers_uids_by_torrent($tid)
	{
		$sql = "select uid from $this->table where tid='$tid' and is_seeder = 0";
		return $this->db()->get_cols($sql);
	}

	public function get_ids_by_torrent($tid)
	{
		$cache_key = 'peers_tid_' . $tid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $tid, 3600);
	}

	public function _get_ids_by_torrent($tid)
	{
		$sql = "select id from $this->table where tid = '$tid' order by id";
		return $this->db()->get_cols($sql);
	}

	public function get_multi_ip_download_count($uid, $ip, $last_action)
	{
		$sql = "select count(1) from $this->table where uid = '$uid' and is_seeder = '0' and ip <> '$ip'  and last_action > '$last_action'";
		return $this->db()->get_count($sql);
	}

	public function get_self_peer($tid, $peer_id)
	{
		$cache_key = 'selfpeer_' . $tid . '_' . $peer_id;
		$params = array(
			$tid,
			$peer_id
		);
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $params, 43200);
	}

	protected function _get_self_peer($params)
	{
		list($tid, $peer_id) = $params;
		$sql = 'select id from ' . $this->table . " where tid = '$tid' and peer_id='$peer_id' limit 1";
		return $this->db()->get_value($sql);
	}

	public function update_peer($arr_fields, $pid)
	{
		$this->update($arr_fields, $pid);
		$tid = $arr_fields['tid'];
		$cache_key = 'peers_tid_' . $tid;
		$ids = $this->get_ids_by_torrent($tid);
		//$ids = $this->cache()->get($cache_key);
		if (empty($ids))
		{
			$ids = array();
			$ids[] = $pid;
			$this->cache()->set($cache_key, $ids, 3600);
		}
		else
		{
			if (in_array($pid, $ids))
			{
				//do nothing
			}
			else
			{
				$ids[] = $pid;
				$this->cache()->set($cache_key, $ids, 3600);
			}
		}
	}

	public function insert_peer($arr_fields)
	{
		$pid = $this->insert($arr_fields);
		$tid = $arr_fields['tid'];
		$peer_id = $arr_fields['peer_id'];
		$cache_key = 'selfpeer_' . $tid . '_' . $peer_id;
		$this->cache()->set($cache_key, $pid);

		$cache_key = 'peers_tid_' . $tid;
		$ids = $this->get_ids_by_torrent($tid);
		//$ids = $this->cache()->get($cache_key);
		if (empty($ids))
		{
			$ids = array();
			$ids[] = $pid;
			$this->cache()->set($cache_key, $ids, 3600);
		}
		else
		{
			$index = array_search($pid, $ids);
			if ($index === false)
			{
				$ids[] = $pid;
				$this->cache()->set($cache_key, $ids, 3600);
			}
		}
		/*
		$cache_key = 'peers_last_action_' . $pid;
		$this->cache()->set($cache_key, $arr_fields['last_action']);

		$cache_key = 'peers_last_action_updatetime_' . $pid;
		$this->cache()->set($cache_key, $this->timestamp);
		*/
		return $pid;
	}

	public function delete_peer($pid)
	{
		if (empty($pid))
		{
			return;
		}
		$row = $this->find($pid);
		if (empty($row))
		{
			return;
		}
		$tid = $row['tid'];
		$cache_key = 'selfpeer_' . $tid . '_' . $row['peer_id'];
		$this->cache()->delete($cache_key);

		$cache_key = 'peers_tid_' . $tid;
		$ids = $this->get_ids_by_torrent($tid);
		//$ids = $this->cache()->get($cache_key);
		if (empty($ids))
		{
			$ids = array();
		}
		else
		{
			$index = array_search($pid, $ids);
			if ($index !== false)
			{
				unset($ids[$index]);
				$this->cache()->set($cache_key, $ids, 3600);
			}
		}
		/*
		$cache_key = 'peers_last_action_' . $pid;
		$this->cache()->delete($cache_key);

		$cache_key = 'peers_last_action_updatetime_' . $pid;
		$this->cache()->delete($cache_key);
		*/
		$this->delete($pid);
	}

	public function delete_peers_by_torrent($tid)
	{
		//@todo 清理缓存
		$sql = "delete from $this->table where tid = '$tid'";
		$this->db()->query($sql);
		/*
		$pids = $this->get_ids_by_torrent($tid);
		$this->delete_peers($pids, $tid);
		*/
	}

	public function delete_peers($pids, $tid)
	{
		$peers_list_cache_key = 'peers_tid_' . $tid;
		$oldids = $this->get_ids_by_torrent($tid);
		//$oldids = $this->cache()->get($peers_list_cache_key);
		foreach ($pids as $pid)
		{
			if (empty($pid))
			{
				continue;
			}
			$row = $this->find($pid);
			$cache_key = 'selfpeer_' . $tid . '_' . $row['peer_id'];
			$this->cache()->delete($cache_key);

			$this->delete($pid);
			/*
			$cache_key = 'peers_last_action_' . $pid;
			$this->cache()->delete($cache_key);

			$cache_key = 'peers_last_action_updatetime_' . $pid;
			$this->cache()->delete($cache_key);
			*/
		}
		if (!empty($oldids))
		{
			$newids = array_diff($oldids, $pids);
			$this->cache()->set($peers_list_cache_key, $newids, 3600);
		}
	}

	public function delete_old_peers($clean_time)
	{
		$start_time = $this->timestamp - $clean_time;
		$sql = "select id from $this->table where last_action < '$start_time' limit 500";
		$ids = $this->db()->get_cols($sql);
		if (empty($ids))
		{
			return;
		}
		foreach ($ids as $id)
		{
			$this->cache()->delete($this->table . '_' . $id);
		}
		$id_str = implode(',', $ids);
		$sql = "delete from $this->table where id in ($id_str)";
		$this->db()->query($sql);
	}

	public function get_ids_range($where)
	{
		$sql = "select tid from $this->table where $where order by id desc";
		return $this->db()->get_cols($sql);
	}

	public function get_ids_by_where($where, $limit = '500', $orderby = '')
	{
		$sql = "select id from $this->table";
		if (!empty($where))
		{
			$sql .= " where $where";
		}
		if (!empty($orderby))
		{
			$sql .= " order by $orderby";
		}
		if (!empty($limit))
		{
			$sql .= " limit $limit";
		}
		return $this->db()->get_cols($sql);
	}

	public function get_peer_from_db($pid)
	{
		$peer = $this->_find($pid);
		if (empty($peer))
		{
			return array();
		}
		else
		{
			return $peer;
		}
	}

	public function get_tid_and_is_seeder_by_uid($uid)
	{
		$cache_key = 'get_tid_and_is_seeder_by_uid' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 120);
	}

	public function _get_tid_and_is_seeder_by_uid($uid)
	{
		$sql = "select tid, is_seeder from $this->table where uid = '$uid'";
		return $this->db()->get_rows($sql);
	}

	public function get_tids_by_uid($uid)
	{
		$cache_key = 'get_tids_by_uid_' . $uid;
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $uid, 120);
	}

	public function _get_tids_by_uid($uid)
	{
		$sql = "select tid from $this->table where uid = '$uid' and is_seeder = 1";
		return $this->db()->get_cols($sql);
	}
}