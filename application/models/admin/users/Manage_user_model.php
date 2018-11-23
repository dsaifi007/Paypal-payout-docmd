<?php

class Manage_user_model extends CI_Model {

    var $order = array('users.id' => 'desc'); // default order
    protected $patient_table = 'patient_info';
    protected $user_table = "users";
    protected $user_patient = "user_patient";
    protected $address_map = "patient_address";
    protected $address = "address";
    protected $emails = [];

    function __construct() {
        parent::__Construct();
    }

    function get_order_list($post) {
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->result();
    }

    function _get_order_list_query($post) {

        // This if condition is work when external filtering is in used  
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
                
            $filter_data = array_filter((array) json_decode($post['external_filtering']));
            foreach ($filter_data as $key => $value) {
                $data[] = str_replace("_", ".", $key);
            }
            $final_data = array_combine($data, $filter_data);
            
            //echo $final_data['address.city'];
            // For Health Insurance
            if (isset($final_data['ptnt.provider'])) {
                unset($final_data['ptnt.provider']);
                $not_null = "ptnt.provider IS NOT NULL";
                $this->db->where($not_null);
            }
            if(isset($final_data['avg.rating'])){               
                $this->db->where(["user_avg_rating.avg_rating"=>(int)$final_data['avg.rating']]);
                unset($final_data['avg.rating']);
            }
            
            $this->db->where($final_data);
        }
        $this->db->select(
                "users.id,
                ptnt.med_id,
                user_avg_rating.avg_rating,
                ptnt.first_name,
                ptnt.last_name,
                users.email,
                users.phone,
                ptnt.gender,
                ptnt.date_of_birth,                             
                users.is_blocked"
        );
        $this->db->from("$this->user_table as users");
        $this->db->join("$this->user_patient as user_patient", "user_patient.user_id = users.id", "LEFT");
        $this->db->join("$this->patient_table as ptnt", "user_patient.patient_id = ptnt.id", "INNER");
        $this->db->join("user_avg_rating", "user_avg_rating.user_id = users.id", "LEFT");

        /*
          -------------------------------------------------------------------------------------
          |when Filtering is on
          -------------------------------------------------------------------------------------
         */
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $this->db->join("$this->address_map as address_map", "address_map.patient_id = ptnt.id", "LEFT");
            $this->db->join("$this->address as address", "address.id = address_map.address_id", "LEFT");
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
        $this->db->from('patient_info');
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
    public function get_all_emails($user_id = null) {
        if ($user_id != null) {
            $this->db->where_in("id", $user_id);
        }
        $this->db->select("email")->from($this->user_table);
        $query = $this->db->get();
        foreach ($query->result_array() as $key => $value) {
            $this->emails[] = trim($value['email']);
        }
        return $this->emails;
    }

    // get user information based on id
    public function get_user_data($user_id) {
        $this->db->where("user_patient.user_id", $user_id);
        $this->db->select(
                "users.id,
                CONCAT(ptnt.first_name,' ',ptnt.last_name) AS name,
                ptnt.gender,
                user_avg_rating.avg_rating,
                ptnt.med_id,
                ptnt.date_of_birth,
                users.email,
                users.phone,
                ptnt.profile_url,
                address.address,
                address.city,
                address.state,
                address.zip_code,
                ptnt.provider,
                ptnt.member_id,
                ptnt.ins_group,
                users.is_blocked"
        );
        $this->db->from("$this->user_table as users");
        $this->db->join("$this->user_patient as user_patient", "user_patient.user_id = users.id", "LEFT");
        $this->db->join("$this->patient_table as ptnt", "user_patient.patient_id = ptnt.id", "INNER");
        $this->db->join("$this->address_map as address_mapping", "address_mapping.patient_id = ptnt.id", "INNER");
        $this->db->join("$this->address as address", "address.id = address_mapping.address_id", "INNER");
        $this->db->join("user_avg_rating", "user_avg_rating.user_id = users.id", "LEFT");
        $query = $this->db->get();
        return $query->row_array();
        //echo "ddd".$this->db->last_query();die;
    }

    // block/unblock of the users based on id
    public function update_user_status_model($user_data) {
        $this->db->where("id", $user_data['user_id']);
        $this->db->update($this->user_table, ["is_blocked" => $user_data['status']]);
        return true;
    }

    //get all state
    public function get_all_state() {
        $query = $this->db->select("DISTINCT(LOWER(state)) AS state")->from($this->address)->get();
        return $query->result_array();
    }

    //get all city
    public function get_all_city() {
        $query = $this->db->select("DISTINCT(city) AS city")->from($this->address)->get();
        return $query->result_array();
    }
    public function getrating() {
        $query = $this->db->select("DISTINCT(avg_rating) AS avg_rating")->from("user_avg_rating")->get();
        return $query->result_array();
    }
    public function get_user_medical_history($id) {
        $this->db->where("patient_info.user_id", $id);
        $this->db->select("patient_info.id AS patient_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS name,
            patient_info.user_id,user_medical_profile.patient_id,(SELECT CONCAT(first_name,' ',last_name) FROM patient_info WHERE id IN(SELECT patient_id FROM user_patient WHERE user_id='".$id."')) AS fullname,
            user_medical_profile.medications,user_medical_profile.allergies,user_medical_profile.past_medical_history,user_medical_profile.social_history,user_medical_profile.family_history
                ");
        $this->db->from("user_medical_profile");
        $this->db->join("patient_info", "patient_info.id = user_medical_profile.patient_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function get_all_users_patient_list_model($user_id) {
        $this->db->where("user_id", $user_id);
        $this->db->where("id != (SELECT patient_id FROM user_patient WHERE user_id ='".$user_id."')");
        $this->db->order_by("id", "ASC");
        $query = $this->db->select("med_id,CONCAT(first_name,' ',last_name) AS ful_name,date_of_birth,gender")->from("patient_info")->get();
        
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }
    
}
