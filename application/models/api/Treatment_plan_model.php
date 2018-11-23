<?php

class Treatment_plan_model extends CI_Model {

    private $dollar_symbol = "$";

    function __construct() {
        parent::__construct();
    }

    public function get_all_treatment_plan($lang) {
        $column = ($lang == "spn")?"title_spn AS title, description_spn AS description":"title,description,amount";
        $this->db->select("id,$column,amount,type,is_recommended")->from($this->config->item("treatment_table"));
        $query = $this->db->get();
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            if ($value['is_recommended'] == $this->config->item("is_recommended")) {
                $val = true;
            } else {
                $val = false;
            }
            $result[$key]['amount'] =  $value['amount'];
            $result[$key]['is_recommended'] = $val;
        }
        return $result;
    }

    function get_all_treatment_plan_based_on_symptoms($data) {
        $this->db->where("id IN(SELECT DISTINCT(treatment_id) FROM `treatment_mapping_symtpoms` WHERE symptom_id IN(" . implode(",",$data['symptom_ids']). "))");
        $this->db->select("title")->from("provider_plan");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

}

?>