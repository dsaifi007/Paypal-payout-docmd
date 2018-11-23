<?php

class Medications_instruction_model extends CI_Model {
    
    protected $medications_instruction_table = 'medications_instruction';


    function __construct() {
        parent::__construct();
    }
    public function get_all_medications_instruction()
    {
        $this->db->order_by("id","DESC"); 
       $this->db->select("*");
       $this->db->from($this->medications_instruction_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_medications_instruction($insertdata,$id = null)
    {
        unset($insertdata['medications_instruction_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->medications_instruction_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->medications_instruction_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_medications_instruction_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->medications_instruction_table)->get();
        return $query->row();
    }
}
?>