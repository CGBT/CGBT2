<?php
class search_keywords_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('search_keywords');
		$this->pk = 'id';
	}


	/**
	 *
	 * @return search_keywords_model
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
		$sql = "select * from $this->table";
		return $this->db()->get_rows($sql);
	}

	public function search($keyword)
	{
		if (ord($keyword) < 127)
		{
			$search_field = 'pinyin';
		}
		else
		{
			$search_field = 'keyword';
		}
		$keyword = $this->db()->real_escape_string($keyword);
		$sql = "select keyword from $this->table where $search_field like '$keyword%' order by count desc limit 15";
		return $this->db()->get_rows($sql);
	}
}