<?php

class Treatment_model extends CI_Model {

    protected $treatment_table = 'provider_plan';

    function __construct() {
        parent::__construct();
    }

    public function get_all_treatment() {
        $this->db->group_by("provider_plan.id");
        $this->db->where("provider_plan.is_deleted",0);
        $this->db->select("provider_plan.id , provider_plan.title,provider_plan.description,provider_plan.title_spn,provider_plan.description_spn,provider_plan.is_recommended,GROUP_CONCAT(symptom.name) AS symptoms");
        $this->db->from($this->treatment_table);
        $this->db->join("treatment_mapping_symtpoms","treatment_mapping_symtpoms.treatment_id = provider_plan.id","LEFT");
        $this->db->join("symptom","symptom.id = treatment_mapping_symtpoms.symptom_id","LEFT");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->result_array();
    }

    // add/update the new record of pharmacy
    public function add_and_update_treatment($insertdata, $id = null) {
        
        unset($insertdata['treatment_submit']);
        $input_data = array();
        $input_data['title'] = $insertdata['title'];
        $input_data['description'] = $insertdata['description'];
        $input_data['title_spn'] = $insertdata['title_spn'];
        $input_data['description_spn'] = $insertdata['description_spn'];
        if ($id != null) {
            $this->db->where("id", $id);
            $this->db->update($this->treatment_table, $input_data);

            # ------------------------------------------------
            $this->db->where("treatment_id", $id);
            $this->db->delete("treatment_mapping_symtpoms");
            #-------------------------------------------------
            if ($insertdata['treatment_plan_id']) {
                $mapp_insert_data = [];
                foreach ($insertdata['treatment_plan_id'] as $v) {
                    $mapp_insert_data[] = [
                        "treatment_id" => $id,
                        "symptom_id" => $v
                    ];
                }
                $this->db->insert_batch("treatment_mapping_symtpoms", $mapp_insert_data);
            }
        } else {

            $this->db->insert($this->treatment_table, $input_data);
            $last_id = $this->db->insert_id();

            if ($insertdata['treatment_plan_id']) {
                $mapp_insert_data = [];
                foreach ($insertdata['treatment_plan_id'] as $v) {
                    $mapp_insert_data[] = [
                        "treatment_id" => $last_id,
                        "symptom_id" => $v
                    ];
                }
                $this->db->insert_batch("treatment_mapping_symtpoms", $mapp_insert_data);
            }
        }
        return true;
    }

    //Get Pharmacy Info
    public function get_treatment_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("*")->from($this->treatment_table)->get();
        return $query->row();
    }

    public function get_all_symptoms() {
        $this->db->select("id,name");
        $this->db->from("symptom");
        $query = $this->db->get();
        return $query->result_array();
    }
    function update_status($id) {
        
        $this->db->update("provider_plan",["is_recommended"=>0]);       
        #---------------------------------------------------
        $this->db->where("id",$id);
        $this->db->update("provider_plan",["is_recommended"=>1]);        
    }
}

?>