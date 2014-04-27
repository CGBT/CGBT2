<?php
class admin_user_controller extends admin_base_controller
{

	/**
	 * user obj
	 * @var object
	 */
	public $users_model;
	public $users_module;

	public function __construct()
	{
		parent::__construct();
		cg::load_module('users_module');
		$this->users_module = users_module::get_instance();
		$this->users_model = $this->users_module->users_model;
	}

	public function index_action()
	{
		return $this->list_aciton();
	}

	public function edit_credits_action()
	{
		if (isset($this->post['submited']))
		{
			$username = isset($this->post['username']) ? $this->post['username'] : '';
			if (empty($this->username))
			{
				$this->showmessage('用户名错误');
			}
			$uid = $this->users_module->users_model->get_uid_by_username($username);
			if (empty($uid))
			{
				$this->showmessage('用户名不存在');
			}
			$field = isset($this->post['field']) ? $this->post['field'] : '';
			if (!in_array($field, array(
				'uploaded',
				'uploaded2',
				'extcredits1'
			)))
			{
				$this->showmessage('请选择积分类型');
			}
			$count = isset($this->post['count']) ? intval($this->post['count']) : '';
			if ($count > 1000)
			{
				$this->showmessage('每次修改的积分值不能超过1000');
			}

			$reason = isset($this->post['reason']) ? $this->post['reason'] : '';

			$logs_fields = array(
				'uid' => $uid,
				'username' => $username,
				'count' => $count,
				'field' => $field,
				'action' => 'admin',
				'createtime' => $this->timestamp,
				'operator' => $this->uid,
				'operator_username' => $this->username,
				'ip' => $this->ip,
				'details' => $reason
			);
			$this->users_module->add_credits($uid, $count, $field, $logs_fields);
		}

		cg::load_model('logs_credits_model');
		$logs_credits_model = logs_credits_model::get_instance();
		$this->data['latest_rows'] = $logs_credits_model->get_latest_rows();
		$this->show('admin/user_edit_credits.php');
	}

	public function edit_group_action()
	{
		$this->data['current_row'] = array();
		$this->data['all_users_group'] = $this->users_module->get_all_users_group();

		cg::load_model('logs_modgroup_model');
		$logs_modgroup = logs_modgroup_model::get_instance();
		$dt = strtotime('2013-04-04 21:42');
		$this->data['rows_logs'] = $logs_modgroup->get_rows("createtime > '$dt'", 'id desc', '200');
		$this->show('admin/user_edit_group.php');
	}

	public function update_group_action()
	{
		$username = $this->post['username'];
		$user = $this->users_module->get_by_username($username);
		if (empty($user))
		{
			die('username not exists');
		}
		$arr_fields = array(
			'class' => $this->post['groupid']
		);
		$this->users_module->users_stat_model->update($arr_fields, $user['uid']);

		cg::load_model('logs_modgroup_model');
		$logs_modgroup = logs_modgroup_model::get_instance();
		$arr_fields = array(
			'uid' => $user['uid'],
			'username' => $username,
			'operator' => $this->username,
			'operator_uid' => $this->uid,
			'old_groupid' => $user['groupid'],
			'new_groupid' => $this->post['groupid'],
			'old_groupname' => $user['group_name'],
			'new_groupname' => $this->users_module->all_users_group[$this->post['groupid']]['name'],
			'createtime' => $this->timestamp,
			'reason' => $this->post['reason']
		);
		$logs_modgroup->insert($arr_fields);

		$dt = strtotime('2013-04-04 21:42');
		$this->data['rows_logs'] = $logs_modgroup->get_rows("createtime > '$dt'", 'id desc', '200');
		$this->data['current_row'] = array();
		$this->data['all_users_group'] = $this->users_module->get_all_users_group();
		$this->show('admin/user_edit_group.php');
	}

	/**
	 * BT用户查询功能
	 * @return html show
	 */
	public function search_action()
	{
		//@todo
		$this->data['search_type_arr'] = array(
			'username' => '用户名',
			'email' => 'Email',
			'regip' => '注册IP'
		);
		//
		if (!empty($_GET))
		{
			$this->search();
		}
		else
		{
			$this->data['search_type'] = 'username';
		}
		$this->template_file = 'admin/user_index.php';
		$this->show();
	}

	public function search()
	{
		$search_type = $this->request('search_type', FILTER_DEFAULT, INPUT_GET);

		switch ($search_type)
		{
			case 'username':
				$search_value = $this->request('search_value', FILTER_DEFAULT, INPUT_GET);
				break;
			case 'email':
				$search_value = $this->request('search_value', FILTER_VALIDATE_EMAIL, INPUT_GET);
				break;
			case 'regip':
				$search_value = $this->request('search_value', FILTER_VALIDATE_IP, INPUT_GET);
				break;
			default:
				$search_value = '';
				break;
		}
		$this->data['search_type'] = $search_type;
		$this->data['search_value'] = $search_value;
		if (empty($search_value))
		{
			return NULL;
		}
		//get user list;
		switch ($search_type)
		{
			case 'username':
				$user = $this->users_module->get_by_username($search_value);
				$this->data['user_list'] = !empty($user) ? array(
					$user
				) : array();
				break;
			case 'email':
				$this->data['user_list'] = $this->users_model->get_by_email($search_value);
				break;
			case 'regip':
				$this->data['user_list'] = $this->users_model->get_by_ip($search_value);
				break;
			default:
				$this->data['user_list'] = array();
				break;
		}
	}

	/**
	 * 用户列表
	 * @return [type] [description]
	 */
	public function list_action()
	{
		$user_count = $this->users_model->get_count();
		if ($user_count)
		{
			$itemPerPage = 20;
			cg::load_core('cg_pager');
			$pager = new cg_pager();
			$page_url = '/admin/user/list/?p=$page';
			$pager = new cg_pager($page_url, $user_count, $itemPerPage);
			$pager->paginate($this->request('p', FILTER_VALIDATE_INT, INPUT_GET, 1));
			//print_r($pager->currentPage);
			//print_r($pager->totalPage);
			$this->data['pager'] = &$pager;
			$start_uid = ($pager->totalPage - $pager->currentPage) * $itemPerPage;
			$end_uid = $start_uid + $itemPerPage;
			$this->data['user_list'] = $this->users_model->get_list_by_uid($start_uid, $end_uid);
		}
		$this->template_file = 'admin/user_list.php';
		$this->show();
	}
}