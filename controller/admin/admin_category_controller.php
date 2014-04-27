<?php
class admin_category_controller extends admin_base_controller
{
	/**
	 *
	 * @var category_model
	 */
	public $category_model;

	public function __construct()
	{
		parent::__construct();
		cg::load_model('category_model');
		$this->category_model = new category_model();
		$this->category_model->use_cache = false;
	}

	public function index_action()
	{
		$all_category = $this->category_model->get_all_category();
		$this->data['current_row'] = array();
		$this->data['all_category'] = $all_category;
		$this->template_file = 'admin/category_index.php';
		$this->show();
	}

	public function rules_update_action()
	{
		$arr_fields = array(
			'rules' => $this->post['rules']
		);
		$category_id = intval($this->post['categoryid']);
		$this->category_model->update($arr_fields, $category_id);
		die('ok');
	}

	public function rules_action()
	{
		$all_category = $this->category_model->get_all_category();
		$this->data['all_category'] = $all_category;
		$this->data['current_category'] = isset($this->get['category']) ? $this->get['category'] : '';

		if (!empty($this->data['current_category']))
		{
			foreach ($all_category as $c)
			{
				if ($c['name_en'] == $this->data['current_category'])
				{
					$this->data['current_rules'] = $c['rules'];
					$this->data['current_id'] = $c['id'];
				}
			}
		}
		else
		{
			$this->data['current_rules'] = null;
		}

		$this->template_file = 'admin/category_rules.php';
		$this->show();
	}

	public function edit_action()
	{
		$all_category = $this->category_model->get_all_category();
		$this->data['all_category'] = $all_category;
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->data['current_row'] = array();
		foreach ($this->data['all_category'] as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/category_index.php';
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
		$this->category_model->update($arr_fields, $id);
		$this->redirect('/admin/category/index');
	}

	public function delete_action()
	{
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->category_model->delete($id);
		$this->redirect('/admin/category/index');
	}

	private function get_params()
	{
		$arr_fields = array();
		$arr_fields['name'] = $this->post['name'];
		$arr_fields['name_en'] = $this->post['name_en'];
		$arr_fields['icon'] = $this->post['icon'];
		$arr_fields['admins'] = $this->post['admins'];
		$arr_fields['forums_fid'] = $this->post['forums_fid'];
		$arr_fields['hot_keywords'] = $this->post['hot_keywords'];
		$arr_fields['admins'] = $this->post['admins'];
		$arr_fields['hot_keywords_count'] = intval($this->post['hot_keywords_count']);
		$arr_fields['app'] = $this->post['app'];
		return $arr_fields;
	}

	public function insert_action()
	{
		$arr_fields = $this->get_params();
		$this->category_model->insert($arr_fields);
		$this->redirect('/admin/category/index');
	}
}