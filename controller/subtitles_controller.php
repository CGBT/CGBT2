<?php
class subtitles_controller extends base_controller
{
	private $torrents_attachments_model;
	private $sid, $page, $mod;
	private $current_subtitle;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
		cg::load_model('torrents_attachments_model');
		$this->torrents_attachments_model = torrents_attachments_model::get_instance();
	}

	public function index_action()
	{
		/**
		 *  /subtitles/
		 *  /subtitles/p1/
		 *  /subtitles/sid/download/
		 *  /subtitles/sid/delete/
		 *
		 */
		$this->page = isset($this->params['page']) ? $this->params['page'] : 1;
		$this->page = intval(str_replace('p', '', $this->page));
		$this->page = intval($this->page) <= 0 ? 1 : intval($this->page);

		$this->sid = isset($this->params['sid']) ? intval($this->params['sid']) : 0;
		$this->mod = empty($this->params['mod']) ? '' : $this->params['mod'];

		if (empty($this->sid) || empty($this->mod))
		{
			$this->index();
		}
		else
		{
			$dict_mod = array(
				'download',
				'delete'
			);
			if (!in_array($this->mod, $dict_mod))
			{
				$this->redirect('/subtitles/');
			}
			$this->current_subtitle = $this->torrents_attachments_model->find($this->sid);
			if (empty($this->current_subtitle))
			{
				die('没有此附件');
			}
			$this->{$this->mod}();
			die();
		}
	}

	private function index()
	{
		$pagesize = 50;
		$count = $this->torrents_attachments_model->count("type='subtitles'");
		$rows_subs = $this->torrents_attachments_model->get_subtitles($this->page, $pagesize);
		$tids = array();
		foreach ($rows_subs as $key => $row)
		{
			if ($row['tid'] > 0)
			{
				$tids[] = $row['tid'];
			}
		}
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		$torrents = $torrents_module->get_torrents($tids);

		foreach ($rows_subs as $key => $row)
		{
			if (!isset($torrents[$row['tid']]))
			{
				unset($rows_subs[$key]);
				continue;
			}
			else
			{
				$rows_subs[$key]['torrent_title'] = $torrents[$row['tid']]['title'];
			}
		}
		cg::load_core('cg_pager');
		$pager = new cg_pager('/subtitles/p$page/', $count, $pagesize, 10);
		$pager->paginate($this->page);
		$this->data['pager'] = $pager;

		$this->data['rows_subs'] = $rows_subs;
		$this->show('subtitles.php');
	}

	private function delete()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			if ($this->current_subtitle['uid'] != $this->uid)
			{
				die('您没有权限删除该附件');
			}
		}
		$row_attachment = $this->torrents_attachments_model->delete($this->sid);
		die('删除成功');
	}

	private function update_download_times()
	{
		$arr_fields = array();
		$arr_fields['download'] = $this->current_subtitle['download'] + 1;
		$this->torrents_attachments_model->update($arr_fields, $this->sid);
	}

	private function download()
	{
		ini_set('display_errors', '0');
		$this->update_download_times();
		if (empty($this->setting['subtitles_save_path']))
		{
			$real_filename = cg::config()->APP_PATH . 'attachments/subtitles/' . $this->current_subtitle['newpath'];
		}
		else
		{
			$real_filename = $this->setting['subtitles_save_path'] . '/' . $this->current_subtitle['newpath'];
		}
		$save_as = $this->current_subtitle['old_name'];
		if (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		{
			$save_as = urlencode($save_as);
		}
		$save_as = str_replace("+", " ", $save_as);
		ob_end_clean();
		header("Content-Disposition: attachment; filename=\"$save_as\"");
		//header("Content-Type: application/x-bittorrent");
		//$filesize = filesize($real_filename);
		//header('Content-Length: ' . $filesize);
		echo file_get_contents($real_filename);
	}
}