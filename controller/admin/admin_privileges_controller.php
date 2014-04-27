<?php
class admin_privileges_controller extends admin_base_controller
{
	private $privileges_model;
	private $all_privileges;

	public function __construct()
	{
		parent::__construct();
		cg::load_model('privileges_model');
		$this->privileges_model = new privileges_model();
		$this->privileges_model->use_cache = false;
		$this->all_privileges = $this->privileges_model->get_all_privileges();
		$this->data['all_privileges'] = $this->all_privileges;
	}

	public function front_action()
	{
		$this->template_file = 'admin/privileges_front.php';
		$this->show();
	}

	public function back_action()
	{
		$this->template_file = 'admin/privileges_back.php';
		$this->show();
	}

	public function addfront_action()
	{
		$this->template_file = 'admin/privileges_addfront.php';
		$this->show();
	}

	public function addback_action()
	{
		$this->template_file = 'admin/privileges_addback.php';
		$this->show();
	}

	public function index_action()
	{
		$this->front_action();
	}

	public function edit_action()
	{
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->data['current_row'] = array();
		foreach ($this->data['all_privileges'] as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/privileges.php';
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
		$this->privileges_model->update_privileges($arr_fields, $id);
		if ($arr_fields['is_front'])
		{
			$this->redirect('/admin/privileges/front/');
		}
		else
		{
			$this->redirect('/admin/privileges/back/');
		}
	}

	public function delete_action()
	{
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->privileges_model->delete_privileges($id);
		$this->redirect('/admin/privileges/index');
	}

	private function get_params()
	{
		$arr_fields = array();
		$arr_fields['is_front'] = $this->post['is_front'];
		$arr_fields['orderid'] = $this->post['orderid'];
		$arr_fields['name'] = $this->post['name'];

		if ($arr_fields['is_front'])
		{
			$arr_fields['name_en'] = $this->post['name_en'];
			$arr_fields['type'] = $this->post['type'];
			$arr_fields['default_value'] = $this->post['default_value'];
			$arr_fields['vip_default_value'] = $this->post['vip_default_value'];
			$arr_fields['admin_default_value'] = $this->post['admin_default_value'];
			$arr_fields['tip'] = $this->post['tip'];
			$arr_fields['can_ban'] = $this->post['can_ban'];
			$arr_fields['status'] = $this->post['status'];
		}
		else
		{
			$arr_fields['name_en'] = $this->post['controller'] . '/' . $this->post['action'];
			$arr_fields['type'] = 'yes_no';
			$arr_fields['default_value'] = '0';
			$arr_fields['vip_default_value'] = '0';
			$arr_fields['admin_default_value'] = '0';
			$arr_fields['tip'] = $this->post['tip'];
			$arr_fields['can_ban'] = '0';
			$arr_fields['status'] = $this->post['status'];
			$arr_fields['controller'] = $this->post['controller'];
			$arr_fields['action'] = $this->post['action'];
		}

		/*
		$o = $this->post['options'];
		$o = str_replace("\r", "\n", $o);
		$o = str_replace("\n\n", "\n", $o);
		$arr_fields['options'] = $o;
		*/
		return $arr_fields;
	}

	public function insert_action()
	{
		$arr_fields = $this->get_params();
		$this->privileges_model->insert_privileges($arr_fields);
		if ($arr_fields['is_front'])
		{
			$this->redirect('/admin/privileges/front/');
		}
		else
		{
			$this->redirect('/admin/privileges/back/');
		}
	}
}