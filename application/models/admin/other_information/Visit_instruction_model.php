<?php

class Visit_instruction_model extends CI_Model {
    
    protected $visit_instruction = 'visit_instruction';


    function __construct() {
        parent::__construct();
    }
    public function get_all_visit_instruction_list()
    {
        $this->db->order_by("id","DESC"); 
       $this->db->select("*");
       $this->db->from($this->visit_instruction);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of visit_instruction
    public function add_and_update_visit_instruction($insertdata,$id = null)
    {
        unset($insertdata['visit_instruction_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->visit_instruction,$insertdata);
            return true;
        }else{
            $this->db->insert($this->visit_instruction,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get visit_instruction Info
    public function get_visit_instruction_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->visit_instruction)->get();
        return $query->row();
    }
}
?>