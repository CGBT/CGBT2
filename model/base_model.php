<?php
class base_model extends cg_model
{
	public $setting = array();

	public function __construct()
	{
		parent::__construct();
		$this->db_config_name = 'newcgbt';
		$this->pk = 'id';
		$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'];
	}

	public function table($table)
	{
		return $this->table_prefix . $table;
	}

	public function get_db_stat_data()
	{
		return $this->db()->get_db_stat_data();
	}
	public function get_all_sql()
	{
		return $this->db()->get_query_sql();
	}

	public function get_sql_count()
	{
		return $this->db()->get_query_count();
	}

	public function get_sleep_times()
	{
		return self::$sleep_times;
	}
}