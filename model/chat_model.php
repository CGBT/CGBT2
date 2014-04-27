<?php
class chat_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('chat');
	}


	/**
	 *
	 * @return chat_model
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

	public function max_id()
	{
		$sql = "select max(id) from $this->table ";
		return $this->db()->get_value($sql);
	}
}