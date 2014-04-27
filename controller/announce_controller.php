<?php
class announce_controller extends cg_controller
{
	private $passkey, $info_hash, $peer_id, $port, $uploaded, $downloaded, $left;
	private $ipv6, $event, $is_seeder, $agent;
	private $user, $torrent, $peer, $current_peer_exists; //current
	private $uid, $tid, $pid;
	private $users_model, $torrents_model, $peers_model;
	private $users_module, $torrents_module;
	private $upthis, $downthis, $extcredits1;
	private $setting, $setting_model;
	private $min_interval, $announce_interval, $min_interval_limit, $peer_clean_time, $peer_force_clean_time;
	private $black_peer_ids, $black_agents, $black_ips;
	private $peers_list;
	//private $t1, $t2;
	public function __construct()
	{
		//$this->t1 = microtime(1);
		ini_set('display_errors', 'off');
		parent::__construct();
		$this->timestamp = time();

		cg::load_model('base_model');
		cg::load_module('base_module');
		cg::load_module('users_module');
		cg::load_model('peers_model');
		$this->users_module = users_module::get_instance();
		$this->peers_model = peers_model::get_instance();
		$this->setting = $this->users_module->setting;
		$this->get_tracker_setting();
	}

	private function get_tracker_setting()
	{
		$this->min_interval = intval($this->setting['tracker_min_interval']);
		$this->announce_interval = intval($this->setting['tracker_announce_interval']);
		$this->min_interval_limit = intval($this->setting['tracker_min_interval_limit']);
		$this->peer_clean_time = intval($this->setting['tracker_peer_clean_time']);
		$this->peer_force_clean_time = intval($this->setting['tracker_peer_force_clean_time']);
		$this->black_peer_ids = funcs::explode($this->setting['tracker_black_peer_id']);
		$this->black_agents = funcs::explode($this->setting['tracker_black_agent']);
		$this->black_ips = funcs::explode($this->setting['tracker_black_ips']);

		if (empty($this->min_interval))
		{
			$this->min_interval = 120;
		}
		if (empty($this->announce_interval))
		{
			$this->announce_interval = 1800;
		}
		if (empty($this->min_interval_limit))
		{
			$this->min_interval_limit = 30;
		}
		if (empty($this->peer_clean_time) || $this->peer_clean_time <= $this->announce_interval)
		{
			$this->peer_clean_time = $this->announce_interval + 120;
		}
	}

	public function index_action()
	{
		//获取所有参数，并检查参数合法性
		$this->get_params();

		//检查客户端软件类型，封禁客户端
		$this->check_agent();

		//根据passkey获取当前的用户
		$this->get_user();

		//一个用户不能多个ip下载
		$this->check_multi_ip_download();

		//根据info_hash获取当前的种子
		$this->get_torrent();

		//检查共享率限制
		$this->check_ratio_limit();

		//查找当前的peer是否存在，如果存在则获取当前的$this->peer
		$this->check_peer_exists();

		//计算本次上传和下载的流量，可能为0
		$this->get_upthis_downthis();

		//获取保种积分
		$this->get_extcredits1();

		//更新用户的上传和下载
		$this->update_user();

		//插入或更新peer
		$this->insert_or_update_peer();

		//更新用户的保种积分
		$this->update_user_extcredits1();

		/***  以下都可以去掉  start ***/

		//更新用户的最后登录时间
		$this->update_user_lastlogin();

		//记录新的客户端
		$this->log_new_agent();

		//上传速度检查，记录高速的日志
		$this->log_high_speed();

		//记录下载完成的日志
		$this->log_completed();

		//清理过期的peers
		$this->delete_old_peers();
		/***    end    ***/

		//先获取该种子所有的peers(3600秒缓存，新增peer和删除peer都实时更新缓存)，
		//计算出种子数和下载数，然后更新种子表数据，然后再输出返回
		$this->get_peers_list();

		//然后更新种子表的种子数和下载数
		$this->update_torrent();

		//返回peer list
		$this->response_peers();
	}

	private function get_peers_list()
	{
		$ids = $this->peers_model->get_ids_by_torrent($this->tid); //取全部的peer，有可能上千，量有点大
		//unset($ids[array_search($this->pid, $ids)]); //不能过滤，update_torrent用到了，response的时候再过滤
		if (empty($ids))
		{
			$this->peers_list = array();
		}
		else
		{
			$cache_key = 'peers_' . md5(json_encode($ids));
			$this->peers_list = cg::cache()->get($cache_key);
			if (empty($this->peers_list))
			{
				$this->peers_list = $this->peers_model->get_peers_by_ids($ids);
				cg::cache()->set($cache_key, $this->peers_list, 60);
			}
		}
	}

	private function log_completed()
	{
		if ($this->event != 'completed')
		{
			return;
		}
		cg::load_model('logs_completed_model');
		$logs_completed_model = logs_completed_model::get_instance();
		$arr_fields = array();
		$arr_fields['tid'] = $this->tid;
		$arr_fields['uid'] = $this->uid;
		$arr_fields['createtime'] = $this->timestamp;
		$logs_completed_model->insert($arr_fields);
	}

	private function get_upthis_downthis()
	{
		$this->upthis = 0;
		$this->downthis = 0;

		if (!$this->current_peer_exists || $this->event == 'started')
		{
			//peer不存在即第一次插入或event为started时，不记录上传下载
			return;
		}

		$period = $this->timestamp - $this->peer['last_action'];
		if ($period < $this->min_interval_limit)
		{
			//两次连接时间差小于30秒则忽略，不记录上传下载
			return;
		}

		if (!empty($this->peer['last_event']) && $this->event == $this->peer['last_event'])
		{
			//连续两次都是started,stopped,completed，则不记录上传下载
			return;
		}

		$this->upthis = max(0, $this->uploaded - $this->peer["uploaded"]);
		$this->downthis = max(0, $this->downloaded - $this->peer["downloaded"]);
	}

	private function response_peers()
	{
		cg::load_class('cg_bcode');
		/*
		 * update_torrent里面，更新了$this->torrent，种子数完成数等已纠正
		 */
		$response_data = array();
		$response_data['interval'] = $this->announce_interval;
		$response_data['min interval'] = $this->min_interval; //限制ut手工更新时间间隔
		$response_data['downloaded'] = intval($this->torrent['complete']);
		$response_data['complete'] = intval($this->torrent['seeder']);
		$response_data['incomplete'] = intval($this->torrent['leecher']);
		$response_data['peers'] = array();

		$i = 0;
		$to_be_deleted_pids = array();

		foreach ($this->peers_list as $key => $row)
		{
			//缓存失效导致
			if (empty($row))
			{
				$this->logs_debug(json_encode($row), 'peer_empty0');
				continue;
			}
			if (!isset($row['uid']))
			{
				$this->logs_debug(json_encode($row), 'peer_empty');
				continue;
			}
			//排除自身,或者可以用peer_id来排除,pid在insert_peer里面获取
			if ($row['id'] == $this->pid)
			{
				continue;
			}
			//last_action默认为$this->timestamp - 300
			if ($row['last_action'] < $this->timestamp - $this->peer_clean_time)
			{
				/* fix memcached setkey error
				$peer = $this->peers_model->get_peer_from_db($row['id']);
				if ($peer['last_action'] > $row['last_action'])
				{
					$this->peers_list[$key] = $peer;
					continue;
				}
				*/
				if ($row['uid'] == 5 || $row['uid'] == 7)
				{
					$p = $this->timestamp - $row['last_action'];
					$this->logs_debug($this->tid . '|||' . $row['id'] . '|||' . $row['last_action'] . '|||' . $p . '|||' . $row['uid'], 'response1');
				}

				//删除过期的peer
				$to_be_deleted_pids[] = $row['id'];
				//continue; //不跳过继续输出
			}

			//会造成数字每个人的不一致
			if ($this->is_seeder && $row['is_seeder'])
			{
				continue;
			}

			if (!empty($row['ip']))
			{
				$response_data['peers'][$i]['ip'] = $row['ip'];
				$response_data['peers'][$i]['port'] = intval($row['port']);
				$i++;
			}
			if (!empty($row['ipv6']))
			{
				$response_data['peers'][$i]['ip'] = $row['ipv6'];
				$response_data['peers'][$i]['port'] = intval($row['port']);
				$i++;
			}
		}
		if (!empty($to_be_deleted_pids))
		{
			$this->peers_model->delete_peers($to_be_deleted_pids, $this->tid);
		}

		if (isset($_GET['debug']) && $this->is_developer())
		{
			$response_data['execute_time'] = $this->get_execute_time();
			$response_data['sql'] = $this->get_all_sql();
			$response_data['cache_keys'] = $this->get_cache_keys();

			echo "query_time";
			print_r($this->data['db_stat']['query_time']);
			echo "connectttime";
			print_r($this->data['db_stat']['connect_time']);
			print_r($response_data);
		}
		else
		{
			echo bencode($response_data);
		}

		//$this->t2 = microtime(1);
		//$this->logs_debug($this->t2 - $this->t1, 'tracker_time');
		die();
	}

	private function update_torrent()
	{
		$arr_fields = array(
			'seeder' => 0,
			'leecher' => 0
		);

		//不包含自身
		foreach ($this->peers_list as $peer)
		{
			if ($peer['last_action'] < $this->timestamp - $this->peer_clean_time)
			{
				//过期了需要清理
				continue;
			}

			if ($peer['is_seeder'])
			{
				$arr_fields['seeder']++;
			}
			else
			{
				$arr_fields['leecher']++;
			}
		}

		/*
		if ($this->event == 'started')
		{
			if (!$this->current_peer_exists)
			{
				if ($this->is_seeder)
				{
					$arr_fields['seeder'] = $this->torrent['seeder'] + 1;
				}
				else
				{
					$arr_fields['leecher'] = $this->torrent['leecher'] + 1;
				}
			}
		}
		elseif ($this->event == 'stopped')
		{
			if ($this->current_peer_exists)
			{
				if ($this->peer['is_seeder'])
				{
					$arr_fields['seeder'] = $this->torrent['seeder'] - 1;
					$arr_fields['seeder'] = $arr_fields['seeder'] < 0 ? 0 : $arr_fields['seeder'];
				}
				else
				{
					$arr_fields['leecher'] = $this->torrent['leecher'] - 1;
					$arr_fields['leecher'] = $arr_fields['leecher'] < 0 ? 0 : $arr_fields['leecher'];
				}
			}
		}
		elseif ($this->event == 'completed')
		{
			$arr_fields['complete'] = $this->torrent['complete'] + 1;
			if (!$this->current_peer_exists)
			{
				if ($this->is_seeder)
				{
					$arr_fields['seeder'] = $this->torrent['seeder'] + 1;
				}
				else
				{
					$arr_fields['leecher'] = $this->torrent['leecher'] + 1;
				}
			}
			else
			{
				if ($this->is_seeder)
				{
					$arr_fields['seeder'] = $this->torrent['seeder'] + 1;
					$arr_fields['leecher'] = $this->torrent['leecher'] - 1;
					$arr_fields['leecher'] = $arr_fields['leecher'] < 0 ? 0 : $arr_fields['leecher'];
				}
			}
		}
		else
		{
			if (!$this->current_peer_exists)
			{
				if ($this->is_seeder)
				{
					$arr_fields['seeder'] = $this->torrent['seeder'] + 1;
				}
				else
				{
					$arr_fields['leecher'] = $this->torrent['leecher'] + 1;
				}
			}
		}
		*/
		if ($this->event == 'completed')
		{
			$arr_fields['complete'] = $this->torrent['complete'] + 1;
		}
		if (intval($arr_fields['seeder']) == intval($this->torrent['seeder']))
		{
			unset($arr_fields['seeder']);
		}
		if (intval($arr_fields['leecher']) == intval($this->torrent['leecher']))
		{
			unset($arr_fields['leecher']);
		}
		if ($this->is_seeder)
		{
			//超过半天则更新，或者跟随seeder,leecher数更新
			$arr_fields['last_action'] = $this->timestamp;
			$arr_fields['last_username'] = $this->user['username'];
			$arr_fields['last_uid'] = $this->uid;
		}
		$cache_key = "tracker_torrent_stat_$this->tid";
		$count = count($arr_fields);

		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();

		if ($count > 0)
		{
			if ($count == 3 && !isset($arr_fields['complete']))
			{
				$data = $this->cache()->get($cache_key);
				if (empty($data))
				{
					//index表，暂无这三个字段，不需要更新index表
					$torrents_module->torrents_stat_model->update($arr_fields, $this->tid);
					$this->cache()->set($cache_key, $this->timestamp, 43200);
				}
			}
			else
			{
				$seeder_updatetime_cache_key = "seeder_updatetime_$this->tid";
				if (isset($arr_fields['complete']) || (isset($arr_fields['seeder']) && $arr_fields['seeder'] < 10) ||
				 (isset($arr_fields['leecher']) && $arr_fields['leecher'] % 3 == 0)) //小于10则立刻更新
				{
					$this->cache()->set($seeder_updatetime_cache_key, $this->timestamp);
					$torrents_module->torrents_stat_model->update($arr_fields, $this->tid);
					$torrents_module->torrents_index_model->update($arr_fields, $this->tid, false);
					if ($this->is_seeder)
					{
						$this->cache()->set($cache_key, $this->timestamp, 43200);
					}
				}
				else //大于10则一个小时更新一次
				{
					$seeder_updatetime = $this->cache()->get($seeder_updatetime_cache_key);
					if (empty($seeder_updatetime))
					{
						$this->cache()->set($seeder_updatetime_cache_key, $this->timestamp);
					}
					else
					{
						if ($this->timestamp - $seeder_updatetime > 3600)
						{
							$this->cache()->set($seeder_updatetime_cache_key, $this->timestamp);
							$torrents_module->torrents_stat_model->update($arr_fields, $this->tid);
							$torrents_module->torrents_index_model->update($arr_fields, $this->tid, false);
							if ($this->is_seeder)
							{
								$this->cache()->set($cache_key, $this->timestamp, 43200);
							}
						}
					}
				}
			}
		}
		//更新torrent之后，需要更新$this->torrent，然后response_peers会用到。
		$this->torrent = $torrents_module->get_torrent($this->tid);
	}

	private function update_user()
	{
		if (!$this->current_peer_exists || $this->event == 'started')
		{
			return;
		}

		$period = $this->timestamp - $this->peer['last_action'];
		if ($period < $this->min_interval_limit)
		{
			return;
		}

		if (!empty($this->peer['last_event']) && $this->event == $this->peer['last_event'])
		{
			return;
		}

		$upthis = $this->upthis;
		$downthis = $this->downthis;
		$extcredits1 = $this->extcredits1;

		if ($upthis > 0 || $downthis > 0)
		{
			$upload_factor = $this->get_upload_factor($this->torrent, $this->uid);
			$download_factor = $this->get_download_factor($this->torrent);
			$upthis2 = $upthis * ($upload_factor - 1);
			$downthis2 = $downthis * (1 - $download_factor);

			//$upthis = $upthis + $upthis2;
			//$downthis = $downthis - $downthis2;
			//$upthis2 = 0; //虚拟上传，暂不记录
			//$downthis2 = 0; //虚拟下载，暂不记录
			if ($upthis > 0 || $downthis > 0 || $upthis2 > 0 || $downthis2 > 0)
			{
				$extcredits1_cache_key = "user_extcredits1_$this->uid";
				$old_extcredits1 = $this->cache()->get($extcredits1_cache_key);
				if (!empty($old_extcredits1))
				{
					$extcredits1 = $extcredits1 + $old_extcredits1['extcredits1'];
				}
				$set = "uploaded = uploaded + $upthis, uploaded2 = uploaded2 + $upthis2,
						downloaded = downloaded + $downthis, downloaded2 = downloaded2 + $downthis2,
						today_uploaded = today_uploaded + $upthis, today_uploaded2 = today_uploaded2 + $upthis2,
						today_downloaded = today_downloaded + $downthis, today_downloaded2 = today_downloaded2 + $downthis2,
						hour_uploaded = hour_uploaded + $upthis, hour_uploaded2 = hour_uploaded2 + $upthis2,
						hour_downloaded = hour_downloaded + $downthis, hour_downloaded2 = hour_downloaded2 + $downthis2,
						last_action = '$this->timestamp', extcredits1 = extcredits1 + $extcredits1";
				$this->users_module->users_stat_model->tracker_update($set, $this->uid);
				$this->cache()->set("lastlogin_$this->uid", $this->timestamp);
				$extcredits1_data = array(
					'time' => $this->timestamp,
					'extcredits1' => 0
				);
				$this->cache()->set($extcredits1_cache_key, $extcredits1_data);
				$this->extcredits1 = 0;
			}
		}
	}

	private function get_download_factor($torrent)
	{
		$isfree = $torrent['isfree'] || $torrent['auto_isfree'];
		$is30p = $torrent['is30p'] || $torrent['auto_is30p'];
		$ishalf = $torrent['ishalf'] || $torrent['auto_ishalf'];
		$size = $torrent['size'];
		$G = 1024 * 1024 * 1024;

		$free_factor = $isfree ? 0 : ($is30p ? 0.3 : ($ishalf ? 0.5 : 1));
		$size_factor = 1;
		if ($size > 5 * $G)
		{
			$size_factor = sprintf("%.2f", 1 / (1 + ($size - 5 * $G) / (20 * $G)));
		}
		return min($free_factor, $size_factor);
	}

	private function get_upload_factor($torrent, $uid)
	{
		if (!$this->setting['enable_upload_factor'])
		{
			return $torrent['auto_is2x'] ? 2 : 1;
		}
		$timestamp = time();
		$added = $torrent['createtime'];
		$size = $torrent['size'];
		$top = $torrent['istop'];
		$recommend = $torrent['isrecommend'];
		$seeder = $torrent['uid'] == $uid;
		$isfree = $torrent['isfree'] || $torrent['auto_isfree'];
		$is30p = $torrent['is30p'] || $torrent['auto_is30p'];
		$ishalf = $torrent['ishalf'] || $torrent['auto_ishalf'];

		$G = 1024 * 1024 * 1024;

		$added_factor = max(1, 1 + ($timestamp - $added - 7 * 86400) / (90 * 86400));
		$added_factor = min(2, $added_factor);

		$size_factor = max(1, 1 + ($size - 2 * $G) / (15 * $G));
		$size_factor = min(2, $size_factor);

		$top_factor = $top ? 1.3 : ($recommend ? 1.2 : 1);
		$free_factor = $seeder ? 1 : ($isfree ? 0.7 : ($is30p ? 0.8 : ($ishalf ? 0.9 : 1)));
		$seeder_factor = $seeder ? 2 : 1;

		$factor = sprintf("%.2f", $added_factor * $size_factor * $top_factor * $free_factor * $seeder_factor);
		$factor = min($factor, 3);
		$factor = max($factor, 1);
		if ($torrent['is2x'] || $torrent['auto_is2x'])
		{
			$factor = max($factor, 2);
		}
		return sprintf("%.1f", $factor);
	}

	private function update_user_extcredits1()
	{
		//按照时间和积分值来更新, 每隔两个小时或积分积累大于0.5则更新
		//如果有上传或者下载也会更新
		if ($this->extcredits1 == 0)
		{
			return;
		}
		$extcredits1_cache_key = "user_extcredits1_$this->uid";
		$extcredits1_data = $this->cache()->get($extcredits1_cache_key);
		if (!empty($extcredits1_data))
		{
			$extcredits1 = $this->extcredits1 + $extcredits1_data['extcredits1'];
			$extcredits1_data = array(
				'time' => $extcredits1_data['time'],
				'extcredits1' => $extcredits1
			);
		}
		else
		{
			$extcredits1 = $this->extcredits1;
			$extcredits1_data = array(
				'time' => $this->timestamp,
				'extcredits1' => $extcredits1
			);
		}

		if ($extcredits1 > 0.5 || $this->timestamp - $extcredits1_data['time'] > 3600 * 2)
		{
			$set = "extcredits1 = extcredits1 + $extcredits1, last_login = '$this->timestamp'";
			$this->users_module->users_stat_model->tracker_update($set, $this->uid);
			$this->cache()->set("lastlogin_$this->uid", $this->timestamp); //既然更新，就多更新一个字段
			$extcredits1_data = array(
				'time' => $this->timestamp,
				'extcredits1' => 0
			);
		}
		$this->cache()->set($extcredits1_cache_key, $extcredits1_data);
	}

	private function update_user_lastlogin()
	{
		$cache_key = "lastlogin_$this->uid";
		$timestamp = $this->cache()->get($cache_key);
		if (empty($timestamp))
		{
			return;
		}
		if ($this->timestamp - $timestamp > 3600 * 3)
		{
			//由于积分是每隔两个小时更新，同时也更新了last_login，因此这里应该不会执行。
			$arr_fields = array();
			$arr_fields['last_login'] = $this->timestamp;
			cg::load_model('users_stat_model');
			$users_stat_model = users_stat_model::get_instance();
			$users_stat_model->update($arr_fields, $this->uid);
			$this->cache()->set($cache_key, $this->timestamp);
		}
	}

	private function get_extcredits1()
	{
		$this->extcredits1 = 0;
		if (!$this->current_peer_exists || $this->event == 'started')
		{
			return;
		}
		$period = $this->timestamp - $this->peer['last_action'];
		if ($period < $this->min_interval_limit)
		{
			return;
		}

		if (!empty($this->peer['last_event']) && $this->event == $this->peer['last_event'])
		{
			return;
		}
		if (!$this->peer['is_seeder'] && $this->is_seeder) //必须从上一次开始即为种子时才记录积分
		{
			return;
		}

		//config
		$extcredits1_max = $this->setting['extcredits1_max']; //最大值
		$extcredits1_min = $this->setting['extcredits1_min']; //最小值
		$extcredits1_size = $this->setting['extcredits1_size']; //5G
		$extcredits1_seeders = $this->setting['extcredits1_seeders']; //7个种子数
		$extcredits1_weeks = $this->setting['extcredits1_weeks']; //8周
		$extcredits1_max_seeders = 200; //大于200按1计算，避免种子数溢出错误。
		//params
		$added = $this->torrent['createtime'];
		$size = $this->torrent['size'];
		$seeders = $this->torrent['seeder'];
		$last_action = $this->peer['last_action'];
		$G = 1024 * 1024 * 1024;
		$weeks = ($this->timestamp - $added) / (7 * 24 * 60 * 60);
		$size = $size / $G / $extcredits1_size;
		if ($seeders > $extcredits1_max_seeders)
		{
			$seeders = $extcredits1_max_seeders;
		}
		$credits = (1 - pow(10, (-$weeks / $extcredits1_weeks))) * $size * (1 + sqrt(2) * pow(10, (-($seeders - 1) / $extcredits1_seeders)));
		$credits = $extcredits1_max * atan($credits / 300) * 2 / pi();
		if ($credits < $extcredits1_min)
		{
			$credits = $extcredits1_min;
		}
		$period = $this->timestamp - $last_action;
		$credits = $credits * $period / 3600;
		$this->extcredits1 = $credits;
		return $credits;
	}

	private function log_high_speed()
	{
		$upspeed = 0;
		if ($this->upthis > 0) //此时 $this->peer 存在
		{
			$period = $this->timestamp - $this->peer['last_action'];
			$upspeed = $this->upthis / $period;
			$upspeed = sprintf("%.2f", $upspeed / 1024 / 1024);
		}
		if ($upspeed > 5)
		{
			//@todo
		}
	}

	private function check_peer_exists()
	{
		$this->pid = $this->peers_model->get_self_peer($this->tid, $this->peer_id);

		if (empty($this->pid))
		{
			//如果不存在，则$this->peer,$this->pid后面都用不到
			//不存在则后面插入之后还需要获取this->peer ??
			$this->current_peer_exists = false;
		}
		else
		{
			$this->peer = $this->peers_model->get_peer($this->pid);
			if (empty($this->peer))
			{
				//批量删除的时候，没有清空selfpeer缓存
				//删除peer的时候，如果selfpeer的缓存没有清理，会出现这种情况
				$this->current_peer_exists = false;
			}
			else
			{
				$this->current_peer_exists = true;
			}
		}

		if ($this->current_peer_exists)
		{
			if ($this->peer['uid'] != $this->uid)
			{
				$this->failure_message("请勿修改Tracker。种子内可能包含多个Tracker地址，请修改为一个(右键-属性修改Tracker)。如果无误请停止种子重新开始。");
			}
			if ($this->event != 'completed' && $this->timestamp - $this->peer['last_action'] < 10)
			{
				$this->failure_message("种子内可能包含多个Tracker地址，请修改为一个(右键-属性修改Tracker)。如果无误请停止种子重新开始。");
			}
		}
	}

	private function insert_or_update_peer()
	{
		if ($this->current_peer_exists)
		{
			if ($this->event == 'stopped')
			{
				$this->peers_model->delete_peer($this->pid, $this->tid);
			}
			else
			{
				$this->update_peer();
			}
		}
		else
		{
			if ($this->event == 'stopped')
			{
				$this->peers_model->delete_peer($this->pid, $this->tid);
				if ($this->uid == 5 || $this->uid == 7 || $this->user['is_moderator'])
				{
					$this->logs_debug($this->tid . '|||' . $this->pid, 'not_exists_delete');
				}
			}
			else
			{
				$this->insert_peer();
			}
		}
	}

	private function update_peer()
	{
		$arr_fields = array();
		$dict_fields = array(
			'tid',
			'uid',
			'peer_id',
			'ip',
			'ipv6',
			'port',
			'uploaded',
			'downloaded',
			'left',
			'is_seeder',
			'agent'
		);
		foreach ($dict_fields as $f)
		{
			if ($this->peer[$f] != $this->{$f})
			{
				$arr_fields[$f] = $this->{$f};
			}
		}
		$arr_fields['tid'] = $this->tid;
		$arr_fields['username'] = $this->user['username'];
		if ($this->event != $this->peer['last_event'])
		{
			$arr_fields['last_event'] = $this->event;
		}

		if ($this->peer['size'] != $this->torrent['size'])
		{
			$arr_fields["size"] = $this->torrent['size'];
		}
		//$arr_fields['connectable'] = '1';
		if ($this->event == "completed")
		{
			$arr_fields["completed_time"] = $this->timestamp;
		}
		$arr_fields["last_action"] = $this->timestamp;
		/*
		$cache_key = 'peers_last_action_' . $this->pid;
		$updatetime_cache_key = 'peers_last_action_updatetime_' . $this->pid;
		$this->cache()->set($cache_key, $this->timestamp);

		if (count($arr_fields) == 1) //only last_action
		{
			$last_action_updatetime = $this->cache()->get($updatetime_cache_key);
			if (empty($last_action_updatetime))
			{
				$last_action_updatetime = 0;
			}
			//@todo 定义这个7200，last_action超过这个时间的peers是真正可以删除的。
			if ($this->timestamp - $last_action_updatetime > $this->peer_force_clean_time)
			{
				cg::load_model('peers_model');
				$peers_model = peers_model::get_instance();
				$peers_model->update($arr_fields, $this->pid);
				$this->cache()->set($updatetime_cache_key, $this->timestamp);
			}
		}
		else
		{
		*/

		$this->peers_model->update_peer($arr_fields, $this->pid);
		//	$this->cache()->set($updatetime_cache_key, $this->timestamp);
		//}
	}

	private function insert_peer()
	{
		$arr_fields = array();
		$arr_fields["tid"] = $this->tid;
		$arr_fields["uid"] = $this->uid;
		$arr_fields["username"] = $this->user['username'];
		$arr_fields["peer_id"] = $this->peer_id;

		$arr_fields["ip"] = $this->ip;
		$arr_fields["ipv6"] = $this->ipv6;
		$arr_fields["port"] = $this->port;

		$arr_fields["uploaded"] = $this->uploaded;
		$arr_fields["downloaded"] = $this->downloaded;
		$arr_fields["left"] = $this->left;
		$arr_fields["is_seeder"] = $this->is_seeder;

		$arr_fields["createtime"] = $this->timestamp;
		$arr_fields["last_action"] = $this->timestamp;
		$arr_fields["last_event"] = $this->event;
		$arr_fields['connectable'] = '1';

		$arr_fields["agent"] = $this->agent;
		$arr_fields["size"] = $this->torrent["size"];

		$this->pid = $this->peers_model->insert_peer($arr_fields); //pid在返回数据时起过滤自身的作用
		//$this->peer = $peers_model->find($this->pid); //后面没用到？
	}

	private function get_torrent()
	{
		cg::load_module('torrents_module');
		$this->torrents_module = torrents_module::get_instance();
		$this->tid = $this->torrents_module->torrents_model->get_tid_by_info_hash($this->info_hash);
		if (empty($this->tid))
		{
			$this->failure_message("种子没有发布或者发布不成功，请检查发种流程。");
		}
		$this->torrent = $this->torrents_module->get_torrent($this->tid);
		if (empty($this->torrent))
		{
			//@todo 主从同步延时，insert的时候没有生成缓存key。
			$this->failure_message("种子发布成功，数据还未同步完成，请稍后更新Tracker。");
		}
		if ($this->torrent['status'] == '-2')
		{
			$this->failure_message("该种子已被删除。");
		}
		if (!$this->is_seeder && $this->torrent['status'] == '0' && $this->uid != $this->torrent['uid'])
		{
			if (!empty($this->setting['tracker_download_unaudited_user']) && !in_array($this->user['username'], funcs::explode($this->setting['tracker_download_unaudited_user'])))
			{
				$this->failure_message("该种子正在审核中，审核通过之前您不能下载。");
			}
		}
	}

	private function check_ratio_limit()
	{
		if ($this->is_seeder || !$this->user['is_user'])
		{
			return;
		}
		if ($this->torrent["auto_isfree"] || $this->torrent["isfree"] || $this->torrent["istop"] || $this->torrent["isrecommend"] || $this->uid == $this->torrent['uid'])
		{
			return;
		}

		if (!$this->setting['enable_ratio_limit'])
		{
			return;
		}
		$downloaded = $this->user['downloaded'] - $this->user['downloaded2'];
		$G = 1024 * 1024 * 1024;
		if ($downloaded < 20 * $G)
		{
			return;
		}
		$dict_ratio_limit = array();
		$rows = funcs::explode($this->setting['ratio_limit']);
		foreach ($rows as $row)
		{
			list($key, $limit) = funcs::explode($row, ':');
			$dict_ratio_limit[$key] = $limit;
		}
		$ratio = $this->user['ratio'];
		$ratio_limit = 0;
		foreach ($dict_ratio_limit as $key => $value)
		{
			if ($downloaded > $key * $G)
			{
				$ratio_limit = $value;
				break;
			}
		}

		if ($ratio < $ratio_limit)
		{
			$this->failure_message("你的共享率过低，你只能下载自己发布的种子，或者下载置顶、推荐、免费的种子并保种，提高上传流量及共享率，达到要求后系统会自动解除限制。");
		}
	}

	private function check_multi_ip_download()
	{
		if ($this->is_seeder || $this->user['extcredits1'] > 100000 || !$this->user['is_user'])
		{
			return;
		}
		if (!isset($this->user['privileges']['multi_ip_download']) || $this->user['privileges']['multi_ip_download'])
		{
			return;
		}
		$last_action = $this->timestamp - $this->announce_interval;
		$download_count = $this->peers_model->get_multi_ip_download_count($this->uid, $this->ip, $last_action);
		if ($download_count > 0)
		{
			$this->failure_message("请勿使用他人账号，也不要把账号给他人使用。同一个账号不能在多个IP地址同时下载。您的账号在其他IP地址已经有下载行为。");
		}
	}

	private function get_user()
	{
		$this->uid = $this->users_module->users_model->get_uid_by_passkey($this->passkey);
		if (empty($this->uid))
		{
			$this->failure_message("用户识别码错误(Unrecognized passkey). 请重新下载种子或修改优特(uTorrent)内种子的Tracker地址(右键-属性里面修改)。");
		}
		$this->user = $this->users_module->get_by_uid($this->uid);
		if (empty($this->user))
		{
			$this->failure_message("用户识别码错误2(Unrecognized passkey). 请重新下载种子或修改优特(uTorrent)内种子的Tracker地址(右键-属性里面修改)。");
		}

		if ($this->user['enabled'] == '0')
		{
			$this->failure_message("因新手考核未通过或其他原因，该用户已被禁用。");
		}
		if ($this->user['parked'] == '1')
		{
			$this->failure_message("用户自己临时停用，请到个人设置页面开启。");
		}
		if (isset($this->user['ismerge']) && $this->user['ismerge'] == '1')
		{
			$this->failure_message("用户已被认领，请更换uTorrent里面本种子的的Tracker地址(右键-属性里面修改)。");
		}
	}

	private function check_black_ips($ip)
	{
		$valid = true;
		if (empty($ip))
		{
			return $valid;
		}
		if (strpos($ip, ':') === false) //ipv4
		{
			$long = ip2long($ip);
			foreach ($this->black_ips as $line)
			{
				if (strpos($line, '-'))
				{
					$arr = funcs::explode($line, '-');
					$min = ip2long($arr[0]);
					$max = ip2long($arr[1]);
					if ($long >= $min && $long <= $max)
					{
						$valid = false;
						break;
					}
				}
				else
				{
					if ($ip == trim($line))
					{
						$valid = false;
						break;
					}
				}
			}
		}
		else //ipv6
		{
			foreach ($this->black_ips as $line)
			{
				if ($ip == trim($line))
				{
					$valid = false;
					break;
				}
			}
		}
		return $valid;
	}

	private function check_agent()
	{
		$agent = $this->agent;
		if (empty($agent))
		{
			$this->failure_message('本站不支持你使用的客户端软件, 请使用网站页面底部提供下载的版本 。');
		}

		$dict_black_peer_id = array();
		foreach ($this->black_peer_ids as $line)
		{
			$arr = funcs::explode($line, ':');
			$dict_black_peer_id[$arr[0]] = $arr[1];
		}

		foreach ($dict_black_peer_id as $key => $client)
		{
			if (stripos($this->peer_id, $key) !== false)
			{
				if ($key == 'UT300B' && stripos($agent, 'server') !== false)
				{
					continue;
				}
				else
				{
					$this->failure_message("本站不支持你使用的客户端软件 $client , 请使用网站页面底部提供下载的版本 。");
					break;
				}
			}
		}
		foreach ($this->black_agents as $black_agent)
		{
			if (stripos($agent, $black_agent) !== false)
			{
				$this->failure_message("本站不支持你使用的客户端软件 $agent , 请使用网站页面底部提供下载的版本 。");
				break;
			}
		}
		if (stripos($agent, 'Azureus') !== false && stripos($agent, 'windows') !== false)
		{
			$this->failure_message("本站不支持你使用的客户端软件 $agent , 请使用网站页面底部提供下载的版本 。");
		}
	}

	private function log_new_agent()
	{
		$agent = $this->agent;
		if (empty($agent))
		{
			return;
		}
		cg::load_model('agentinfo_model');
		$agentinfo_model = agentinfo_model::get_instance();
		$old_agent = $agentinfo_model->get_all_agent();
		if (in_array($agent, $old_agent))
		{
			return;
		}
		$new_agent = $this->cache()->get('new_agent');
		if (empty($new_agent))
		{
			$new_agent = array();
			$new_agent['agent'][] = $agent;
			$new_agent['starttime'] = $this->timestamp;
			$this->cache()->set('new_agent', $new_agent);
		}
		else
		{
			if (!in_array($agent, $new_agent['agent']))
			{
				$new_agent['agent'][] = $agent;
				$this->cache()->set('new_agent', $new_agent);
			}


			if (count($new_agent['agent']) > 0 && $this->timestamp - $new_agent['starttime'] > 43200)
			{
				foreach ($new_agent["agent"] as $agent)
				{
					$agentinfo_model->insert_agent($agent);
				}
				$new_agent["starttime"] = time();
				$new_agent["agent"] = array();
				$this->cache()->set('new_agent', $new_agent);
			}
		}
	}

	/**
	 * 跟客户端返回警告消息，uTorrent支持
	 * 暂未用到
	 */
	private function warning_message($s)
	{
		cg::load_class('cg_bcode');
		$data = array(
			'warning message' => $s,
			'interval' => $this->announce_interval,
			'min interval' => $this->min_interval,
			'downloaded' => 0,
			'complete' => 0,
			'incomplete' => 0,
			'peers' => array()
		);
		echo bencode($data);
		die();
	}

	private function failure_message($s)
	{
		cg::load_class('cg_bcode');
		$data = array(
			'failure reason' => $s,
			'interval' => $this->announce_interval,
			'min interval' => $this->min_interval,
			'downloaded' => 0,
			'complete' => 0,
			'incomplete' => 0,
			'peers' => array()
		);
		echo bencode($data);
		die();
	}

	private function get_params()
	{
		$params = "passkey,info_hash,peer_id,port,uploaded,downloaded,left";
		$dict_params = explode(",", $params);
		foreach ($dict_params as $p)
		{
			if (!isset($_GET[$p]))
			{
				$this->failure_message("错误：缺少参数 $p");
				break;
			}
			$this->{$p} = $_GET[$p];
		}

		//$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$this->ipv6 = isset($_GET["ipv6"]) ? $_GET['ipv6'] : "";

		$valid_ip = true;
		if (stripos($this->ip, ':') === false)
		{
			if (ip2long($this->ip) == -1 || ip2long($this->ip) === false)
			{
				$this->failure_message("你的IP地址: $this->ip 有误,不允许访问本网站!");
			}
		}
		else
		{
			if (stripos($this->ip, 'fe') === 0)
			{
				$this->ip = '';
			}
		}
		$valid_ip = $this->check_black_ips($this->ip);
		if (!$valid_ip)
		{
			$this->failure_message("你的IP地址: $this->ip 不允许访问本网站!");
		}

		if (stripos($this->ipv6, 'fe') === 0)
		{
			$this->ipv6 = '';
		}
		else
		{
			$valid_ip = $this->check_black_ips($this->ipv6);
			if (!$valid_ip)
			{
				$this->failure_message("你的IP地址: $this->ipv6 不允许访问本网站!");
			}
		}

		$this->event = isset($_GET['event']) ? $_GET['event'] : '';
		$dict_event = array(
			'started',
			'stopped',
			'completed'
		);
		if ($this->event != '' && !in_array($this->event, $dict_event))
		{
			$this->failure_message('参数错误: event: ' . $this->event);
		}
		$this->agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : '';

		$this->peer_id = $this->stripslashes($this->peer_id);
		if (strlen($this->peer_id) != 20)
		{
			$this->failure_message('参数错误: peer_id');
		}
		$this->peer_id = urlencode($this->peer_id);
		$this->info_hash = $this->stripslashes($this->info_hash);
		$this->info_hash = bin2hex($this->info_hash);
		$this->port = intval($this->port);
		$this->uploaded = floatval($this->uploaded);
		$this->downloaded = floatval($this->downloaded);
		$this->left = floatval($this->left);
		$this->is_seeder = $this->left == '0' ? true : false;


		if ($this->port <= 0 || $this->port > 0xffff)
		{
			$this->failure_message('参数错误: port: ' . $this->port);
		}
		if ($this->uploaded < 0)
		{
			$this->failure_message('参数错误: uploaded: ' . $this->uploaded);
		}
		if ($this->downloaded < 0)
		{
			$this->failure_message('参数错误: downloaded: ' . $this->downloaded);
		}
		if ($this->left < 0)
		{
			$this->failure_message('参数错误: left: ' . $this->left);
		}
		if (strlen($this->passkey) != 32)
		{
			$this->failure_message('参数错误: passkey: ' . $this->passkey);
		}
		if (strlen($this->info_hash) != 40)
		{
			$this->failure_message('参数错误: info_hash: ' . $this->info_hash);
		}
	}

	private function stripslashes($s)
	{
		$magic_quotes_gpc = get_magic_quotes_gpc();
		if ($magic_quotes_gpc)
		{
			$s = stripslashes($s);
		}
		return $s;
	}

	private function get_all_sql()
	{
		return $this->setting_model->get_all_sql();
	}

	private function get_cache_keys()
	{
		return $this->cache()->keys();
	}

	private function is_developer()
	{
		if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'])
		{
			return true;
		}
		if (in_array($this->user['username'], funcs::explode($this->setting['admins_developer'])))
		{
			return true;
		}
		if (in_array($this->ip, funcs::explode($this->setting['admins_trust_ips'])))
		{
			return true;
		}
		return false;
	}

	private function logs_debug($txt, $logtype = '')
	{
		$arr_fields = array(
			'createtime' => $this->timestamp,
			'logtype' => $logtype,
			'txt' => $txt
		);
		cg::load_model('logs_debug_model');
		$logs_debug_model = logs_debug_model::get_instance();
		$logs_debug_model->insert($arr_fields);
	}

	private function delete_old_peers()
	{
		$cache_key = 'delete_old_peers';
		$delete_flag = $this->cache()->get($cache_key);
		if (empty($delete_flag))
		{
			$this->cache()->set($cache_key, '1', 600);
			$clean_time = max($this->peer_clean_time, $this->peer_force_clean_time);
			$this->peers_model->delete_old_peers($clean_time);
		}
	}
}