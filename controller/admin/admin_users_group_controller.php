<?php
class admin_users_group_controller extends admin_base_controller
{
	private $all_users_group;
	private $all_privileges;
	private $users_module;

	public function __construct()
	{
		parent::__construct();

		cg::load_module('users_module');
		$this->users_module = users_module::get_instance();
		$this->users_module->users_group_model->use_cache = false;
		$this->all_privileges = $this->users_module->all_privileges;
		$this->all_users_group = $this->users_module->get_all_users_group();
	}

	public function index_action()
	{
		$this->data['all_users_group'] = $this->all_users_group;
		$this->data['all_privileges'] = $this->all_privileges;

		$this->template_file = 'admin/users_group_list.php';
		$this->show();
	}

	public function edit_action()
	{
		$this->data['all_users_group'] = $this->all_users_group;
		$this->data['all_privileges'] = $this->all_privileges;
		foreach ($this->data['all_privileges'] as $key => $row)
		{
			if (!$row['is_front'])
			{
				unset($this->data['all_privileges'][$key]);
			}
		}
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->data['current_row'] = array();
		foreach ($this->all_users_group as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/users_group_edit.php';
		$this->show();
	}

	public function editadmin_action()
	{
		$this->data['all_users_group'] = $this->all_users_group;
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->data['current_row'] = array();
		foreach ($this->all_users_group as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/users_group_editadmin.php';
		$this->show();
	}

	public function updateadmin_action()
	{
		$id = intval($this->post['editid']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$arr_fields = array();
		$p = array();
		foreach ($this->post['chk_privileges'] as $privilege)
		{
			$p[$privilege] = 1;
		}
		$arr_fields['admin_privileges'] = json_encode($p);
		$arr_fields['id'] = $id;

		$this->users_module->users_group_model->update($arr_fields, $id);
		$this->redirect('/admin/users_group/index');
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

		$this->users_module->users_group_model->update($arr_fields, $id);
		$this->redirect('/admin/users_group/index');
	}

	private function get_params()
	{
		$arr_fields = array();
		$arr_fields['name'] = $this->post['name'];
		$arr_fields['color'] = $this->post['color'];
		$arr_fields['min_credits'] = $this->post['min_credits'];
		$arr_fields['max_credits'] = $this->post['max_credits'];

		$privileges = array();
		foreach ($this->all_privileges as $key => $row)
		{
			if (isset($this->post[$row['name_en']]))
			{
				$privileges[$row['name_en']] = $this->post[$row['name_en']];
			}
		}
		$arr_fields['privileges'] = json_encode($privileges);
		return $arr_fields;
	}

	public function insert_action()
	{
		$arr_fields = $this->get_params();
		$this->users_module->users_group_model->insert($arr_fields);
		$this->redirect('/admin/users_group/index');
	}
}