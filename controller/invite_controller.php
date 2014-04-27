<?php
class invite_controller extends base_controller
{
	private $price_min = 800;
	private $price_max = 2000;
	private $invite_model;
	private $start_date = '2012-02-26';

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		$this->check_login();
		cg::load_model('invite_model');
		$this->invite_model = invite_model::get_instance();
		$this->data['selected_nav'] = 'invite';
	}

	public function index_action()
	{
		$this->buy_action();
	}

	public function buy_action()
	{
		$current_price = $this->calc_current_price();
		$extcredits1 = $this->user['extcredits1'];

		if (isset($this->post['issubmit']))
		{
			$this->check_have_privileges('credits2invite', true);
			if ($this->user['class'] < 12)
			{
				if ($extcredits1 < $current_price + 200)
				{
					$this->showmessage("你当前的保种积分为 $extcredits1 。您的保种积分必须大于" . ($current_price + 200) . "才能购买邀请！关于保种积分的说明请看公告。", true);
				}
			}
			$start_time = time() - 86400 * 14;
			$count = $this->invite_model->count("uid='{$this->uid}' and createtime > '$start_time'");
			if ($count >= 10)
			{
				$this->showmessage("您近期购买的邀请过多，请过一段时间之后再买!", true);
			}

			$invitecount = isset($this->post['invitecount']) ? intval($this->post['invitecount']) : '0';
			if ($invitecount <= 0)
			{
				$this->showmessage('请填写要购买的邀请数量', true);
			}
			$invitecount = 1;
			if ($extcredits1 - 200 < $current_price * $invitecount)
			{
				$this->showmessage("您的保种积分不足以购买这么多邀请! 您的保种积分必须满足购买后仍然大于200的条件。", true);
			}
			$this->show_no_error_ajax_message();

			$md5 = md5(date("Y-m-d H:i:s") . $this->uid . mt_rand(100000, 999999));
			$arr_fields = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'code' => $md5,
				'expiretime' => $this->timestamp + 7 * 86400,
				'createtime' => $this->timestamp,
				'price' => $current_price
			);
			$this->invite_model->insert($arr_fields);
			cg::load_module('users_module');
			$users_module = users_module::get_instance();
			$logs_fields = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'createtime' => $this->timestamp,
				'count' => (-1) * $current_price,
				'field' => 'extcredits1',
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'action' => 'extcretids12invite'
			);
			$users_module->add_credits($this->uid, (-1) * $current_price, 'extcredits1', $logs_fields);
			$msg_data = array(
				'msg' => '操作成功',
				'error' => false,
				'return_url' => '/invite/index/'
			);
			$this->showmessage($msg_data);
		}
		$this->data['current_price'] = $current_price;
		$this->get_invite();
		$this->show('invite.php');
	}

	private function get_invite()
	{
		$this->data['rows_invite'] = $this->invite_model->get_rows("uid='{$this->uid}'", 'id desc');
		$this->data['kaohe_data'] = $this->get_pass_kaohe_data($this->data['rows_invite']);
	}

	private function get_pass_kaohe_data($rows_invite)
	{
		if (empty($rows_invite))
		{
			return array();
		}
		$uids = "";
		foreach ($rows_invite as $row)
		{
			$uids .= $row['used_uid'] . ',';
		}
		$uids = substr($uids, 0, -1);

		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$uploaded = $this->setting['newbie_uploaded'];
		$downloaded = $this->setting['newbie_downloaded'];
		$extcredits1 = $this->setting['newbie_extcredits1'];
		$newbie_days = $this->setting['newbie_days'];
		$newbie_startdate = $this->setting['newbie_startdate'];

		$rows = $users_module->users_stat_model->get_pass_kaohe_users($uids, $uploaded, $downloaded, $extcredits1);
		$data = array();
		$data['pass_count'] = 0;
		foreach ($rows as $row)
		{
			if ($row['createtime'] < strtotime($newbie_startdate))
			{
				$row['all_pass'] = '-1';
			}

			$data['pass_data'][$row['username']] = $row['all_pass'];
			if ($row['all_pass'] == '1')
			{
				$data['pass_count'] += 1;
			}
		}
		return $data;
	}

	private function calc_current_price()
	{
		$total_invite_count = $this->invite_model->count();
		$avg = intval($total_invite_count / (($this->timestamp - strtotime($this->start_date)) / 86400));
		$in24hours = $this->timestamp - 86400;
		$today_count = $this->invite_model->count("createtime > '$in24hours'");
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
}
