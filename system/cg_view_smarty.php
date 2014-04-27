<?php
require_once 'smarty/Smarty.class.php';

class cg_view_smarty extends Smarty
{

	//no use
	private $template_engine = 'smarty';

	public function __construct($template_dir)
	{
		parent::__construct();

		$this->compile_dir = '';
		$this->template_dir = cg::config()->APP_PATH . 'view/' . $template_dir . '/';

		$this->compile_check = true;
		$this->left_delimiter = "<!--{";
		$this->right_delimiter = "}-->";

		$this->caching = true;
		$this->cache_lifetime = 60;
		$this->debugging = false;

		$cache_dir = cg::config()->config['cache']['file']['cache_dir'];
		if (!is_dir($cache_dir))
		{
			$cache_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "cache";
		}
		if (!is_dir($cache_dir))
		{
			mkdir($cache_dir, 0777);
		}

		$this->compile_dir = $cache_dir . '/template_compile';
		if (!is_dir($this->compile_dir))
		{
			mkdir($this->compile_dir, 0777);
		}

		$this->cache_dir = $cache_dir . '/template_cache';
		if (!is_dir($this->cache_dir))
		{
			mkdir($this->cache_dir, 0777);
		}
	}
}