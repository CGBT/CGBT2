<?php
class toolbox_controller extends base_controller
{
	private $price_min = 800;
	private $price_max = 2000;
	private $invite_model;
	private $start_date = '2012-02-26';

	public function index_action()
	{
		$this->check_login();
		$this->data['selected_nav'] = 'toolbox';
		$this->data['title'] = '工具箱-';
		$forums_user = $this->get_forums_user_data();
		$this->data['user_money'] = $forums_user[$this->setting['forums_money_field']];
		$this->data['user_extcredits1'] = $this->user['extcredits1'];
		$this->data['current_invite_price'] = $this->calc_current_invite_price();
		$this->data['current_mod_price'] = $this->get_current_mod_price();
		$this->show('toolbox.php');
	}

	public function get_forums_user_data()
	{
		cg::load_model('forums_discuzx_model');
		$forums_discuzx_model = forums_discuzx_model::get_instance();
		return $forums_discuzx_model->get_user_info($this->username);
	}


	/**
	 * 检查是否可以兑换上传流量
	 * 共享率低于标准才可以，
	 * 不启用共享率限制的话不可兑换，下载小于20G不可兑换
	 * /torrents/id/download/里面也有类似函数
	 * 差别在于下载小于20G的处理
	 */
	private function check_allow_touploaded()
	{
		if (!$this->setting['enable_ratio_limit'])
		{
			return false;
		}
		$downloaded = $this->user['downloaded'];
		$G = 1024 * 1024 * 1024;
		if ($downloaded < 20 * $G)
		{
			return false;
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
			return true;
		}
		return false;
	}

	public function extcredits12uploaded_action()
	{
		$extcredits12uploaded_need_extcredits1 = intval($this->setting['extcredits12uploaded_need_extcredits1']);
		if ($extcredits12uploaded_need_extcredits1 <= 0)
		{
			$this->showmessage('保种积分兑换虚拟上传流量功能暂未开通', true);
		}
		if (!$this->check_allow_touploaded())
		{
			$this->showmessage('仅共享率过低的受限用户可以兑换虚拟上传流量', true);
		}

		$extcredits1 = isset($this->post['extcredits1']) ? intval($this->post['extcredits1']) : 0;
		if ($extcredits1 <= 0)
		{
			$this->showmessage('保种积分输入错误', true);
		}
		$user_extcredits1 = intval($this->user['extcredits1']);

		if ($extcredits1 < $extcredits12uploaded_need_extcredits1)
		{
			$this->showmessage("请输入正确的保种积分! 最少需要 $extcredits12uploaded_need_extcredits1 保种积分。");
		}
		elseif ($extcredits1 > $user_extcredits1)
		{
			$this->showmessage("您没有这么多保种积分!");
		}
		elseif ($extcredits1 > $this->setting['extcredits12uploaded_max'])
		{
			$this->showmessage("您每次可兑换的保种积分上限为 {$this->setting['extcredits12uploaded_max']} ");
		}

		//得到上一次和本次更新日期
		if ($this->setting['extcredits12uploaded_days_interval'] > 0)
		{
			cg::load_model('logs_credits_model');
			$logs_credits_model = logs_credits_model::get_instance();
			$last_time = intval($logs_credits_model->last_extcredits12uploaded_time($this->uid));
			if ($last_time > 0)
			{
				//比较两个时间
				$days_interval = intval(($this->timestamp - $last_time) / 86400);
				if ($days_interval < $this->setting['extcredits12uploaded_days_interval'])
				{
					$this->showmessage("您在 {$this->setting['extcredits12uploaded_days_interval']} 天内只能兑换1次。");
				}
			}
		}

		$uploaded = intval($extcredits1 / $extcredits12uploaded_need_extcredits1);
		$extcredits1 = $uploaded * $extcredits12uploaded_need_extcredits1;

		cg::load_module('users_module');
		$users_module = users_module::get_instance();

		$logs_fields = array(
			'count' => -1 * $extcredits1,
			'field' => 'extcredits1',
			'action' => 'extcredits12uploaded2'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$users_module->add_credits($this->uid, -1 * $extcredits1, 'extcredits1', $logs_fields);

		$logs_fields = array(
			'count' => $uploaded,
			'field' => 'uploaded2',
			'action' => 'extcredits12uploaded2'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$users_module->add_credits($this->uid, $uploaded, 'uploaded2', $logs_fields);

		$msg = "操作成功， $extcredits1 保种积分兑换为 $uploaded G 虚拟上传流量 !";
		$this->send_pm('', $this->username, '', $msg);
		$this->showmessage($msg, false);
	}

	public function money2uploaded_action()
	{
		$money2uploaded_need_money = intval($this->setting['money2uploaded_need_money']);
		if ($money2uploaded_need_money <= 0)
		{
			$this->showmessage('金币兑换虚拟上传流量功能暂未开通', true);
		}
		if (!$this->check_allow_touploaded())
		{
			$this->showmessage('仅共享率过低的受限用户可以兑换虚拟上传流量', true);
		}


		$forums_money_field = $this->setting['forums_money_field'];
		if (empty($forums_money_field))
		{
			$this->showmessage('金币兑换虚拟上传流量功能暂未开通', true);
		}

		$money = isset($this->post['money']) ? intval($this->post['money']) : 0;
		if ($money <= 0)
		{
			$this->showmessage('金币数量错误', true);
		}
		$forums_user = $this->get_forums_user_data();
		$user_money = intval($forums_user[$forums_money_field]);

		if ($money < $money2uploaded_need_money)
		{
			$this->showmessage("请输入正确的金币数量! 最少需要 $money2uploaded_need_money 个金币。");
		}
		elseif ($money > $user_money)
		{
			$this->showmessage("您没有这么多金币!");
		}
		elseif ($money > $this->setting['money2uploaded_max'])
		{
			$this->showmessage("您每次可兑换的金币上限为 {$this->setting['money2uploaded_max']} ");
		}

		//得到上一次和本次更新日期
		if ($this->setting['money2uploaded_days_interval'] > 0)
		{
			cg::load_model('logs_credits_model');
			$logs_credits_model = logs_credits_model::get_instance();
			$last_time = intval($logs_credits_model->last_money2uploaded_time($this->uid));
			if ($last_time > 0)
			{
				//比较两个时间
				$days_interval = intval(($this->timestamp - $last_time) / 86400);
				if ($days_interval < $this->setting['money2uploaded_days_interval'])
				{
					$this->showmessage("您在 {$this->setting['money2uploaded_days_interval']} 天内只能兑换1次。");
				}
			}
		}

		$uploaded = intval($money / $money2uploaded_need_money);
		$money = $uploaded * $money2uploaded_need_money;

		$forums_discuzx_model = forums_discuzx_model::get_instance();
		$forums_discuzx_model->add_credits($forums_money_field, -1 * $money, $this->user['forums_uid']);

		cg::load_module('users_module');
		$users_module = users_module::get_instance();

		$logs_fields = array(
			'count' => -1 * $money,
			'field' => 'money',
			'action' => 'money2uploaded2'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$users_module->add_credits($this->uid, -1 * $money, 'money', $logs_fields);

		$logs_fields = array(
			'count' => $uploaded,
			'field' => 'uploaded2',
			'action' => 'money2uploaded2'
		);
		$logs_fields = array_merge($logs_fields, $this->logs_credits_fields);
		$users_module->add_credits($this->uid, $uploaded, 'uploaded2', $logs_fields);


		$msg = "操作成功， $money 金币 兑换为 $uploaded G 虚拟上传流量 !";
		$this->send_pm('', $this->username, '', $msg);
		$this->showmessage($msg, false);
	}

	private function calc_current_invite_price()
	{
		cg::load_model('invite_model');
		$invite_model = invite_model::get_instance();
		$total_invite_count = $invite_model->count();
		$avg = intval($total_invite_count / (($this->timestamp - strtotime($this->start_date)) / 86400));
		$in24hours = $this->timestamp - 86400;
		$today_count = $invite_model->count("createtime > '$in24hours'");
		if ($avg == 0)
		{
			$avg = 1;
		}
		$current_price = intval($this->price_min + $today_count * ($this->price_max - $this->price_min) / $avg);
		$current_price = min($current_price, $this->price_max);
		$current_price = max($current_price, $this->price_min);

		if ($this->user['class'] == 12)
		{
			$current_price = 0;
		}
		return $current_price;
	}

	private function get_current_mod_price()
	{
		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$rows = $torrents_price_mod_model->get_top_3();
		$current_price = $this->setting['mod_price_min'];
		if (!empty($rows) && count($rows) >= 3)
		{
			$rows_price = array();
			foreach ($rows as $row)
			{
				$rows_price[] = $row['sort_price'];
			}
			$current_price = min($rows_price) + 1;
		}
		return $current_price;
	}

	public function price_mod_delete_action()
	{
		if (!$this->user['is_admin'] && !$this->user['is_moderator'])
		{
			$this->showmessage('没有权限');
		}
		$id = isset($this->post['id']) ? intval($this->post['id']) : 0;
		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$torrents_price_mod_model->delete_mod($id);

		$details = array();
		$details['price_mod_id'] = $id;
		$arr_fields = array();
		$arr_fields['uid'] = $this->uid;
		$arr_fields['username'] = $this->username;
		$arr_fields['createtime'] = $this->timestamp;
		$arr_fields['is_moderator'] = $this->user['is_moderator'];
		$arr_fields['is_admin'] = $this->user['is_admin'];
		$arr_fields['tid'] = 0;
		$arr_fields['action'] = 'delete_price_mod_top';
		$arr_fields['details'] = json_encode($details);

		cg::load_model('logs_actions_model');
		$logs_actions_model = new logs_actions_model();
		$logs_actions_model->insert($arr_fields);

		$this->showmessage('操作成功');
	}

	public function price_mod_action()
	{
		$this->check_have_privileges('price_top', true);
		$current_price = $this->get_current_mod_price();
		$current_price_uploaded = intval($current_price / 4);
		$current_price_uploaded2 = intval($current_price / 2);
		$current_price_downloaded2 = intval($current_price * 2);

		$G = 1024 * 1024 * 1024;
		$extcredits1 = intval($this->user['extcredits1']);
		$uploaded = intval($this->user['uploaded'] / $G);
		$uploaded2 = intval($this->user['uploaded2'] / $G);
		$downloaded2 = intval($this->user['downloaded2'] / $G);

		$price = isset($this->post['price']) ? intval($this->post['price']) : 0;
		if ($price <= 0)
		{
			$this->showmessage('参数错误');
		}
		$sort_price = 0;
		$credit_type = $this->post['credit_type'];
		if ($credit_type == 'extcredits1')
		{
			$sort_price = $price;
			$max = max($price, $current_price);
			if ($extcredits1 < $max + 200)
			{
				$this->showmessage("你当前的保种积分为 $extcredits1 。您的保种积分必须大于" . ($max + 200) . "才能竞价置顶！", true);
			}
		}
		elseif ($credit_type == 'uploaded')
		{
			$sort_price = $price * 4;
			$max = max($price, $current_price_uploaded);
			if ($uploaded < $max + 500)
			{
				$this->showmessage("你当前的上传流量为  $uploaded G 。您的上传流量必须大于" . ($max + 500) . "才能竞价置顶！", true);
			}
		}
		elseif ($credit_type == 'uploaded2')
		{
			$sort_price = $price * 2;
			$max = max($price, $current_price_uploaded2);
			if ($uploaded2 < $current_price_uploaded2)
			{
				$this->showmessage("你当前的上传流量为  $uploaded2 G 。您的虚拟上传流量必须大于" . $max . "才能竞价置顶！", true);
			}
		}
		elseif ($credit_type == 'downloaded2')
		{
			$sort_price = intval($price / 2);
			$max = max($price, $current_price_downloaded2);
			if ($downloaded2 < $current_price_downloaded2)
			{
				$this->showmessage("你当前的虚拟下载流量为  $downloaded2 G 。您的虚拟下载流量必须大于" . $max . "才能竞价置顶！", true);
			}
		}
		else
		{
			$this->showmessage('参数错误');
		}
		if ($sort_price < $current_price)
		{
			$this->showmessage('您的出价过低');
		}

		$tid = isset($this->post['tid']) ? intval($this->post['tid']) : 0;
		if ($tid <= 0)
		{
			$this->showmessage('参数错误');
		}
		$start_time = strtotime($this->post['start_time']);
		if ($start_time < $this->timestamp - 3600 || date('H', $start_time) > 22 || date('H', $start_time) < 8)
		{
			$this->showmessage('置顶起始时间错误，起始时间不能在一个小时之前，也不能在22:00-8:00之间。');
		}

		$arr_fields = array(
			'tid' => $tid,
			'type' => 'top',
			'start_time' => $start_time,
			'end_time' => $start_time + 86400,
			'uid' => $this->uid,
			'sort_price' => $sort_price,
			'price' => $price,
			'price_type' => $credit_type,
			'username' => $this->username,
			'enabled' => '1',
			'status' => '1'
		);

		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$torrents_price_mod_model->insert_mod($arr_fields);

		$logs_fields = array(
			'uid' => $this->uid,
			'username' => $this->username,
			'createtime' => $this->timestamp,
			'count' => (-1) * $price,
			'field' => $credit_type,
			'operator' => $this->uid,
			'operator_username' => $this->username,
			'ip' => $this->ip,
			'action' => 'price_mod_top'
		);
		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$users_module->add_credits($this->uid, -1 * $price, $credit_type, $logs_fields);
		$this->showmessage('竞价置顶设置成功，请根据设置的时间，到时间后(时间有误差，大概为5分钟左右)观察效果!');
	}
}
