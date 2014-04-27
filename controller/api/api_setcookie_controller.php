<?php
class api_setcookie_controller extends base_controller
{

	public function default_style_action()
	{
		$default_style = isset($this->post['default_style']) ? $this->post['default_style'] : '';
		if (!empty($default_style))
		{
			cg::load_core('cg_cookie');
			cg_cookie::set('default_style', $default_style);
		}
	}
}