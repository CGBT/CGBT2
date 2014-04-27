<?php
class admin_setting_controller extends admin_base_controller
{

	public function save($arr_fields, $action)
	{
		cg::load_model('setting_model');
		$setting_model = new setting_model();
		$arr_setting = array();
		foreach ($arr_fields as $f)
		{
			$arr_setting[$f] = $this->post[$f];
		}
		$setting_model->set($arr_setting);
		$this->redirect("/admin/setting/$action/");
	}

	public function index_action()
	{
		$arr_fields = array(
			'site_name',
			'site_domain',
			'static_prefix',
			'forums_url',
			'forums_type',
			'site_qq_qun',
			'site_qq_qun_name',
			'site_cookie_expire',
			'online_time',
			'login_fail_time',
			'login_fail_count',
			'check_invite_code',
			'check_forums_user_valid',
			'header_background_pic',
			'search_page_announce'
		);

		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function forums_action()
	{
		$arr_fields = array(
			'forums_template_dir',
			'forums_register_url',
			'forums_url',
			'forums_lost_password_url',
			'forums_thread_url',
			'forums_modify_password_url'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function tracker_action()
	{
		$arr_fields = array(
			'tracker_url',
			'tracker_announce_interval',
			'tracker_min_interval',
			'tracker_min_interval_limit',
			'tracker_log_speed',
			'tracker_download_unaudited_user',
			'tracker_peer_clean_time',
			'tracker_peer_force_clean_time',
			'tracker_black_agent',
			'tracker_black_peer_id',
			'tracker_black_ips'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function admins_action()
	{
		$arr_fields = array(
			'admins_admins',
			'admins_developer',
			'admins_deliver',
			'admins_trust_ips'
		);

		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function credits_action()
	{
		$arr_fields = array(
			'extcredits1_max',
			'extcredits1_min',
			'extcredits1_size',
			'extcredits1_seeders',
			'extcredits1_weeks',
			'download_need_extcredits1',
			'modify_title_need_extcredits1',
			'forums_money_field',
			'money2uploaded_need_money',
			'money2uploaded_days_interval',
			'money2uploaded_max',
			'extcredits12uploaded_need_extcredits1',
			'extcredits12uploaded_days_interval',
			'extcredits12uploaded_max',
			'torrents_award',
			'req_seed_extcredits1',
			'mod_price_min',
			'upload_sub_extcredits1',
			'torrents_comments_extcredits2',
			'torrents_award_extcredits2',
			'torrents_rate_extcredits2',
			'upload_sub_extcredits2'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function upload_action()
	{
		$arr_fields = array(
			'torrents_source',
			'torrents_size_limit',
			'images_size_limit',
			'subtitles_size_limit',
			'nfos_size_limit',
			'softsite_size_limit',
			'upload_note',
			'torrents_save_path',
			'images_save_path',
			'subtitles_save_path',
			'nfos_save_path',
			'softsite_save_path',
			'images_domain',
			'delete_torrents_reasons',
			'audit_torrents_reasons',
			'download_torrents_name_prefix',
			'torrents_price',
			'torrents_price_tax',
			'torrents_price_times'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function rule_action()
	{
		$arr_fields = array(
			'enable_ratio_limit',
			'ratio_limit',
			'enable_seed_count_limit',
			'seed_count_limit',
			'enable_seed_size_limit',
			'seed_size_limit',
			'all_free',
			'all_2x',
			'new_torrents_free_time',
			'new_torrents_30p_time',
			'new_torrents_half_time',
			'torrents_free_min_size',
			'hot_torrents_seed_size_limit',
			'hot_torrents_seed_count_limit',
			'enable_upload_factor',
			'upload_factor_link'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}

	public function newbie_action()
	{
		$arr_fields = array(
			'newbie_enable',
			'newbie_startdate',
			'newbie_days',
			'newbie_uploaded',
			'newbie_downloaded',
			'newbie_extcredits1'
		);
		$this->data['action'] = str_replace('_action', '', $this->action_name);
		if (isset($this->post['submitbtn']))
		{
			$this->save($arr_fields, $this->data['action']);
		}
		$this->template_file = 'admin/setting.php';
		$this->show();
	}
}