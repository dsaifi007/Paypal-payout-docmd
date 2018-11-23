<?php

class Symptoms_model extends CI_Model {
    
    protected $symptoms_table = 'symptom';
    protected $address = "address";

    function __construct() {
        parent::__construct();
    }
    public function get_all_symptoms()
    {
        $this->db->order_by("id","DESC"); 
        $this->db->where("is_deleted",0);
       $this->db->select("id,CONCAT(`name`,'(english)','\n,',`sp_name`,'(spanish)') AS name,CONCAT(`additional_info`,'(english)','\n,',`sp_additional_info`,'(spanish)') AS visit_instruction");
       $this->db->from($this->symptoms_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_symptoms($insertdata,$id = null)
    {
        unset($insertdata['symptoms_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->symptoms_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->symptoms_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_symptoms_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->symptoms_table)->get();
        return $query->row();
    }
}
?>