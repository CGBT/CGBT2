<?php
class agentinfo_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('agentinfo');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return agentinfo_model
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

	public function get_all_agent()
	{
		$cache_key = 'all_agent';
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, '', 0);
	}

	protected function _get_all_agent()
	{
		$sql = 'select agent from ' . $this->table;
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $row)
		{
			$data[] = $row['agent'];
		}
		return $data;
	}

	public function insert_agent($agent)
	{
		$arr_fields = array();
		$arr_fields['agent'] = $agent;
		$this->insert($arr_fields);
		$this->cache()->delete('all_agent');
	}
}