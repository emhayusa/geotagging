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
			$long = $this->input->get('longitude');
			$lat = $this->input->get('latitude');
			$namadokumen = date("Ymd_His");
			$now = date("Y-m-d H:i:s");

			$cek = glob('./assets/images/'.$namadokumen.'.*');
			if(count($cek) > 0)
				unlink($cek[0]);

			$config['upload_path'] = './assets/images/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = '3072';
			$this->load->library('upload', $config);
			$Obj = (object)[];

			if ($lat == null || $long == null){
				$Obj->message = "Longitude or Latitude is Null ";
				echo json_encode($Obj);
			}else{
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
					//echo json_encode($data);
					//error_log(print_r($data, TRUE));
					//error_log(print_r($_GET, TRUE));
					//error_log(print_r($_POST, TRUE));
					
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
	
	public function view()
	{
		$Obj = (object)[];
		$Obj->num_rows = $this->Photo_model->get_all()->num_rows();
		$datas = $this->Photo_model->get_all()->result();
		$Obj->datas = $datas;
		/*
		$Obj-> data = [
				 array('id' => 0, 'name' => 'Badan Informasi Geospasial', 'tipe' => 0, 'server' => 0, 'url' => 'http://portal.ina-sdi.or.id/arcgis/rest/services'),
				 array('id' => 1, 'name' => 'Badan Meteorologi, Klimatologi, dan Geofisika', 'tipe' => 0, 'server' => 0, 'url' => 'http://gis.bmkg.go.id/arcgis/rest/services'),
				 array('id' => 2, 'name' => 'Badan Nasional Penanggulangan Bencana', 'tipe' => 0, 'server' => 0, 'url' => 'http://service1.inarisk.bnpb.go.id:6080/arcgis/rest/services'),
				 array('id' => 3, 'name' => 'Kementerian Koordinator Bidang Perekonomian', 'tipe' => 0, 'server' => 1, 'url' => 'http://geoportal.satupeta.go.id:8080/geoserver/wms'),
				 array('id' => 4, 'name' => 'Kementerian Lingkungan Hidup dan Kehutanan (KLHK)', 'tipe' => 0, 'server' => 0, 'url' => 'http://geoportal.menlhk.go.id/arcgis/rest/services'),
				 array('id' => 5, 'name' => 'Provinsi Aceh', 'tipe' => 1, 'server' => 0, 'url' => 'http://gisportal.acehprov.go.id:6080/arcgis/rest/services'),
				 array('id' => 6, 'name' => 'Provinsi Sumatera Barat', 'tipe' => 1, 'server' => 1, 'url' => 'http://sumbarprov.ina-sdi.or.id:8080/geoserver/wms'),
				 array('id' => 7, 'name' => 'Kementrian Pertanian', 'tipe' => 0, 'server' => 0, 'url' => 'http://sig.pertanian.go.id/ArcGIS/rest/services')
				 
			   ];
		*/
		echo json_encode($Obj);
	}
	

}
