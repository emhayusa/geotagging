<?php
class Photo_model extends CI_Model {

    var $table   = 'photo';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
	function get_all()
    {
		$this->db->select("nama_file,latitude,longitude,waktu");
		$this->db->from($this->table);
		$this->db->where('status',1);
		$this->db->order_by("id_photo", "asc");
		return $this->db->get();
	}
		
	function get_by_id($id)
    {
		$this->db->from($this->table);
		$this->db->where('id_photo',$id);
		return $this->db->get();
	}
	
	function insert($data)
	{
	    $this->db->insert($this->table, $data);
	}
	
	function update($id, $data){
	    $this->db->where('id_photo', $id);
	    $this->db->update($this->table, $data);
	}
		
	function remove($id){
	    $this->db->where('id_photo', $id);
	    $this->db->delete($this->table);
	}
	
}
?>