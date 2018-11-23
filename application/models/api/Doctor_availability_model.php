<?php

/*
  class name : doctor_availability_model Model
 */

class Doctor_availability_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function doctor_availabilty_insert_model($data) {
        //$this->db->insert("doctor_availability_list",$data);
        $insert_data = [
            "doctor_id" => $data['doctor_id'],
            "type" => $data['type'],
            "slots" => json_encode($data['slots'])
        ];
        //dd($insert_data);
        $this->db->where(["doctor_id" => $data['doctor_id']]);
        $this->db->select("doctor_availability_id")
                ->from("doctor_availability_list");
        $query = $this->db->get();
        if ( $query->num_rows() == 0) {
            $query = $this->db->insert("doctor_availability_list", $insert_data);
			//var_dump($query);
			//echo $this->db->_error_message();
			//dd($this->db->error()); it will work when db_debug = FALSE
        } else {			
            $this->db->where("doctor_id",$data['doctor_id']);
            $query = $this->db->update("doctor_availability_list", $insert_data);
        }
    }

    public function doctor_daily_date_insert() {
        $now = time();
        $alldate = [];
        for ($i = 0; $i <= 29; $i++) {
            $this->db->select("date_available");
            $this->db->where("date_available", date('Y-m-d', $now + (60 * 60 * 24 * $i)));
            $query = $this->db->get('date_availability_list');
            if ($query->num_rows() == 0) {
                $alldate[]['date_available'] = date('Y-m-d', $now + (60 * 60 * 24 * $i));
            }
        }
        
        //dd($alldate);
        if (!empty($alldate)) {
            $this->db->insert_batch("date_availability_list", $alldate);
        }
        return $alldate;
    }

    public function doctor_onetime_date($data) {
        // $data is one date 
        $this->db->select("id");
        $this->db->where("date_available", $data);
        $query = $this->db->get('date_availability_list');
        if ($query->num_rows() == 0) {
            $this->db->insert("date_availability_list", ["date_available" => $data]);
            return $this->db->insert_id();
        } else {
            return $query->row_array()['id'];
        }
    }

    public function get_date_id($date) {
        if (!empty($date)) {
            $this->db->select("id");
            $this->db->where_in("date_available", $date);
            $query = $this->db->get('date_availability_list');
            return (count($query->result_array() > 0)) ? $query->result_array() : [];
        }
    }



    function get_doctor_slot_model($doctor_id,$type) {
        $this->db->where("doctor_id",$doctor_id);
        $this->db->where("type",$type);
        $this->db->select("type,slots");
        $q = $this->db->get("doctor_availability_list"); 
        return count($q->row_array()) ? $q->row_array() : false;
    }

     public function get_doctor_free_date($doctor_id,$abbrevation="-08:00") {
		 
        $this->db->group_by("date_available");
        $this->db->where("`date_available` >= CURRENT_DATE()");
        $this->db->where("CONCAT(`date_available`,' ',start_time) <= (CURRENT_DATE() + INTERVAL 30 DAY)");
        $this->db->where(["slot_status" => 0,"doctor_id"=>$doctor_id]);
        $this->db->select("date_available AS date,CONVERT_TZ(NOW(), '+00:00', '".$abbrevation."') AS current_loc_time,DATE_FORMAT(CONVERT_TZ(date_available ,'+00:00', '".$abbrevation."'),'%Y-%m-%d') AS date_available, CONVERT_TZ(DATE_FORMAT(CONCAT(date_available, ' ', `start_time`),'%Y/%m/%d %H:%i:%s'), '+00:00', '".$abbrevation."') AS date_time");
        $this->db->from("doctor_slots_list");
        $query = $this->db->get();
        
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function get_doctor_free_slot($data) {
        $a  = substr($data['time_abbreviation'], 0,1);
        $tm= ($a == "-")?"+":"-";
        //echo $data['time_abbreviation'];die;
        if ($data['date'] == date("Y-m-d")) {
            $this->db->where(["slot_status" => 0, "doctor_id" => $data['doctor_id'], "date_available" => $data['date']]);
            $this->db->having("slot_loc_time > current_loc_time");
        } else {
            $this->db->where(["slot_status" => 0, "doctor_id" => $data['doctor_id'], "date_available" => $data['date']]);
        }
        $this->db->select("id,doctor_id,start_time, end_time,TIME_FORMAT(
        CONVERT_TZ(NOW(), '+00:00', '".$data['time_abbreviation']."'),
        '%H:%i:%s') AS current_loc_time,TIME_FORMAT(CONVERT_TZ(CONCAT(date_available,' ',`start_time`),'+00:00','".$data['time_abbreviation']."'),'%H:%i:%s') AS slot_loc_time
                ,CONVERT_TZ(CONCAT(date_available,' ',`start_time`),'+00:00','" .$data['time_abbreviation']."') AS current_date_time_slot_only");
        $this->db->from("doctor_slots_list");
        $query = $this->db->get();
        //echo  $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }


}

?>