<?php

class Spacility_model extends CI_Model {
    
    protected $spacility = 'spacility';
    

    function __construct() {
        parent::__construct();
    }
    public function get_all_specialties()
    {
        $this->db->order_by("id","DESC"); 
        $this->db->where("is_deleted",0);
       $this->db->select("*");
       $this->db->from($this->spacility);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of spacility
    public function add_and_update_spacility($insertdata,$id = null)
    {
        unset($insertdata['spacility_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->spacility,$insertdata);
            return true;
        }else{
            $this->db->insert($this->spacility,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get spacility Info
    public function get_speciality_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->spacility)->get();
        return $query->row();
    }
}
?>