<?php

class Prescription_model extends CI_Model {

    protected $pharmacy_table = 'pharmacies';
    protected $address = "address";
    protected $day_data = [];
    protected $day_data_json = [];

    function __construct() {
        parent::__construct();
    }

    function get_order_list($post) {
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        
        return $query->result();
    }

    function _get_order_list_query($post = '') {

        // This if condition is work when external filtering is in used  
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $filter_data = array_filter(json_decode($post['external_filtering'],true));
            // Working from tommorow
            
            if (isset($filter_data['patient_availability_date'])) {
                $filter_data['appointment.patient_availability_date'] = $filter_data['patient_availability_date'];
                unset($filter_data['patient_availability_date']);
            }
            if (isset($filter_data['gender'])) {
                $filter_data['doctors.gender'] = $filter_data['gender'];
                unset($filter_data['gender']);
            }
            if (isset($filter_data['status'])) {
                $filter_data['b.status'] = $filter_data['status'];
                unset($filter_data['status']);
            }
            $this->db->where($filter_data);
           
        }
        $this->db->select("DISTINCT(a.appointment_id) AS appointment_id,
            a.`med_id`,
            a.user_id,
            doctors.first_name AS doctor_first_name,
            doctors.last_name AS doctor_last_name,
            a.doctor_id,
            provider_plan.title,
            a.`patient_med_id`,
            patient_info.first_name,
            patient_info.last_name,
            patient_info.date_of_birth,
            date(a.patient_availability_date_and_time) AS date,
            time(a.patient_availability_date_and_time) AS time,
            a.type,b.status");
        $this->db->from("all_appointment AS a");
        $this->db->join("prescriptions AS b", "b.appointment_id=a.appointment_id", "INNER");
        $this->db->join("patient_info", "patient_info.id = a.patient_id", "LEFT");
        $this->db->join("doctors", "doctors.id = a.doctor_id", "LEFT");
        $this->db->join("appointment", "appointment.id = a.appointment_id", "LEFT");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "LEFT");
        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }

        /* foreach ($post['where_in'] as $index => $value){

          $this->db->where_in($index, $value);
          } */

        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all($post) {
        $this->_count_all_bb_order($post);
        $query = $this->db->count_all_results();
        return $query;
    }

    public function _count_all_bb_order($post) {
        $this->db->from($this->pharmacy_table);
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_appt_presc_status_model($data) {
        //dd($data);
        $this->db->where("appointment_id", $data['id']);
        $this->db->update("prescriptions", ["status" => $data['status']]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    //Update pharmacy status based on pharmacy id
    public function update_pharmacy_status_model($pharmacy_data) {
        $this->db->where("id", $pharmacy_data['id']);
        $this->db->update($this->pharmacy_table, ["is_blocked" => $pharmacy_data['status']]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    //Get Pharmacy Info
    public function get_pharmacy_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("*")->from($this->pharmacy_table)->get();
        return $query->row();
    }

    //get all state
    public function get_filter_data() {
        $this->db->select("appointment.id,appointment.patient_availability_date AS date ,provider_plan.title")->from("appointment");
        $this->db->join("provider_plan", "provider_plan.id=appointment.treatment_provider_plan_id", "INNER");
        $query = $this->db->get();
        return $query->result_array();
    }

    //get all city
    public function get_all_city() {
        $query = $this->db->select("DISTINCT(city) AS city")->from($this->pharmacy_table)->get();
        return $query->result_array();
    }

    // get all get_all_specilities
    public function get_all_specilities() {
        $query = $this->db->select("id,name")->from($this->specility_table)->get();
        return $query->result_array();
    }

    public function get_admin_note($appt_id) {
        $this->db->where("admin_name", $this->session->userdata('name'));
        $this->db->where("appointment_id", $appt_id);
        $this->db->order_by("updated_at", "DESC");
        $this->db->select("id,admin_name,appointment_id,note,CONVERT_TZ(updated_at,'+00:00','-08:00') AS updated_at");
        $this->db->from("admin_appointment_note");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

}

?>