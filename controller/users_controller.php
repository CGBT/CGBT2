<?php
class users_controller extends base_controller
{

	/**
	 *
	 * @var users_module
	 */
	private $users_module;

	public function __construct()
	{
		parent::__construct();

		cg::load_module('users_module');
		$this->users_module = users_module::get_instance();
	}

	public function index_action()
	{
		$this->check_login();
		$username = $this->params['username'];
		$username = urldecode($username);
		if (strlen($username) >= 30 || strpos($username, ' '))
		{
			$this->showmessage('参数错误', true);
		}
		if (!preg_match('/^[a-z0-9@\_]+$/i', $username))
		{
			$this->showmessage('参数错误', true);
		}
		$uid = $this->users_module->users_model->get_uid_by_username($username);
		$this->redirect("/user/$uid/");
		die();
	}
}