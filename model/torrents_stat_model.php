<?php
class torrents_stat_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('torrents_stat');
	}

	/**
	 *
	 * @return torrents_stat_model
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

	public function update_stat()
	{
		$sql = "update $this->table a set
				a.seeder = (select count(1) from cgbt_peers b where b.is_seeder=1 and b.tid = a.id),
                a.leecher = (select count(1) from cgbt_peers b where b.is_seeder=0 and b.tid = a.id)";
		$this->db()->query($sql);
	}

	public function get_extcredits1_speed($torrents_ids)
	{
		if (empty($torrents_ids))
		{
			return 0;
		}
		$str_ids = implode(',', $torrents_ids);
		$sql = "select sum(extcredits1) from $this->table where id in ($str_ids)";
		return $this->db()->get_value($sql);
	}

	public function update_extcredits1_speed($setting)
	{
		$cache_key = 'update_extcredits1_speed_stat';
		return $this->get_cache_data('_' . __FUNCTION__, $cache_key, $setting, 1800);
	}

	public function _update_extcredits1_speed($setting)
	{
		$extcredits1_max = $setting['extcredits1_max']; //最大值
		$extcredits1_min = $setting['extcredits1_min']; //最小值
		$extcredits1_size = $setting['extcredits1_size']; //5G
		$extcredits1_seeders = $setting['extcredits1_seeders']; //7个种子数
		$extcredits1_weeks = $setting['extcredits1_weeks']; //8周
		$table_torrents = $this->table('torrents');
		$sql = "update $table_torrents a, $this->table b set b.extcredits1=
		($extcredits1_max*2/pi()*atan((1-pow(10, (a.createtime-unix_timestamp())/($extcredits1_weeks*7)/86400))*(a.size/1024/1024/1024/$extcredits1_size)*(1+sqrt(2)*pow(10,(1-(if(b.seeder>$extcredits1_seeders, 1,b.seeder)))/$extcredits1_seeders))/300))
		where a.id=b.id and b.last_action > unix_timestamp() - 1*86400";
		$this->db()->query($sql);

		$sql = "update $this->table set extcredits1='$extcredits1_min' where extcredits1 > 0 and extcredits1 < '$extcredits1_min'";
		$this->db()->query($sql);
	}
}