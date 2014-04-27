<?php
class base_module
{
	public $setting;
	public $all_category;

	public function __construct()
	{
		$this->get_setting();
	}

	public function get_category()
	{
		cg::load_model('category_model');
		$this->category_model = category_model::get_instance();
		$this->all_category = $this->category_model->get_all();
	}

	public function get_setting()
	{
		cg::load_model('setting_model');
		$this->setting_model = setting_model::get_instance();
		$this->setting = $this->setting_model->get_all();
	}
}
