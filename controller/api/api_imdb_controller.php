<?php
class api_imdb_controller extends base_controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index_action()
	{
		cg::load_model('imdb_model');
		$imdb_model = imdb_model::get_instance();

		$imdb_id = $this->get['imdb_id'];
		$data = $imdb_model->get_by_id($imdb_id);
		$data = json_decode($data, true);
		$movie_data = array(
			'year' => $data['year'],
			'name' => $data['also_known_as'][0],
			'name_en' => str_replace(' ', '.', $data['title']),
			'country' => $data['country'][0],
			'genres' => $data['genres'],
			'actors' => $data['actors']
		);
		echo json_encode($movie_data);
		die();
	}
}