<?php
class torrents_mod_model extends base_model
{
	private $all_data_cache_key = 'all_torrents_mod';

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_mod');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return torrents_mod_model
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

	public function insert_or_update_mod($arr_fields)
	{
		$arr_fields['enabled'] = '0';
		if ($arr_fields['end_time'] >= $this->timestamp && $arr_fields['start_time'] <= $this->timestamp)
		{
			$arr_fields['enabled'] = '1';
		}
		$exists_id = $this->exists_id($arr_fields['tid'], $arr_fields['type']);
		if ($exists_id > 0)
		{
			$this->update($arr_fields, $exists_id);
			$this->cache()->delete($this->all_data_cache_key);
		}
		else
		{
			$this->insert($arr_fields);
			$this->cache()->delete($this->all_data_cache_key);
		}
	}

	public function exists_id($tid, $type)
	{
		$sql = "select id from $this->table where tid = '$tid' and type = '$type' and status = '1'";
		return $this->db()->get_value($sql);
	}

	public function delete_mod($tid, $type)
	{
		$sql = "update $this->table set status = -1 where tid = '$tid' and type = '$type' and status = '1'";
		$this->db()->query($sql);
		$this->cache()->delete($this->all_data_cache_key);
	}

	public function cron_check_enabled()
	{
		$ids = array();
		$tids = array();
		$top_tids = array();
		$untop_tids = array();

		$sql = "select id, tid, type from $this->table where (start_time > '$this->timestamp' or end_time < '$this->timestamp') and enabled = '1' and status = '1'";
		$rows = $this->db()->get_rows($sql);
		foreach ($rows as $row)
		{
			$ids[] = $row['id'];
			$tids[] = $row['tid'];
			if ($row['type'] == 'top')
			{
				$untop_tids[] = $row['tid'];
			}
		}
		$this->set_enabled($ids, '0');

		$ids = array();
		$sql = "select id from $this->table where start_time < '$this->timestamp' and end_time > '$this->timestamp' and enabled = '0' and status = '1'";
		$rows = $this->db()->get_rows($sql);
		foreach ($rows as $row)
		{
			$ids[] = $row['id'];
			$tids[] = $row['tid'];
			if ($row['type'] == 'top')
			{
				$top_tids[] = $row['tid'];
			}
		}
		$this->set_enabled($ids, '1');
		$tids = array_unique($tids);
		if (!empty($top_tids) || !empty($untop_tids))
		{
			$this->cache()->delete($this->all_data_cache_key);
		}
		return array(
			'top_tids' => $top_tids,
			'untop_tids' => $untop_tids
		);
	}

	public function set_enabled($ids, $enabled = '1')
	{
		if (empty($ids))
		{
			return;
		}
		$str_ids = implode(',', $ids);
		$sql = "update $this->table set enabled = '$enabled' where id in ($str_ids)";
		$this->db()->query($sql);
	}

	public function get_by_tid($tid)
	{
		$data = $this->get_all_mod();
		if (isset($data[$tid]))
		{
			return $data[$tid];
		}
		else
		{
			return array();
		}
	}

	public function get_all_mod()
	{
		$cache_key = 'all_torrents_mod';
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, '', 0);
	}

	public function _get_all_mod()
	{
		$sql = "select * from $this->table where enabled = '1' and status = '1'";
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $row)
		{
			if ($row['start_time'] <= $this->timestamp && $row['end_time'] >= $this->timestamp)
			{
				$data[$row['tid']][$row['type']]['start_time'] = $row['start_time'];
				$data[$row['tid']][$row['type']]['end_time'] = $row['end_time'];
			}
		}
		return $data;
	}
}