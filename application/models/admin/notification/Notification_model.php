<?php

class Notification_model extends CI_Model {

    protected $notification_table = 'notification_content';
    protected $schedule_time = NULL;

    function __construct() {
        parent::__construct();
    }

    public function get_all_notification() {
        $this->db->order_by("id", "desc");
        $this->db->select("id,name,additional_info,notification_scheduler_id,notification_type");
        $this->db->from($this->notification_table);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_notification_schedule() {
        $this->db->select("id,name");
        $this->db->from("notification_schedule");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_users() {
        $this->db->select("user_patient.user_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS name");
        $this->db->from("patient_info");
        $this->db->join("user_patient", "patient_info.id=user_patient.patient_id", "INNER JOIN");
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_all_doctors() {
        $this->db->select("id,CONCAT(first_name,' ',last_name) AS name");
        $this->db->from("doctors");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function GetAllNewUsers() {
        //Condition-1 - Get all users which coming from last 30 days
        $result = array();
        $this->db->where("patient_info.`created_date` BETWEEN CURRENT_DATE - INTERVAL 30 DAY AND CURRENT_DATE",null,false);
        $this->db->select("user_patient.user_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS full_name");
        $this->db->from("patient_info");
        $this->db->join("user_patient", "patient_info.id=user_patient.patient_id", "INNER JOIN");
        $query = $this->db->get();        
        $result = $query->result_array();
        
        // If user not having any appointment from the registration
        $result1 = array();
        $this->db->where("user_patient.user_id NOT IN(SELECT user_id FROM `appointment`)",null,false);
        $this->db->select("user_patient.user_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS full_name");
        $this->db->from("patient_info");
        $this->db->join("user_patient", "patient_info.id=user_patient.patient_id", "INNER JOIN");
        $query1 = $this->db->get();
        $result1 = $query1->result_array();      
        return array_merge($result,$result1);
    }
    
    public function GetAllNewDoctors() {
        //Condition-1 - Get all users which coming from last 30 days
        $result = array();
        $this->db->where(" doctors.id NOT IN(SELECT DISTINCT(doctor_id) AS doctor_id FROM `appointment` WHERE `created_date` BETWEEN CURRENT_DATE - INTERVAL 30 DAY AND CURRENT_DATE AND doctor_id IS NOT NULL)",null,false);
        $this->db->select("doctors.id,CONCAT(doctors.first_name,' ',doctors.last_name) AS full_name");
        $this->db->from("doctors");
        $query = $this->db->get();            
        $result = $query->result_array();
        
        // If user not having any appointment from the registration
        $result1 = array();
        $this->db->where("doctors.id NOT IN(SELECT DISTINCT(doctor_id) AS doctor_id FROM `appointment` WHERE doctor_id IS NOT NULL)",null,false);
        $this->db->select("doctors.id,CONCAT(doctors.first_name,' ',doctors.last_name) AS full_name");
        $this->db->from("doctors");
        $query1 = $this->db->get();      
        $result1 = $query1->result_array();      
        return array_merge($result,$result1);
    }
    
    

    // add/update the new record of pharmacy
    public function add_and_update_notification($insertdata, $id = null) {
        unset($insertdata['save']);
        if ($id != null) {
            $this->db->where("id", $id);
            $this->db->update($this->notification_table, $insertdata);
            return true;
        } else {
            $insertdata['created_date'] = $this->config->item("date");
            $this->db->insert($this->notification_table, $insertdata);
            return $this->db->insert_id();
        }
    }

    public function update_user_ids_model($data) {
        unset($data['save']);

        if (isset($data['user_ids'])) {
            $update_data = [
                "notification_type" => $data['notification_type'],
                "users_ids" => json_encode($data['user_ids']),
                "doctor_ids" => NULL,
            ];
        } elseif (isset($data['doctor_ids'])) {
            $update_data = [
                "notification_type" => $data['notification_type'],
                "doctor_ids" => json_encode($data['doctor_ids']),
                "users_ids" => NULL,
            ];
        } else {
            $update_data = [
                "notification_type" => $data['notification_type'],
                "users_ids" => NULL,
                "doctor_ids" => NULL,
            ];
        }

        $this->db->where("id", $data['id']);
        $this->db->update("notification_content", $update_data);
        return true;
    }

    public function send_notification_model($notification_type = 2) {
        $this->db->where_in("notification_scheduler_id", $notification_type);
        $this->db->order_by("id", "ASC");
        $this->db->select("id,notification_scheduler_id,notification_type,users_ids,doctor_ids,name,additional_info,date(schedule_time) AS schedule_time,created_date");
        $this->db->from($this->notification_table);
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    //Get Pharmacy Info
    public function get_notification_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("id,name,additional_info,sp_name,sp_additional_info")->from($this->notification_table)->get();
        return $query->row();
    }

    public function updatescheduler_id_model($input_data) {
        unset($input_data['confirm']);
        if (isset($input_data['schedule_time']) && $input_data['schedule_time']) {
            $date_time = date_create(str_replace("/", "-", $input_data['schedule_time']));
            $this->schedule_time = date_format($date_time, "Y-m-d H:i:s");
            $insert_data = ["notification_scheduler_id" => $input_data['notification_scheduler_id'], "schedule_time" => $this->schedule_time];
        }
        $insert_data = ["notification_scheduler_id" => $input_data['notification_scheduler_id']];

        $this->db->where("id", $input_data['item_id']);
        $this->db->update("notification_content", $insert_data);
        return true;
        //echo $this->db->last_query();die;
    }

}

?>