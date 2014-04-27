<?php
class cg_view
{
	private $template_engine = 'php'; //no use
	private $template_dir = '';
	private $lang;
	private $default_lang;
	private $lang_data = array();
	private $vars;

	public function __construct($template_dir = '')
	{
		$this->get_lang();
		$this->load_lang_file('lang.php');
		$this->template_dir = $template_dir;
	}

	public function display($filename)
	{
		$this->fetch($filename, true);
	}

	public function fetch($filename, $display = false)
	{
		extract($this->vars, EXTR_SKIP);
		if ($display)
		{
			include $this->get_template_file($filename);
			return;
		}
		else
		{
			ob_start();
			include $this->get_template_file($filename);
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}

	public function assign($var, $value = null)
	{
		if (is_array($var))
		{
			foreach ($var as $key => $val)
			{
				if ($key != '')
				{
					$this->vars[$key] = $val;
				}
			}
		}
		else
		{
			if ($var != '')
			{
				$this->vars[$var] = $value;
			}
		}
	}

	public function lang($words, $args = '')
	{
		if (!isset($this->lang_data[$words]))
		{
			return $words;
		}
		$args = func_get_args();
		$words = array_shift($args);
		return vsprintf($this->lang_data[$words], $args);
	}

	public function load_lang_file($filename)
	{
		$lang_file = cg::config()->APP_PATH . 'lang/' . $this->lang . '/' . $filename;
		if (!file_exists($lang_file))
		{
			$lang_file = cg::config()->APP_PATH . 'lang/' . $this->default_lang . '/' . $filename;
		}
		if (!file_exists($lang_file))
		{
			//die('lang file :' . $lang_file . ' not exists');
		}
		$this->lang_data += include $lang_file;
	}

	private function get_template_file($filename)
	{
		$file = cg::config()->APP_PATH . 'view/' . $this->template_dir . '/' . $filename;
		if (!file_exists($file))
		{
			$file = cg::config()->APP_PATH . 'view/' . cg::config()->config['view']['default_template'] . '/' . $filename;
		}
		return $file;
	}

	private function get_lang()
	{
		$lang_config = cg::config()->config['lang'];
		$this->lang = $lang_config['default'];
		$this->default_lang = $this->lang;
		return; //@todo


		if (!$lang_config['multi_lang'])
		{
			if (empty($this->lang))
			{
				$this->lang = 'cn';
			}
			return;
		}

		$all_langs = $lang_config['all'];

		if (isset($_GET['lang']))
		{
			if (in_array($_GET['lang'], array_keys($all_langs)))
			{
				setcookie('lang', $_GET['lang'], time() + 86400 * 30, '/');
				$this->lang = $_GET['lang'];
			}
		}
		elseif (isset($_COOKIE['lang']))
		{
			if (in_array($_COOKIE['lang'], array_keys($all_langs)))
			{
				$this->lang = $_COOKIE['lang'];
			}
		}
		else
		{
			$lang = array_search($this->language(), $all_langs);
			if ($lang !== false)
			{
				$this->lang = $lang;
				setcookie('lang', $lang, time() + 86400 * 30, '/');
			}
		}
	}

	/**
	 * @return string  zh-cn,en-us
	 */
	private function language()
	{
		$langcode = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$langcode = (!empty($langcode)) ? explode(';', $langcode) : $langcode;
		$langcode = (!empty($langcode[0])) ? explode(',', $langcode[0]) : $langcode;
		return $langcode[0];
	}
}
