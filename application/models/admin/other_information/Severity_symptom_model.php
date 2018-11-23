<?php

class Severity_symptom_model extends CI_Model {

    protected $severity_of_symptoms = 'severity_of_symptoms';

    function __construct() {
        parent::__construct();
    }

    public function get_all_severity_symptoms() {
        $this->db->order_by("id", "DESC");
        $this->db->where("is_deleted", 0);
        $this->db->select("*");
        $this->db->from($this->severity_of_symptoms);
        $query = $this->db->get();
        return $query->result_array();
    }

    // add/update the new record of pharmacy
    public function add_and_update_severity_symptoms($insertdata, $id = null) {
        unset($insertdata['severity_symptom_submit']);
        if ($id != null) {
            $this->db->where("id", $id);
            $this->db->update($this->severity_of_symptoms, $insertdata);
            return true;
        } else {
            $this->db->insert($this->severity_of_symptoms, $insertdata);
            return $this->db->insert_id();
        }
    }

    //Get Pharmacy Info
    public function get_severity_symptom_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("*")->from($this->severity_of_symptoms)->get();
        return $query->row();
    }

}

?>