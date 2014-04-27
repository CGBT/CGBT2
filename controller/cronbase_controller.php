<?php
class cronbase_controller extends base_controller
{

	public function beforeRun($resource, $action, $module_name = '')
	{
		parent::beforeRun($resource, $action, $module_name);
		if (!$this->is_developer())
		{
			exit('error');
		}
		set_time_limit(0);
	}

	public function index($dict_crons)
	{
		$force_run_method = isset($this->get['force_run']) ? $this->get['force_run'] : '';
		if (!empty($force_run_method))
		{
			//$controller = get_called_class();
			$controller = 'cron_controller';
			if (!method_exists($controller, $force_run_method))
			{
				die('method not exists');
			}
			$interval = isset($dict_crons[$force_run_method]) ? $dict_crons[$force_run_method] : '1';
			$this->exec($force_run_method, $interval, true);
			return;
		}
		foreach ($dict_crons as $cron => $interval)
		{
			$this->exec($cron, $interval, false);
		}
	}

	public function exec($method, $interval, $force = false)
	{
		//$controller = get_called_class();
		$controller = 'cron_controller';
		$cache_key = 'cron_' . $controller . '_' . $method;
		$data = $this->cache()->get($cache_key);
		if (empty($data) || $force)
		{
			$start_time = time();
			$result = $this->{$method}($force);
			$this->cache()->set($cache_key, '1', $interval);
			$end_time = time();
			if (empty($result))
			{
				$result = 'ok';
			}
			$this->log($controller, $method, $result, $start_time, $end_time, $interval, 1, $force);
		}
		else
		{
			$start_time = $end_time = time();
			$this->log($controller, $method, '', $start_time, $end_time, $interval, 0, $force);
		}
	}

	private function log($controller, $method, $result, $start_time, $end_time, $interval, $real_exec, $force)
	{
		cg::load_model('logs_cron_model');
		$logs_cron_model = logs_cron_model::get_instance();
		$arr_fields = array(
			'createtime' => $start_time,
			'endtime' => $end_time,
			'controller' => $controller,
			'method' => $method,
			'exec_result' => $result,
			'force' => $force,
			'real_exec' => $real_exec,
			'interval' => $interval
		);
		$logs_cron_model->insert($arr_fields);
	}
}