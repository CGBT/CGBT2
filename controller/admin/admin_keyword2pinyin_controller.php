<?php
/**
 * 搜索关键词转拼音
 */
class admin_keyword2pinyin_controller extends admin_base_controller
{

	public function __construct()
	{
		parent::__construct();
		set_time_limit(0);
		ini_set("memory_limit", "256M");
	}

	public function index_action()
	{
		$html = <<<HTML
		<div style="padding:10px;font-size:16px;line-height:25px;">
		<a target='_blank' href='/admin/keyword2pinyin/update'>更新全部</a><br />
	   	<br />
		</div>
HTML;
		$this->data['html'] = $html;
		$this->show('admin/keyword2pinyin.php');
	}

	public function update_action()
	{
		cg::load_model('search_keywords_model');
		$search_keywords_model = search_keywords_model::get_instance();
		$rows = $search_keywords_model->get_all();
		foreach ($rows as $row)
		{
			$keyword = funcs::utf82gbk($row['keyword']);
			$pinyin = $this->get_pinyin($keyword);
			$arr_fields = array(
				'pinyin' => $pinyin
			);
			$search_keywords_model->update($arr_fields, $row['id']);
		}
	}

	private function get_pinyin($string)
	{
		cg::load_class('pinyin_table');
		$pinyin_table = pinyin_table::get_pinyin_table();

		$flow = array();
		for($i = 0; $i < strlen($string); $i++)
		{
			if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe)
			{
				$h = ord($string[$i]);
				if (isset($string[$i + 1]))
				{
					$i++;
					$l = ord($string[$i]);
					if (isset($pinyin_table[$h][$l]))
					{
						array_push($flow, $pinyin_table[$h][$l]);
					}
					else
					{
						array_push($flow, $h);
						array_push($flow, $l);
					}
				}
				else
				{
					array_push($flow, ord($string[$i]));
				}
			}
			else
			{
				array_push($flow, ord($string[$i]));
			}
		}
		$s = "";
		foreach ($flow as $v)
		{
			$s .= $v[0];
		}
		return $s;
	}
}