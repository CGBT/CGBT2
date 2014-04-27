<?php
class user_auth
{
	public $auth_type = 'internal';

	public function __construct($auth_type = '')
	{
		if (!empty($auth_type))
		{
			$this->auth_type = $auth_type;
		}
	}

	/**
	 * $data['result'] = -1; //user not exists;
	 * $data['result'] = -2; //user password not correct
	 * $data['result'] = -3; //other error
	 * $data['result'] =  0; //success
	 *
	 * @param string $username
	 * @param string $password
	 * @return array $result
	 */
	public function check_login($username, $password)
	{
		$data = $this->{'check_login_' . $this->auth_type}($username, $password);
		return $data;
	}

	private function check_login_internal($username, $password)
	{
		$data = array();
		cg::load_module('users_module');
		$users_module = new users_module();
		$row = $users_module->get_by_username($username);
		if (empty($row))
		{
			$data['result'] = -1;
		}
		else
		{
			$secret = pack("H*", $row['salt']);
			if ($row['password'] != md5($secret . $password . $secret))
			{
				$data['result'] = -2;
			}
			else
			{
				$data['result'] = 0;
				$data['uid'] = $row['uid'];
				$data['email'] = $row['email'];
			}
		}
		return $data;
	}

	private function check_login_discuzx($username, $password)
	{
		cg::load_model('forums_discuzx_model');
		$forums = forums_discuzx_model::get_instance();
		return $forums->check_login($username, $password);
	}

	public function syn_login_discuzx($uid)
	{
		cg::load_model('forums_discuzx_model');
		$forums = forums_discuzx_model::get_instance();
		return $forums->synlogin($uid);
	}

	public function syn_logout_discuzx()
	{
		cg::load_model('forums_discuzx_model');
		$forums = forums_discuzx_model::get_instance();
		return $forums->synlogout();
	}

	private function check_login_phpwind()
	{
		;
	}
}