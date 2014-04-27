<?php
class top_controller extends base_controller
{
	private $type;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
		$this->data['selected_nav'] = 'top';
	}

	public function index_action()
	{
		$this->users_action();
	}

	private function check_type($action)
	{
		$type = $this->params['type'];
		if ($action == 'users')
		{
			if (empty($type))
			{
				$type = 'total_credits';
			}
			$dict_users_type = array(
				'total_credits',
				'ratio',
				'extcredits1',
				'uploaded',
				'downloaded',
				'extcredits2'
			);
		}
		elseif ($action == 'torrents')
		{
			if (empty($type))
			{
				$type = 'seeder';
			}
			$dict_users_type = array(
				'seeder',
				'leecher',
				'complete'
			);
		}
		elseif ($action == 'school')
		{
			if (empty($type))
			{
				$type = 'users';
			}
			$dict_users_type = array(
				'users',
				'downloaded',
				'uploaded',
				'ratio',
				'extcredits1',
				'extcredits2'
			);
		}
		if (!in_array($type, $dict_users_type))
		{
			$this->showmessage('参数错误');
		}
		$this->type = $type;
	}

	public function users_action()
	{
		$this->check_type('users');
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$uids = $users_module->users_stat_model->top_uids($this->type);
		$uids = array_slice($uids, 0, 100);
		$this->data['users'] = $users_module->get_by_uids($uids);
		$this->show('top_users.php');
	}

	public function school_action()
	{
		$this->check_type('school');
	}

	public function torrents_action()
	{
		$this->check_type('torrents');
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$tids = $torrents_module->torrents_index_model->top_tids($this->type);
		$this->data['torrents'] = $torrents_module->get_torrents($tids);
		$this->show('top_torrents.php');
	}
}