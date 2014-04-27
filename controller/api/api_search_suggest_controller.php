<?php
class api_search_suggest_controller extends base_controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);

		$this->check_login();
	}

	public function index_action()
	{
		$this->check_login();
		$keyword = isset($this->get['q']) ? $this->get['q'] : '';
		if (empty($keyword))
		{
			return;
		}
		cg::load_model('search_keywords_model');
		$search_keywords_model = search_keywords_model::get_instance();
		$rows = $search_keywords_model->search($keyword);
		if (empty($rows))
		{
			echo "没有结果，请更换关键词重新搜索！\n";
		}
		else
		{
			foreach ($rows as $row)
			{
				echo $row['keyword'] . "\n";
			}
		}
	}
}