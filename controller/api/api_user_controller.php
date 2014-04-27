<?php
class api_user_controller extends base_controller
{
	private $auth_key = "031fXxVutTt18ZJPW6g57gEQ44jfl58VxI7t7RI3TShgkNr215eKmSw3Z9Gm1XIg";
	private $users_module;

	public function index_action()
	{
		$this->check_username_action();
	}

	public function exists_action()
	{
		$username = isset($this->post['username']) ? $this->post['username'] : '';
		cg::load_module('users_module');
		$this->users_module = new users_module();
		$user = $this->users_module->get_by_username($username);
		if (empty($user))
		{
			echo '0';
		}
		else
		{
			echo '1';
		}
	}

	public function check_username_action()
	{
		$username = isset($this->post['username']) ? $this->post['username'] : '';
		$password = isset($this->post['password']) ? $this->post['password'] : '';
		$validate = isset($this->post['validate']) ? $this->post['validate'] : '';

		if ($validate != md5($username . $password . $this->auth_key))
		{
			die('1');
		}

		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();

		$data = $forums_discuzx_model->check_login($username, $password);
		if ($data["result"] != 0)
		{
			die('2');
		}
		else
		{
			cg::load_module('users_module');
			$users_module = new users_module();
			$user = $users_module->get_by_username($username);
			if (empty($user))
			{
				die('2');
			}
			else
			{
				echo $user['passkey'];
			}
		}
	}
}
