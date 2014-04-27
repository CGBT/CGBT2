<?php
class admin_index_controller extends admin_base_controller
{

	public function index_action()
	{
		$this->get_site_stat();
		$this->template_file = 'admin/index_index.php';
		$this->show();
	}

	public function phpinfo_action()
	{
		$this->template_file = 'admin/index_phpinfo.php';
		$this->show();
	}

	public function iframe_phpinfo_action()
	{
		$this->show('admin/phpinfo.php');
	}

	public function index2_action()
	{
		$this->get['same_hour'] = true;
		$this->get_site_stat();
		$this->template_file = 'admin/index_index.php';
		$this->show();
	}

	public function logscron_action()
	{
		$page = isset($this->get['page']) ? intval($this->get['page']) : 1;
		$this_page_url = '/admin/index/logscron/?';
		$params = array(
			'page' => $page
		);
		$this->data['controller'] = $this->data['method'] = $this->data['real_exec'] = '';
		if (!empty($this->get['controller']))
		{
			$params['controller'] = $this->get['controller'];
			$this->data['controller'] = $this->get['controller'];
			$this_page_url .= '&controller=' . $this->get['controller'];
		}
		if (!empty($this->get['method']))
		{
			$params['method'] = $this->get['method'];
			$this->data['method'] = $this->get['method'];
			$this_page_url .= '&method=' . $this->get['method'];
		}
		if (isset($this->get['real_exec']))
		{
			$params['real_exec'] = 1;
			$this->data['real_exec'] = 1;
			$this_page_url .= '&real_exec=1';
		}
		cg::load_model('logs_cron_model');
		$logs_cron_model = logs_cron_model::get_instance();

		$this->data['dict_method'] = $logs_cron_model->get_all_method();
		$data = $logs_cron_model->get_cron_list($params);
		$this->data['list_count'] = $data['count'];
		$this->data['list_rows'] = $data['rows'];

		cg::load_core('cg_pager');
		$total = $this->data['list_count'];
		$this_page_url = $this->get_current_page_url('page');
		$pager = new cg_pager($this_page_url, $total, 100, 6);
		$pager->paginate($page);
		$this->data['pager'] = &$pager;
		$this->show('admin/logs_cron.php');
	}

	private function get_current_page_url($page_param = 'page')
	{
		$url = $_SERVER['SCRIPT_NAME'] . "?$page_param=\$$page_param&";
		unset($this->get[$page_param]);
		$params = $this->get;
		$url .= http_build_query($params);
		rtrim($url, '&');
		return $url;
	}

	public function myip_stat_action()
	{
		cg::load_model('torrents_model');
		$torrents_model = torrents_model::get_instance();
		$this->data['myip_stat_rows'] = $torrents_model->get_myip_stats();

		$this->data['sumsize'] = 0;
		$this->data['sumcount'] = 0;
		foreach ($this->data['myip_stat_rows'] as $key => $row)
		{
			$size = sprintf("%.2f", $row['size'] / 1024 / 1024 / 1024);
			$this->data['myip_stat_rows'][$key]['size'] = $size;
			$this->data['sumsize'] += $size;
			$this->data['sumcount'] += $row['torrents_count'];
		}
		$this->show('admin/index_myip.php');
	}

	private function get_site_stat()
	{
		cg::load_model('logs_sitestat_model');
		$logs_sitestat_model = new logs_sitestat_model();

		if (isset($this->get['same_hour']))
		{
			$this->data['site_stat_rows'] = $logs_sitestat_model->get_admin_index_data('same_hour');
		}
		else
		{
			$this->data['site_stat_rows'] = $logs_sitestat_model->get_admin_index_data('');
		}
	}

	public function daystats_action()
	{
		cg::load_model('logs_daystats_model');
		$logs_daystats_model = logs_daystats_model::get_instance();
		$this->data['daystats'] = $logs_daystats_model->get_rows('', 'id desc', '100');

		$start_timestamp = strtotime(date("Y-m-d"));

		cg::load_model('users_stat_model');
		$users_stat_model = users_stat_model::get_instance();
		$users_count = $users_stat_model->count("last_login > '$start_timestamp'");

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
		$today = date("Y-m-d");

		$arr_fields = array();
		$arr_fields['thedate'] = $today;
		$arr_fields['users_count'] = $users_count;
		$arr_fields['search_count'] = $search_count;
		$arr_fields['completed_count'] = $completed_count;
		$arr_fields['browse_count'] = $browse_count;
		$arr_fields['download_count'] = $download_count;
		$arr_fields['login_count'] = $login_count;
		$this->data['today_stats'][0] = $arr_fields;
		$this->show('admin/index_daystats.php');
	}

	public function getcache_action()
	{
		if (isset($this->get['exec']))
		{
			$cache_key = $this->post['cache_key'];
			$this->data['cache'] = $this->cache()->get($cache_key);
		}
		$this->show('admin/index_getcache.php');
	}

	public function clearcache_action()
	{
		if (isset($this->get['exec']))
		{
			$cache_key = $this->post['cache_key'];
			$id_from = intval($this->post['id_from']);
			$id_to = intval($this->post['id_to']);
			if ($id_from > 0 && $id_to > 0)
			{
				if (stripos($cache_key, '$id') === false)
				{
					$cache_key = $cache_key . '_$id';
				}
				for($i = $id_from; $i <= $id_to; $i++)
				{
					$delete_cache_key = str_replace('$id', $i, $cache_key);
					$this->cache()->delete($delete_cache_key);
				}
			}
			$this->data['cache'] = $this->cache()->get($cache_key);
			$this->cache()->delete($cache_key);
		}
		$this->show('admin/index_clearcache.php');
	}

	public function memcache_action()
	{
		if (isset($this->get['exec']))
		{
			set_time_limit(120);
			$sleep_time = intval($this->post['sleep_time']);
			$servers = cg::config()->config['cache']['memcache']['server'];
			$this->data['cache'] = $this->get_mc_stats($servers, $sleep_time);

			$arr_fields = $this->data['cache']['all'];
			$arr_fields['createtime'] = $this->timestamp;
			cg::load_model('logs_memcached_model');
			$logs_memcached_model = logs_memcached_model::get_instance();
			$logs_memcached_model->insert($arr_fields);
		}
		$this->show('admin/index_memcache.php');
	}

	public function memcached_action()
	{
		if (isset($this->get['exec']))
		{
			set_time_limit(120);
			$sleep_time = intval($this->post['sleep_time']);
			$servers = cg::config()->config['cache']['memcache']['server'];
			$this->data['cache'] = $this->get_mcd_stats($servers, $sleep_time);
		}
		$this->show('admin/index_memcached.php');
	}

	private function get_mcd_stats($servers, $sleep_time)
	{
		$data = array(
			'all' => array()
		);
		$sum_read_bytes = 0;
		$sum_written_bytes = 0;
		$sum_get = 0;
		$sum_set = 0;

		$data['all']['total_size'] = 0;
		$data['all']['used_size'] = 0;

		foreach ($servers as $server)
		{
			$mc = new Memcached();
			$mc->addserver($server['host'], $server['port']);
			$s = $server['host'] . ':' . $server['port'];
			$stats1 = $mc->getStats();
			sleep($sleep_time);
			$stats2 = $mc->getStats();
			$stats1 = array_values($stats1);
			$stats2 = array_values($stats2);
			$stats1 = $stats1[0];
			$stats2 = $stats2[0];
			$stat['uptime'] = $stats2['uptime'];
			$stat['start_time'] = date("Y-m-d H:i:s", time() - $stats2['uptime']);

			$stat['used_size'] = $stats2['bytes'] / 1024 / 1024 . "MBytes";
			//$stat['total_size'] = $stats2['limit_maxbytes'] / 1024 / 1024 . "MBytes";



			$stat['curr_connections'] = $stats1['curr_connections'] . '-' . $stats2['curr_connections'];
			$stat['total_connections'] = $stats2['total_connections'];
			$stat['increase_connections'] = $stats2['total_connections'] - $stats1['total_connections'];
			$stat['cmd_get'] = $stats2['cmd_get'];
			$stat['increase_cmd_get'] = $stats2['cmd_get'] - $stats1['cmd_get'];
			$stat['increase_cmd_get_avg'] = ($stats2['cmd_get'] - $stats1['cmd_get']) / $sleep_time;

			$stat['cmd_set'] = $stats2['cmd_set'];
			$stat['increase_cmd_set'] = $stats2['cmd_set'] - $stats1['cmd_set'];
			$stat['increase_cmd_set_avg'] = ($stats2['cmd_set'] - $stats1['cmd_set']) / $sleep_time;

			$stat['increase_get_hits'] = $stats2['get_hits'] - $stats1['get_hits'];
			$stat['increase_get_misses'] = $stats2['get_misses'] - $stats1['get_misses'];

			$stat['increase_get_hits_percent'] = sprintf('%.2f', ($stats2['get_hits'] - $stats1['get_hits']) * 100 /
			 ($stats2['get_hits'] - $stats1['get_hits'] + $stats2['get_misses'] - $stats1['get_misses'])) . '%';
			$stat['increase_get_misses_percent'] = sprintf('%.2f', ($stats2['get_misses'] - $stats1['get_misses']) * 100 /
			 ($stats2['get_hits'] - $stats1['get_hits'] + $stats2['get_misses'] - $stats1['get_misses'])) . '%';

			$stat['bytes_read'] = $stats2['bytes_read'] / 1024 / 1024 / 1024 . 'GBytes';
			$stat['increase_bytes_read'] = ($stats2['bytes_read'] - $stats1['bytes_read']) / 1024 . 'KBytes';
			$stat['bytes_read_speed'] = ($stats2['bytes_read'] - $stats1['bytes_read']) / 1024 / $sleep_time . 'KBytes/s';

			$stat['bytes_written'] = $stats2['bytes_written'] / 1024 / 1024 / 1024 . 'GBytes';
			$stat['increase_bytes_written'] = ($stats2['bytes_written'] - $stats1['bytes_written']) / 1024 . 'KBytes';
			$stat['bytes_written_speed'] = ($stats2['bytes_written'] - $stats1['bytes_written']) / 1024 / $sleep_time . 'KBytes/s';

			$sum_read_bytes += $stat['increase_bytes_read'];
			$sum_written_bytes += $stat['increase_bytes_written'];
			$sum_get += $stat['increase_cmd_get'];
			$sum_set += $stat['increase_cmd_set'];

			//$data['all']['total_size'] += $stats2['limit_maxbytes'];
			$data['all']['used_size'] += $stats2['bytes'];

			$data[$s] = $stat;
		}

		$data['all']['total_size'] = $data['all']['total_size'] / 1024 / 1024 . "MBytes";
		$data['all']['used_size'] = $data['all']['used_size'] / 1024 / 1024 . "MBytes";

		$data['all']['bytes_read_speed'] = $sum_read_bytes / $sleep_time / (count($servers)) . "KBytes/s";
		$data['all']['bytes_written_speed'] = $sum_written_bytes / $sleep_time / (count($servers)) . "KBytes/s";

		$data['all']['increase_cmd_get_speed'] = $sum_get / $sleep_time / (count($servers));
		$data['all']['increase_cmd_set_speed'] = $sum_set / $sleep_time / (count($servers));

		return $data;
	}

	private function get_mc_stats($servers, $sleep_time)
	{
		$data = array(
			'all' => array()
		);
		$sum_read_bytes = 0;
		$sum_written_bytes = 0;
		$sum_get = 0;
		$sum_set = 0;

		$data['all']['total_size'] = 0;
		$data['all']['used_size'] = 0;

		foreach ($servers as $server)
		{
			$mc = new Memcache();
			$mc->addserver($server['host'], $server['port']);
			$s = $server['host'] . ':' . $server['port'];
			$stats1 = $mc->getstats();
			sleep($sleep_time);
			$stats2 = $mc->getstats();
			$stat['uptime'] = $stats2['uptime'];
			$stat['start_time'] = date("Y-m-d H:i:s", time() - $stats2['uptime']);
			$stat['used_size'] = $stats2['bytes'] / 1024 / 1024 . "MBytes";
			$stat['total_size'] = $stats2['limit_maxbytes'] / 1024 / 1024 . "MBytes";

			$stat['curr_connections'] = $stats1['curr_connections'] . '-' . $stats2['curr_connections'];
			$stat['total_connections'] = $stats2['total_connections'];
			$stat['increase_connections'] = $stats2['total_connections'] - $stats1['total_connections'];
			$stat['cmd_get'] = $stats2['cmd_get'];
			$stat['increase_cmd_get'] = $stats2['cmd_get'] - $stats1['cmd_get'];
			$stat['increase_cmd_get_avg'] = ($stats2['cmd_get'] - $stats1['cmd_get']) / $sleep_time;

			$stat['cmd_set'] = $stats2['cmd_set'];
			$stat['increase_cmd_set'] = $stats2['cmd_set'] - $stats1['cmd_set'];
			$stat['increase_cmd_set_avg'] = ($stats2['cmd_set'] - $stats1['cmd_set']) / $sleep_time;

			$stat['increase_get_hits'] = $stats2['get_hits'] - $stats1['get_hits'];
			$stat['increase_get_misses'] = $stats2['get_misses'] - $stats1['get_misses'];

			$stat['increase_get_hits_percent'] = sprintf('%.2f', ($stats2['get_hits'] - $stats1['get_hits']) * 100 /
			 ($stats2['get_hits'] - $stats1['get_hits'] + $stats2['get_misses'] - $stats1['get_misses'])) . '%';
			$stat['increase_get_misses_percent'] = sprintf('%.2f', ($stats2['get_misses'] - $stats1['get_misses']) * 100 /
			 ($stats2['get_hits'] - $stats1['get_hits'] + $stats2['get_misses'] - $stats1['get_misses'])) . '%';

			$stat['bytes_read'] = $stats2['bytes_read'] / 1024 / 1024 / 1024 . 'GBytes';
			$stat['increase_bytes_read'] = ($stats2['bytes_read'] - $stats1['bytes_read']) / 1024 . 'KBytes';
			$stat['bytes_read_speed'] = ($stats2['bytes_read'] - $stats1['bytes_read']) / 1024 / $sleep_time . 'KBytes/s';

			$stat['bytes_written'] = $stats2['bytes_written'] / 1024 / 1024 / 1024 . 'GBytes';
			$stat['increase_bytes_written'] = ($stats2['bytes_written'] - $stats1['bytes_written']) / 1024 . 'KBytes';
			$stat['bytes_written_speed'] = ($stats2['bytes_written'] - $stats1['bytes_written']) / 1024 / $sleep_time . 'KBytes/s';

			$sum_read_bytes += $stat['increase_bytes_read'];
			$sum_written_bytes += $stat['increase_bytes_written'];
			$sum_get += $stat['increase_cmd_get'];
			$sum_set += $stat['increase_cmd_set'];

			$data['all']['total_size'] += $stats2['limit_maxbytes'];
			$data['all']['used_size'] += $stats2['bytes'];

			$data[$s] = $stat;
		}

		$data['all']['total_size'] = $data['all']['total_size'] / 1024 / 1024 . "MBytes";
		$data['all']['used_size'] = $data['all']['used_size'] / 1024 / 1024 . "MBytes";

		$data['all']['bytes_read_speed'] = $sum_read_bytes / $sleep_time / (count($servers)) . "KBytes/s";
		$data['all']['bytes_written_speed'] = $sum_written_bytes / $sleep_time / (count($servers)) . "KBytes/s";

		$data['all']['increase_cmd_get_speed'] = $sum_get / $sleep_time / (count($servers));
		$data['all']['increase_cmd_set_speed'] = $sum_set / $sleep_time / (count($servers));

		return $data;
	}

	public function credits_action()
	{
		cg::load_model('logs_credits_model');
		cg::load_core('cg_pager');
		$itemPerPage = 20;
		$logs_credits_model = logs_credits_model::get_instance();
		$total = $logs_credits_model->count();
		$pager = new cg_pager('/admin/index/credits/?p=$page', $total, $itemPerPage, 10);
		$this->page = isset($this->get['p']) ? intval($this->get['p']) : 1;
		$pager->paginate($this->page);
		$limit = $pager->limit;
		$this->data['rows_credits'] = $logs_credits_model->get_rows('', 'id desc', $limit);
		$this->data['pager'] = &$pager;
		$this->template_file = 'admin/credits.php';
		$this->show();
	}

	public function agentinfo_action()
	{
		cg::load_model('agentinfo_model');
		$agentinfo_model = agentinfo_model::get_instance();
		$this->data['agentinfo'] = $agentinfo_model->get_rows();
		$this->show('admin/agentinfo.php');
	}

	public function logslogin_action()
	{
		cg::load_model('logs_login_model');
		$username = isset($this->get['username']) ? $this->get['username'] : '';
		if (!empty($username))
		{
			$users_model = users_model::get_instance();
			$uid = $users_model->get_uid_by_username($username);
			if (empty($uid))
			{
				$uid = -1;
			}
			$logs_login_model = logs_login_model::get_instance();
			$this->data['logs_login'] = $logs_login_model->get_rows('uid =' . $uid, 'createtime desc', 100);
		}
		else
		{
			$logs_login_model = logs_login_model::get_instance();
			$this->data['logs_login'] = $logs_login_model->get_rows('', 'createtime desc', 100);
		}
		$this->show('admin/logs_login.php');
	}
}