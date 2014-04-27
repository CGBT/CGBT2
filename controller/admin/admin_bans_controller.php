<?php
class admin_bans_controller extends admin_base_controller
{
	private $all_bans;
	private $all_privileges;
	private $users_bans_model;

	public function __construct()
	{
		parent::__construct();
		cg::load_model('users_bans_model');
		$this->users_bans_model = users_bans_model::get_instance();
		$this->data['all_bans'] = $this->users_bans_model->get_all_bans(); //@todo pager
		$this->get_all_privileges();
		foreach ($this->data['all_bans'] as $key => $row)
		{
			$this->data['all_bans'][$key]['privileges_name_cn'] = $this->data['all_privileges'][$row['privileges_name']];
		}
	}

	private function get_all_privileges()
	{
		cg::load_model('privileges_model');
		$privileges_model = privileges_model::get_instance();
		$this->all_privileges = $privileges_model->get_all_privileges();
		foreach ($this->all_privileges as $key => $privileges)
		{
			if (!$privileges['can_ban'])
			{
				//unset($this->all_privileges[$key]);
				continue;
			}
			$this->data['all_privileges'][$privileges['name_en']] = $privileges['name'];
		}
	}

	public function index_action()
	{
		$this->template_file = 'admin/bans_list.php';
		$this->show();
	}

	public function add_action()
	{
		$this->data['current_row'] = array();
		$this->template_file = 'admin/bans_edit.php';
		$this->show();
	}

	public function edit_action()
	{
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->data['current_row'] = array();
		foreach ($this->data['all_bans'] as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/bans_edit.php';
		$this->show();
	}

	public function update_action()
	{
		$arr_fields = $this->get_params();
		$id = intval($this->post['editid']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$arr_fields['id'] = $id;
		$row_old_ban = $this->users_bans_model->find($id);
		$this->users_bans_model->update($arr_fields, $id);
		if ($arr_fields['status'] == '1' && $arr_fields['privileges_name'] == 'login')
		{
			$this->ban_user($arr_fields['uid'], '0');
		}
		elseif ($arr_fields['status'] == '0' && $row_old_ban['privileges_name'] == 'login')
		{
			$this->ban_user($arr_fields['uid'], '1');
		}
		$cache_key = 'all_enabled_bans';
		$this->cache()->delete($cache_key);
		$this->redirect('/admin/bans/index');
	}

	private function get_params()
	{
		$arr_fields = array();
		$arr_fields['username'] = $this->post['username'];
		$arr_fields['starttime'] = strtotime($this->post['starttime']);
		$arr_fields['endtime'] = strtotime($this->post['endtime']);
		$arr_fields['reason'] = $this->post['reason'];
		$arr_fields['memo'] = $this->post['memo'];
		$arr_fields['status'] = $this->post['status'];
		$arr_fields['privileges_name'] = $this->post['privileges_name'];
		$arr_fields['privileges_value'] = $this->post['privileges_value'];

		cg::load_model('users_model');
		$users_model = users_model::get_instance();
		$arr_fields['uid'] = $users_model->get_uid_by_username($arr_fields['username']);
		if (empty($arr_fields['uid']))
		{
			die('username not exists');
		}
		$arr_fields['updatetime'] = $this->timestamp;
		$arr_fields['operator'] = $this->username;
		$arr_fields['operator_uid'] = $this->uid;

		return $arr_fields;
	}

	private function ban_user($uid, $enable = '0')
	{
		$arr_fields['enabled'] = $enable;
		cg::load_model('users_model');
		$users_model = users_model::get_instance();
		$users_model->update($arr_fields, $uid);
	}

	public function insert_action()
	{
		$arr_fields = $this->get_params();
		$arr_fields['createtime'] = $this->timestamp;
		$this->users_bans_model->insert($arr_fields);
		if ($arr_fields['status'] == '1' && $arr_fields['privileges_name'] == 'login')
		{
			$this->ban_user($arr_fields['uid'], 0);
		}
		$cache_key = 'all_enabled_bans';
		$this->cache()->delete($cache_key);
		$this->redirect('/admin/bans/index');
	}
}