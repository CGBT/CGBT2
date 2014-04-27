<?php
class upload_controller extends base_controller
{
	/**
	 *
	 * @var array
	 */
	private $current_category = array();
	private $category = '';
	private $torrents_module;
	private $tid;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
		$this->get_category();
		foreach ($this->all_category as $key => $category)
		{
			if ($category['app'] != 'torrents')
			{
				unset($this->all_category[$key]);
			}
		}
		$this->data['all_category'] = $this->all_category;
		$this->data['selected_nav'] = 'upload';
		$this->data['title'] = '发布种子-';

		cg::load_module('torrents_module');
		$this->torrents_module = new torrents_module();
	}

	public function index_action()
	{
		$this->data['privileges_info'] = $this->check_have_privileges('upload', false);

		$this->category = $this->params['category'];
		$dict_action = array(
			'insert',
			'update',
			'ajax_insert_check',
			'ajax_update_check',
			'api'
		);
		if (empty($this->category)) //注意事项页面
		{
			$this->current_category = array();
			$this->category_index();
		}
		elseif (in_array($this->category, array_keys($this->all_category))) //发布种子页面
		{
			$this->current_category = $this->all_category[$this->category];
			$this->category_index();
		}
		elseif ($this->category == 'api') //播种机接口 /upload/api
		{
			$this->category = isset($this->post['category']) ? $this->post['category'] : '';
			if (empty($this->category))
			{
				$this->category = isset($this->get['category']) ? $this->get['category'] : '';
			}
			if (in_array($this->category, array_keys($this->all_category)))
			{
				$this->current_category = $this->all_category[$this->category];
			}
			else
			{
				$this->current_category = '';
			}
			$this->api_upload();
			$this->showmessage('功能开发中...');
		}
		elseif (in_array($this->category, $dict_action)) //提交表单 或 ajax验证
		{
			$action = $this->category;
			$this->category = $this->post['category']; //@todo 检查所有方法，可能有些没有category
			$this->current_category = $this->all_category[$this->category];
			$this->$action();
		}
		elseif ($this->category == 'receive_images')
		{
			$this->receive_images();
		}
		elseif ($this->category == 'receive_attach')
		{
			$this->receive_attach();
		}
		elseif ($this->category == 'delete_attach')
		{
			$this->delete_attach();
		}
		else
		{
			$this->showmessage('参数错误');
		}
	}

	private function fix_descr_img_ubb($descr)
	{
		$descr = str_replace('http://6movie.org/topics/', 'http://www.imdb.com/title/', $descr);
		//[back=#fbfbfb]
		$descr = preg_replace('/\[back=#[0-9a-f]{6}\]/i', '', $descr);
		$descr = str_replace('[/back]', '', $descr);
		$descr = str_replace('[img=image]', '[img]', $descr);

		// [img=,546,66]= > [img=546,66]
		$descr = preg_replace('/\[img=(.*?),(\d+),(\d+)\]/i', '[img=$2,$3]', $descr);
		$descr = preg_replace('/\[img=(.*?),(\d+),\]/i', '[img=$2,0]', $descr);
		$descr = preg_replace('/\[img=(\d+),\]/i', '[img=$1,0]', $descr);
		$descr = preg_replace('/\[font=(.*?)\]/i', '', $descr);
		$descr = str_replace('[/font]', '', $descr);
		$descr = str_replace('&quot;', '"', $descr);
		$descr = str_replace('#xhe_tmpurl', '', $descr);
		//[url=http://bt.neu6.edu.cn/attachment.php?aid=MzgzNDc1M3w4OTlkOTRhZnwxMzY3MTc2MTA4fGFjYjhoSURGUXN6S25oKzhXbnN4NUYwVk9lZmtpY21Gd254NlpUWlIxQkdZMzJj&nothumb=yes]下载[/url]
		$descr = preg_replace('/\[url=http:\/\/bt\.neu6(.*?)yes\]下载\[\/url\]/i', '', $descr);
		$descr = preg_replace('/(title\/tt[0-9]{7})([^\/])/i', '$1/$2', $descr);
		return $descr;
	}

	private function insert()
	{
		$this->check_form(false);
		$this->check_torrent_file(false);
		$this->tid = $this->create_torrent_id();

		$path = empty($this->setting['torrents_save_path']) ? 'attachments/torrents/' : $this->setting['torrents_save_path'];
		$saved_file = $this->save_torrent_file($this->tid, $path);
		$torrent_info = $this->get_torrent_info($path . $saved_file['new_name']);

		$count = $this->torrents_module->torrents_model->check_infohash_exists($torrent_info['info_hash']);
		if ($count > 0)
		{
			$this->showmessage('种子已经发布过了');
		}
		$count = $this->torrents_module->torrents_model->check_bt_infohash_exists($torrent_info['bt_info_hash']);
		if ($count > 0)
		{
			$this->showmessage('种子已经发布过了');
		}

		$arr_fields = array();
		foreach ($this->current_category['options'] as $option)
		{
			$arr_fields[$option['bind_field']] = $this->post[$option['variable']];
		}
		$privileges_info = $this->check_have_privileges('dont_need_audit', false);
		$status = $privileges_info['have_privileges'] ? '1' : '0';

		$arr_fields['id'] = $this->tid;
		$arr_fields['status'] = $status; //@todo 如果有审核功能，则默认值为0
		$arr_fields['category'] = $this->current_category['name_en'];
		$arr_fields['createtime'] = time();
		$arr_fields['updatetime'] = time();
		$arr_fields['uid'] = $this->user['uid'];
		$arr_fields['username'] = $this->user['username'];
		$arr_fields['user_title'] = $this->user['title'];
		$arr_fields['descr'] = $this->fix_descr_img_ubb($this->post['descr']);

		$arr_fields['save_as'] = $torrent_info['name'];
		$arr_fields['filename'] = $saved_file['new_name'];
		$arr_fields['info_hash'] = $torrent_info['info_hash'];
		$arr_fields['bt_info_hash'] = $torrent_info['bt_info_hash'];
		$arr_fields['files'] = count($torrent_info['files']);
		$arr_fields['size'] = $torrent_info['total_length'];
		$arr_fields['price'] = isset($this->post['price']) ? intval($this->post['price']) : 0;

		if ($this->user['is_admin'] || $this->user['is_moderator'])
		{
			$arr_fields['iscollection'] = isset($this->post['iscollection']) ? intval($this->post['iscollection']) : '0';
			$arr_fields['istop'] = isset($this->post['istop']) ? intval($this->post['istop']) : '0';
			$arr_fields['is0day'] = isset($this->post['is0day']) ? intval($this->post['is0day']) : '0';
			$arr_fields['isfree'] = isset($this->post['isfree']) ? intval($this->post['isfree']) : '0';
			$arr_fields['isrecommend'] = isset($this->post['isrecommend']) ? intval($this->post['isrecommend']) : '0';
			$arr_fields['is2x'] = isset($this->post['is2x']) ? intval($this->post['is2x']) : '0';
			$arr_fields['is30p'] = isset($this->post['is30p']) ? intval($this->post['is30p']) : '0';
			$arr_fields['ishalf'] = isset($this->post['ishalf']) ? intval($this->post['ishalf']) : '0';
			$arr_fields['ishot'] = isset($this->post['ishot']) ? intval($this->post['ishot']) : '0';
			$arr_fields['isft'] = isset($this->post['isft']) ? intval($this->post['isft']) : '0';
			$arr_fields['anonymous'] = isset($this->post['anonymous']) ? intval($this->post['anonymous']) : '0';

			$arr_fields['top_limit_time'] = isset($this->post['top_limit_time']) ? intval($this->post['top_limit_time']) : '0';
			$arr_fields['free_limit_time'] = isset($this->post['free_limit_time']) ? intval($this->post['free_limit_time']) : '0';

			if ($arr_fields['top_limit_time'])
			{
				$start_time = strtotime($this->post['top_start_time']);
				$end_time = strtotime($this->post['top_end_time']);
				if ($end_time - $start_time >= 3600 && $start_time >= $this->timestamp - 86400 && $end_time >= $this->timestamp + 3600)
				{
					$mod_fields = array(
						'start_time' => $start_time,
						'end_time' => $end_time,
						'type' => 'top',
						'tid' => $this->tid,
						'operator_uid' => $this->uid,
						'operator_username' => $this->username,
						'status' => 1
					);
					$this->torrents_module->torrents_mod_model->insert_or_update_mod($mod_fields);
					if ($mod_fields['end_time'] >= $this->timestamp && $mod_fields['start_time'] <= $this->timestamp)
					{
						$arr_fields['istop'] = 1;
					}
				}
			}
			if ($arr_fields['free_limit_time'])
			{
				$start_time = strtotime($this->post['free_start_time']);
				$end_time = strtotime($this->post['free_end_time']);
				if ($end_time - $start_time >= 3600 && $start_time >= $this->timestamp - 86400 && $end_time >= $this->timestamp + 3600)
				{
					$mod_fields = array(
						'start_time' => $start_time,
						'end_time' => $end_time,
						'type' => 'free',
						'tid' => $this->tid,
						'operator_uid' => $this->uid,
						'operator_username' => $this->username,
						'status' => 1
					);
					$this->torrents_module->torrents_mod_model->insert_or_update_mod($mod_fields);
					if ($mod_fields['end_time'] >= $this->timestamp && $mod_fields['start_time'] <= $this->timestamp)
					{
						$arr_fields['isfree'] = 1;
					}
				}
			}
		}
		$arr_fields['oldname'] = '';

		//根据大小检查重复种子
		$this->data['tid'] = $this->tid;
		$this->data['torrents'] = $this->torrents_module->get_same_size_torrents($torrent_info['total_length'], $this->tid);
		$this->data['torrents_count'] = count($this->data['torrents']);
		if ($this->data['torrents_count'] > 0)
		{
			$arr_fields['status'] = '0';
		}
		$this->torrents_module->insert_torrent($arr_fields, $torrent_info['files']);

		//更新图片表tid
		cg::load_model('torrents_images_model');
		$torrents_images_model = torrents_images_model::get_instance();
		$torrents_images_model->update_tid_by_guid($this->tid, $this->post['guid']);

		//更新附件表
		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$torrents_attachments_model->update_tid_by_guid($this->tid, $this->post['guid']);

		//先给论坛发帖子
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();

		//$torrent = $this->torrents_module->get_torrent($this->tid);
		$title = $this->torrents_module->create_torrent_title($arr_fields);
		$fid = $this->current_category['forums_fid'];

		$domain = $this->setting['site_domain'];
		$download_url = "种子下载地址(支持IPv4和IPv6)：[url]{$domain}/torrents/{$this->tid}/download/[/url]\n\n";
		$arr_fields['descr'] = $download_url . $arr_fields['descr'];
		$forums_tid = $forums_discuzx_model->new_thread($this->user['forums_uid'], $this->username, $title, $arr_fields['descr'], $fid);

		if (intval($forums_tid) > 0)
		{
			$arr_fields = array();
			$arr_fields['forums_tid'] = $forums_tid;
			$this->torrents_module->update_torrent($arr_fields, $this->tid, false);
		}
		else
		{
			$this->showmessage('种子发布到论坛失败!', true);
		}

		//发消息
		$from_username = $this->setting['admins_deliver'];
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$from_user = $users_module->get_by_username($from_username);
		if (!empty($from_user))
		{
			$from_uid = $from_user['forums_uid'];
			$msgto = $this->username;
			$subject = '感谢您发布种子';
			$message = "感谢您发布种子： $title \n";
			$message .= "请检查您的种子是否处于审核区，如果在审核区请尽量完善种子介绍并耐心等待管理员审核通过。\n";
			$message .= "如果不在审核区，则表示您的种子已经直接进入种子列表。\n";
			$message .= "请尽量多保种，长时间保种，时间越长保种积分越多。";
			$isusername = 1;
			$forums_discuzx_model->pm_send($from_uid, $msgto, $subject, $message, $isusername);
		}

		//记录日志
		cg::load_model('logs_actions_model');
		$logs_action_model = logs_actions_model::get_instance();
		$arr_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'is_moderator' => $this->user['is_moderator'],
			'is_admin' => $this->user['is_admin'],
			'tid' => $this->tid,
			'action' => 'insert_torrent'
		);
		$arr_fields['details'] = array(
			'tid' => $this->tid,
			'torrent_title' => $title
		);
		$arr_fields['details'] = json_encode($arr_fields['details']);
		$logs_action_model->insert($arr_fields);

		if (isset($this->user['total_upload_times']) && isset($this->user['total_upload_size']))
		{
			$arr_fields = array(
				'total_upload_times' => $this->user['total_upload_times'] + 1,
				'total_upload_size' => $this->user['total_upload_size'] + $torrent_info['total_length']
			);
			$users_module->users_stat_model->update($arr_fields, $this->uid);
		}

		$this->show('upload_done.php');
		//$this->showmessage($msg, true);
	}

	private function update()
	{
		$this->check_form(false);
		$this->tid = intval($this->post['tid']) > 0 ? intval($this->post['tid']) : 0;
		if ($this->tid == 0)
		{
			$this->showmessage('参数错误');
		}
		$old_torrent = $this->torrents_module->get_torrent($this->tid);

		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			if ($old_torrent['uid'] != $this->uid)
			{
				$this->showmessage('您没有权限修改本种子', true);
			}
		}

		$arr_fields = array();
		foreach ($this->current_category['options'] as $option)
		{
			$arr_fields[$option['bind_field']] = $this->post[$option['variable']];
		}

		$dict_bot_username = array(
			'k36',
			'k37',
			'k39',
			'k40'
		);
		if (in_array($old_torrent['username'], $dict_bot_username))
		{
			$arr_fields['uid'] = $this->uid;
			$arr_fields['username'] = $this->username;
			$arr_fields['user_title'] = $this->user['title'];
		}

		$arr_fields['category'] = $this->current_category['name_en'];
		$arr_fields['updatetime'] = time();
		$arr_fields['audit_note'] = '';
		$arr_fields['descr'] = $this->fix_descr_img_ubb($this->post['descr']);
		$arr_fields['price'] = isset($this->post['price']) ? intval($this->post['price']) : 0;

		if ($this->user['is_admin'] || $this->user['is_moderator'])
		{
			$arr_fields['iscollection'] = isset($this->post['iscollection']) ? intval($this->post['iscollection']) : '0';
			$arr_fields['istop'] = isset($this->post['istop']) ? intval($this->post['istop']) : '0';
			$arr_fields['is0day'] = isset($this->post['is0day']) ? intval($this->post['is0day']) : '0';
			$arr_fields['isfree'] = isset($this->post['isfree']) ? intval($this->post['isfree']) : '0';
			$arr_fields['isrecommend'] = isset($this->post['isrecommend']) ? intval($this->post['isrecommend']) : '0';
			$arr_fields['is2x'] = isset($this->post['is2x']) ? intval($this->post['is2x']) : '0';
			$arr_fields['is30p'] = isset($this->post['is30p']) ? intval($this->post['is30p']) : '0';
			$arr_fields['ishalf'] = isset($this->post['ishalf']) ? intval($this->post['ishalf']) : '0';
			$arr_fields['ishot'] = isset($this->post['ishot']) ? intval($this->post['ishot']) : '0';
			$arr_fields['isft'] = isset($this->post['isft']) ? intval($this->post['isft']) : '0';
			$arr_fields['anonymous'] = isset($this->post['anonymous']) ? intval($this->post['anonymous']) : '0';

			$arr_fields['top_limit_time'] = isset($this->post['top_limit_time']) ? intval($this->post['top_limit_time']) : '0';
			$arr_fields['free_limit_time'] = isset($this->post['free_limit_time']) ? intval($this->post['free_limit_time']) : '0';

			if ($arr_fields['top_limit_time'])
			{
				if ($this->post['top_start_time'] != $this->post['old_top_start_time'] || $this->post['top_end_time'] != $this->post['old_top_end_time'])
				{
					$start_time = strtotime($this->post['top_start_time']);
					$end_time = strtotime($this->post['top_end_time']);
					if ($end_time - $start_time >= 3600 && $start_time >= $this->timestamp - 86400 && $end_time >= $this->timestamp + 3600)
					{
						$mod_fields = array(
							'start_time' => $start_time,
							'end_time' => $end_time,
							'type' => 'top',
							'tid' => $this->tid,
							'operator_uid' => $this->uid,
							'operator_username' => $this->username,
							'status' => 1
						);
						$this->torrents_module->torrents_mod_model->insert_or_update_mod($mod_fields);
						if ($mod_fields['end_time'] >= $this->timestamp && $mod_fields['start_time'] <= $this->timestamp)
						{
							$arr_fields['istop'] = 1;
						}
					}
				}
			}
			else
			{
				if (isset($old_torrent['mod']['top']))
				{
					$this->torrents_module->torrents_mod_model->delete_mod($this->tid, 'top');
				}
			}
			if ($arr_fields['free_limit_time'])
			{
				if ($this->post['free_start_time'] != $this->post['old_free_start_time'] || $this->post['top_free_time'] != $this->post['old_free_end_time'])
				{
					$start_time = strtotime($this->post['free_start_time']);
					$end_time = strtotime($this->post['free_end_time']);
					if ($end_time - $start_time >= 3600 && $start_time >= $this->timestamp - 86400 && $end_time >= $this->timestamp + 3600)
					{
						$mod_fields = array(
							'start_time' => $start_time,
							'end_time' => $end_time,
							'type' => 'free',
							'tid' => $this->tid,
							'operator_uid' => $this->uid,
							'operator_username' => $this->username,
							'status' => 1
						);
						$this->torrents_module->torrents_mod_model->insert_or_update_mod($mod_fields);
						if ($mod_fields['end_time'] >= $this->timestamp && $mod_fields['start_time'] <= $this->timestamp)
						{
							$arr_fields['isfree'] = 1;
						}
					}
				}
			}
			else
			{
				if (isset($old_torrent['mod']['free']))
				{
					$this->torrents_module->torrents_mod_model->delete_mod($this->tid, 'free');
				}
			}
		}

		$this->torrents_module->update_torrent($arr_fields, $this->tid);

		//更新图片表tid
		cg::load_model('torrents_images_model');
		$torrents_images_model = torrents_images_model::get_instance();
		$torrents_images_model->update_tid_by_guid($this->tid, $this->post['guid']);

		//更新附件表
		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$torrents_attachments_model->update_tid_by_guid($this->tid, $this->post['guid']);

		//先给论坛发帖子
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$torrent = $this->torrents_module->get_torrent($this->tid);

		cg::load_module('users_module');
		$users_module = new users_module();
		$torrent_user = $users_module->get_by_uid($torrent['uid']);

		$domain = $this->setting['site_domain'];
		$download_url = "种子下载地址(支持IPv4和IPv6)：[url]{$domain}/torrents/{$this->tid}/download/[/url]\n\n";
		$arr_fields['descr'] = $download_url . $arr_fields['descr'];

		$fid = $this->current_category['forums_fid'];
		$forums_tid = $torrent['forums_tid'];
		$forums_tid = $forums_discuzx_model->update_thread($forums_tid, $torrent_user['forums_uid'], $torrent_user['username'], $torrent['title'], $arr_fields['descr'], $fid);

		//记录日志
		cg::load_model('logs_actions_model');
		$logs_action_model = logs_actions_model::get_instance();
		$action = in_array($old_torrent['username'], $dict_bot_username) ? 'update_bot_torrent' : 'update_torrent';
		$arr_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'is_moderator' => $this->user['is_moderator'],
			'is_admin' => $this->user['is_admin'],
			'tid' => $this->tid,
			'action' => $action
		);
		$arr_fields['details'] = array(
			'tid' => $this->tid,
			'torrent_title' => $torrent['title']
		);
		$arr_fields['details'] = json_encode($arr_fields['details']);
		$logs_action_model->insert($arr_fields);

		//发消息
		$from_username = $this->setting['admins_deliver'];
		$from_user = $users_module->get_by_username($from_username);
		if (!empty($from_user))
		{
			$from_uid = $from_user['forums_uid'];
			$msgto = $torrent['username'];
			$subject = '您发布的种子被修改';
			$message = "您发布的种子被修改：  \n";
			$message .= "修改前种子名称为：{$old_torrent['title']} \n";
			$message .= "修改后种子名称为：{$torrent['title']} \n";
			$isusername = 1;
			$forums_discuzx_model->pm_send($from_uid, $msgto, $subject, $message, $isusername);
		}

		$this->showmessage('修改完成');
	}

	private function api_save_torrent_file($torrent_id, $path)
	{
		cg::load_class('file_upload');
		$max_size = intval($this->setting['torrents_size_limit']) * 1024 * 1024;
		$upload_config = array(
			'save_path' => $path,
			'max_size' => $max_size,
			'type' => 'torrent',
			'parent_id' => $torrent_id
		);
		$file_upload = new file_upload($upload_config);
		$ret = $file_upload->save('torrent_file');
		if ($ret === false)
		{
			return false;
		}
		return $file_upload->saved_file[0];
	}

	private function save_torrent_file($torrent_id, $path)
	{
		cg::load_class('file_upload');
		$max_size = intval($this->setting['torrents_size_limit']) * 1024 * 1024;
		$upload_config = array(
			'save_path' => $path,
			'max_size' => $max_size,
			'type' => 'torrent',
			'parent_id' => $torrent_id
		);
		$file_upload = new file_upload($upload_config);
		$ret = $file_upload->save('torrent_file');
		if ($ret === false)
		{
			$msg = '种子文件上传失败，错误代码' . $file_upload->errno;
			$this->showmessage($msg);
		}
		return $file_upload->saved_file[0];
	}

	private function get_torrent_info($torrent_file)
	{
		cg::load_class('cg_bcode');
		$dict = bdecode(file_get_contents($torrent_file));
		if (!is_array($dict) || !isset($dict['info']))
		{
			$this->showmessage('种子文件错误');
		}
		$data = array();
		$new_dict = array();
		$dict_all_keys = array(
			'length',
			'files',
			'name',
			'piece length',
			'pieces'
		);
		foreach ($dict_all_keys as $key)
		{
			if (isset($dict['info'][$key]))
			{
				$new_dict['info'][$key] = $dict['info'][$key];
			}
		}
		$data['bt_info_hash'] = sha1(bencode($new_dict['info']));
		$new_dict['info']['private'] = 1;
		$have_other_key = false;
		foreach ($dict['info'] as $key => $value)
		{
			if (!in_array($key, $dict_all_keys) && $key != 'source' && $key != 'private')
			{
				$have_other_key = true;
				break;
			}
		}
		if ($have_other_key)
		{
			$new_dict['info']['source'] = $this->setting['torrents_source'];
		}
		else
		{
			$new_dict['info']['source'] = !empty($dict['info']['source']) ? $dict['info']['source'] : $this->setting['torrents_source'];
		}
		$data['info_hash'] = sha1(bencode($new_dict['info']));
		copy($torrent_file, $torrent_file . '.ori');
		file_put_contents($torrent_file, bencode($new_dict));
		$data['total_length'] = 0;
		if (isset($dict['info']['length']))
		{
			$data['total_length'] = $dict['info']['length'];
			$data['files'][0]['filename'] = $dict['info']['name'];
			$data['files'][0]['length'] = $dict['info']['length'];
		}
		else
		{
			$i = 0;
			foreach ($dict['info']['files'] as $file)
			{
				$data['total_length'] += $file['length'];
				$data['files'][$i]['filename'] = $dict['info']['name'] . '/' . implode('/', $dict['info']['files'][$i]['path']);
				$data['files'][$i]['length'] = $dict['info']['files'][$i]['length'];
				$i++;
			}
		}
		$data['name'] = $dict['info']['name'];
		return $data;
	}

	private function check_torrent_file()
	{
		$result['error'] = false;
		//种子文件，只做扩展名检查
		if (!isset($_FILES['torrent_file']))
		{
			$result['error'] = true;
			$result['msg'] = '请选择种子文件';
			$result['field'] = 'torrent_file';
			$result['field_type'] = 'file';
		}
		elseif ($_FILES['torrent_file']['error'] == 4)
		{
			$result['error'] = true;
			$result['msg'] = '请选择种子文件';
			$result['field'] = 'torrent_file';
			$result['field_type'] = 'file';
		}
		else
		{
			$ext = substr($_FILES['torrent_file']['name'], strlen($_FILES['torrent_file']['name']) - 8);
			if ($ext != '.torrent')
			{
				$result['error'] = true;
				$result['msg'] = '上传的不是种子文件';
				$result['field'] = 'torrent_file';
				$result['field_type'] = 'file';
			}
		}
		$this->show_result($result, false);
	}

	private function show_result($result, $inajax)
	{
		if ($result['error'])
		{
			if ($inajax)
			{
				die(json_encode($result));
			}
			else
			{
				$this->showmessage($result['msg']);
			}
		}
		else
		{
			if ($inajax)
			{
				die(json_encode($result));
			}
		}
	}

	private function category_index()
	{
		$this->data['action'] = 'upload';
		$this->data['current_category'] = $this->current_category;
		$this->data['guid'] = funcs::guid();
		$this->setting['upload_note'] = funcs::ubb2html($this->setting['upload_note']);
		$this->template_file = 'upload.php';
		$this->show();
	}

	private function api_upload()
	{
		$this->check_torrent_file(false);
		$this->tid = $this->create_torrent_id();
		$path = empty($this->setting['torrents_save_path']) ? 'attachments/torrents/' : $this->setting['torrents_save_path'];
		$saved_file = $this->api_save_torrent_file($this->tid, $path);
		if ($saved_file === false)
		{
			die('error: torrent file zero size error');
		}
		$torrent_info = $this->get_torrent_info($path . $saved_file['new_name']);
		$tid = $this->torrents_module->torrents_model->check_infohash_exists($torrent_info['info_hash']);
		if ($tid > 0)
		{
			echo $tid;
			die();
			$this->showmessage('种子已经发布过了', true);
		}
		$tid = $this->torrents_module->torrents_model->check_bt_infohash_exists($torrent_info['bt_info_hash']);
		if ($tid > 0)
		{
			echo $tid;
			die();
			$this->showmessage('种子已经发布过了2', true);
		}
		$arr_fields = array();
		foreach ($this->current_category['options'] as $option)
		{
			if (isset($this->post[$option['variable']])) //播种机发种没有这些字段
			{
				$arr_fields[$option['bind_field']] = htmlspecialchars($this->post[$option['variable']], ENT_QUOTES);
			}
		}
		if (empty($this->post['name']))
		{
			$arr_fields['name'] = $torrent_info['name'];
		}
		$arr_fields['id'] = $this->tid;
		if ($this->user['is_moderator'] || $this->user['is_admin']) //@todo 审核权限
		{
			$arr_fields['status'] = '1';
		}
		else
		{
			$arr_fields['status'] = '0';
		}
		$arr_fields['category'] = $this->current_category['name_en'];
		$arr_fields['createtime'] = time();
		$arr_fields['updatetime'] = time();
		$arr_fields['uid'] = $this->user['uid'];
		$arr_fields['username'] = $this->user['username'];
		$arr_fields['user_title'] = $this->user['title'];
		$arr_fields['descr'] = ''; //@todo check xss
		$arr_fields['save_as'] = $torrent_info['name'];
		$arr_fields['filename'] = $saved_file['new_name'];
		$arr_fields['info_hash'] = $torrent_info['info_hash'];
		$arr_fields['bt_info_hash'] = $torrent_info['bt_info_hash'];
		$arr_fields['files'] = count($torrent_info['files']);
		$arr_fields['size'] = $torrent_info['total_length'];
		$arr_fields['oldname'] = '';
		$this->torrents_module->insert_torrent($arr_fields, $torrent_info['files']);

		//先给论坛发帖子
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();

		//$torrent = $this->torrents_module->get_torrent($this->tid);
		$title = $this->torrents_module->create_torrent_title($arr_fields);
		$fid = $this->current_category['forums_fid'];

		$domain = $this->setting['site_domain'];
		$download_url = "种子下载地址(支持IPv4和IPv6)：[url]{$domain}/torrents/{$this->tid}/download/[/url]\n\n";
		$arr_fields['descr'] = $download_url . $arr_fields['descr'];

		$forums_tid = $forums_discuzx_model->new_thread($this->user['forums_uid'], $this->username, $title, $arr_fields['descr'], $fid);

		if (intval($forums_tid) > 0)
		{
			$arr_fields = array();
			$arr_fields['forums_tid'] = $forums_tid;
			$this->torrents_module->update_torrent($arr_fields, $this->tid, false);
		}
		else
		{
			//$this->showmessage('种子发布到论坛失败!', true);
		}

		//发消息
		$from_username = $this->setting['admins_deliver'];
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$from_user = $users_module->get_by_username($from_username);
		if (!empty($from_user))
		{
			$from_uid = $from_user['forums_uid'];
			$msgto = $this->username;
			$subject = '感谢您发布种子';
			$message = "感谢您发布种子： $title \n";
			$message .= "请检查您的种子是否处于审核区，如果在审核区请尽量完善种子介绍并耐心等待管理员审核通过。\n";
			$message .= "请尽量多保种，长时间保种，时间越长保种积分越多。";
			$isusername = 1;
			$forums_discuzx_model->pm_send($from_uid, $msgto, $subject, $message, $isusername);
		}

		cg::load_model('logs_actions_model');
		$logs_action_model = logs_actions_model::get_instance();
		$arr_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'is_moderator' => $this->user['is_moderator'],
			'is_admin' => $this->user['is_admin'],
			'tid' => $this->tid,
			'action' => 'api_insert_torrent'
		);
		$arr_fields['details'] = array(
			'tid' => $this->tid,
			'torrent_title' => $title
		);
		$arr_fields['details'] = json_encode($arr_fields['details']);
		$logs_action_model->insert($arr_fields);

		if (isset($this->user['total_upload_times']) && isset($this->user['total_upload_size']))
		{
			$arr_fields = array(
				'total_upload_times' => $this->user['total_upload_times'] + 1,
				'total_upload_size' => $this->user['total_upload_size'] + $torrent_info['total_length']
			);
			$users_module->users_stat_model->update($arr_fields, $this->uid);
		}

		echo $this->tid;
		die();
		$this->showmessage($this->tid, false);
	}

	private function receive_attach()
	{
		cg::load_class('file_upload');
		$type = $this->get['type']; //subtitles or nfo
		if ($type == 'subtitles')
		{
			$path = empty($this->setting['subtitles_save_path']) ? 'attachments/subtitles' : $this->setting['subtitles_save_path'];
		}
		else
		{
			$path = empty($this->setting['nfos_save_path']) ? 'attachments/nfos' : $this->setting['nfos_save_path'];
		}
		$max_size = intval($this->setting['subtitles_size_limit']) * 1024 * 1024;
		//$path = "attachments/subtitles";
		$upload_cofig = array(
			'save_path' => $path,
			'max_size' => $max_size,
			'type' => $type
		);
		$file_upload = new file_upload($upload_cofig);
		if (isset($this->get['swfupload']))
		{
			$ret = $file_upload->save('Filedata');
		}
		else
		{
			$ret = $file_upload->save('filedata');
		}
		if ($ret === false)
		{
			$arr = array();
			$arr['err'] = '上传出错了，错误代码' . $file_upload->errno . ',错误描述：' . $file_upload->errors[$file_upload->errno];
			$arr['msg'] = '';
			die(json_encode($arr));
		}
		else
		{
			$saved_file = $file_upload->saved_file[0];
		}
		$arr_fields = array();
		if (isset($this->get['tid']))
		{
			$arr_fields['tid'] = intval($this->get['tid']);
		}
		else
		{
			$arr_fields['tid'] = '0'; //发布种子之后，根据guid更新tid
		}
		$arr_fields['type'] = $type;
		$arr_fields['filesize'] = $saved_file['size'];
		$arr_fields['old_name'] = $saved_file['old_name'];
		$arr_fields['newpath'] = $saved_file['new_name'];
		$arr_fields['file_md5'] = $saved_file['file_md5'];
		$arr_fields['download'] = '0';
		$arr_fields['uid'] = $this->user['uid'];
		$arr_fields['username'] = $this->user['username'];
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['guid'] = isset($this->get['guid']) ? $this->get['guid'] : '';

		cg::load_model('torrents_attachments_model');
		$torrents_attachments_model = torrents_attachments_model::get_instance();
		$attach_id = $torrents_attachments_model->insert($arr_fields);

		$upload_sub_extcredits1 = intval($this->setting['upload_sub_extcredits1']);
		$logs_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'count' => $upload_sub_extcredits1,
			'field' => 'extcredits1',
			'operator' => $this->uid,
			'operator_username' => $this->username,
			'ip' => $this->ip,
			'action' => 'upload_sub_extcredits1'
		);

		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$users_module->add_credits($this->uid, $upload_sub_extcredits1, 'extcredits1', $logs_fields);

		//奖励5个土豪金
		$upload_sub_extcredits2 = intval($this->setting['upload_sub_extcredits2']);
		$logs_fields = array(
			'count' => $upload_sub_extcredits2,
			'field' => 'extcredits2',
			'action' => 'upload_subtitles'
		);
		$logs_fields = array_merge($this->logs_credits_fields, $logs_fields);
		$users_module->add_credits($this->uid, $upload_sub_extcredits2, 'extcredits2', $logs_fields);


		$saved_file_name = $saved_file['new_name'];
		$arr = array();
		$arr['err'] = '';
		$arr['msg'] = array(
			'url' => '!' . $saved_file['old_name'],
			'download_url' => $saved_file['file_md5'],
			'attach_id' => $attach_id,
			'name' => $saved_file['old_name']
		);
		die(json_encode($arr));
	}

	private function receive_images()
	{
		cg::load_class('file_upload');
		$path = empty($this->setting['images_save_path']) ? 'attachments/images' : $this->setting['images_save_path'];
		$max_size = intval($this->setting['images_size_limit']) * 1024 * 1024;
		$upload_cofig = array(
			'save_path' => $path,
			'max_size' => $max_size,
			'type' => 'image'
		);
		$file_upload = new file_upload($upload_cofig);
		$ret = $file_upload->save('filedata');
		if ($ret === false)
		{
			$arr = array();
			$arr['err'] = '上传出错了，错误代码' . $file_upload->errno . ',错误描述：' . $file_upload->errors[$file_upload->errno];
			$arr['msg'] = '';
			die(json_encode($arr));
		}
		else
		{
			$saved_file = $file_upload->saved_file[0];
		}

		$arr_fields = array();
		$arr_fields['filesize'] = $saved_file['size'];
		$arr_fields['file_md5'] = $saved_file['file_md5'];
		$arr_fields['oldpath'] = '';
		$arr_fields['newpath'] = $saved_file['new_name'];
		$arr_fields['views'] = '0';
		$arr_fields['createtime'] = $this->timestamp;
		cg::load_model('images_model');
		$images_model = images_model::get_instance();
		$images_id = $images_model->insert($arr_fields);

		$arr_fields = array();
		$arr_fields['images_id'] = $images_id;
		$arr_fields['uid'] = $this->user['uid'];
		$arr_fields['username'] = $this->user['username'];
		$arr_fields['guid'] = $this->get['guid'];
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['tid'] = '0'; //发布种子之后，根据guid更新tid
		cg::load_model('torrents_images_model');
		$torrents_images_model = new torrents_images_model();
		$torrents_images_model->insert($arr_fields);

		$domain = empty($this->setting['images_domain']) ? cg::config()->APP_URL . 'attachments/images/' : $this->setting['images_domain'];
		$saved_file_name = $domain . $saved_file['new_name'];
		$arr = array();
		$arr['err'] = '';
		$arr['msg'] = '!' . $saved_file_name;
		die(json_encode($arr));
	}

	private function create_torrent_id()
	{
		return $this->torrents_module->create_torrent_id($this->uid);
	}

	private function ajax_insert_check()
	{
		$this->check_form(true);
	}

	private function ajax_update_check()
	{
		$this->check_form(true);
	}

	private function check_form($inajax = false)
	{
		$result['error'] = false;
		//检查表单填写是否完整，是否包含过滤词等等
		foreach ($this->current_category['options'] as $option)
		{
			$type = $option['type'];
			$field = $option['variable'];
			$value = isset($this->post[$field]) ? $this->post[$field] : '';
			$dict_replace = array(
				'[',
				']',
				'　'
			);
			if (!is_array($value))
			{
				$value = str_replace($dict_replace, '', $value);
				$value = funcs::full2half($value);
			}

			if ($option['required'] && empty($value))
			{
				$result['error'] = true;
				$result['msg'] = '请填写' . $option['title'];
				$result['field'] = $field;
				$result['field_type'] = $type;
				break;
			}

			if ($field == 'imdb')
			{
				if (empty($this->post['imdb']))
				{
					continue;
				}

				if ($this->post['imdb'] != 'http://www.imdb.com/title/tt0468569/' && preg_match('/^http:\/\/www\.imdb\.com\/title\/tt(\d{7})\/$/i', $this->post['imdb'], $matches))
				{
					$this->post['imdb'] = 'tt' . $matches[1];
				}
				else
				{
					$result['error'] = true;
					$result['msg'] = 'IMDB链接格式错误';
					$result['field'] = $field;
					$result['field_type'] = $type;
					break;
				}
			}
			else
			{
				if ($type == 'date')
				{
					$this->post[$field] = intval(str_replace('-', '', $value));
				}
				elseif ($type == 'year')
				{
					$this->post[$field] = intval($value);
				}
				elseif ($type == 'text' || $type == 'select' || $type == 'select_input')
				{
					$this->post[$field] = htmlspecialchars($value, ENT_QUOTES);
				}
				elseif ($type == 'selects')
				{
					$this->post[$field] = htmlspecialchars(implode('/', $value), ENT_QUOTES);
				}
			}
		}

		if (!$result['error'] && empty($this->post['descr']))
		{
			$result['error'] = true;
			$result['msg'] = '请填写种子介绍';
			$result['field'] = "descr";
			$result['field_type'] = 'textarea';
		}

		if (!$result['error'] && !$this->data['privileges_info']['have_privileges'])
		{
			$result['error'] = true;
			$result['msg'] = $this->data['privileges_info']['msg'];
			$result['field'] = "descr";
			$result['field_type'] = 'textarea';
		}

		if (!$result['error'] && strpos($this->post['descr'], 'base64,') !== false)
		{
			$result['error'] = true;
			$result['msg'] = '种子介绍内图片格式错误，请删掉后重新上传图片!';
			$result['field'] = 'descr';
			$result['field_type'] = 'textarea';
		}
		if (!isset($this->post['descr']))
		{
			$this->logs_debug($this->uid . '|||' . json_encode($this->post), 'upload empty post');
		}
		$this->post['descr'] = htmlspecialchars($this->post['descr'], ENT_QUOTES);
		$this->show_result($result, $inajax);
		return true;
	}
}