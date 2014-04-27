<?php
class cron_controller extends cronbase_controller
{

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		if (!$this->is_developer())
		{
			exit('error');
		}
		set_time_limit(0);
	}

	public function update_total_credits()
	{
		cg::load_model("users_stat_model");
		$users_stat_model = users_stat_model::get_instance();
		$users_stat_model->update_total_credits();
	}

	public function fetch_queue_pic()
	{
		$dst_dir = empty($this->setting['images_save_path']) ? 'attachments/images' : $this->setting['images_save_path'];

		cg::load_core('cg_http');
		cg::load_model('url_queue_model');
		$url_queue_model = url_queue_model::get_instance();
		$url_queue_model->set_fetched_timeout();
		$ids = $url_queue_model->get_queue_ids(200);
		if (empty($ids))
		{
			return;
		}
		$url_queue_model->set_fetching($ids);
		foreach ($ids as $id)
		{
			$row_queue_url = $url_queue_model->find($id);
			$url = $row_queue_url['url'];
			$http = new cg_http($url);
			$data = $http->send_request();
			if ($data === false)
			{
				$url_queue_model->set_not_fetched($id);
				continue;
			}
			else
			{
				$md5 = md5($data);
				$file_size = strlen($data);
				$ext = strtolower(trim(substr($url, strrpos($url, '.') + 1)));
				$dst_filename = $md5{0} . $md5{1} . '/' . $md5{2} . $md5{3} . '/' . $md5 . '.' . $ext;
				funcs::recursive_mkdir($dst_dir, $dst_filename);
				file_put_contents($dst_dir . '/' . $dst_filename, $data);
				$arr_fields = array(
					'file_md5' => $md5,
					'fetched' => '1',
					'size' => $file_size,
					'path' => $dst_filename
				);
				$url_queue_model->update($arr_fields, $id);
			}
		}
	}

	public function replace_pic_in_descr()
	{
		$images_domain = empty($this->setting['images_domain']) ? cg::config()->APP_URL . 'attachments/images/' : $this->setting['images_domain'];
		cg::load_model('url_queue_model');
		cg::load_model('torrents_descr_model');
		$torrents_descr_model = torrents_descr_model::get_instance();
		$url_queue_model = url_queue_model::get_instance();
		$ids = $url_queue_model->get_tobereplaced_ids(200);
		$replace_urls = array();
		foreach ($ids as $key => $id)
		{
			$row_queue_url = $url_queue_model->find($id);
			if (empty($row_queue_url['path']))
			{
				unset($ids[$key]);
				continue;
			}
			$tid = $row_queue_url['tid'];
			$replace_urls[$tid]['old'][] = $row_queue_url['url'];
			$replace_urls[$tid]['new'][] = $images_domain . $row_queue_url['path'];
		}
		foreach ($replace_urls as $tid => $urls)
		{
			$descr_id = $torrents_descr_model->get_first_tids_id($tid);
			$row_descr = $torrents_descr_model->find($descr_id);
			$descr = str_replace($urls['old'], $urls['new'], $row_descr['descr']);
			$torrents_descr_model->update(array(
				'descr' => $descr
			), $descr_id);
		}
		if (!empty($ids))
		{
			$url_queue_model->set_replaced($ids);
		}
	}

	public function create_images_url_queue_from_descr()
	{
		cg::load_model('url_queue_model');
		cg::load_model('torrents_descr_model');
		$torrents_descr_model = torrents_descr_model::get_instance();
		$url_queue_model = url_queue_model::get_instance();

		$torrents_descr_model->use_cache = false;
		$ids = $torrents_descr_model->get_queue_ids(2000);

		foreach ($ids as $id)
		{
			$row = $torrents_descr_model->find($id);
			$urls = $this->parse_pic_url($row['descr']);
			foreach ($urls as $url)
			{
				$url_md5 = md5($url);
				$count = $url_queue_model->url_md5_exists($url_md5);
				if ($count > 0)
				{
					continue;
				}
				$arr_fields = array(
					'url' => $url,
					'url_md5' => $url_md5,
					'fetched' => 0,
					'createtime' => $this->timestamp,
					'tid' => $row['tid']
				);
				$url_queue_model->insert($arr_fields);
			}
			$torrents_descr_model->update(array(
				'url_queued' => 1
			), $id);
		}
	}

	protected function parse_pic_url($descr)
	{
		$matches = array();
		preg_match_all('/\[url=(http:\/\/[\w-\*,:\%\.\(\)\/]+?(\.jpg|\.png|\.gif))\]/i', $descr, $matches);
		$arr1 = $matches[1];

		$matches = array();
		preg_match_all('/\[img.*?\](http:\/\/[\w-\*,:\?\&=\%\.\(\)\/]+?)\[\/img\]/i', $descr, $matches);
		$arr2 = $matches[1];

		$arr_pics = array_merge($arr1, $arr2);
		foreach ($arr_pics as $key => $pic)
		{
			if (stripos($pic, "cgbt.cn") !== false || stripos($pic, "cgbt.org") !== false)
			{
				unset($arr_pics[$key]);
			}
		}
		return $arr_pics;
	}

	public function clear_bans_status()
	{
		cg::load_model('users_bans_model');
		$users_bans_model = users_bans_model::get_instance();
		$all_enabled_bans = $users_bans_model->get_all_enabled_bans();

		cg::load_model('users_model');
		$users_model = users_model::get_instance();
		foreach ($all_enabled_bans as $rows)
		{
			foreach ($rows as $row)
			{
				if ($row['endtime'] < $this->timestamp && $row['privileges_name'] == 'login')
				{
					$arr_fields['enabled'] = '1';
					$users_model->update($arr_fields, $row['uid']);
				}
			}
		}
		$users_bans_model->clear_bans_status();
	}

	public function delete_unaudited_torrents()
	{
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();

		$rows = $torrents_module->torrents_model->get_category_is_bozhongji_torrents();
		foreach ($rows as $row)
		{
			$forums_discuzx_model->delete_thread($row['forums_tid']);
			$torrents_module->delete_torrent($row['id'], $row['info_hash']);

			//发消息
			$from_username = $this->setting['admins_deliver'];
			$from_user = $users_module->get_by_username($from_username);
			if (!empty($from_user))
			{
				$reason = "种子处于审核区超过12小时未修改分类";
				$from_uid = $from_user['forums_uid'];
				$msgto = $row['username'];
				$subject = '您发布的种子被删除';
				$message = "您发布的种子被删除： $row[name] \n";
				$message .= "删种原因：$reason \n";
				$message .= "如果是违规种子，请勿再次发布。请仔细阅读发种相关规则。\n";
				$message .= "如果您对本次删种操作有所疑议，请到论坛发帖说明。谢谢合作。\n";
				$isusername = 1;
				$forums_discuzx_model->pm_send($from_uid, $msgto, $subject, $message, $isusername);
			}
		}
	}

	public function replace_pic_action()
	{
		$dict_crons = array(
			'replace_pic_in_descr' => 5
		);
		foreach ($dict_crons as $cron => $interval)
		{
			$this->exec($cron, $interval);
		}
	}

	public function fetch_pic_action()
	{
		$dict_crons = array(
			'replace_pic_in_descr' => 111,
			'create_images_url_queue_from_descr' => 105,
			'fetch_queue_pic' => 108
		);
		parent::index($dict_crons);
	}

	public function index_action()
	{
		$dict_crons = array(
			'clear_today_uploaded_downloaded' => 3590,
			'clear_hour_uploaded_downloaded' => 110,
			'update_sitestat' => 590,
			'get_dayuser' => 1000,
			'update_comments_count' => 600,
			'delete_unaudited_torrents' => 3700,
			'clear_bans_status' => 3600,
			'delete_no_tid_images' => 3580,
			'newbie_task' => 3590,
			'update_bt_info_hash' => 119,
			'update_extcredits1_speed' => 1700,
			'update_torrents_mod' => 230,
			'update_torrents_price_mod' => 230,
			'update_total_credits' => 14000
		);
		parent::index($dict_crons);
	}

	protected function update_bt_info_hash()
	{
		cg::load_model('torrents_model');
		$torrents_model = torrents_model::get_instance();
		$rows = $torrents_model->bt_info_hash_is_empty_torrents();
		if (empty($rows))
		{
			return;
		}
		cg::load_class('cg_bcode');
		foreach ($rows as $row)
		{
			$info_hash = $this->get_info_hash($row['filename']);
			if (!empty($info_hash))
			{
				$arr_fields = array(
					'bt_info_hash' => $info_hash
				);
				$torrents_model->update($arr_fields, $row['id']);
			}
		}
	}

	protected function get_info_hash($filename)
	{
		$path = empty($this->setting['torrents_save_path']) ? 'attachments/torrents/' : $this->setting['torrents_save_path'];
		$filename = $path . $filename;
		$dict = cg_bcode::bdecode_file($filename);
		if (empty($dict))
		{
			return '';
		}
		unset($dict['info']['source']);
		unset($dict['info']['private']);
		unset($dict['info']['ttg_tag']);
		return sha1(bencode($dict['info']));
	}

	protected function newbie_task()
	{
		$starttime = strtotime(date("Y-m-d", $this->timestamp - 62 * 86400));
		$endtime = strtotime(date("Y-m-d", $this->timestamp - 61 * 86400));
		if ($starttime < strtotime($this->setting['newbie_startdate']))
		{
			return;
		}
		$uploaded = $this->setting['newbie_uploaded'] * 1024 * 1024 * 1024;
		$extcredits1 = $this->setting['newbie_extcredits1'];
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$uids = $users_module->users_stat_model->fail_newbie_task_uids($starttime, $endtime, $uploaded, $extcredits1);
		$arr_fields = array(
			'enabled' => 0
		);
		foreach ($uids as $uid)
		{
			$users_module->users_model->update($arr_fields, $uid);
		}
	}

	protected function delete_no_tid_images()
	{
		if (date("H") != '4')
		{
			return;
		}
		cg::load_model('torrents_images_model');
		$torrents_images_model = torrents_images_model::get_instance();
		$torrents_images_model->delete_no_tid_images();
	}

	protected function update_comments_count()
	{
		return;
		cg::load_model('forums_discuzx_model');
		$forum_discuzx_model = forums_discuzx_model::get_instance();
		$rows = $forum_discuzx_model->get_lastest_thread_replies();
		foreach ($rows as $row)
		{
			if ($row['replies'] > 0)
			{
			}
		}
	}

	protected function get_dayuser()
	{
		if (date("H") > 0) //0点执行
		{
			return;
		}
		$yesterday = date("Y-m-d", $this->timestamp - 86400);
		cg::load_model('logs_daystats_model');
		$logs_daystats_model = logs_daystats_model::get_instance();
		$count = $logs_daystats_model->count("thedate = '$yesterday'");
		if ($count > 0)
		{
			return;
		}

		$start_timestamp = $this->timestamp - 86400;

		cg::load_model('users_stat_model');
		$users_stat_model = users_stat_model::get_instance();
		$users_count = $users_stat_model->count("last_action > '$start_timestamp'");

		cg::load_model('logs_search_model');
		$logs_search_model = logs_search_model::get_instance();
		$search_count = $logs_search_model->count("createtime > '$start_timestamp'");

		cg::load_model('logs_completed_model');
		$logs_completed_model = logs_completed_model::get_instance();
		$completed_count = $logs_completed_model->count("createtime > '$start_timestamp'");

		cg::load_model('logs_browse_model');
		$logs_browse_model = logs_browse_model::get_instance();
		$browse_count = $logs_browse_model->count("createtime > '$start_timestamp'");

		cg::load_model('logs_download_model');
		$logs_download_model = logs_download_model::get_instance();
		$download_count = $logs_download_model->count("createtime > '$start_timestamp'");

		cg::load_model('logs_login_model');
		$logs_login_model = logs_login_model::get_instance();
		$login_count = $logs_login_model->count("createtime > '$start_timestamp'");

		$arr_fields = array();
		$arr_fields['thedate'] = $yesterday;
		$arr_fields['users_count'] = $users_count;
		$arr_fields['search_count'] = $search_count;
		$arr_fields['completed_count'] = $completed_count;
		$arr_fields['browse_count'] = $browse_count;
		$arr_fields['download_count'] = $download_count;
		$arr_fields['login_count'] = $login_count;

		$logs_daystats_model->insert($arr_fields);
	}

	protected function update_sitestat()
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
	}

	protected function delete_no_used_peers_ips()
	{
		cg::load_model('peers_connectable_model');
		$peers_connectable_model = peers_connectable_model::get_instance();
		$peers_connectable_model->delete_no_used_peers_ips();
	}

	protected function import_peers_ips()
	{
		cg::load_model('peers_connectable_model');
		$peers_connectable_model = peers_connectable_model::get_instance();
		$peers_connectable_model->import_peers_ips();
	}

	protected function reset_peers_connectable()
	{
		if (date("H") >= 0 && date("H") <= 7)
		{
			return;
		}
		cg::load_model('peers_connectable_model');
		$peers_connectable_model = peers_connectable_model::get_instance();
		$peers_connectable_model->reset_connectable();
	}

	protected function check_peers_connectable()
	{
		$this->import_peers_ips();

		cg::load_model('peers_connectable_model');
		$peers_connectable_model = peers_connectable_model::get_instance();
		$rows = $peers_connectable_model->get_to_check_ips();

		//-1,未检查，0 两个都不可连接，1 ip可连接 2ipv6可连接 3两个都可连接
		$data = array();
		foreach ($rows as $row)
		{
			$ip_connectable = funcs::check_peer_connectable($row['ip'], $row['port']) ? 1 : 0;
			$ipv6_connectable = funcs::check_peer_connectable($row['ipv6'], $row['port']) ? 2 : 0;
			$data[$ip_connectable + $ipv6_connectable][] = $row['id'];
		}
		print_r($data);
		$peers_connectable_model->set_connect($data);
	}

	public function check_peers_connectable_action()
	{
		$dict_crons = array(
			'check_peers_connectable' => 118,
			'reset_peers_connectable' => 3600 * 4 - 100,
			'delete_no_used_peers_ips' => 301
		);
		parent::index($dict_crons);
	}

	public function update_extcredits1_speed()
	{
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$torrents_module->update_extcredits1_speed();
	}

	public function update_torrents_mod()
	{
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$torrents_module->update_torrents_mod();
	}

	public function update_torrents_price_mod()
	{
		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$torrents_price_mod_model->cron_check_enabled();
	}

	public function clear_hour_uploaded_downloaded()
	{
		if (date("i") > 2) //每隔两分钟执行一次
		{
			return;
		}
		$cache_key = 'clear_hour_user_data';
		$data = $this->cache()->get($cache_key);
		if (empty($data))
		{
			cg::load_model('users_stat_model');
			$users_stat_model = users_stat_model::get_instance();
			$users_stat_model->clear_hour_uploaded_downloaded();
			$this->cache()->set($cache_key, '1', 180);
		}
	}

	public function clear_today_uploaded_downloaded()
	{
		if (date("H") == '00')
		{
			$cache_key = 'clear_today_user_data';
			$data = $this->cache()->get($cache_key);
			if (empty($data))
			{
				cg::load_model('users_stat_model');
				$users_stat_model = users_stat_model::get_instance();
				$users_stat_model->clear_today_uploaded_downloaded();
				$this->cache()->set($cache_key, '1', 3600);
			}
		}
	}
}
