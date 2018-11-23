<?php

class Diagnosis_model extends CI_Model {
    
    protected $diagnosis_table = 'admin_diagnosis';


    function __construct() {
        parent::__construct();
    }
    public function get_all_diagnosis_list()
    {
       $this->db->order_by("id","DESC");  
       $this->db->where("is_deleted",0);
       $this->db->select("*");
       $this->db->from($this->diagnosis_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_diagnosis($insertdata,$id = null)
    {
        unset($insertdata['diagnosis_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->diagnosis_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->diagnosis_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_diagnosis_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->diagnosis_table)->get();
        return $query->row();
    }
}
?>