<?php
class discuzx_style_model extends base_model
{

	public function __construct()
	{
		$this->db_config_name = 'newcgbtdiscuzx';
		$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'];
	}

	public function get_all()
	{
		$styleid = $this->get_styleid();

	}

	public function get_styleid()
	{
		$cache_key = 'discuzx_styleid';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, '', 1800);
	}

	protected function _get_styleid()
	{
		$sql = "select svalue from {$this->table_prefix}common_setting where skey = 'styleid' limit 1";
		$styleid = $this->db()->get_value($sql);
		if (intval($styleid) <= 0)
		{
			return 1;
		}
		return intval($styleid);
	}

	public function get_extstyle($styleid)
	{
		$cache_key = 'discuzx_extstyle';
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, $styleid, 1800);
	}


	protected function _get_extstyle($styleid)
	{
		$sql = "select extstyle from {$this->table_prefix}common_style where styleid = '$styleid' limit 1";
		$extstyle = $this->db()->get_value($sql);
		list($extstyle, $default_style) = explode('|', $extstyle);
		$data['extstyle'] = explode("\t", $extstyle);
		$data['default_style'] = $default_style;
		$data['verhash'] = mt_rand(100, 999);
		return $data;
	}

}
