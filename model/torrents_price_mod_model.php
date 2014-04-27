<?php
class torrents_price_mod_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_price_mod');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return torrents_price_mod_model
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

	public function get_all()
	{
		$sql = "select * from $this->table where start_time <= '$this->timestamp' and end_time >= '$this->timestamp' and type = 'top' and enabled = '1' and status = '1' order by sort_price desc";
		$rows1 = $this->db()->get_rows($sql);
		$ids = array();
		foreach ($rows1 as $row)
		{
			$ids[] = $row['id'];
		}
		$str_ids = implode(',', $ids);
		$sql = "select * from $this->table";
		if (!empty($ids))
		{
			$sql .= " where id not in ($str_ids)";
		}
		$sql .= " order by start_time desc limit 200";
		$rows2 = $this->db()->get_rows($sql);
		return array_merge($rows1, $rows2);
	}

	public function get_top_3()
	{
		$cache_key = 'torrents_price_mod_top';
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, '', 0);
	}

	public function _get_top_3()
	{
		$sql = "select * from $this->table where start_time <= '$this->timestamp' and end_time >= '$this->timestamp' and type = 'top' and enabled = '1' and status = '1' order by sort_price desc, id limit 3";
		return $this->db()->get_rows($sql);
	}

	public function insert_mod($arr_fields)
	{
		$arr_fields['enabled'] = '0';
		if ($arr_fields['end_time'] >= $this->timestamp && $arr_fields['start_time'] <= $this->timestamp)
		{
			$arr_fields['enabled'] = '1';
		}
		$this->insert($arr_fields);
		$this->cache()->delete('torrents_price_mod_top');
	}

	public function delete_mod($id)
	{
		$sql = "update $this->table set status = -1 where id = '$id'";
		$this->db()->query($sql);
		$this->cache()->delete('torrents_price_mod_top');
	}

	public function cron_check_enabled()
	{
		$sql = "select id from $this->table where (start_time > '$this->timestamp' or end_time < '$this->timestamp') and enabled = '1'";
		$ids1 = $this->db()->get_cols($sql);
		$this->set_enabled_false($ids1);

		$sql = "select id from $this->table where start_time < '$this->timestamp' and end_time > '$this->timestamp' and enabled = '0' and status = '1'";
		$ids2 = $this->db()->get_cols($sql);
		$this->set_enabled_true($ids2);

		if (!empty($ids1) || !empty($ids2))
		{
			$this->cache()->delete('torrents_price_mod_top');
		}
	}

	public function set_enabled_true($ids)
	{
		if (empty($ids))
		{
			return;
		}
		$str_ids = implode(',', $ids);
		$sql = "update $this->table set enabled = '1' where id in ($str_ids)";
		$this->db()->query($sql);
	}

	public function set_enabled_false($ids)
	{
		if (empty($ids))
		{
			return;
		}
		$str_ids = implode(',', $ids);
		$sql = "update $this->table set enabled = '0' where id in ($str_ids)";
		$this->db()->query($sql);
	}
}