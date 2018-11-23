<?php

class Manage_other_information_model extends CI_Model {

    protected $medication = "medication";

    function __construct() {
        parent::__construct();
    }

    public function get_allmedications($language=null) {
        $field = ($language=="spn")?"sp_name as name,sp_additional_info as additional_info":"name,additional_info";
        $this->db->select("id,$field")->from($this->medication);
        $query = $this->db->get();
        return (count($query->result_array()) > 0 ) ? $query->result_array() : false;

    }
    public function get_allallergies($language=null) {
        $field = ($language=="spn")?"sp_name as name,sp_additional_info as additional_info":"name,additional_info";
        $this->db->select("id,$field")->from("allergies");
        $query = $this->db->get();
        return (count($query->result_array()) > 0 ) ? $query->result_array() : false;

    }
   public function get_alldiagnosis($language=null) {
        $field = ($language=="spn")?"sp_name as name,sp_additional_info as additional_info":"name,additional_info";
        $this->db->select("id,$field")->from("admin_diagnosis");
        $query = $this->db->get();
        return (count($query->result_array()) > 0 ) ? $query->result_array() : false;

    }
}

?>