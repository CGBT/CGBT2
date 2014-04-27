<?php
/**
 * 管理后台菜单配置
 * 右侧子导航名数量为1或者名称为空则不显示
 *
 * 顶级菜单1 = array(
 *    左侧菜单名1|controller|action|右侧子导航名1|是否显示
 *    左侧菜单名1|controller|action|右侧子导航名2|是否显示
 *    左侧菜单名2|controller|action|右侧子导航名|是否显示
 *    左侧菜单名3|controller|action|右侧子导航名|是否显示
 * ),
 * 顶级菜单2 = array(
 *    左侧菜单名1|controller|action|右侧子导航名1|是否显示
 *    左侧菜单名1|controller|action|右侧子导航名2|是否显示
 *    左侧菜单名2|controller|action|右侧子导航名|是否显示
 *    左侧菜单名3|controller|action|右侧子导航名|是否显示
 * )
 */
return array(
	'首页' => array(
		"站点数据|index|index|站点数据(实时)|1",
		"站点数据|index|index2|站点数据(对比)|1",
		"缓存统计|index|memcache|缓存统计|1",
		"缓存统计|index|getcache|查看缓存|1",
		"缓存统计|index|clearcache|清理缓存|1",
		"日统计|index|daystats|日统计|1",
		"保种机统计|index|myip_stat|保种机统计|1",
		"phpinfo|index|phpinfo|phpinfo|1",
		"积分日志|index|credits|积分日志|1",
		"客户端统计|index|agentinfo|客户端统计|1",
		"登陆日志|index|logslogin|登录日志|1",
		"cron日志|index|logscron|cron日志|1",
		"更新索引|torrentindex|index|批量更新索引|1",
		"更新索引|torrentindex|update|批量更新索引-更新|0",
		"更新关键词拼音|keyword2pinyin|index|更新关键词拼音|1",
		"更新关键词拼音|keyword2pinyin|update|更新关键词拼音-更新|0"
	),
	'系统设置' => array(
		"系统设置|setting|index|系统设置|1",
		"管理员设置|setting|admins|管理员设置|1",
		"论坛设置|setting|forums|论坛设置|1",
		"Tracker设置|setting|tracker|Tracker设置|1",
		"积分设置|setting|credits|积分设置|1",
		"发布种子|setting|upload|发布种子|1",
		"规则设置|setting|rule|规则设置|1",
		"新手考核设置|setting|newbie|新手考核设置|1"
	),
	'分类设计' => array(
		"分类管理|category|index|分类管理|1",
		"分类管理|category|edit|分类管理-修改|0",
		"分类管理|category|insert|分类管理-插入|0",
		"分类管理|category|update|分类管理-更新|0",
		"分类管理|category_options|index|分类表单设计|1",
		"分类管理|category_options|edit|分类表单设计-修改|0",
		"分类管理|category_options|insert|分类表单设计-插入|0",
		"分类管理|category_options|update|分类表单设计-更新|0",
		"分类管理|category|rules|分类规则|1",
		"分类管理|category|rules_update|分类规则-更新|0"
	),
	'用户管理' => array(
		"用户管理|user|search|用户查询|1",
		"用户管理|user|list|用户列表|1",
		"用户管理|user|edit_group|修改用户组|1",
		"用户管理|user|update_group|修改用户组|0",
		"用户管理|user|edit_credits|修改用户积分|1",

		"用户管理|users_group|index|用户组列表|1",
		"用户管理|users_group|edit|用户组-修改|0",
		"用户管理|users_group|update|用户组-更新|0",
		"用户管理|users_group|editadmin|用户组-修改管理权限|0",
		"用户管理|users_group|updateadmin|用户组-更新管理权限|0",

		"权限管理|privileges|index|前台权限|1",
		"权限管理|privileges|edit|权限管理-修改|0",
		"权限管理|privileges|insert|权限管理-插入|0",
		"权限管理|privileges|update|权限管理-更新|0",

		"权限管理|privileges|front|前台权限|1",
		"权限管理|privileges|back|后台权限|1",
		"权限管理|privileges|addfront|添加前台权限|1",
		"权限管理|privileges|addback|添加后台权限|1",

		"封禁用户|bans|add|封禁用户权限|1",
		"封禁用户|bans|index|封禁列表|1",
		"封禁用户|bans|insert|封禁用户-插入|0",
		"封禁用户|bans|edit|封禁用户-修改|0",
		"封禁用户|bans|update|封禁用户-更新|0"
	)
);
