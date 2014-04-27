<?php
class setting_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('setting');
	}

	/**
	 *
	 * @return setting_model
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

	public function set($skey, $svalue = '')
	{
		if (is_array($skey))
		{
			foreach ($skey as $key => $value)
			{
				$arr_fields = array();
				$arr_fields['svalue'] = $value;
				$this->db()->update($this->table, $arr_fields, "`skey` = '$key'");
			}
		}
		else
		{
			$arr_fields = array();
			$arr_fields['svalue'] = $svalue;
			$this->db()->update($this->table, $arr_fields, "`skey` = '$skey'");
		}
		$this->push_cache_status();
		$this->get_all();
		$this->pop_cache_status();
	}

	public function get($key)
	{
		$data = $this->get_all();
		return $data[$key];
	}

	public function get_all()
	{
		$cache_key = 'setting';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	protected function _get_all()
	{
		$sql = "select * from $this->table";
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $row)
		{
			$data[$row['skey']] = $row['svalue'];
		}
		return $data;
	}
}