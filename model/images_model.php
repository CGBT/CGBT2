<?php
class images_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('images');
	}

	/**
	 *
	 * @return images_model
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

	public function get_images($start, $limit)
	{
		$sql = "select * from $this->table where newpath = '' order by id limit $start, $limit";
		return $this->db()->get_rows($sql);
	}
}