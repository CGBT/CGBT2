<?php
class userbar_controller extends base_controller
{
	private $bar_config = array();
	private $bar_type = 0;
	private $bar_user = array();

	public function index_action()
	{
		$username = $this->params['username'];
		if (empty($username))
		{
			$this->showmessage('参数错误');
		}

		$this->bar_type = intval($this->params['bar_type']);
		if (empty($this->bar_type))
		{
			$this->showmessage('参数错误');
		}

		cg::load_module('users_module');
		$users_module = users_module::get_instance();
		$uid = $users_module->users_model->get_uid_by_username($username);
		if (empty($this->uid))
		{
			$this->showmessage('用户不存在');
		}
		$this->bar_user = $users_module->get_by_uid($uid);
		if (empty($this->bar_user))
		{
			$this->showmessage('用户不存在');
		}
		$this->bar_config = $this->get_bar_config();

		if (!isset($this->bar_config[$this->bar_type]))
		{
			$this->showmessage('参数错误');
		}
		$this->bar_config = $this->bar_config[$this->bar_type];
		if ($this->bar_type == '6' || $this->bar_type == '7')
		{
			if (isset($this->bar_user['privileges']['userbar2']) && !$this->bar_user['privileges']['userbar2'])
			{
				$this->showmessage('该用户所在用户组不能使用高级流量条图片');
			}
		}
		else
		{
			if (isset($this->bar_user['privileges']['userbar']) && !$this->bar_user['privileges']['userbar'])
			{
				$this->showmessage('该用户所在用户组不能使用流量条图片');
			}
		}
		$this->process_data();
		$this->create_png();
	}

	private function process_data()
	{
		$this->bar_user['uploaded'] = $this->bar_user['uploaded_text'];
		$this->bar_user['downloaded'] = $this->bar_user['downloaded_text'];

		foreach ($this->bar_config as $item_name => $item)
		{
			if (substr($item_name, 0, 5) == 'item_')
			{
				$item_config = explode(',', $this->bar_config[$item_name]);
				$this->bar_config[$item_name] = array();
				$this->bar_config[$item_name]['x'] = $item_config[0];
				$this->bar_config[$item_name]['y'] = $item_config[1];
				$this->bar_config[$item_name]['font'] = empty($item_config[2]) ? $this->bar_config['font'] : $item_config[2];
				$this->bar_config[$item_name]['font_size'] = empty($item_config[3]) ? $this->bar_config['font_size'] : $item_config[3];
				$this->bar_config[$item_name]['font_color'] = empty($item_config[4]) ? $this->bar_config['font_color'] : $item_config[4];
			}
		}
	}

	private function create_png()
	{
		$cfg = $this->bar_config;
		$image = new Imagick($cfg['bg_pic']);
		$image->setImageFormat('png');
		$draw = new ImagickDraw();
		foreach ($this->bar_config as $item_name => $item)
		{
			if (substr($item_name, 0, 5) == 'item_')
			{
				$user_field = substr($item_name, 5);
				$draw->setFont($cfg[$item_name]['font']);
				$draw->setFontSize($cfg[$item_name]['font_size']);
				$textColor = new ImagickPixel($cfg[$item_name]['font_color']);
				$draw->setFillColor($textColor);
				$image->annotateimage($draw, $cfg[$item_name]['x'], $cfg[$item_name]['y'], 0, $this->bar_user[$user_field]);
			}
		}
		header("Content-Type: image/png");
		echo $image;
	}

	private function get_bar_config()
	{
		$bar_config = array();
		$i = 1;
		$bar_config[$i] = array(
			'name' => 'nange1',
			'font_color' => '#ffffff',
			'font_size' => 14,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/1.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "170,30",
			'item_uploaded' => "280,38,,12",
			'item_downloaded' => "378,38,,12",
			'item_ratio' => "485,38",
			'item_extcredits1' => "50,60"
		);


		$i = 2;
		$bar_config[$i] = array(
			'name' => 'nange2',
			'font_color' => '#ffffff',
			'font_size' => 16,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/2.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "147,26",
			'item_uploaded' => "306,26",
			'item_downloaded' => "452,26",
			'item_ratio' => "600,26",
			'item_extcredits1' => "50,60"
		);

		$i = 3;
		$bar_config[$i] = array(
			'name' => 'awang',
			'font_color' => '#f020e9',
			'font_size' => 16,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/3.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "30,22,,,#f020e9",
			'item_uploaded' => "176,22,,,#39a7e4",
			'item_downloaded' => "302,22,,,#8cc53f",
			'item_ratio' => "430,22,,,#eba90b",
			'item_extcredits1' => "50,60"
		);

		$i = 4;
		$bar_config[$i] = array(
			'name' => '4',
			'font_color' => '#000000',
			'font_size' => 14,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/4.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "120,18",
			'item_uploaded' => "315,18,,,#1d6e1d",
			'item_downloaded' => "460,18,,,#3939d6",
			'item_ratio' => "595,18,,,#a25b0e",
			'item_extcredits1' => "50,60"
		);

		$i = 5;
		$bar_config[$i] = array(
			'name' => '5',
			'font_color' => '#222222',
			'font_size' => 14,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/5.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "83,25",
			'item_uploaded' => "205,25",
			'item_downloaded' => "325,25",
			'item_ratio' => "440,25",
			'item_extcredits1' => "50,60"
		);

		$i = 6;
		$bar_config[$i] = array(
			'name' => '5',
			'font_color' => '#222222',
			'font_size' => 16,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/6.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "110,30",
			'item_uploaded' => "234,30",
			'item_downloaded' => "375,30",
			'item_ratio' => "520,30",
			'item_extcredits1' => "50,60"
		);

		$i = 7;
		$bar_config[$i] = array(
			'name' => 'Test',
			'font_color' => '#ffffff',
			'font_size' => 14,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/7.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "186,57",
			'item_uploaded' => "26,110",
			'item_downloaded' => "28,178",
			'item_ratio' => "45,144"
		);

		$i = 8;
		$bar_config[$i] = array(
			'name' => 'Test',
			'font_color' => '#ffffff',
			'font_size' => 14,
			'font' => 'static/font/arialbd.ttf',
			'bg_pic' => 'static/images/bar/8.png',
			//'item'=> "x,y,font,font_size,font_color',
			'item_username' => "50,56,,20,#959595",
			'item_uploaded' => "47,108,,16,#f3eba9",
			'item_downloaded' => "47,133,,16,#f3eba9",
			'item_ratio' => "47,159,,16,#f3eba9"
		);
		return $bar_config;
	}
}