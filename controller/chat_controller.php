<?php
class chat_controller extends base_controller
{
	/**
	 *
	 * /chat/room/  all users
	 * /chat/room/cutehalo
	 * /chat/room/tdk
	 * /chat/room/tdk/cutehalo/hulupiao/ihipop
	 *
	 * /chat/?room=xxx  page
	 * /chat/say/       json
	 * /chat/ban/       json
	 * /chat/del/       json
	 *
	 * /chat/list/      page
	 * /chat/history/?room=xx
	 *
	 */
	private $room = '';
	private $room_admin = '';
	private $room_name = '';
	private $is_room_admin = false;
	private $max_record = 1000; //读取的最多消息数量;
	private $all_ban_user, $is_ban_user;
	private $users_module, $chat_model;
	private $counter = 0;

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);

		cg::load_module('users_module');
		$this->users_module = new users_module();
		cg::load_model('chat_model');
		$this->chat_model = chat_model::get_instance();
		if (empty($this->user))
		{
			$this->user["username"] = "guest" . substr(str_replace(".", "", str_replace(":", "", $this->ip)), -6);
			$this->user["uid"] = 0;
			$this->user["class"] = 0;
			$this->user['title'] = $this->user['username'];
			$this->user['is_admin'] = false;
			$this->user['is_moderator'] = false;
			$this->data['user'] = $this->user;
		}

		$this->all_ban_user = $this->cache()->get("chat_ban_user");
		if (empty($this->all_ban_user))
		{
			$this->all_ban_user = array();
		}
		if (!empty($this->all_ban_user) && in_array($this->user["username"], $this->all_ban_user))
		{
			$this->is_ban_user = true;
		}
		$privileges_info = $this->check_have_privileges('chat_use_ubb', false);
		$this->data['chat_use_ubb'] = $privileges_info['have_privileges'];
	}

	public function get_action()
	{
		$this->get_room();
		$this->output();
	}

	private function output($action = '')
	{
		if ($this->is_ban_user)
		{
			$data["action"] = "ban";
			die(json_encode($data));
		}
		$html = "";
		if ($this->counter == 0)
		{
			$this->counter = intval($this->chat_model->max_id());
		}
		$start = isset($this->post['start']) ? intval($this->post['start']) : 0;
		$reply_me = isset($this->post['reply_me']) ? intval($this->post['reply_me']) : 0;
		$my_chat = isset($this->post['my_chat']) ? intval($this->post['my_chat']) : 0;

		if ($this->counter - $start > $this->max_record || $this->counter < $start || $action == 'refresh')
		{
			$start = $this->counter - $this->max_record;
		}
		$cache_keys = array();
		for($i = $start + 1; $i <= $this->counter; $i++)
		{
			$cache_keys[] = $this->room . '_' . $i;
		}
		if (empty($cache_keys))
		{
			$rows = array();
		}
		else
		{
			$rows = $this->cache()->get($cache_keys);
		}
		//ksort($rows);
		foreach ($rows as $key => $row)
		{
			if (empty($row))
			{
				$pk_id = str_replace($this->room . '_', '', $key);
				$row = $this->chat_model->_find($pk_id);
				$this->cache()->set($key, $row);
			}
			if (isset($row['reply_uid']) && $row['reply_uid'] > 0 && $row['reply_uid'] != $this->uid)
			{
				if (!$this->is_room_admin && $this->uid != $row['uid'])
				{
					continue;
				}
			}
			if ($reply_me && $my_chat)
			{
				if ($row['reply_uid'] != $this->uid && $row['uid'] != $this->uid)
				{
					continue;
				}
			}
			else
			{
				if ($reply_me && $row['reply_uid'] != $this->uid)
				{
					continue;
				}

				if ($my_chat && $row['uid'] != $this->uid)
				{
					continue;
				}
			}
			if (strpos($row['createtime'], ":") === false)
			{
				$row['createtime'] = date("H:i:s", $row['createtime']);
			}
			if (empty($row) || empty($row['txt']) || (isset($row['status']) && $row['status'] == '-1'))
			{
				unset($rows[$key]);
				continue;
			}
			if (isset($row['parse_ubb']) && $row['parse_ubb'])
			{
				$row["txt"] = funcs::ubb2html($row["txt"]);
			}
			else
			{
				$row["txt"] = htmlspecialchars($row['txt'], ENT_QUOTES);
			}
			if (!empty($row["user_title"]))
			{
				$display_name = $row["user_title"];
			}
			else
			{
				$display_name = $row["username"];
			}

			$html .= "<li class='cl'><div class='chat-avatar'><a target='_blank' href='/user/$row[uid]/'><img src='" . $this->setting['forums_url'] .
			 "uc_server/avatar.php?uid=$row[forums_uid]&size=small'></a></div>";
			if ($this->is_room_admin)
			{
				$html .= "<span class='manage-action'><span onclick=\"del('$row[id]');\">x</span> <span onclick=\"ban('$row[username]');\">b</span> <a target='_blank' href='/user/$row[uid]/'>u</a></span>";
			}
			$html .= "<div class='pmt'></div><div class='pmd'><span onclick=\"reply_user('$display_name','{$row['uid']}');\" style='cursor:pointer;'>$display_name: </span><br>$row[txt]<br><span style='color:#ccc'>$row[createtime]</span></div></li>\n";
		}
		$result["txt"] = $html;
		$result["action"] = $action;
		$result["start"] = $this->counter;
		die(json_encode($result));
	}

	public function say_action()
	{
		$this->get_room();
		$txt = isset($this->post['txt']) ? $this->post['txt'] : '';
		if ($this->is_ban_user)
		{
			$this->output();
			die();
		}
		if (!empty($txt))
		{
			//$txt = htmlspecialchars($txt, ENT_QUOTES);
			$arr_fields = array();
			$arr_fields["createtime"] = $this->timestamp;
			$arr_fields["user_title"] = $this->user["title"];
			$arr_fields["username"] = $this->user["username"];
			$arr_fields["forums_uid"] = $this->user["forums_uid"];
			$arr_fields["uid"] = $this->user["uid"];
			$arr_fields["class"] = $this->user["class"];
			$arr_fields["ip"] = $this->ip;
			$arr_fields["txt"] = $txt;
			$arr_fields["room"] = $this->room;
			$arr_fields["reply_uid"] = intval($this->post['reply_uid']);
			$this->counter = $this->chat_model->insert($arr_fields);
			$arr_fields["id"] = $this->counter;
			$arr_fields["createtime"] = date("H:i:s", $arr_fields["createtime"]);
			$privileges_info = $this->check_have_privileges('chat_use_ubb', false);
			$arr_fields['parse_ubb'] = $privileges_info['have_privileges'] ? '1' : '0';
			$this->cache()->set($this->room . "_" . $this->counter, $arr_fields, 86400 * 7);
		}
		$this->output();
	}

	public function unban_action()
	{
		if (!$this->is_room_admin)
		{
			return;
		}
		$ban_user = isset($this->get['ban_user']) ? $this->get['ban_user'] : '';
		if (empty($ban_user))
		{
			return;
		}

		$index = array_search($ban_user, $this->all_ban_user);
		if ($index !== false)
		{
			unset($this->all_ban_user[$index]);
			$this->cache()->set("chat_ban_user", $this->all_ban_user, 86400);
			die("unban user: $ban_user done!");
		}
	}

	public function ban_action()
	{
		$this->get_room();
		if (!$this->is_room_admin)
		{
			return;
		}

		$ban_user = isset($this->post['ban_user']) ? $this->post['ban_user'] : '';
		if (empty($ban_user))
		{
			return;
		}

		if (in_array($ban_user, $this->all_ban_user))
		{
			return;
		}

		$user = $this->users_module->get_by_username($ban_user);
		if (!empty($user) && ($user['is_moderator'] || $user['is_admin']))
		{
			die("can't ban $ban_user !");
		}
		else
		{
			$this->all_ban_user[] = $ban_user;
			$this->cache()->set("chat_ban_user", $this->all_ban_user, 86400);

			$txt = "[color=red]" . $ban_user . '已被封禁发言!' . "[/color]";
			$arr_fields = array();
			$arr_fields["createtime"] = $this->timestamp;
			$arr_fields["user_title"] = '系统消息';
			$arr_fields["username"] = $this->username;
			$arr_fields["uid"] = $this->uid;
			$arr_fields["class"] = '100';
			$arr_fields["ip"] = $this->ip;
			$arr_fields["txt"] = $txt;
			$arr_fields["room"] = $this->room;
			$arr_fields["parse_ubb"] = 1;
			$this->counter = $this->chat_model->insert($arr_fields);
			$arr_fields["id"] = $this->counter;
			$arr_fields["createtime"] = date("H:i:s", $arr_fields["createtime"]);
			$this->cache()->set($this->room . "_" . $this->counter, $arr_fields, 86400);
			die("ban user: $ban_user done!");
		}
		$this->output();
	}

	public function del_action()
	{
		$this->get_room();
		if (!$this->is_room_admin)
		{
			return;
		}
		$id = isset($this->post['id']) ? intval($this->post['id']) : 0;
		if ($id <= 0)
		{
			return;
		}
		$this->cache()->delete($this->room . "_" . $id);
		$arr_fields = array(
			'status' => '-1'
		);
		$this->chat_model->update($arr_fields, $id);
		$this->output('refresh');
	}

	public function zhixing_action()
	{
		$this->room_action();
	}

	public function index_action()
	{
		$this->room_action();
	}

	private function get_room()
	{
		if (isset($this->post['room']))
		{
			$this->check_room_key();
			$this->room = $this->post['room'];
			if (empty($this->room))
			{
				$this->room_admin = '_';
			}
			elseif (stripos('/', $this->room) === false)
			{
				$this->room_admin = $this->room; //一个人
			}
			else
			{
				$users = explode('/', $this->room);
				if (count($users) == 2) // a/b 两个人
				{
					$this->room_admin = $this->room;
				}
				else // a/b/c 多个人
				{
					$this->room_admin = $users[0];
				}
			}
		}
		else
		{
			if (empty($this->params))
			{
				$this->room = '_';
				$this->room_admin = '';
				$this->room_name = '聊天广场';
			}
			elseif (isset($this->params['room'])) //1个人
			{
				$this->room = $this->params['room'];
				$this->room_admin = $this->room;
				$this->room_name = $this->room . '的房间';
			}
			elseif (count($this->params) == 2) //两个人
			{
				sort($this->params);
				$this->room = strtolower(implode('/', $this->params));
				$this->room_admin = $this->room; //两个人都是admin
				$this->room_name = $this->params[0] . '和' . $this->params[1] . '的二人私密聊天室';
				if (stripos('/' . $this->room_admin . '/', '/' . $this->username . '/') === false)
				{
					$this->showmessage('您不能访问别人的私密聊天室');
				}
			}
			else //多个人
			{
				$this->room_admin = $this->params[0]; //第一个人是admin
				sort($this->params);
				$this->room = strtolower(implode('/', $this->params));
				$this->room_name = $this->room . '的多人间聊天室';
				if (stripos('/' . $this->room . '/', '/' . $this->username . '/') === false)
				{
					$this->showmessage('您不能访问别人的私密聊天室');
				}
			}
		}
		if (!preg_match('/^[a-z0-9\/_]+$/i', $this->room))
		{
			$this->showmessage('房间不存在');
		}
		if ($this->user['is_admin'] || $this->user['is_moderator'])
		{
			$this->is_room_admin = true;
		}
		if (!empty($this->username) && (stripos('/' . $this->room_admin . '/', '/' . $this->username . '/') !== false || $this->username == $this->room_admin))
		{
			$this->is_room_admin = true;
		}
	}

	public function room_action()
	{
		$this->get_room();
		$this->data['room'] = $this->room;
		$this->data['room_name'] = $this->room_name;
		$this->data['room_admin'] = $this->room_admin;
		$this->data['room_key'] = md5(cg::config()->config['system_salt_key'] . $this->room . date("Ym"));
		$this->data['is_room_admin'] = $this->is_room_admin;
		if ($this->action_name == 'zhixing_action')
		{
			$this->show('zhixing_chat.php');
		}
		else
		{
			$this->show('chat.php');
		}
	}

	private function check_room_key()
	{
		$room = isset($this->post['room']) ? $this->post['room'] : '';
		$room_key = isset($this->post['room_key']) ? $this->post['room_key'] : '';
		if (md5(cg::config()->config['system_salt_key'] . $room . date("Ym")) != $room_key)
		{
			$this->output('error_refresh');
			die();
		}
	}

	private function get_smilies()
	{
		global $smilies, $smilies_dir;
		$table = "<table style='margin:0 auto;'>";
		$count = count($smilies);
		for($i = 0; $i < $count; $i++)
		{
			$table .= "<tr>";
			for($j = 0; $j < 9; $j++)
			{
				$i++;
				if ($i < $count)
				{
					$table .= "<td><img src='$smilies_dir/" . $smilies[$i][2] . "' style='width:30px;height:30px;cursor:pointer' onclick=\"insert_smilies(' " . $smilies[$i][1] .
					 "')\"></td>";
				}
			}
			$table .= "</tr>\n";
		}
		$table .= "</table>";
		return $table;
	}

	private function convert_smilies($txt)
	{
		global $smilies, $smilies_dir;
		$smilies_searcharray = array();
		$smilies_replacearray = array();

		foreach ($smilies as $smiley)
		{
			$smilies_searcharray[$smiley[0]] = "/" . $smiley[1] . "/i";
			$smilies_replacearray[$smiley[0]] = '<img style="width:30px;height:30px;" src="' . $smilies_dir . '/' . $smiley[2] . '" />';
		}
		return preg_replace($smilies_searcharray, $smilies_replacearray, $txt, 3);
	}
}
