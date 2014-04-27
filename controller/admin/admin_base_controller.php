<?php
class admin_base_controller extends base_controller
{
	public $all_admin_privileges = array();

	public function beforeRun($resource, $action, $module = '')
	{
		parent::beforeRun($resource, $action, $module);
		$this->check_login();
		$this->check_admins();
		$this->init_privilages();
		$this->check_current_privilege();
		$this->parse_menu();
		$this->data['title'] = '后台管理-';
	}

	private function is_founder()
	{
		return $this->username == cg::config()->config['system_founder'];
	}

	public function check_admins()
	{
		if ($this->is_founder())
		{
			return;
		}
		if (in_array($this->username, funcs::explode($this->setting['admins_admins'])))
		{
			return;
		}
		$this->showmessage('没有权限');
	}

	public function parse_menu()
	{
		$all_admin_privileges = $this->data['all_admin_privileges'];
		$current_controller = str_replace('admin_', '', $this->controller_name);
		$current_controller = str_replace('_controller', '', $current_controller);
		$current_action = str_replace('_action', '', $this->action_name);

		$controller_action = array();
		$data = array();

		//遍历所有权限，判断当前用户是否有权限，生成controller_action
		foreach ($all_admin_privileges as $top_name => $rows)
		{
			foreach ($rows as $key => $row)
			{
				list($side_name, $controller, $action, $subnav_name, $display) = explode('|', $row);
				if (!$this->check_have_one_privilege($controller, $action))
				{
					unset($all_admin_privileges[$top_name][$key]);
				}
				else
				{
					$controller_action[$controller . '/' . $action] = $top_name . '|' . $row;
				}
			}
			if (empty($all_admin_privileges[$top_name]))
			{
				unset($all_admin_privileges[$top_name]);
			}
		}
		//遍历用户的权限，得到顶级菜单，以及当前选中的菜单
		foreach ($all_admin_privileges as $top_name => $rows)
		{
			foreach ($rows as $key => $row)
			{
				list($side_name, $controller, $action, $subnav_name, $display) = explode('|', $row);
				if (empty($subnav_name))
				{
					$subnav_name = $side_name;
				}
				if ($display && $current_controller == $controller && $this->action_name == $current_action)
				{
					$data['top_menu_active'] = $top_name;
					$data['side_menu_active'] = $side_name;
					$data['subnav_menu_active'] = $subnav_name;
				}
				if (!isset($data['top_menu'][$top_name]))
				{
					$data['top_menu'][$top_name] = '/admin/' . $controller . '/' . $action . '/';
				}
			}
		}
		//没有获取到当前选中的菜单，则根据controller_action重新获取
		if (!isset($data['top_menu_active']))
		{
			if (!isset($controller_action[$current_controller . '/' . $current_action]))
			{
				$this->showmessage("当前权限未定义");
			}
			list($top_name, $side_name, $controller, $action, $subnav_name, $display) = explode('|', $controller_action[$current_controller . '/' . $current_action]);
			$data['top_menu_active'] = $top_name;
			$data['side_menu_active'] = $side_name;
			$data['subnav_menu_active'] = $subnav_name;
		}
		//根据顶级菜单，获取左侧二级菜单
		foreach ($all_admin_privileges[$data['top_menu_active']] as $row)
		{
			list($side_name, $controller, $action, $subnav_name, $display) = explode('|', $row);
			if ($display && !isset($data['side_menu'][$side_name]))
			{
				$data['side_menu'][$side_name] = '/admin/' . $controller . '/' . $action . '/';
			}
		}
		//根据二级菜单，获取三级菜单
		foreach ($all_admin_privileges[$data['top_menu_active']] as $row)
		{
			list($side_name, $controller, $action, $subnav_name, $display) = explode('|', $row);
			if ($display && $data['side_menu_active'] == $side_name)
			{
				if (!isset($data['subnav_menu'][$subnav_name]))
				{
					$data['subnav_menu'][$subnav_name] = '/admin/' . $controller . '/' . $action . '/';
				}
			}
		}
		$this->data['menu'] = $data;
	}

	public function init_privilages()
	{
		$this->all_admin_privileges = cg::config()->config['admin_privileges'];
		$this->data['all_admin_privileges'] = $this->all_admin_privileges;
	}

	public function check_have_one_privilege($controller, $action)
	{
		if ($this->is_developer() || $this->is_founder())
		{
			return true;
		}
		$p = $controller . '/' . $action;
		if (isset($this->user['admin_privileges'][$p]) && $this->user['admin_privileges'][$p])
		{
			return true;
		}
		return false;
	}

	private function check_current_privilege()
	{
		$controller = str_replace('admin_', '', $this->controller_name);
		$controller = str_replace('_controller', '', $controller);
		$action = str_replace('_action', '', $this->action_name);
		if (!$this->check_have_one_privilege($controller, $action))
		{
			$this->showmessage('没有权限');
		}
	}
}