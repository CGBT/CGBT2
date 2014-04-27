<?php
/**
 * 更新索引表数据
 * 0 工具索引页面 /admin/torrentindex/index
 * 1 更新全部         /admin/torrentindex/update?category=all
 * 2 更新某个分类 /admin/torrentindex/update?category=movie
 * 3 更新某个种子 /admin/torrentindex/update?tid=1239
 */
class admin_torrentindex_controller extends admin_base_controller
{
	private $torrents_module;
	private $category;

	public function __construct()
	{
		parent::__construct();

		cg::load_module('torrents_module');
		$this->torrents_module = new torrents_module();
		$this->all_category = $this->torrents_module->all_category;

		set_time_limit(0);
		ini_set("memory_limit", "256M");
	}

	public function index_action()
	{
		$html = <<<HTML
		<div style="padding:10px;font-size:16px;line-height:25px;">
		<a target='_blank' href='/admin/torrentindex/update?category=all'>更新全部</a><br />
	    <a target='_blank' href='/admin/torrentindex/update?tid='>更新某个种子</a><br />

		<a target='_blank' href='/admin/torrentindex/update?category=movie'>更新电影</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=tv'>更新剧集</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=music'>更新音乐</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=comic'>更新动漫</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=game'>更新游戏</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=zongyi'>更新综艺</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=sports'>更新体育</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=software'>更新软件</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=study'>更新学习</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=other'>更新其他</a><br />
		<a target='_blank' href='/admin/torrentindex/update?category=documentary'>更新记录片</a><br />

		<br />
		</div>
HTML;
		$this->data['html'] = $html;
		$this->show('admin/update_torrentindex.php');

	}

	public function update_action()
	{
		$url = "/admin/torrentindex/update?";
		$tid = isset($this->get['tid']) ? intval($this->get['tid']) : 0;
		$category = isset($this->get['category']) ? $this->get['category'] : '';

		if (!isset($this->all_category[$category]) && !empty($category))
		{
			die('category error');
		}
		$this->category = $category;

		$where = array();
		if ($tid > 0)
		{
			$where = " id = '$tid'";
		}
		if (!empty($category))
		{
			$where = " category = '$category'";
			$url .= "category=$category";
		}
		$count = $this->torrents_module->torrents_model->count($where);
		$page = isset($this->get['page']) ? $this->get['page'] : 1;
		$page_size = 5000;
		$page_count = ceil($count / $page_size);
		$start = ($page - 1) * $page_size;
		$ids = $this->torrents_module->torrents_model->get_ids_by_sql($where, 'id', $start, $page_size);

		function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}

		foreach ($ids as $id)
		{
			$starttime = microtime_float();
			$this->torrents_module->insert_torrents_index($id);
			$endtime = microtime_float();

			$s = $id . "<br />\n";
			$s = ($endtime - $starttime) . "<br />\n";
			funcs::direct_output($s);
			//break;
		}
		//echo $page_count;
		$newpage = $page + 1;
		if ($newpage <= $page_count)
		{
			$url .= "&page=" . $newpage;
			echo "<meta http-equiv='refresh' content='1; url=$url' />";
		}
	}
}