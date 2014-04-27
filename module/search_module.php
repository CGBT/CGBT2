<?php
class search_module extends base_module
{
	private $current_category;
	private $torrents_index_model;

	//url参数
	public $params;
	public $category;
	public $search_params; //url params
	private $dict_search_params;

	//页面内显示的搜索选项
	private $search_item_year;
	private $search_item_custom;
	private $search_item_checkbox;
	private $search_item_orderby;
	private $search_items;
	private $hot_keywords;
	private $ids_range = '';
	private $keyword_param = '';
	private $keyword = '';
	private $orderby = '';
	private $page_params = '';
	private $page = 1;
	private $page_url = '';
	private $sql_where = array();
	private $pagesize = 50;

	//搜索模块，比如 api/search, rss, search
	public $controller = 'search';

	public function __construct()
	{
		parent::__construct();

		$this->get_category();
		cg::load_model('torrents_index_model');
		$this->torrents_index_model = torrents_index_model::get_instance();
		$today_upload_count = $this->torrents_index_model->today_upload_count();
		$all_upload_count = $this->torrents_index_model->all_upload_count();
		foreach ($this->all_category as $key => $category)
		{
			if ($category['app'] != 'torrents')
			{
				unset($this->all_category[$key]);
			}
			$this->data['today_upload_count'][$key] = isset($today_upload_count[$category['id']]) ? $today_upload_count[$category['id']] : 0;
			$this->data['all_upload_count'][$key] = isset($all_upload_count[$category['id']]) ? $all_upload_count[$category['id']] : 0;
		}
		$this->data['today_upload_count']['all'] = array_sum($this->data['today_upload_count']);
		$this->data['all_upload_count']['all'] = array_sum($this->data['all_upload_count']);
		$this->data['all_category'] = $this->all_category;
	}

	/**
	 *
	 * @return search_module
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

	public function get_params()
	{
		//获取当前查询的分类和搜索条件url参数
		//先过滤checkbox，再过滤orderby, keyword, page, 剩下的检查是哪个分类的。检查页面是否需要跳转。
		$this->get_search_params();

		//获取keyword和orderby,page
		$this->get_keyword_orderby_page();

		//根据当前分类，校验所有参数有效性，去掉无用的url参数，获取sql
		$this->check_valid_params();

		$this->data['dict_search_params'] = $this->dict_search_params;
		foreach ($this->dict_search_params as $key => $value)
		{
			$this->data['dict_search_params'][$key] = htmlspecialchars($value, ENT_QUOTES);
		}
		$this->data['search_params'] = htmlspecialchars($this->search_params, ENT_QUOTES);
	}

	public function index_action()
	{

		//获取生成页面需要搜索项目，对应的链接，where条件，调用的model::method，生成页面用
		//参数转化为sql也用到
		$this->get_all_search_items();

		//获取所有的搜索参数，检查参数合法性
		$this->get_params();

		//检查url是否需要跳转，获取当前页面分页需要传递的参数
		$this->redirect_url();

		//获取"当前分类"的每个搜索条件的链接，其他分类的在模板里面生成
		$this->get_search_links();

		//正在做种，正在下载等，先获取id范围，再从索引表in(ids)，然后筛选排序处理
		$this->get_ids_range();

		//把url参数，搜索条件等转化为sql
		$this->get_search_sql();

		//根据sql获取种子id
		$ids2 = $this->get_torrents_ids();
		if ($this->page == 1 && $this->controller != 'audit')
		{
			$ids1 = $this->get_top_torrents_ids($ids2);
			$ids_mod_top = $this->get_price_mod_top_torrents_ids();
		}
		else
		{
			$ids1 = array();
			$ids_mod_top = array();
		}

		if (count($ids2) > 0)
		{
			$this->logs_search_keyword();
		}
		$all_ids = array_unique(array_merge($ids1, $ids2));
		$all_ids = array_merge($ids_mod_top, $all_ids);
		$cache_key = md5(json_encode($all_ids));
		$this->data['torrents'] = cg::cache()->get($cache_key);
		if (empty($this->data['torrents']))
		{
			//获取种子数据
			$this->data['torrents'] = $this->get_torrents($all_ids);
			if (!empty($ids_mod_top))
			{
				foreach ($this->data['torrents'] as $key => $torrent)
				{
					if (in_array($torrent['id'], $ids_mod_top))
					{
						$this->data['torrents'][$key]['is_mod_top'] = true;
					}
				}
			}
			cg::cache()->set($cache_key, $this->data['torrents'], 60);
		}

		//分页
		$this->get_pager();

		//获取orderby sql
		//print_r($this->get_all_sql());
		//print_r($this->get_execute_time());
		$this->data['search_items'] = $this->search_items;
		$this->data['current_category'] = $this->current_category;
	}

	private function logs_search_keyword()
	{
		$length = mb_strlen($this->keyword, 'UTF-8');
		if ($length >= 2 && $length <= 15)
		{
			cg::load_model('logs_search_model');
			$logs_search_model = logs_search_model::get_instance();
			$category_id = isset($this->all_category[$this->current_category]) ? $this->all_category[$this->current_category]['id'] : 0;
			$arr_fields = array(
				'uid' => $this->uid,
				'keyword' => $this->keyword,
				'category' => $category_id,
				'createtime' => time()
			);
			$logs_search_model->insert($arr_fields);
		}
	}

	public function get_template_data()
	{
		return $this->data;
	}

	private function get_ids_range()
	{
		$ids = array();
		$ids_keywords = array();
		$use_ids = false;
		$use_keywords = false;

		if ($this->current_category == 'all')
		{
			$params = array(
				'fav',
				'seeding',
				'leeching',
				'complete',
				'view'
			);
			$current_param = '';
			foreach ($params as $p)
			{
				if (in_array($p, $this->dict_search_params))
				{
					$current_param = $p;
					break;
				}
			}
			$table = '';
			switch ($current_param)
			{
				case 'fav':
					$table = 'favorite';
					$where = " uid = '$this->uid'";
					break;
				case 'leeching':
					$table = 'peers';
					$where = " uid = '$this->uid' and is_seeder=0 ";
					break;
				case 'seeding':
					$table = 'peers';
					$where = " uid = '$this->uid' and is_seeder=1 ";
					break;
				case 'complete':
					$table = 'logs_completed';
					$where = " uid = '$this->uid' ";
					break;
				case 'view':
					$table = 'logs_browse';
					$where = " uid = '$this->uid' ";
					break;
				default:
					$table = '';
			}
			if ($table != '')
			{
				$model = cg::load_model($table . '_model', true);
				$ids = $model->get_ids_range($where);
				$use_ids = true;
			}
		}
		$this->data['keyword'] = '';
		if (!empty($this->keyword))
		{
			$this->keyword = htmlspecialchars($this->keyword, ENT_QUOTES);
			$sql = array();
			$this->keyword = str_replace('.', " ", $this->keyword);
			$this->keyword = trim(preg_replace('/ +/', ' ', $this->keyword));
			$arr_keyword = funcs::explode($this->keyword, " ");
			foreach ($arr_keyword as $k)
			{
				if (empty($k))
				{
					continue;
				}
				$k = funcs::escape_mysql_wildcards($k);
				$k = addslashes($k);
				$sql[] = " keywords like '%$k%' ";
			}
			$this->data['keyword'] = $this->keyword;
			if (!empty($sql))
			{
				$where = implode(' and ', $sql);
				cg::load_model('torrents_keywords_model');
				$torrents_keywords_model = torrents_keywords_model::get_instance();
				$ids_keywords = $torrents_keywords_model->get_ids_range($where);
				$use_keywords = true;
			}
		}

		$final_ids = array();
		if ($use_ids && $use_keywords)
		{
			$final_ids = array_intersect($ids, $ids_keywords);
		}
		else
		{
			if ($use_ids)
			{
				$final_ids = $ids;
			}
			elseif ($use_keywords)
			{
				$final_ids = $ids_keywords;
			}
		}

		if ($use_ids || $use_keywords)
		{
			$this->ids_range = implode($final_ids, ',');
			if (empty($this->ids_range))
			{
				$this->ids_range = '-1'; //flag
			}
		}
		else
		{
			//默认为空页面
			$this->ids_range = '0';
		}
	}

	private function get_torrents($ids)
	{
		cg::load_module('torrents_module');
		$torrents_module = torrents_module::get_instance();
		return $torrents_module->get_torrents($ids);
	}

	private function get_price_mod_top_torrents_ids()
	{
		cg::load_model('torrents_price_mod_model');
		$torrents_price_mod_model = torrents_price_mod_model::get_instance();
		$rows = $torrents_price_mod_model->get_top_3();
		$tids = array();
		foreach ($rows as $row)
		{
			$tids[] = $row['tid'];
		}
		return $tids;
	}

	private function get_top_torrents_ids($ids)
	{
		if (empty($ids))
		{
			return array();
		}
		//所有页面都取最多10个置顶 @todo 每个分类置顶数量后台定义
		$where = implode(' and ', $this->sql_where);
		if (stripos($where, 'istop') === false)
		{
			//$where .= empty($where) ? '(istop = 1 or isrecommend = 1)' : ' and (istop = 1 or isrecommend = 1)';
			$where .= empty($where) ? 'istop = 1' : ' and istop = 1';
		}
		if ($this->current_category == 'all')
		{
			$limit = 10;
		}
		else
		{
			$limit = 100;
		}
		return $this->torrents_index_model->get_ids_by_sql($where, 'id desc', 0, $limit);
	}

	private function get_torrents_ids()
	{
		$where = implode(' and ', $this->sql_where);
		if (empty($this->orderby))
		{
			$orderby = 'id desc';
		}
		else
		{
			$orderby = $this->orderby;
		}
		$limit = $this->pagesize;
		$start = ($this->page - 1) * $limit;

		$this->data['torrents_count'] = $this->torrents_index_model->get_count($where);
		if ($this->data['torrents_count'] == 0)
		{
			return array();
		}
		else
		{
			return $this->torrents_index_model->get_ids_by_sql($where, $orderby, $start, $limit);
		}
	}

	private function get_search_sql()
	{
		$sql = array();
		if ($this->current_category != 'all')
		{
			$sql[] = "category = '" . $this->all_category[$this->current_category]['id'] . "'";
		}

		foreach ($this->dict_search_params as $param)
		{
			if (in_array($param, $this->search_item_orderby))
			{
				$this->orderby = array_search($param, $this->search_item_orderby);
				if (in_array('asc', $this->dict_search_params))
				{
					$this->orderby .= " asc";
				}
				else
				{
					$this->orderby .= " desc";
				}
				break;
			}
		}

		foreach ($this->dict_search_params as $param)
		{
			if (in_array($param, array_keys($this->search_item_checkbox)))
			{
				switch ($param)
				{
					case 'my':
						$sql[] = " uid =  '$this->uid'";
						break;
					case 'subs':
						$sql[] = ' subs = 1 ';
						break;
					case 'collection':
						$sql[] = ' iscollection = 1 ';
						break;
					case 'dead':
						$sql[] = ' seeder = 0 ';
						break;
					case 'free':
						$sql[] = ' isfree = 1 ';
						break;
					case '30p':
						$sql[] = ' is30p = 1 ';
						break;
					case 'half':
						$sql[] = ' ishalf = 1 ';
						break;
					case 'top':
						$sql[] = ' istop= 1 ';
						break;
					case 'recmd':
						$sql[] = ' isrecommend = 1 ';
						break;
					case 'hd':
						$sql[] = ' ishd = 1 ';
						break;
					case 'sd':
						$sql[] = ' ishd = 0 ';
						break;
				}
			}
			elseif (in_array($param, $this->search_item_custom['all']['search_items']['my']['params']))
			{
				/*
				if (!empty($this->ids_range))
				{
					$sql[] = " id in ($this->ids_range)";
				}
				*/
			}
			elseif (in_array($param, $this->search_item_custom['all']['search_items']['hot']['params']))
			{

				switch ($param)
				{
					case 'free':
						$sql[] = ' isfree = 1 ';
						break;
					case 'top':
						$sql[] = ' istop= 1 ';
						break;
					case 'recmd':
						$sql[] = ' isrecommend = 1 ';
						break;
					case '24h':
						$dt = time() - 86400;
						$sql[] = " createtime > $dt ";
						$this->orderby = ' seeder desc ';
						break;
					case '3days':
						$dt = time() - 86400 * 3;
						$sql[] = " createtime > $dt ";
						$this->orderby = ' seeder desc ';
						break;
					case 'week':
						$dt = time() - 86400 * 7;
						$sql[] = " createtime > $dt ";
						$this->orderby = ' seeder desc ';
						break;
					case 'month':
						$dt = time() - 86400 * 30;
						$sql[] = " createtime > $dt ";
						$this->orderby = ' seeder desc ';
						break;
				}
			}
			else
			{
				//page,asc,,,
				//@todo 多个字符的情况，暂时没有 这里sql为空的问题最好在url判断里解决
				$first_char = substr($param, 0, 1);
				if ($first_char != 'p' && $first_char != 'o' && $first_char != 'x' && $param != 'asc')
				{
					$other_char = substr($param, '1');
					if (isset($this->search_items[$this->current_category]['search_items'][$first_char]))
					{
						$tmp_sql = $this->search_items[$this->current_category]['search_items'][$first_char]['options'][$other_char]['sql'];
						if (!empty($tmp_sql))
						{
							$sql[] = $tmp_sql;
						}
					}
				}
			}
		}
		if (!in_array('all', $this->dict_search_params) && !in_array('dead', $this->dict_search_params) && $this->controller != 'audit')
		{
			//$sql[] = 'seeder > 0';
		}
		/*
		$this->data['keyword'] = '';
		if (!empty($this->keyword))
		{
			$this->keyword = str_replace('.', " ", $this->keyword);
			$arr_keyword = funcs::explode($this->keyword, " ");
			foreach ($arr_keyword as $k)
			{
				$k = funcs::escape_mysql_wildcards($k);
				$sql[] = " keyword like '%$k%' ";
			}
			$this->data['keyword'] = htmlspecialchars($this->keyword, ENT_QUOTES);
		}
		*/
		if ($this->ids_range != '0') //使用关键词查询了
		{
			if ($this->ids_range == '-1') //没有结果
			{
				$sql[] = "id in (0)";
			}
			else //有结果
			{
				$sql[] = "id in ($this->ids_range)";
			}
		}
		if ($this->controller == 'audit')
		{
			$sql[] = 'status = 0';
		}
		else
		{
			$sql[] = 'status = 1';
		}
		$this->sql_where = $sql;
	}

	private function redirect_url()
	{
		$oldurl = urldecode($_SERVER['REQUEST_URI']);

		if (empty($this->params) && empty($this->get))
		{
			if (substr($_SERVER['REQUEST_URI'], -1) != '/')
			{
				$this->redirect("/$this->controller/");
			}
		}

		if ($this->current_category == 'all')
		{
			$newurl = "/$this->controller/" . $this->search_params;
		}
		else
		{
			$newurl = "/$this->controller/" . $this->current_category . "/" . $this->search_params;
		}

		if ($this->controller == 'rss')
		{
			$newurl .= '/' . $this->params['passkey'];
		}
		/* 前面没有unset
		if (!empty($this->orderby))
		{
			$newurl .= empty($this->search_params) ? '' : '-';
			$newurl .= $this->orderby;
		}
		if (!empty($this->keyword))
		{
			$newurl .= empty($this->search_params) ? '' : '-';
			$newurl .= $this->keyword;
		}
		if (!empty($this->page_params))
		{
			$newurl .= empty($this->search_params) ? '' : '-';
			$newurl .= $this->page_params;
		}
		*/
		$newurl .= '/';
		$newurl = str_replace("//", "/", $newurl);
		$newurl = str_replace("//", "/", $newurl);

		if ($oldurl != $newurl)
		{
			$this->redirect($newurl);
		}
		$this->data['url'] = htmlspecialchars($newurl, ENT_QUOTES);

		//@todo 拆分出去
		if (preg_match('/([\/|-])(p\d+)([-|\/])/i', $newurl))
		{
			$this->page_url = preg_replace('/([\/|-])(p\d+)([-|\/])/i', '$1p$page$3', $newurl);
		}
		else
		{
			if (!empty($this->dict_search_params))
			{
				$this->page_url = preg_replace('/(.*)\//i', '$1-p$page/', $newurl);
			}
			else
			{
				$this->page_url = preg_replace('/(.*)\//i', '$1/p$page/', $newurl);
			}
		}
	}

	private function check_valid_params()
	{
		if ($this->search_params == '')
		{
			return;
		}
		//检查所有可能的url参数
		$all_valid_params = array();
		$all_valid_params = array_merge($all_valid_params, array_keys($this->search_item_checkbox));
		$all_valid_params = array_merge($all_valid_params, array_values($this->search_item_orderby));
		$all_valid_params[] = 'asc';

		if ($this->current_category == 'all')
		{
			//分类为all时，可用的参数包括：orderby + checkbox + all分类 + asc + xxxx(keyword) + p123(page)
			foreach ($this->search_item_custom['all']['search_items'] as $k => $v)
			{
				$all_valid_params = array_merge($all_valid_params, array_keys($v['options']));
			}
		}
		else
		{
			// 分类不为all，比如分类为movie，可用的参数包括：
			// orderby + checkbox + 分类参数 + asc + xxxx(keyword)
			foreach ($this->search_items[$this->current_category]['search_items'] as $variable => $options)
			{
				foreach ($options['options'] as $k => $option)
				{
					$all_valid_params[] = $variable . $k;
				}
			}
		}

		foreach ($this->dict_search_params as $key => $var)
		{
			//@todo $var为空，则$var[0]会报错
			if (empty($var))
			{
				continue;
			}
			if ($var[0] != 'x' && $var[0] != 'p') //keyword , page
			{
				if (array_search($var, $all_valid_params) === false)
				{
					unset($this->dict_search_params[$key]);
				}
			}
		}

		$this->search_params = implode('-', $this->dict_search_params);
	}

	private function get_keyword_orderby_page()
	{
		foreach ($this->dict_search_params as $key => $var)
		{
			$char = substr($var, 0, 1);
			if ($char == "x")
			{
				$s = ',|<|>|\"|\'|&|;|(|)|\\';
				$this->keyword_param = trim(str_replace(explode('|', $s), '', $var));
				$this->keyword = substr($var, 1);
				$this->dict_search_params[$key] = $this->keyword_param;
				//unset($this->dict_search_params[$key]);
			}
			elseif ($char == 'o')
			{
				$this->orderby = $var;
				//unset($this->dict_search_params[$key]);
			}
			elseif ($char == "p")
			{
				$this->page_params = $var;
				//unset($this->dict_search_params[$key]);
				$this->page = intval(substr($var, 1));
			}
		}
		if (empty($this->page))
		{
			$this->page = '1';
		}
	}

	private function get_pager()
	{
		cg::load_core('cg_pager');
		$total = $this->data['torrents_count'];
		$pager = new cg_pager($this->page_url, $total, $this->pagesize, 10);
		$pager->paginate($this->page);
		$this->data['pager'] = &$pager;
	}

	private function get_search_params()
	{
		/*
		 *
		 * /search/movie/
		 * /search/y1-g2/ => /search/movie/y1-g2/
		 * /search/movie/y1-g2/
		 * /search/seeding/
		 * /saarch/seeding-o1-sabc/
		 *
	 	 * /search/my-fav/
	 	 * /search/hot-free/
	 	 * /search/hot-top/
		 * /search/my-seeding/
		 *
		 * /search/free/
		 * /search/hd/
		 * /search/sd/
		 * /search/active/
		 * /search/dead/
		 * /search/subs/
		 * /search/2x/
		 * /search/half/
		 * /search/30p/
		 * /search/movie/y1-g2-free-hd-sd-active-2x/
		 * /search/free-hd-sd-active-2x-o3-sxxx/
		 *
		 * /search/o1
		 * /search/o10
		 *
		 * movie,y,1
		 * all,hot,free
		 * 分别对应category,variable,params
		 *
		 */
		if ($this->controller == 'search' || $this->controller == 'audit')
		{
			if (count($this->params) == 1)
			{
				//search/movie/
				if (in_array($this->params['search_params'], array_keys($this->all_category)))
				{
					$this->current_category = $this->params['search_params'];
					$this->search_params = '';
				}
				else
				{
					//search/a1-b2
					//search/free
					//search/sxxxx
					$this->current_category = 'all';
					$this->search_params = $this->params['search_params'];
				}
			}
			elseif (count($this->params) == 2)
			{
				//movie/y1
				if (in_array($this->params['category'], array_keys($this->all_category)))
				{
					$this->current_category = $this->params['category'];
				}
				else
				{
					//xxx/y1 => //all/y1
					$this->current_category = 'all';
				}
				$this->search_params = $this->params['search_params'];
			}
			else
			{
				$this->current_category = 'all';
				$this->search_params = '';
			}
		}
		elseif ($this->controller == 'rss')
		{
			if (count($this->params) == 1)
			{
				//rss/passkey
				$this->current_category = 'all';
				$this->search_params = '';
			}
			elseif (count($this->params) == 2)
			{
				//rss/my/passkey
				//rss/movie/passkey
				if (in_array($this->params['search_params'], array_keys($this->all_category)))
				{
					$this->current_category = $this->params['search_params'];
					$this->search_params = '';
				}
				else
				{
					$this->current_category = 'all';
					$this->search_params = $this->params['search_params'];
				}
			}
			else
			{
				//rss/movie/y1/passkey
				if (in_array($this->params['category'], array_keys($this->all_category)))
				{
					$this->current_category = $this->params['category'];
				}
				else
				{
					$this->current_category = 'all';
				}
				$this->search_params = $this->params['search_params'];
			}
			$this->passkey = $this->params['passkey'];
		}
		elseif ($this->controller == 'audit')
		{
			if (count($this->params) == 1)
			{
				//search/movie/
				if (in_array($this->params['search_params'], array_keys($this->all_category)))
				{
					$this->current_category = $this->params['search_params'];
					$this->search_params = '';
				}
				else
				{
					//search/a1-b2
					//search/free
					//search/sxxxx
					$this->current_category = 'all';
					$this->search_params = $this->params['search_params'];
				}
			}
			else
			{
				$this->current_category = 'all';
				$this->search_params = '';
			}
		}
		$this->search_params = urldecode($this->search_params);
		$this->dict_search_params = funcs::explode($this->search_params, '-');
		return;
	}

	private function get_search_links()
	{
		foreach ($this->search_items[$this->current_category]['search_items'] as $variable => $search_items)
		{
			//判断当前分类的参数比如y1,d0是否在url参数内
			//如果是all分类可能会有多个参数在url里面
			$in_url_params = array_intersect($this->dict_search_params, $search_items['params']);

			foreach ($search_items['options'] as $key => $option)
			{
				if ($this->current_category == 'all')
				{
					$link_str = $key;
				}
				else
				{
					$link_str = $variable . $key;
				}

				$tmp = $this->dict_search_params;
				if (empty($in_url_params))
				{
					if ($key == '0') //不限
					{
					}
					else
					{
						$tmp[] = $link_str;
					}
				}
				else
				{
					if ($key == '0') //不限
					{
						$tmp = array_diff($tmp, $in_url_params);
					}
					else
					{
						$tmp = array_diff($tmp, $in_url_params);
						$tmp[] = $link_str;
					}
				}
				$link = implode('-', $tmp);
				$this->search_items[$this->current_category]['search_items'][$variable]['options'][$key]['link'] = $link;
				if ($key == '0')
				{
					$checked = empty($in_url_params);
				}
				else
				{
					$checked = in_array($link_str, $this->dict_search_params);
				}
				$this->search_items[$this->current_category]['search_items'][$variable]['options'][$key]['checked'] = $checked;
			}
		}
	}

	private function get_search_item_year()
	{
		$search = array();
		$start_year = date("Y", time());
		$end_year = 2007;
		for($i = $start_year; $i >= $end_year; $i--)
		{
			$search[strval($i)] = "year='$i'";
		}
		$search["2000-2006"] = "year >= '2000' and year<='2006'";
		$search["1990s"] = "year >= '1990' and year < '2000'";
		$search["1980s"] = "year >= '1980' and year < '1990'";
		$search["1970s"] = "year >= '1970' and year < '1980'";
		$search["1970以前"] = "year < '1970'";
		$search["其他"] = "year = ''";
		$this->search_item_year = $search;
	}

	private function get_search_item_custom()
	{
		$search['all'] = array(
			'name' => '全部',
			'search_items' => array(
				'my' => array(
					'name' => '我的种子',
					'options' => array(
						'0' => array(
							'title' => '不限'
						),
						'fav' => array(
							'title' => '我收藏的种子'
						),
						'my' => array(
							'title' => '我发布的种子'
						),
						'seeding' => array(
							'title' => '我正在做种的种子'
						),
						'leeching' => array(
							'title' => '我正在下载的种子'
						),
						'complete' => array(
							'title' => '我下载完成的种子'
						),
						'view' => array(
							'title' => '我最近查看的种子'
						)
					)
				),
				'hot' => array(
					'name' => '热门种子',
					'options' => array(
						'0' => array(
							'title' => '不限'
						),
						'free' => array(
							'title' => '免费的种子'
						),
						'top' => array(
							'title' => '置顶的种子'
						),
						'recmd' => array(
							'title' => '推荐的种子'
						),
						'24h' => array(
							'title' => '24小时内热门'
						),
						'3days' => array(
							'title' => '3天内热门'
						),
						'week' => array(
							'title' => '一周内热门'
						),
						'month' => array(
							'title' => '本月内热门'
						)
					)
				)
			)
		);
		foreach ($search['all']['search_items'] as $variable => $search_items)
		{
			$search['all']['search_items'][$variable]['params'] = array();
			foreach ($search_items['options'] as $key => $option)
			{
				if ($key != '0')
				{
					$search['all']['search_items'][$variable]['params'][] = $key;
				}
				$search['all']['search_items'][$variable]['options'][$key]['link'] = $key;
				$search['all']['search_items'][$variable]['options'][$key]['checked'] = false;
			}
		}

		$search['all']['hot_keywords'] = isset($this->hot_keywords[0]) ? $this->hot_keywords[0] : '';
		$this->search_item_custom = $search;
	}

	private function get_search_item_checkbox()
	{
		$dict_checkbox = array(
			'my' => '我的种子',
			'collection' => '合集',
			'active' => '活动种子',
			'all' => '全部种子',
			'dead' => '死种',
			'subs' => '有字幕',
			'free' => '免费',
			'top' => '置顶',
			'recmd' => '推荐',
			'hd' => '高清',
			'sd' => '标清',
			'2x' => '2x',
			'30p' => '30%',
			'half' => '50%'
		);

		$this->search_item_checkbox = $dict_checkbox;
		$this->data['search_item_checkbox'] = $dict_checkbox;
	}

	private function get_search_item_orderby()
	{
		$dict_orderby = array(
			'id' => 'o1',
			'seeder' => 'o2',
			'leecher' => 'o3',
			'view' => 'o4',
			'complete' => 'o5',
			'size' => 'o6',
			'files' => 'o7',
			'category' => 'o8'
		);

		$dict_orderby_text = array(
			'o1' => '时间',
			'o2' => '种子数',
			'o3' => '下载数',
			'o4' => '查看数',
			'o5' => '完成数',
			'o6' => '大小',
			'o7' => '文件数',
			'o8' => '种子分类'
		);
		$this->search_item_orderby = $dict_orderby;
		$this->data['search_item_orderby'] = $dict_orderby;
		$this->data['search_item_orderby_text'] = $dict_orderby_text;
	}

	private function get_hot_keywords()
	{
		cg::load_model('logs_search_model');
		$logs_search_model = logs_search_model::get_instance();
		$this->hot_keywords = $logs_search_model->get_hot_keywords();

		foreach ($this->all_category as $category)
		{
			$dict_keywords_count[$category['id']] = $category['hot_keywords_count'];
			if (!isset($this->hot_keywords[$category['id']]))
			{
				$this->hot_keywords[$category['id']] = array();
			}
		}
		$dict_keywords_count['0'] = 38;
		foreach ($this->hot_keywords as $key => $keywords)
		{
			$this->hot_keywords[$key] = implode(',', array_slice($keywords, 0, $dict_keywords_count[$key]));
		}
	}

	private function get_search_items()
	{
		$search = array();
		$search['all'] = $this->search_item_custom['all'];

		foreach ($this->all_category as $category => $category_details)
		{
			$search[$category]['name'] = $category_details['name']; //电影
			$search[$category]['hot_keywords'] = $this->hot_keywords[$category_details['id']];
			$search[$category]['search_items'] = array();

			foreach ($category_details['options'] as $search_items) //年份,地区,类型,格式
			{
				if ($search_items['status'] == '0')
				{
					continue;
				}
				if ($search_items['insearch_item'] == '1')
				{
					$items = array();
					$items['name'] = $search_items['title'];
					$items['params'] = array();
					//$items['params'][0] = $search_items['variable_search'] . '0';
					$items['options'] = array();
					$items['options'][0]['title'] = '不限';
					$items['options'][0]['sql'] = '';
					$items['options'][0]['link'] = '';
					$items['options'][0]['checked'] = true;

					if ($search_items['bind_field'] == 'year')
					{
						$dict_options = $this->search_item_year;
						$i = 0;
						foreach ($dict_options as $key => $option)
						{
							$items['options'][$i + 1]['title'] = $key;
							$items['options'][$i + 1]['sql'] = $option;
							$items['options'][$i + 1]['link'] = $search_items['variable_search'] . ($i + 1);
							$items['params'][$i + 1] = $search_items['variable_search'] . ($i + 1);
							$i++;
						}
					}
					else
					{
						$dict_options = funcs::explode($search_items['options']);
						foreach ($dict_options as $i => $option)
						{
							$items['options'][$i + 1]['title'] = $option;
							if ($search_items['type'] == 'selects') //多选
							{
								$items['options'][$i + 1]['sql'] = $search_items['bind_field'] . " & " . pow(2, $i);
							}
							else
							{
								$items['options'][$i + 1]['sql'] = $search_items['bind_field'] . " = '" . ($i + 1) . "'";
							}
							$items['options'][$i + 1]['link'] = $search_items['variable_search'] . ($i + 1);
							$items['params'][$i + 1] = $search_items['variable_search'] . ($i + 1);
						}
					}
					$search[$category]['search_items'][$search_items['variable_search']] = $items;
				}
			}
		}
		$this->search_items = $search;
	}

	public function get_all_search_items()
	{
		//自动更新热门关键词
		$this->get_hot_keywords();

		//获取年份的搜索条件
		$this->get_search_item_year();

		//获取"全部"分类的自定义搜索条件
		$this->get_search_item_custom();

		//获取checkbox的搜索条件
		$this->get_search_item_checkbox();

		//排序的url参数,o1-o10
		$this->get_search_item_orderby();

		//获取电影，剧集等分类的所有搜索条件
		$this->get_search_items();
	}

	private function redirect($url)
	{
		header('Location: ' . $url);
	}
}
