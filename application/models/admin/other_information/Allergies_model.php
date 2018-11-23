<?php

class Allergies_model extends CI_Model {
    
    protected $allergies_table = 'allergies';
    

    function __construct() {
        parent::__construct();
    }
    public function get_all_allergies()
    {
       $this->db->order_by("id","DESC"); 
       $this->db->where("is_deleted",0);
       $this->db->select("*");
       $this->db->from($this->allergies_table);
       $query = $this->db->get();
       return $query->result_array();
    }
    // add/update the new record of pharmacy
    public function add_and_update_allergy($insertdata,$id = null)
    {
        unset($insertdata['allergy_submit']);
        if ($id != null) {
            $this->db->where("id",$id);
            $this->db->update($this->allergies_table,$insertdata);
            return true;
        }else{
            $this->db->insert($this->allergies_table,$insertdata);  
            return $this->db->insert_id();       
        }
    }

    //Get Pharmacy Info
    public function get_allergy_info($id)
    {
        $this->db->where("id",$id);
        $query = $this->db->select("*")->from($this->allergies_table)->get();
        return $query->row();
    }
}
?>