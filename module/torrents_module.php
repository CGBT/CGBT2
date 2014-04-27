<?php
class torrents_module extends base_module
{
	public $torrents_model;
	public $torrents_stat_model;
	public $torrents_descr_model;
	public $torrents_index_model;
	public $torrents_keywords_model;
	public $users_module;
	public $torrents_mod_model;

	public function __construct()
	{
		parent::__construct();

		cg::load_model('torrents_model');
		cg::load_model('torrents_stat_model');
		cg::load_model('torrents_descr_model');
		cg::load_model('torrents_index_model');
		cg::load_model('torrents_keywords_model');
		cg::load_module('users_module');
		cg::load_model('torrents_mod_model');

		$this->torrents_model = torrents_model::get_instance();
		$this->torrents_stat_model = torrents_stat_model::get_instance();
		$this->torrents_descr_model = torrents_descr_model::get_instance();
		$this->torrents_index_model = torrents_index_model::get_instance();
		$this->torrents_keywords_model = torrents_keywords_model::get_instance();
		$this->users_module = users_module::get_instance();
		$this->torrents_mod_model = torrents_mod_model::get_instance();
		$this->get_category();
	}

	/**
	 *
	 * @return torrents_module
	 */
	public static function get_instance()
	{
		static $instance;
		$name = __CLASS__;
		if (!isset($instance[$name]))
		{
			$instance[$name] = new $name();
		}
		return $instance[$name];
	}

	public function delete_torrent($tid, $info_hash)
	{
		$this->torrents_model->delete_torrent($tid, $info_hash);
		$this->torrents_stat_model->delete($tid);
		$this->torrents_index_model->delete($tid, false);

		cg::load_model('peers_model');
		$peers_model = peers_model::get_instance();
		$peers_model->delete_peers_by_torrent($tid);
		//@todo delete files,attachments,descar,unlink($file)
	}

	public function import_torrent($arr_fields)
	{
		$torrents_id = $this->torrents_model->insert($arr_fields);
		if ($torrents_id > 0)
		{
			$arr_fields['id'] = $torrents_id;
			$this->torrents_stat_model->insert($arr_fields);

			$arr_fields['tid'] = $torrents_id;
			unset($arr_fields['id']);
			$this->torrents_descr_model->insert($arr_fields);
		}
	}

	public function insert_torrent($arr_fields, $files = array(), $update_index = true)
	{
		$torrents_id = $this->torrents_model->insert($arr_fields);
		if ($torrents_id > 0)
		{
			$this->torrents_model->cache()->set("infohash2tid_" . $arr_fields['info_hash'], $torrents_id, 0);

			$arr_fields['id'] = $torrents_id;
			$this->torrents_stat_model->insert($arr_fields);

			if ($update_index)
			{
				$arr_fields = $this->torrent2index($arr_fields);
				$this->torrents_index_model->insert($arr_fields);

				$arr_fields2 = array(
					'id' => $arr_fields['id'],
					'keywords' => $arr_fields['keyword']
				);
				$this->torrents_keywords_model->insert($arr_fields2);
			}

			$arr_fields['tid'] = $torrents_id;
			unset($arr_fields['id']);
			$this->torrents_descr_model->insert($arr_fields);
		}

		if (!empty($files))
		{
			cg::load_model('files_model');
			$files_model = files_model::get_instance();
			$files_model->insert_files($files, $torrents_id);
		}

		return $torrents_id;
	}

	public function update_torrent($arr_fields, $pk_id, $update_index = true)
	{
		$this->torrents_model->update($arr_fields, $pk_id);

		$count = $this->torrents_stat_model->exists($pk_id);
		if ($count > 0)
		{
			$this->torrents_stat_model->update($arr_fields, $pk_id);
		}
		else
		{
			$arr_fields['id'] = $pk_id;
			$this->torrents_stat_model->insert($arr_fields);
		}
		if ($update_index)
		{
			$torrent = $this->get_torrent($pk_id);
			$arr_fields = array_merge($torrent, $arr_fields);
			$arr_fields = $this->torrent2index($arr_fields);
			$this->torrents_index_model->update($arr_fields, $pk_id, false);

			$arr_fields2 = array(
				'keywords' => $arr_fields['keyword']
			);
			$this->torrents_keywords_model->update($arr_fields2, $pk_id);
		}


		if (isset($arr_fields['descr']))
		{
			$arr_fields['url_queued'] = '0';
			$arr_fields['tid'] = $pk_id;
			unset($arr_fields['id']);

			$torrents_descr_pk_id = $this->torrents_descr_model->get_first_tids_id($arr_fields['tid']); //@todo change order type when upgrade done
			if ($torrents_descr_pk_id > 0)
			{
				$this->torrents_descr_model->update($arr_fields, $torrents_descr_pk_id);
			}
			else
			{
				$this->torrents_descr_model->insert($arr_fields);
			}
		}
	}

	public function get_torrent($id)
	{
		$torrents = $this->torrents_model->find($id);
		if (empty($torrents))
		{
			return array();
		}
		$torrents_stat = $this->torrents_stat_model->find($id);
		$torrents['mod'] = $this->torrents_mod_model->get_by_tid($id);
		$torrents['mod'] = $this->calc_remain_time($torrents['mod']);

		$torrents['category_icon'] = $this->all_category[$torrents['category']]['icon'];
		$torrents['category_name'] = $this->all_category[$torrents['category']]['name'];
		$torrents['size_text'] = funcs::mksize($torrents['size']);
		$torrents['createtime_text'] = date("Y-m-d H:i", $torrents['createtime']);

		//$user = $this->users_module->get_by_uid($torrents['uid']); //@todo : save title in torrents table
		//$torrents['user_title'] = empty($user['title']) ? $torrents['username'] : $user['title'];
		$torrents['user_title'] = empty($torrents['user_title']) ? $torrents['username'] : $torrents['user_title'];
		//$torrents['new_flag'] = $user['last_browse'] < $torrents['createtime'];
		$torrents['new_flag'] = false; //@todo
		$torrents['date'] = $this->pdate($torrents['date']);
		return $this->process_torrent_fields(array_merge((array)$torrents, (array)$torrents_stat));
	}

	private function calc_remain_time($torrents_mod)
	{
		if (isset($torrents_mod['free']))
		{
			$remain_time = $torrents_mod['free']['end_time'] - time();
			if ($remain_time < 0)
			{
				unset($torrents_mod['free']);
			}
			else
			{
				$days = intval($remain_time / 86400);
				$hours = intval(($remain_time - $days * 86400) / 3600);
				$minutes = intval(($remain_time - $days * 86400 - $hours * 3600) / 60);
				$s = '限时免费，剩余：';
				$s .= $days > 0 ? $days . '天' : '';
				$s .= $hours > 0 ? $hours . '小时' : '';
				$s .= $days == 0 && $minutes > 0 ? $minutes . '分钟' : '';
				$torrents_mod['free']['remain_time'] = $s;
			}
		}
		return $torrents_mod;
	}

	private function pdate($date)
	{
		if (strlen($date) != 8)
		{
			return '';
		}
		$newdate = substr($date, 0, 4);
		$month = substr($date, 4, 2);
		if ($month != '00')
		{
			$newdate .= '-' . $month;
			$day = substr($date, 6, 2);
			if ($day != '00')
			{
				$newdate .= '-' . $day;
			}
		}
		return $newdate;
	}

	private function get_torrent_upload_factor($torrent, $uid = 0)
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

	private function get_torrent_download_factor($torrent)
	{
		return sprintf("%.1f", 1);
	}

	public function create_torrent_title($torrent)
	{
		//@todo 发布种子的时候入库，记录title字段，批量生成title
		//$title = '[' . $this->all_category[$torrent['category']]['name'] . ']';
		$title = '';
		foreach ($this->all_category[$torrent['category']]['options'] as $options)
		{
			//如果新增字段，由于缓存，torrent里面没有该字段
			if ($options['intitle'] && $options["bind_field"] != '' && isset($torrent[$options["bind_field"]]))
			{
				if (stripos($title, '[' . $torrent[$options["bind_field"]] . ']') === false)
				{
					$title .= '[' . $torrent[$options["bind_field"]] . ']';
				}
			}
		}
		$title = str_replace(array(
			'[ ]',
			'[]',
			'[0]',
			'[其他]'
		), '', $title);
		return $title;
	}

	public function get_torrents($ids)
	{
		$rows = array();
		foreach ($ids as $id)
		{
			$torrent = $this->get_torrent($id);
			if (!empty($torrent))
			{
				$rows[$id] = $torrent;
			}
		}
		return $rows;
	}

	private function process_torrent_fields($torrent)
	{
		$torrent['simple_createtime'] = funcs::get_simple_datetime($torrent['createtime']);
		$torrent['simple_last_action'] = funcs::get_simple_datetime($torrent['last_action']);
		$torrent['forums_url'] = str_replace('{$tid}', $torrent['forums_tid'], $this->setting['forums_thread_url']);
		$torrent['title'] = $this->create_torrent_title($torrent);
		$torrent['oldname'] = str_replace('[]', '', $torrent['oldname']);
		$torrent['imdb_link'] = empty($torrent['imdb']) ? '' : 'http://www.imdb.com/title/' . $torrent['imdb'] . '/';
		if (stripos($torrent['title'], '720p') !== false || stripos($torrent['title'], '1080') !== false)
		{
			$torrent['ishd'] = '1';
		}
		else
		{
			$torrent['ishd'] = '0';
		}

		$torrent['auto_isfree'] = $this->setting['all_free'] ? true : false;
		$torrent['auto_is2x'] = $this->setting['all_2x'] ? true : false;
		$torrent['auto_is30p'] = isset($this->setting['all_30p']) && $this->setting['all_30p'] ? true : false;
		$torrent['auto_ishalf'] = isset($this->setting['all_half']) && $this->setting['all_half'] ? true : false;

		if ($this->setting['new_torrents_free_time'] > 0 && time() - $torrent['createtime'] < $this->setting['new_torrents_free_time'] * 3600)
		{
			$torrent['auto_isfree'] = true;
		}
		if ($this->setting['new_torrents_30p_time'] > 0 && time() - $torrent['createtime'] < $this->setting['new_torrents_30p_time'] * 3600)
		{
			$torrent['auto_is30p'] = true;
		}
		if ($this->setting['new_torrents_half_time'] > 0 && time() - $torrent['createtime'] < $this->setting['new_torrents_half_time'] * 3600)
		{
			$torrent['auto_ishalf'] = true;
		}
		if ($torrent['size'] < $this->setting['torrents_free_min_size'] * 1024 * 1024 * 1024)
		{
			$torrent['is2x'] = false;
			$torrent['isfree'] = false;
			$torrent['is30p'] = false;
			$torrent['ishalf'] = false;
			$torrent['auto_is2x'] = false;
			$torrent['auto_isfree'] = false;
			$torrent['auto_is30p'] = false;
			$torrent['auto_ishalf'] = false;
		}
		if (isset($torrent['mod']['top']))
		{
			if ($torrent['mod']['top']['start_time'] <= time() && $torrent['mod']['top']['end_time'] >= time())
			{
				$torrent['istop'] = true;
			}
		}
		if (isset($torrent['mod']['free']))
		{
			if ($torrent['mod']['free']['start_time'] <= time() && $torrent['mod']['free']['end_time'] >= time())
			{
				$torrent['isfree'] = true;
			}
		}
		$torrent['price'] = isset($torrent['price']) ? $torrent['price'] : 0;
		$torrent['anonymous'] = isset($torrent['anonymous']) ? $torrent['anonymous'] : 0;
		$torrent['isft'] = isset($torrent['isft']) ? $torrent['isft'] : 0;
		$torrent['ishot'] = isset($torrent['ishot']) ? $torrent['ishot'] : 0;
		$torrent['upload_factor'] = $this->get_torrent_upload_factor($torrent);
		$torrent['download_factor'] = $this->get_torrent_download_factor($torrent);
		$torrent['extcredits1'] = sprintf('%.2f', $torrent['extcredits1']);

		return $torrent;
	}

	public function insert_torrents_index($torrent_id)
	{
		$torrent = $this->get_torrent($torrent_id);
		$arr_fields = $this->torrent2index($torrent);
		if ($this->torrents_index_model->exists($arr_fields['id']))
		{
			$this->torrents_index_model->update($arr_fields, $arr_fields['id'], false);
		}
		else
		{
			$this->torrents_index_model->insert($arr_fields);
		}
	}

	private function torrent2index($torrent)
	{
		$arr_fields = array();
		$category = $torrent['category'];
		$arr_fields['category'] = $this->all_category[$category]['id'];
		$keyword = $torrent['oldname'];
		foreach ($this->all_category[$category]['options'] as $option)
		{
			$field = $option['bind_field'];
			if ($option['type'] == 'year')
			{
				$arr_fields['year'] = $torrent['year'];
			}
			elseif ($option['type'] == 'date')
			{
				$arr_fields['date'] = $torrent['date'];
			}
			elseif ($option['type'] == 'select')
			{
				$dict_options = funcs::explode($option['options']);
				$arr_fields[$field] = array_search($torrent[$field], $dict_options) + 1;
			}
			elseif ($option['type'] == 'select_input')
			{
				$dict_options = funcs::explode($option['options']);

				if (!in_array($torrent[$field], $dict_options))
				{
					$torrent[$field] = '其他';
				}
				$arr_fields[$field] = array_search($torrent[$field], $dict_options) + 1;
			}
			elseif ($option['type'] == 'selects')
			{
				$dict_options = funcs::explode($option['options']);
				$dict_values = array_unique(funcs::explode($torrent[$field]));
				$v = 0;
				foreach ($dict_values as $value)
				{
					$index = array_search($torrent[$field], $dict_options);
					if ($index === false)
					{
						continue;
					}
					$v += pow(2, $index);
				}
				$arr_fields[$field] = $v;
			}
			else
			{
			}
			if (!empty($torrent[$field]))
			{
				if (stripos($keyword, $torrent[$field]) === false)
				{
					$keyword .= ',' . $torrent[$field];
				}
			}
		}
		$keyword .= ',' . $torrent['id'];
		if (!empty($torrent['imdb']))
		{
			$keyword .= ',' . $torrent['imdb'];
		}
		$keyword .= ',' . $torrent['save_as'];
		$dict_replace = array(
			' ',
			'/',
			'\\',
			'.',
			',',
			'[',
			']',
			'(',
			')',
			'其他'
		);
		$keyword = str_replace($dict_replace, '', $keyword);
		$arr_fields['keyword'] = $keyword;
		$subtitles = array(
			'无需字幕' => '1',
			'暂无字幕' => '0',
			'英文字幕' => '0',
			'中文字幕' => '1',
			'中英字幕' => '1',
			'帖内英文字幕' => '1',
			'帖内中文字幕' => '1',
			'帖内中英字幕' => '1',
			'其他' => '1'
		);
		if (isset($torrent['subtitle']) && isset($subtitles[$torrent['subtitle']])) //@todo undefined index
		{
			$arr_fields['subs'] = $subtitles[$torrent['subtitle']];
		}
		else
		{
			$arr_fields['subs'] = '0';
		}

		if (stripos($arr_fields['keyword'], '720p') !== false || stripos($arr_fields['keyword'], '1080') !== false)
		{
			$arr_fields['ishd'] = '1';
		}
		$arr_fields = array_merge($torrent, $arr_fields);
		return $arr_fields;
	}

	public function get_same_size_torrents($size, $tid)
	{
		$ids = $this->torrents_index_model->get_same_size_tids($size);
		$index = array_search($tid, (array)$ids);
		if ($index !== false)
		{
			unset($ids[$index]);
		}

		if (empty($ids))
		{
			return array();
		}
		return $this->get_torrents($ids);
	}

	public function get_descr($tid)
	{
		$id = $this->torrents_descr_model->get_first_tids_id($tid);
		return $this->torrents_descr_model->find($id);
	}

	public function get_dupe_torrents()
	{
		$rows = $this->torrents_model->get_dupe_torrents('2013-04-16');
		$torrents = array();
		foreach ($rows as $key => $row)
		{
			$torrents[$key]['a'] = $this->get_torrent($row['aid']);
			$torrents[$key]['b'] = $this->get_torrent($row['bid']);
		}
		return $torrents;
	}

	public function create_torrent_id($uid)
	{
		cg::load_model('torrents_id_model');
		$torrents_id_model = torrents_id_model::get_instance();
		$arr_field = array();
		$arr_field['createtime'] = time();
		$arr_field['uid'] = $uid;
		return $torrents_id_model->insert($arr_field);
	}

	public function update_extcredits1_speed()
	{
		$this->torrents_index_model->update_extcredits1_speed($this->setting);
		$this->torrents_stat_model->update_extcredits1_speed($this->setting);
	}

	public function update_torrents_mod()
	{
		$data = $this->torrents_mod_model->cron_check_enabled();
		if (!empty($data['top_tids']))
		{
			foreach ($data['top_tids'] as $tid)
			{
				$arr_fields = array(
					'istop' => 1
				);
				$this->torrents_model->update($arr_fields, $tid);
				$this->torrents_index_model->update($arr_fields, $tid);
			}
		}
		if (!empty($data['untop_tids']))
		{
			foreach ($data['untop_tids'] as $tid)
			{
				$arr_fields = array(
					'istop' => 0
				);
				$this->torrents_model->update($arr_fields, $tid);
				$this->torrents_index_model->update($arr_fields, $tid);
			}
		}
	}
}
