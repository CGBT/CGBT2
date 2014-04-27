<?php
class logs_cron_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('logs_cron');
		$this->pk = 'id';
	}

	/**
	 *
	 * @return logs_cron_model
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

	public function get_all_method()
	{
		$dict_method = array(
			'check_peers_connectable',
			'clear_bans_status',
			'clear_hour_uploaded_downloaded',
			'clear_today_uploaded_downloaded',
			'create_images_url_queue_from_descr',
			'delete_no_tid_images',
			'delete_no_used_peers_ips',
			'delete_unaudited_torrents',
			'fetch_queue_pic',
			'get_dayuser',
			'newbie_task',
			'replace_pic_in_descr',
			'reset_peers_connectable',
			'update_bt_info_hash',
			'update_comments_count',
			'update_extcredits1_speed',
			'update_sitestat',
			'update_torrents_mod',
			'update_torrents_price_mod',
			'update_total_credits'
		);
		return $dict_method;
	}

	public function get_cron_list($params)
	{
		$where = array();
		if (isset($params['controller']))
		{
			$where[] = " controller = '{$params['controller']}'";
		}
		if (isset($params['method']))
		{
			$where[] = " method = '{$params['method']}'";
		}
		if (isset($params['real_exec']) && $params['real_exec'])
		{
			$where[] = " real_exec = '1'";
		}
		$where = empty($where) ? '' : " where " . implode(' and ', $where);
		$sql = "select count(1) from $this->table $where";
		$count = $this->db()->get_count($sql);
		$order_by = !empty($params['order_by']) ? trim($params['order_by']) : 'id';
		$sort = !empty($params['sort']) ? trim($params['sort']) : 'desc';
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$pagesize = isset($params['pagesize']) ? intval($params['pagesize']) : 100;
		$page_count = ceil($count / $pagesize);
		if ($page > $page_count)
		{
			$page = $page_count;
		}
		if ($page < 1)
		{
			$page = 1;
		}
		$start = ($page - 1) * $pagesize;
		$sql = "select * from $this->table $where order by $order_by $sort limit $start, $pagesize ";
		$rows = $this->db()->get_rows($sql);
		$data['count'] = $count;
		$data['rows'] = $rows;
		return $data;
	}

	public function get_lastest()
	{
		$sql = "select * from $this->table order by id desc limit 100";
		return $this->db()->get_rows($sql);
	}
}