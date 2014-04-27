<?php
abstract class forums_model extends base_model
{
	public $db_config_name;
	public $table_prefix;
	public $charset;
	public $gbk_charset = false;
	public $uc_config;

	public function __construct($forums_config)
	{
		$this->db_config_name = $forums_config['db_config_name'];
		$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'];
		$this->charset = cg::config()->config['db'][$this->db_config_name]['charset'];
		if (strtolower($this->charset) == 'gbk')
		{
			$this->gbk_charset = true;
			cg::load_class('funcs');
		}
		$this->uc_config = $forums_config['uc_config'];
	}

	abstract protected function check_login($username, $password);

	abstract protected function synlogin($uid);

	abstract protected function synlogout();
	/*
	abstract protected function update_user();

	abstract protected function get_posts_count();

	abstract protected function get_posts();

	abstract protected function new_post();

	abstract protected function new_thread();

	abstract protected function get_user_info();

	abstract protected function send_msg();

	abstract protected function check_new_msg();

	abstract protected function get_thread_list();
	*/
}