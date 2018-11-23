<?php

/*
  class name : Appoinment Model
 */

class Appoinment_model extends CI_Model {

    protected $symptoms_array = [];

    function __construct() {
        parent::__construct();
    }

    /*
      --------------------------------------------------------------------------------------------
      |             Get the Address of the Patient based on Id
      --------------------------------------------------------------------------------------------
     */

    public function get_appointed_doctor_model($patient_id, $specility_id) {
        /* We are find the patient state based on patient id */
        $this->db->where("patient_address.patient_id", $patient_id);
        $this->db->select("address.id,address.state")
                ->from("address");
        $this->db->join("patient_address", 'address.id = patient_address.address_id', "INNER");
        $query = $this->db->get();
        $result = $query->row();
        // get appointed doctor
        return $this->get_appointed_doctor_id_model($result->state, $specility_id);
    }

    /*
      ---------------------------------------------------------------------------------
      | We are finding those doctors, Who have same state with the patient and
      | specility choose by the patient(Get Appointed Doctor)
      | Basicaly we are appointed the doctor to the user
      ---------------------------------------------------------------------------------
     */

    public function get_appointed_doctor_id_model($state, $spacility_id) {
        $this->db->where("spacility.id", $spacility_id);
        $this->db->where("address.state", $state);

        $query = $this->db->select("doctors.id AS doctor_id")->from("doc_professional_info");
        $this->db->join("doctors", 'doctors.id = doc_professional_info.doctor_id', "INNER");
        $this->db->join("spacility", 'spacility.id = doc_professional_info.speciality_id', "INNER");
        $this->db->join("doctor_address", 'doctor_address.doctor_id = doctors.id', "INNER");
        $this->db->join("address", 'address.id = doctor_address.address_id', "INNER");
        $query = $this->db->get();
        return count($query->result_array()) > 0 ? $query->result_array() : false;
    }

    /*
      ---------------------------------------------------------------------------------
      | Insert the appoinment detail in db Table
      ---------------------------------------------------------------------------------
     */

    public function insert_appoinment_detail($data, $appointed_doctor_id = null) {
        unset($data['payment_method_type']);
        unset($data['payment_method_nonce']);
        unset($data['paypal_email']);
        unset($data['venmo_id']);
        $appoinment_data = array_merge([
            //"doctor_id"=>$appointed_doctor_id,
            'created_date' => date("Y-m-d h:i:s")], $data);

        $datetime['patient_availability_date_and_time'] = $data['patient_availability_date'] . ' ' . $data['patient_availability_time'];
        $insert_data = array_merge($data, $datetime, $appoinment_data);
        $insert_data['insurance_status'] = (@$insert_data['insurance_status']) ? 1 : 0;
        unset($insert_data['symptom_ids']);
        // Insert the Appoinment data in table
        $this->db->trans_start();
        $this->db->insert("appointment", $insert_data);
        $last_appoinment_id = $this->db->insert_id();
        $this->db->trans_complete();

        // update the doctor slot status  after create the apoppointment
        $this->db->where(['doctor_id' => $data['doctor_id'], 'id' => $data['slot_id']]);
        $this->db->update("doctor_slots", ['status' => $this->config->item("update_doctor_slot_status")]);

        // Making the array for mapping the appointment id and symptoms id
        foreach ($data['symptom_ids'] as $key => $symptom_id) {
            $this->symptoms_array[] = [
                "appointment_id" => $last_appoinment_id,
                "symptom_id" => $symptom_id
            ];
        }
        $this->db->insert_batch("appointment_symptom", $this->symptoms_array);

        // we are sending the response of API 
        $this->db->where("appointment.id", $last_appoinment_id);
        $this->db->select("
				appointment.id,
				appointment.patient_availability_date_and_time AS date,
				doc_professional_info.additional_info AS instruction"
        );
        $this->db->from("appointment");
        $this->db->join("doc_professional_info", 'doc_professional_info.doctor_id = appointment.doctor_id', "LEFT");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->row(); // response sent 
    }

    /*
      ---------------------------------------------------------------------------------
      | get the current user device token
      ---------------------------------------------------------------------------------
     */

    public function get_device_token($patient_id) {
        $this->db->select("id,device_token")->from("doctors");
        $this->db->where("id", $patient_id);
        //$this->db->where("id = (SELECT doctor_id FROM appointment WHERE patient_id IN('".$patient_id."') LIMIT 1)", NULL, FALSE);
        $query = $this->db->get();
        return $query->row();
    }

    /*
      ---------------------------------------------------------------------------------
      | get the array structure and insert the notification data from DB
      ---------------------------------------------------------------------------------
     */

    public function get_notification_data($appointment_id) {
        $this->db->where("a.id", $appointment_id);
        $this->db->select("a.id as appointment_id,CONCAT(b.first_name,' ',b.last_name) as name,b.profile_url ,c.type")->from("appointment as a");
        $this->db->join("patient_info as b", "a.patient_id = b.id", "LEFT");
        $this->db->join("provider_plan as c", "c.id = a.treatment_provider_plan_id", "LEFT");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : array();
    }

    // this function will get the free slot of doctor if no 
    // slot is available then return false  
    public function user_appointment_booking_model($data, $language) {
        //echo date("Y-m-d H:i:s");die;
        // if ($data['date'] == date("Y-m-d")) {
        $a = substr($data['time_abbreviation'], 0, 1);
        $tm = ($a == "-") ? "+" : "-";
        // 	$this->db->where([
        // 		"date_availability_list.date_available" =>$data['date'],
        // 		"TIME_FORMAT(CONVERT_TZ(CONCAT(date_availability_list.date_available,' ',hour_list.`start_time`),'+00:00','" .$tm.substr($data['time_abbreviation'],1) . "'), '%H:%i:%s') >"=>date("H:i:s")
        // 	]);
        // }else{
        $this->db->where("date_availability_list.date_available ='" . $data['date'] . "'");
        //}
        $this->db->where("doctor_slots.status", $this->config->item("free_slot_status"));
        $this->db->where("address.state", $data['state']);
        $this->db->where("spacility.name", $data['spacility']);
        $this->db->where("doctors.is_blocked", 0);
        $is_doctor_profile_completed = "date_of_birth IS NOT NULL AND gender IS NOT NULL";
        $this->db->where($is_doctor_profile_completed);
        //$this->config->item("lang_code")
        //$lang = "l.lang_code = 'eng-spn' OR eng";
        $this->db->where("language.lang_code IN ('eng-spn' OR '" . $language . "')");
        //$this->db->where("language.lang_code = '".$language."'");
        //$this->db->or_where("l.lang_code",$language);
        $this->db->group_by("doctor_slots.slot_id");
        $this->db->limit(1, 0);

        $this->db->select(
                "doctor_slots.id, 
				doctor_slots.doctor_id, start_time,end_time,
        DATE(CONVERT_TZ(NOW(), '+00:00', '" . $data['time_abbreviation'] . "')) AS local_current_date
                               ");
        $this->db->from("doctor_slots");
        $this->db->join('hour_list', 'hour_list.id = doctor_slots.slot_id', "LEFT");
        $this->db->join('date_availability_list', 'date_availability_list.id = doctor_slots.date_id', "LEFT");
        $this->db->join('doctors', 'doctor_slots.doctor_id= doctors.id', "LEFT");
        $this->db->join('doctor_speciality', 'doctor_speciality.doctor_id = doctors.id', "LEFT");
        $this->db->join('spacility', 'spacility.id = doctor_speciality.spacility_id', "LEFT");
        $this->db->join('doctor_address', 'doctor_address.doctor_id = doctor_slots.doctor_id', "LEFT");
        $this->db->join('address', 'address.id = doctor_address.address_id ', "LEFT");
        $this->db->join('language', 'language.language_id = doctors.language_id ', "LEFT");
        $query = $this->db->get();
        // when we will get the doctor id then get all free slot of the them(doctor)

        if (count($query->row_array() > 0) && !empty($query->row_array())) {

            $result = $query->row_array();

            // get the all free slot of the selected doctor from above
            $this->db->where("doctor_id", $result['doctor_id']);
            $this->db->where("slot_status", 0);


            if ($data['date'] == $result['local_current_date']) {
                $this->db->where("date_available", $data['date']);
                $this->db->having("slot_loc_time > current_loc_time");
            } else {
                $this->db->where("date_available", $data['date']);
            }

            $this->db->select(
                    "id, 				
					doctor_id, 
					start_time,
					end_time,
					date_available,					
					TIME_FORMAT(
        CONVERT_TZ(NOW(), '+00:00', '" . $data['time_abbreviation'] . "'),
        '%H:%i:%s') AS current_loc_time,
		TIME_FORMAT(CONVERT_TZ(CONCAT(date_available,' ',`start_time`),'+00:00','" . $data['time_abbreviation'] . "'),'%H:%i:%s') AS slot_loc_time,CONVERT_TZ(CONCAT(date_available,' ',`start_time`),'+00:00','" . $data['time_abbreviation'] . "') AS current_date_time_slot_only");
            $this->db->from("doctor_slots_list");
            $q = $this->db->get();
            return $q->result_array();
        } else {
            return false;
        }
    }

    public function appointment_completed_model($id) {
        $query = $this->db->get_where("appointment", ['id' => $id]);
        if ($query->num_rows() > 0) {
            $this->db->where("id", $id);
            $this->db->update("appointment", ['status' => 6]);

            # on the doctor toggle if appointment is On-call
            $this->db->where("id IN(SELECT doctor_id FROM appointment WHERE id = '" . $id . "' AND treatment_provider_plan_id = '4')");
            $this->db->update("doctors", ['is_loggedin' => '1']);
            return true;
        } else {
            return false;
        }
    }

    public function getThirtyDaysSlotModel($data, $lang) {

        //dd($data);
        //$this->db->having("datetime BETWEEN '" . date($data['from']." H:i:s",strtotime('+30 minutes')) . "' AND '" . date($data['to']." H:i:s") . "'");
        $this->db->where("date_available BETWEEN '" . $data['from'] . "' AND '" . $data['to'] . "'");
        $this->db->where([
            "state" => $data['state'],
            "slot_status" => 0,
            "is_blocked" => 0,
        ]);
        $this->db->where("FIND_IN_SET('" . $data['speciality_id'] . "',spacility_id) AND date_of_birth IS NOT NULL");
        //$this->db->where("");
        //$this->db->where("language_id IN(3,(SELECT language_id FROM language WHERE lang_code ='" . $lang . "'))");
        $this->db->group_by("date_available");
        $this->db->select("CONCAT(date_available,' ',start_time) AS datetime ,date_available AS date");
        $this->db->from("doctor_slots_list");
        $query = $this->db->get();
        //dd($query->result_array());
        // echo $this->db->last_query();die;
        //die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function submited_prescription_model($data) {

        $query = $this->db->get_where("appointment", ['id' => $data['appointment_id']]);

        if ($query->num_rows() > 0) {
            $this->db->where("id", $data['appointment_id']);
            $this->db->update("appointment", ['is_submited' => 1]);
            //echo $this->db->last_query();die;
            return true;
        } else {
            return false;
        }
    }

    public function appointment_initiative_model($id) {
        //$this->db->where("patient_availability_date_and_time BETWEEN CURRENT_TIMESTAMP() AND DATE_ADD(CURRENT_TIMESTAMP(),INTERVAL 22 MINUTE)");
        $this->db->where("`patient_availability_date_and_time` <= CURRENT_TIMESTAMP() AND DATE_ADD(patient_availability_date_and_time,INTERVAL 22 MINUTE) >= CURRENT_TIMESTAMP()");
        $this->db->where([
            'id' => $id]);
        $this->db->select("id")->from("appointment");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $this->db->where("id", $id);
            $this->db->update("appointment", ['status' => 8]);
            return true;
        } else {
            return false;
        }
    }

    public function get_doctor_notification_data($data) {
        $json_data = [];
        $this->db->where("appointment_id", $data['appointment_id']);
        $this->db->select("appointment_id,doctor_id,user_id,
	                doctor AS name,type,(SELECT `device_token` FROM `users` WHERE id =(SELECT `user_id` FROM `appointment` WHERE id = '" . $data['appointment_id'] . "')) AS device_token");
        $query = $this->db->from("all_appointment")->get();
        return $query->row_array();
    }

    public function appointment_data_insert($data) {
        $this->db->insert("doctor_on_call_notification", $data);
    }

    public function appointment_visit_instruction_insert($data) {
        $insert_data = array();
        foreach ($data['visit_instruction'] as $value) {
            $insert_data[] = [
                "visit_instruction" => $value,
                "appointment_id" => $data['appointment_id']
            ];
        }
        $r = $this->db->insert_batch("appointment_visit_instruction", $insert_data);
        return ($r) ? true : false;
    }

    public function get_notification_user_data($appt_id) {
        $this->db->where("appointment.id", $appt_id);
        $this->db->select("appointment.id,users.id AS user_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS user_name,patient_info.profile_url AS user_profile_url,appointment.status, appointment.user_id,users.device_token AS user_device_token,appointment.time_abbreviation");
        $this->db->from("appointment");
        $this->db->join("users", "users.id = appointment.user_id", "INNER");
        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "INNER");
        //$this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $query = $this->db->get();
        return $query->row_array();
    }

    function insert_notification_user_data($apt_id, $response, $result) {
        $data = array();
        unset($result['user_device_token']);
        $data = [
            "notification_data" => json_encode(array_merge(["notification" => $response['title'], "data" => $result])),
            "fcm_response" => $response['fcm_resp'],
            "appointment_id" => $apt_id
        ];
        $this->db->insert("user_on_call_notification", $data);
    }

    public function get_info($data) {
        $this->db->select("CONCAT('Dr',' ',first_name,' ',last_name) AS doctor_name , (SELECT device_token FROM users WHERE id = '" . $data['user_id'] . "') AS user_device_token");
        $row = $this->db->from("doctors")->get();
        return $row->row_array();
    }

}

?>