<?php
class logs_search_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_search');
		$this->pk = 'id';
	}


	/**
	 *
	 * @return logs_search_model
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

	public function get_hot_keywords()
	{
		$cache_key = 'hot_keyword';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, '', 3600);
	}

	public function _get_hot_keywords()
	{
		$starttime = $this->timestamp - 7 * 86400;
		$sql = "select category, keyword, count(1) c from $this->table where createtime > '$starttime' group by category, keyword  having c > 10 order by c desc";
		$rows = $this->db()->get_rows($sql);
		$data = array();
		foreach ($rows as $key => $row)
		{
			$data[$row['category']][] = $row['keyword'];
		}
		return $data;
	}
}