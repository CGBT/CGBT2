<?php
class admin_category_options_controller extends admin_base_controller
{
	/**
	 *
	 * @var category_model
	 */
	public $category_model;
	const options_fields_count = 10;

	public function __construct()
	{
		parent::__construct();
		cg::load_model('category_model');
		$this->category_model = new category_model();

		cg::load_model('category_options_model');
		$this->category_options_model = new category_options_model();

		$this->category_model->use_cache = false;
		$this->category_options_model->use_cache = false;

		$this->data['all_category'] = $this->category_model->get_all();
		$this->data['current_category'] = isset($this->get['category']) ? $this->get['category'] : '';
		$this->get_options($this->data['current_category']);
		$this->data['current_row'] = array();

		$this->data['dict_bind_field'] = $this->get_bind_field();
	}

	public function index_action()
	{
		$this->template_file = 'admin/category_options_index.php';
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
		foreach ($this->data['all_category_options'] as $row)
		{
			if ($row['id'] == $id)
			{
				$this->data['current_row'] = $row;
			}
		}
		$this->template_file = 'admin/category_options_index.php';
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
		$this->category_options_model->update($arr_fields, $id);
		$this->redirect('/admin/category_options/index');
	}

	public function delete_action()
	{
		$id = intval($this->get['id']);
		if ($id <= 0)
		{
			$this->showmessage('id error');
		}
		$this->category_options_model->delete($id);
		$this->redirect('/admin/category_options/index');
	}

	private function get_options($category)
	{
		if (empty($category))
		{
			$this->data['all_category_options'] = $this->category_options_model->get_all_category_options();
		}
		else
		{
			$this->data['all_category_options'] = $this->data['all_category'][$category]['options'];
		}
	}

	private function get_params()
	{
		$arr_fields = array();
		$arr_fields['category'] = $this->post['category'];
		$arr_fields['orderid'] = $this->post['orderid'];
		$arr_fields['title'] = $this->post['title'];
		$arr_fields['bind_field'] = $this->post['bind_field'];
		$arr_fields['variable'] = $this->post['variable'];
		$arr_fields['variable_search'] = $this->post['variable_search'];
		$arr_fields['type'] = $this->post['type'];
		$o = $this->post['options'];
		$o = str_replace("\r", "\n", $o);
		$o = str_replace("\n\n", "\n", $o);
		$arr_fields['options'] = $o;
		$arr_fields['insearch_item'] = isset($this->post['insearch_item']) ? '1' : '0';
		$arr_fields['insearch_keyword'] = isset($this->post['insearch_keyword']) ? '1' : '0';
		$arr_fields['intitle'] = isset($this->post['intitle']) ? '1' : '0';
		$arr_fields['indetail'] = isset($this->post['indetail']) ? '1' : '0';
		$arr_fields['intag'] = isset($this->post['intag']) ? '1' : '0';
		$arr_fields['required'] = $this->post['required'];
		$arr_fields['status'] = $this->post['status'];
		$arr_fields['tip'] = $this->post['tip'];
		return $arr_fields;
	}

	public function insert_action()
	{
		$arr_fields = $this->get_params();
		$this->category_options_model->insert($arr_fields);
		$this->redirect('/admin/category_options/index');
	}

	private function get_bind_field()
	{
		$dict_bind_field = array(
			'name',
			'name_en',
			'year',
			'date',
			'district',
			'type',
			'format',
			'subtitle',
			'actor',
			'memo',
			'imdb',
			'season'
		);
		for($i = 1; $i <= 2; $i++)
		{
			$dict_bind_field[] = "opt" . $i;
		}
		for($i = 1; $i <= 2; $i++)
		{
			$dict_bind_field[] = "text" . $i;
		}
		$dict_bind_field_book = array(
			'',
			'category',
			'school',
			'building',
			'publisher',
			'version',
			'link',
			'price',
			'sold'
		);
		$dict_bind_field = array_merge($dict_bind_field, $dict_bind_field_book);
		return $dict_bind_field;
	}
}