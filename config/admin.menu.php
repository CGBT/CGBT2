<?php
/**
 * 管理后台菜单配置文件
 */
return array(
	0 => array(
		0 => array('name' => '首页', 'link' => '/admin/index'),
	),
	1 => array(
		0 => array('name' => '系统设置', 'link' => '/admin/setting/index'),
		1 => array('name' => '论坛设置', 'link' => '/admin/setting/forums'),
		2 => array('name' => '种子分类', 'link' => '/admin/category/index'),
		3 => array('name' => '种子分类表单设计', 'link' => '/admin/category_options/index'),
	),
	2 => array(
		0 => array('name' => '用户管理', 'link' => '/admin/user/index'),
	),

);
?>