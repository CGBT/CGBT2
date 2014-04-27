<?php
class category_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('category');
	}

	/**
	 *
	 * @return category_model
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

	public function get_all()
	{
		$rows_category = $this->get_all_category();
		cg::load_model('category_options_model');
		$category_options_model = new category_options_model();
		$rows_category_options = $category_options_model->get_all_category_options();

		$data = array();
		cg::load_class('funcs');
		foreach ($rows_category as $row_category)
		{
			$data[$row_category['name_en']] = $row_category;
			$data[$row_category['name_en']]['properties'] = funcs::explode($row_category['properties']);
			$data[$row_category['name_en']]['options'] = $this->get_options_by_category($rows_category_options, $row_category['name_en']);
		}
		return $data;
	}

	private function get_options_by_category($rows_category_options, $category_name_en)
	{
		$data = array();
		foreach ($rows_category_options as $options)
		{
			if ($options['category'] == $category_name_en)
			{
				$data[] = $options;
			}
		}
		return $data;
	}

	public function get_all_category()
	{
		$cache_key = 'category';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key);
	}

	protected function _get_all_category()
	{
		$sql = "select * from $this->table";
		return $this->db()->get_rows($sql);
	}
}