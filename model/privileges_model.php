<?php
class privileges_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('privileges');
	}

	/**
	 *
	 * @return privileges_model
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

	public function delete_privileges($pk_id)
	{
		$cache_key = 'privileges';
		$this->cache()->delete($cache_key);
		$this->delete($pk_id);
	}

	public function insert_privileges($arr_fields)
	{
		$cache_key = 'privileges';
		$this->cache()->delete($cache_key);
		$this->insert($arr_fields);
	}

	public function update_privileges($arr_fields, $pk_id)
	{
		$cache_key = 'privileges';
		$this->cache()->delete($cache_key);
		$this->update($arr_fields, $pk_id);
	}

	public function get_all_privileges()
	{
		$cache_key = 'privileges';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	protected function _get_all_privileges()
	{
		$sql = "select * from $this->table order by is_front desc , orderid";
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $row)
		{
			if (empty($row['name_en']))
			{
				$row['name_en'] = $row['controller'] . '/' . $row['action'];
			}
			$data[$row['name_en']] = $row;
		}
		return $data;
	}
}