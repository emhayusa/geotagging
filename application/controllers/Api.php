<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
    {
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		//setlocale(LC_ALL,'id_ID');
		setlocale(LC_ALL,'Indonesian');
		$this->load->model('Photo_model', '', TRUE);
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}

	function unggah()
    {

			$long = $this->input->post('longitude');
			$lat = $this->input->post('latitude');
			$namadokumen = date("Ymd_His");
			$now = date("Y-m-d H:i:s");

			$cek = glob('./assets/images/'.$namadokumen.'.*');
			if(count($cek) > 0)
				unlink($cek[0]);

			$config['upload_path'] = './assets/images/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = '3072';
	
			$this->load->library('upload', $config);
		
			if ( ! $this->upload->do_upload("image"))
			{
				echo strip_tags($this->upload->display_errors());
			}
			else
			{
				$hasil = $this->upload->data();
				$fileasli = $hasil['full_path'];
				$filex = $hasil['file_path'].$namadokumen.$hasil['file_ext'];  
				rename(  $fileasli , $filex );
				//insert into DB

				$data = array(
				'nama_file' => $namadokumen.$hasil['file_ext'],
				'latitude' => $lat,
				'longitude' => $long,
				'waktu' => $now,
				'status' => 1  
				);

				$Obj = (object)[];
				//setup response
				//Transfering data to Model
				$this->Photo_model->insert($data);
				if ($this->db->error()['message']) {
					$Obj->message = $this->db->error()['message'];
				} else if (!$this->db->affected_rows()) {
					$Obj->message = "No affected rows";
				} else {
					$Obj->latitude = $lat;
					$Obj->longitude = $long;
					$Obj->filename =  $namadokumen.$hasil['file_ext'];	
					$Obj->message = "Data successfully inserted.";
				}



				echo json_encode($Obj);
			}
    }
}
