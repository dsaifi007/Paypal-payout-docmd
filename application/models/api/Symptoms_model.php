<?php

/*
  class name : Symptoms_model
 */

class Symptoms_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /*
      --------------------------------------------------------------------------------------------
      |							Get all symptoms
      --------------------------------------------------------------------------------------------
     */

    public function get_all_symptoms_model($lang) {
        $select_columns = ($lang == "spn") ? "sp_name AS name,sp_additional_info AS additional_info" : "name,additional_info";
        $query = $this->db->select("id,$select_columns")
                ->from($this->config->item("symptoms_table"))
                ->get();
        return $query->result_array();
    }

    /*
      --------------------------------------------------------------------------------------------
      |						   Get all Severity Symptoms
      --------------------------------------------------------------------------------------------
     */

    public function get_all_severity_symptoms_model($lang) {
        $select_columns = ($lang == "spn") ? "sp_name AS name,sp_additional_info AS additional_info" : "name,additional_info";

        $query = $this->db->select("id,$select_columns")
                ->from($this->config->item("severity_symptoms_table"))
                ->get();
        return $query->result_array();
    }

}

?>