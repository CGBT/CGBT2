<?php
class rss_controller extends base_controller
{
	private $search_module;

	public function index_action()
	{
		$this->check_passkey();

		$this->data['app_url'] = cg::config()->APP_URL;
		cg::load_module('search_module');
		$this->search_module = new search_module();

		//传递参数
		$this->search_module->params = $this->params;
		$this->search_module->uid = $this->uid;
		$this->search_module->controller = 'rss';

		//执行
		$this->search_module->index_action();

		//获取结果
		$this->data += $this->search_module->get_template_data();
		$this->template_file = 'rss.php';
		$this->show();
	}

	public function check_passkey()
	{
		if (empty($this->passkey))
		{
			$this->showmessage('加用户识别码(passkey)参数错误! ');
		}

		if ($this->user["enabled"] == "0")
		{
			$this->showmessage("您的账号已被封禁!");
		}
		if ($this->user["status"] == "0")
		{
			$this->showmessage("因为长期共享率过低，您已经被封禁下载权限，不能使用rss功能。!");
		}
		$this->check_have_privileges('rss', true);
	}
}
