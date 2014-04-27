<?php
class index_controller extends base_controller
{

	/**
	 * update_sitestat() @ cron_controller
	 */
	private function get_sitestat_data()
	{
		cg::load_module('users_module');
		cg::load_module('torrents_module');
		cg::load_model('peers_model');
		cg::load_model('logs_sitestat_model');

		$users_module = users_module::get_instance();
		$torrents_module = torrents_module::get_instance();
		$peers_model = peers_model::get_instance();
		$log_sitestat_model = logs_sitestat_model::get_instance();

		$arr_fields = array();
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['date'] = date("Y-m-d");
		$arr_fields['total_user_count'] = $users_module->users_model->count();

		$start_time = $this->timestamp - 1200;
		$arr_fields['online_user'] = $users_module->users_stat_model->count("last_login > '$start_time'");

		$arr_fields['torrent_count'] = $torrents_module->torrents_index_model->count();
		$arr_fields['active_torrent_count'] = $torrents_module->torrents_index_model->count(' seeder > 0');

		$arr_fields['peer_user_count'] = $peers_model->get_peer_user_count('defalt');
		$arr_fields['seeder_count'] = $peers_model->get_peer_user_count('1');
		$arr_fields['leecher_count'] = $peers_model->get_peer_user_count('0');

		$arr_fields['total_peer_count'] = $peers_model->count();
		$arr_fields['leech_peer_count'] = $peers_model->count('is_seeder=0');
		$arr_fields['seed_peer_count'] = $arr_fields['total_peer_count'] - $arr_fields['leech_peer_count'];

		$arr_fields['total_size'] = $torrents_module->torrents_index_model->get_total_size('0');
		$arr_fields['active_size'] = $torrents_module->torrents_index_model->get_total_size('1');

		$arr_fields['online_guest'] = '0';
		$arr_fields['max_user_time'] = '0';
		$arr_fields['max_online_user'] = '0';

		$log_sitestat_model->insert($arr_fields);

		$arr_fields['total_size_text'] = funcs::mksize($arr_fields['total_size']);
		$arr_fields['active_size_text'] = funcs::mksize($arr_fields['active_size']);
		return $arr_fields;
	}

	public function index_action()
	{
		$this->check_login();
		$this->data['selected_nav'] = 'index';
		$this->data['title'] = '首页-';
		cg::load_model('logs_sitestat_model');
		$logs_sitestat_model = new logs_sitestat_model();
		$this->data['site_stat'] = $logs_sitestat_model->get_index_site_stat();
		if (empty($this->data['site_stat']))
		{
			$this->cache()->delete('index_site_stat');
			$this->data['site_stat'] = $this->get_sitestat_data();
		}
		cg::load_model('users_stat_model');
		$users_stat_model = new users_stat_model();
		$users_module = new users_module();
		$users = $this->data['user_group_count'] = $users_stat_model->get_user_group_count();
		$all_users_group = $users_module->all_users_group;
		$arr_users = array();
		foreach ($all_users_group as $key => $group)
		{
			$arr_users[$key]['name'] = $group['name'];
			$arr_users[$key]['count'] = 0;
		}
		foreach ($users as $key => $user)
		{
			$arr_users[$user['class']]['count'] = $user['count'];
		}

		$this->data['users'] = $arr_users;
		$this->check_new_task();

		$this->data['room'] = '_';
		$this->data['room_key'] = md5(cg::config()->config['system_salt_key'] . '_' . date("Ym"));
		if ($this->user['is_admin'] || $this->user['is_moderator'])
		{
			$this->data['is_room_admin'] = true;
		}
		else
		{
			$this->data['is_room_admin'] = false;
		}
		$privileges_info = $this->check_have_privileges('chat_use_ubb', false);
		$this->data['chat_use_ubb'] = $privileges_info['have_privileges'];
		$this->show('index.php');
	}

	private function check_new_task()
	{
		if (!$this->user['is_user'] && $this->user['groupid'] > 0)
		{
			return;
		}
		if ($this->user['createtime'] < strtotime($this->setting['newbie_startdate']))
		{
			return;
		}
		$pass_kaohe = false;
		$G = 1024 * 1024 * 1024;
		if ($this->user['uploaded'] > $this->setting['newbie_uploaded'] * $G && $this->user['downloaded'] > $this->setting['newbie_downloaded'] * $G &&
		 $this->user['extcredits1'] > $this->setting['newbie_extcredits1'])
		{
			$pass_kaohe = true;
		}
		$msg = "<span style='font-size:14px;font-weight:bold;'>";
		$msg .= "<br />新注册用户请注意，您必须参加新手考核。<br />";
		$msg .= "考核要求：注册时间  {$this->setting['newbie_days']} 天内上传流量必须大于 {$this->setting['newbie_uploaded']} G，下载流量大于  {$this->setting['newbie_downloaded']}G，保种积分大于 {$this->setting['newbie_extcredits1']} 积分。<br />";
		$msg .= "您当前上传流量为  {$this->user['uploaded_text']}, 下载流量为 {$this->user['downloaded_text']}，保种积分为 {$this->user['extcredits1']} 。<br />";
		if (!$pass_kaohe)
		{
			$msg .= "<span style='color:red'>您尚未通过考核。</span><br />";
		}
		else
		{
			$msg .= "<span style='color:red'>您已经通过考核。</span><br />";
		}
		$msg .= "新人请仔细阅读网站各种公告及规则，关于保种问题，优特错误，上传慢等解决方法请看网站公告。</span>";
		if ($this->timestamp - $this->user['createtime'] > 65 * 86400 && $pass_kaohe)
		{
			return;
		}
		$this->data['kaohe_msg'] = $msg;
		return $msg;
	}
}
