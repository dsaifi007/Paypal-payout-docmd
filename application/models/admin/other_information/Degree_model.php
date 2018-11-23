<?php

class Degree_model extends CI_Model {
    
    protected $degree_table = 'doctor_degree';

    function __construct() {
        parent::__construct();
    }
    public function get_all_degree()
    {
       $this->db->order_by("id","DESC"); 
       $this->db->where("is_deleted",0);
       $this->db->select("*");
       $this->db->from($this->degree_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_degree($insertdata,$id = null)
    {
        unset($insertdata['degree_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->degree_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->degree_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_degree_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->degree_table)->get();
        return $query->row();
    }
}
?>