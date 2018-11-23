<?php

class Rejected_doctor_model extends CI_Model {

    var $order = array('id' => 'desc'); // default order
    protected $patient_table = 'patient_info';
    protected $doctor_table = "doctors";
    protected $doc_profes_info = "doc_professional_info";
    protected $user_patient = "user_patient";
    protected $address_map = "doctor_address";
    protected $address = "address";
    protected $specility_table = "spacility";
    protected $emails = [];
    protected $doctor_pending_status = "pending";
    protected $doctor_accept_status = "accept";
    protected $doctor_reject_status = "reject";

    function __construct() {
        parent::__Construct();
    }

    function get_order_list($post) {
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function _get_order_list_query($post) {

        // This if condition is work when external filtering is in used  
        if ($post['external_filtering'] != '') {
            $filter_data = (array) json_decode($post['external_filtering']);
            foreach ($filter_data as $key => $value) {
                $data[] = str_replace("_", ".", $key);
            }
            $final_filter = array_combine($data, $filter_data);
            $this->db->where($final_filter);
        }

        $this->db->select(
                "doctors.id,
                doctors.first_name,
                doctors.last_name,
                doctors.email,
                doctors.phone,
                doctors.gender,
                doctors.date_of_birth,                             
                doctors.is_blocked"
        );
        $this->db->from("$this->doctor_table as doctors");
        $this->db->where("status", $this->doctor_reject_status);
        //$this->db->get();
        //echo  $this->db->last_query();die;

        /*
          -------------------------------------------------------------------------------------
          |       when Filtering is on
          -------------------------------------------------------------------------------------
         */
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $this->db->join("$this->address_map as address_map", "address_map.doctor_id = doctors.id", "LEFT");
            $this->db->join("$this->address as address", "address.id = address_map.address_id", "INNER");
            $this->db->join("$this->doc_profes_info as doc_profes_info", "doc_profes_info.doctor_id = doctors.id", "INNER");
            $this->db->join("$this->specility_table as splct", "splct.id = doc_profes_info.speciality_id", "INNER");
            //$this->db->get();
            //echo  $this->db->last_query();die;
        }
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
        //print_r($post);die;
        $this->db->from($this->doctor_table);
        //$this->db->where($post['where']);
        //foreach ($post['where_in'] as $index => $value){
        //$this->db->where_in($index, $value);
        //}
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    // get all email 
    public function get_all_emails($user_id ) {       
        $this->db->where_in("id", $user_id);
        $this->db->select("GROUP_CONCAT(email) AS emails")->from($this->doctor_table);
        $query = $this->db->get();
        $result = $query->result_array();
        $this->emails = explode(",", $result[0]['emails']);       
        return $this->emails;
    }

    // Get individual doctor data
    public function get_doctor_info($id) {
        $this->db->where("doctor.id", $id);
        $this->db->select(
                "doctor.id,
                CONCAT(doctor.first_name,' ',doctor.last_name) AS name,
                doctor.gender,
                doctor.date_of_birth,
                doctor.email,
                doctor.phone,
                doctor.profile_url,
                address.address,
                address.city,
                address.state,
                address.zip_code,
                doc_profes_info.undergraduate_university,
                doc_profes_info.medical_school,
                doc_profes_info.residency,
                doc_profes_info.medical_license_number,
                spacility.id as spacility_id,
                (SELECT `name` FROM `language` WHERE language_id = (SELECT language_id FROM doctors WHERE id='" . $id . "') LIMIT 1) AS language,
                (SELECT `id` as id FROM `doctor_degree` WHERE id = (SELECT degree_id FROM doc_professional_info WHERE doctor_id='" . $id . "') LIMIT 1) AS Degree
                ");
        $this->db->from("$this->doctor_table as doctor");
        $this->db->join("$this->doc_profes_info as doc_profes_info", "doc_profes_info.doctor_id = doctor.id", "LEFT");
        $this->db->join("$this->specility_table as spacility", "spacility.id = doc_profes_info.speciality_id", "LEFT");
        $this->db->join("$this->address_map as address_mapping", "address_mapping.doctor_id = doctor.id", "LEFT");
        $this->db->join("$this->address as address", "address.id = address_mapping.address_id", "LEFT");
        $query = $this->db->get();
        return $query->row_array();
        //dd($query->row_array());
        //echo $this->db->last_query();die;
    }

    //update_doctor_rating_commission
    public function update_doctor_rating_commission($doctor_data) {
        $this->db->where(["id" => $doctor_data['doctor_id']]);
        $this->db->where("date_of_birth IS NOT NULL AND gender IS NOT NULL");
        $this->db->update($this->doctor_table, ["status" => $this->doctor_accept_status, 'is_blocked' => 0, "commission" => $doctor_data['commission']]);
        if ($this->db->affected_rows()) {
            $this->db->insert("user_to_doctor_rating", ["doctor_id" => $doctor_data['doctor_id'], 'rating' => $doctor_data['rating'], "created_at" => $this->config->item("date")]);
            $this->db->insert("doctor_avg_rating", ["doctor_id" => $doctor_data['doctor_id'], 'avg_rating' => $doctor_data['rating']]);
            return true;
        } else {
            return false;
        }
    }

    // block/unblock of the users based on id
    public function update_user_status_model($user_data) {
        $this->db->where("id", $user_data['user_id']);
        $this->db->update($this->doctor_table, ["is_blocked" => $user_data['status']]);
        return true;
    }

    public function update_rejected_status_model($user_data) {
        $this->db->where("email", $user_data['email']);
        $this->db->update($this->doctor_table, ["status" => "reject"]);
        return true;
    }

    // block/unblock of the users based on id
    public function update_degree_speciality_model($data) {
        // dd($data);
        $column = ['speciality_id' => $data['selected_id']];
        if ($data['column_name'] == "degree") {
            $column = ['degree_id' => $data['selected_id']];
        }
        $this->db->where("doctor_id", $data['id']);
        $this->db->update("doc_professional_info", $column);
        //echo $this->db->last_query();die;
        return true;
    }

    public function get_all_degree() {
        $query = $this->db->select("id,degree")->from("doctor_degree")->get();
        return $query->result_array();
    }

    public function get_all_speciality() {
        $query = $this->db->select("id,name")->from("spacility")->get();
        return $query->result_array();
    }

    //get all state
    public function get_all_state() {
        $query = $this->db->select("state_code AS state")->from("state_list")->get();
        return $query->result_array();
    }

    //get all city
    public function get_all_city() {
        $query = $this->db->select("DISTINCT(city) AS city")->from($this->address)->get();
        return $query->result_array();
    }

    // get all get_all_specilities
    public function get_all_specilities() {
        $query = $this->db->select("id,name")->from($this->specility_table)->get();
        return $query->result_array();
    }
 
}

?>