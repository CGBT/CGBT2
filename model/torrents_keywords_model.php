<?php
class torrents_keywords_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_keywords');
	}

	/**
	 *
	 * @return torrents_keywords_model
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

	public function get_ids_range($where)
	{
		$cache_key = 'keywords_' . md5($where);
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $where, 60);
	}

	public function _get_ids_range($where)
	{
		$sql = "select id from $this->table where $where";
		return $this->db()->get_cols($sql);
	}
}