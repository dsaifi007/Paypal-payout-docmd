<?php

class Medication_model extends CI_Model {
    
    protected $medication_table = 'medication';
    protected $address = "address";

    function __construct() {
        parent::__construct();
    }
    public function get_all_medication()
    {
        $this->db->order_by("id","DESC"); 
        $this->db->where("is_deleted",0);
       $this->db->select("id,CONCAT(`name`,'(Englsih)','\n',`sp_name`,'(Spanish)') AS name , CONCAT(`additional_info`,'(Englsih)','\n,',`sp_additional_info`,'(Spanish)') AS medication_instruction");
       $this->db->from($this->medication_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_medication($insertdata,$id = null)
    {
        unset($insertdata['medication_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->medication_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->medication_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_medication_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->medication_table)->get();
        return $query->row();
    }
}
?>