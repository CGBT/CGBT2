<?php
class audit_controller extends base_controller
{
	private $search_module;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
		$this->data['selected_nav'] = 'audit';
		$this->data['title'] = '待审核种子-';
		cg::load_module('search_module');
		$this->search_module = new search_module();
	}

	public function index_action()
	{
		//传递参数
		$this->search_module->params = $this->params;
		$this->search_module->uid = $this->uid;
		$this->search_module->controller = 'audit';

		//执行
		$this->search_module->index_action();

		//获取结果
		$this->data += $this->search_module->get_template_data();
		$this->template_file = 'audit.php';
		$this->show();
	}

	public function demo_action()
	{
		$this->template_file = 'search.html';
		$this->show();
	}
}
