<?php

class On_call_appoinment_model extends CI_Model {

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

    public function on_call_insert_appoinment_detail($data) {
        $insert_data = $data;
        $insert_data['created_date'] = date("Y-m-d H:i:s");
        $insert_data['patient_availability_date '] = date("Y-m-d");
        $insert_data['patient_availability_time '] = date("H:i:s");
        $insert_data['patient_availability_date_and_time '] = date("Y-m-d H:i:s");
        unset($insert_data['symptom_ids']);
        unset($insert_data['payment_method_type']);
        unset($insert_data['payment_method_nonce']);
        unset($insert_data['paypal_email']);
        unset($insert_data['venmo_id']);
        // Insert the Appoinment data in table
        $this->db->trans_start();
        $this->db->insert("appointment", $insert_data);
        $last_appoinment_id = $this->db->insert_id();

        // Making the array for mapping the appointment id and symptoms id
        foreach ($data['symptom_ids'] as $key => $symptom_id) {
            $this->symptoms_array[] = [
                "appointment_id" => $last_appoinment_id,
                "symptom_id" => $symptom_id
            ];
        }
        $this->db->insert_batch("appointment_symptom", $this->symptoms_array);

        // save the notification status for the on call appointment
        $this->db->insert("notification_sent_after_fifteen_minute", ["appointment_id" => $last_appoinment_id]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            // Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            // Everything is Perfect. 
            // Committing data to the database.
            $this->db->trans_commit();
            return $last_appoinment_id;
        }
    }

    public function getCurrentlyAvalDoctorOnTimeSlot($data, $lang = "eng", $doctor_id = null) {
        // for appoointment later today
        if ($doctor_id != null) {
            $this->db->where("doctors.id", $doctor_id);
        }
//        $this->db->where("(TIMESTAMP(date_availability_list.date_available,hour_list.end_time)
//             BETWEEN '" . $this->config->item("date") . "' AND '" . date('Y-m-d H:i:s', strtotime('+30 minutes')) . "')");
        $this->db->where("address.state IN (SELECT state FROM address
             WHERE id IN (SELECT address_id FROM patient_address WHERE 
                patient_id ='" . $data['patient_id'] . "'))", null, false);
        $this->db->where("doctors.date_of_birth IS NOT NULL");
        $this->db->where("doctor_slots.status IN(0,1)");
        $this->db->where("language.lang_code =('eng-spn' OR '" . $lang . "')");
        $condition = [
            "doctors.is_blocked" => 0,
            "doctors.is_loggedin" => '1',
            "doctor_speciality.spacility_id" => $data['doc_speciality_id']
        ];
        $this->db->where($condition);
        $this->db->select("
             doctors.id,doctor_slots.status
             ")->from("doctor_slots");
        $this->db->join("date_availability_list", "date_availability_list.id = doctor_slots.date_id", "INNER");
        $this->db->join("hour_list", "hour_list.id = doctor_slots.slot_id", "INNER");
        $this->db->join("doctors", "doctors.id = doctor_slots.doctor_id", "INNER");
        $this->db->join("doctor_speciality", "doctors.id = doctor_speciality.doctor_id", "INNER");
        $this->db->join("doctor_address", "doctor_address.doctor_id = doctors.id", "INNER");
        $this->db->join("address", "address.id = doctor_address.address_id", "INNER");
        $this->db->join("language", "language.language_id = doctors.language_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;  
        //doctors.date_of_birth IS NOT NULL
        //"address.state = "IL"
        //( TIMESTAMP(date_availability_list.date_available,hour_list.end_time) BETWEEN "2018-07-13 13:45:00" AND "2018-07-13 14:15:00" )"]
        return ($query->num_rows() > 0) ? $query->result_array() : array();
        //dd($query->row_array());
    }

    // get the currently available doctors which is toggle on
    public function GetToggleOnDoctor($data1, $lang1 = "eng", $doctor_id = null) {
        /*
          on_call.doctor_id,
          on_call.doctor_time,
          dct.email,
          dct.is_loggedin,
          CONCAT(
          dct.first_name,
          dct.last_name
          ) AS user_name,
          dct.is_blocked,
          dct.date_of_birth,

          doctor_speciality.spacility_id
         */
        // for appoointment later today
        if ($doctor_id != null) {
            $this->db->where("dct.id", $doctor_id);
        }
        $this->db->where("(on_call.doctor_id IS NULL OR
                 on_call.doctor_time < '" . $this->config->item("date") . "')");
        $this->db->where("address.state IN (SELECT state FROM address
             WHERE id IN (SELECT address_id FROM patient_address WHERE 
                patient_id ='" . $data1['patient_id'] . "'))", null, false);
        $this->db->where("dct.date_of_birth IS NOT NULL");
        $this->db->where("lang.lang_code =('eng-spn' OR '" . $lang1 . "')");
        $condition = [
            "dct.is_blocked" => 0,
            "doctor_speciality.spacility_id" => $data1['doc_speciality_id'],
            "dct.is_loggedin" => '1'
        ];
        $this->db->where($condition);
        $this->db->select("GROUP_CONCAT(dct.id) AS doctor_id")->from("doctors AS dct");
        $this->db->join("on_going_appointment_date_time AS on_call", "on_call.doctor_id = dct.id", "LEFT");
        $this->db->join("doctor_speciality", "dct.id = doctor_speciality.doctor_id", "INNER");
        $this->db->join("doctor_address", "doctor_address.doctor_id = dct.id", "INNER");
        $this->db->join("address", "address.id = doctor_address.address_id", "INNER");
        $this->db->join("language AS lang", "lang.language_id = dct.language_id", "INNER");

        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->row_array() : array();
        //dd($query->row_array():array());;
        //echo $this->db->last_query();die;
    }

    /*
      ---------------------------------------------------------------------------------
      | get the current user device token
      ---------------------------------------------------------------------------------
     */

    public function get_doctor_device_token($doctor_ids) {
        $this->db->select("GROUP_CONCAT(device_token SEPARATOR '||||') AS device_token")->from("doctors");
        $this->db->where_in("id", $doctor_ids);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_user_device_token($user_id = null, $appointment_id = null) {
        $this->db->select("device_token")->from("users");
        if ($user_id != null) {
            $this->db->where("id", $user_id);
        } else {
            $this->db->where("id IN(SELECT user_id from appointment where id ='" . $appointment_id . "')");
        }
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->row_array();
    }

    /*
      ---------------------------------------------------------------------------------
      | get the array structure and insert the notification data from DB
      ---------------------------------------------------------------------------------
     */

    // Send this notification to all doctor 
    public function get_user_notification_data($appointment_id, $patient_id) {
        $json_data = [];
        $this->db->where("appointment_id", $appointment_id);
        $this->db->select("appointment_id,name,age,gender,symptoms,(SELECT `profile_url` FROM `patient_info` WHERE id = '" . $patient_id . "') AS profile_url,type");
        $query = $this->db->from("all_appointment")->get();
        $json_data = array("notification" => array(
                "title" => "New On-Call Request!",
                "body" => $this->lang->line("new_on_call_request_body"),
                "type" => $this->lang->line("new_on_call_request_constant") //"ON_CALL_APPT",
            ),
            "data" => $query->row_array()
        );
        /* $fields = array(
          'registration_ids' => array(1),
          'priority' => 10,
          "notification"=>$json_data
          ); */
        return $json_data;
    }

    // after accept the appointment by the current doctor then send notification to particular user
    public function get_doctor_notification_data($data) {
        $json_data = [];
        $this->db->where("appointment_id", $data['appointment_id']);
        $this->db->select("appointment_id,doctor_id,user_id,
                doctor AS name,type,(SELECT `profile_url` FROM `doctors` WHERE id = '" . $data['doctor_id'] . "') AS profile_url,
                (SELECT GROUP_CONCAT(degree) AS degree FROM doctor_degree INNER JOIN doctor_degree_mapping AS a ON a.degree_id = doctor_degree.id WHERE a.doctor_id='" . $data['doctor_id'] . "') AS degree");
        $query = $this->db->from("all_appointment")->get();
        $json_data = array(
            "title" => $this->lang->line("doctor_located"),
            "body" => sprintf($this->lang->line("doctor_located_body"), $query->row_array()['name']), //"Your On-Call Request Accepted by " . $query->row_array()['name'] . "",
            "type" => $this->lang->line("doctor_located_constant"),
            "data" => $query->row_array()
        );
        return $json_data;
    }

    public function getDoctorIdAndCreateDate($id) {
        $this->db->where("id", $id['appointment_id']);
        //$this->db->where("doctor_id IS NULL");
        $query = $this->db->select("doctor_id,
        (created_date+INTERVAL 15 MINUTE) AS created_date,time_abbreviation,
        (SELECT device_token FROM `doctors` WHERE id = '" . $id['doctor_id'] . "')
        AS device_token
        ")->from("appointment")->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    public function updateDoctorIdAndTime($data) {
        //---------------------------------------------------------------------
        $this->db->where("id", $data['appointment_id']);
        $this->db->update("appointment", ['doctor_id' => $data['doctor_id']]);

        //---------------------------------------------------------------------
        $query = $this->db->get_where("on_going_appointment_date_time", ["doctor_id" => $data['doctor_id']]);
        if ($query->num_rows() > 0) {
            $this->db->where("doctor_id", $data['doctor_id']);
            $this->db->update("on_going_appointment_date_time", ["doctor_time" => date("Y-m-d H:i:s", strtotime('+30 minutes'))]);
        } else {
            $this->db->insert("on_going_appointment_date_time", [
                'doctor_id' => $data['doctor_id'],
                "doctor_time" => date("Y-m-d H:i:s", strtotime('+30 minutes'))
            ]);
        }
        //---------------------------------------------------------------------
        // you can also use date for doctor
        $query = "id IN(SELECT id FROM (SELECT 
    doctor_slots.id
    FROM
    `doctor_slots`
    INNER JOIN date_availability_list ON date_availability_list.id = doctor_slots.date_id
    INNER JOIN hour_list ON hour_list.id = doctor_slots.slot_id
    INNER JOIN doctors ON doctors.id = doctor_slots.doctor_id
    WHERE doctor_slots.doctor_id = '" . $data['doctor_id'] . "' AND 
                ( TIMESTAMP(date_availability_list.date_available,hour_list.end_time)
                BETWEEN '" . date('Y-m-d H:i:s') . "'AND'" . date('Y-m-d H:i:s', strtotime('+60 minutes')) . "')) AS doctor_slot_table)";
        $this->db->where($query);
        $this->db->where("doctor_id", $data['doctor_id']);
        $this->db->update("doctor_slots", ['status' => 1]);
    }

    public function storeNotificationData($notification, $doctor_ids, $fcm_response, $appt_id) {
        $post_data = array();
        $notification['data']['notify_time'] = $this->config->item("date");

        foreach ($doctor_ids as $value) {
            $post_data[] = [
                "appointment_id" => $appt_id,
                "doctor_id" => $value,
                "notification_data" => json_encode($notification),
                "fcm_response" => $fcm_response,
                "created_date" => $this->config->item("date")
            ];
        }
        $this->db->insert_batch("doctor_on_call_notification", $post_data);
        return true;
    }

    public function userStoreNotificationData($notification, $user_id, $fcm_response, $appt_id, $doctor_id = NULL) {
        $post_data = array();
        $notification['data']['notify_time'] = $this->config->item("date");
        $post_data = [
            "appointment_id" => $appt_id,
            "user_id" => $user_id,
            "doctor_id" => $doctor_id,
            "notification_data" => json_encode($notification),
            "fcm_response" => $fcm_response,
            "created_date" => $this->config->item("date")
        ];
        $this->db->insert("user_on_call_notification", $post_data);
        return true;
    }

    // this function will get the free slot of doctor if no 
    // slot is available then return false  
    public function user_appointment_booking_model($data, $language) {

        $this->db->where("doctor_slots.date_id IN(SELECT id 
       FROM   `date_availability_list` 
       WHERE  date_available ='" . $data['date'] . "')");
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
        doctor_slots.doctor_id, 
        hour_list.start_time,
        hour_list.end_time");
        $this->db->from("doctor_slots");
        $this->db->join('hour_list', 'hour_list.id = doctor_slots.slot_id', "LEFT");
        $this->db->join('doctors', 'doctor_slots.doctor_id= doctors.id', "LEFT");
        $this->db->join('doctor_speciality', 'doctor_speciality.doctor_id = doctors.id', "LEFT");
        $this->db->join('spacility', 'spacility.id = doctor_speciality.spacility_id', "LEFT");
        $this->db->join('doctor_address', 'doctor_address.doctor_id = doctor_slots.doctor_id', "LEFT");
        $this->db->join('address', 'address.id = doctor_address.address_id ', "LEFT");
        $this->db->join('language', 'language.language_id = doctors.language_id ', "LEFT");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        // when we will get the doctor id then get all free slot of the them(doctor)

        if (count($query->row_array() > 0) && !empty($query->row_array())) {

            $result = $query->row_array();

            // get the all free slot of the selected doctor from above
            if ($data['date'] == date("Y-m-d")) {
                $this->db->where("date_availability_list.date_available", $data['date']);
                $this->db->where("hour_list.start_time > ", date("H:i:s"));
            } else {
                $this->db->where("date_availability_list.date_available", $data['date']);
            }
            $this->db->where("doctor_slots.doctor_id", $result['doctor_id']);
            $this->db->where("doctor_slots.status", 0);
            $this->db->select(
                    "doctor_slots.id, 
            doctor_slots.doctor_id, 
            hour_list.start_time,
            hour_list.end_time");
            $this->db->from("doctor_slots");
            $this->db->join('hour_list', 'hour_list.id = doctor_slots.slot_id', "INNER");
            $this->db->join('date_availability_list', 'date_availability_list.id = doctor_slots.date_id', "LEFT");

            $q = $this->db->get();

            return $q->result_array();
        } else {
            return false;
        }
    }

    public function getAppointmentData($id) {
        $this->db->group_by("appointment_symptom.appointment_id");
        $this->db->having("appointment_symptom.appointment_id", $id);
        $this->db->select("appointment.id,
                appointment.user_id,appointment.patient_id,GROUP_CONCAT(appointment_symptom.symptom_id) AS symptom_ids,appointment.doc_speciality_id,
                appointment.payment_method_id,appointment.severity_of_symptom_id,appointment.treatment_provider_plan_id,
                appointment.symptom_start_date")->from("appointment");
        $this->db->join("appointment_symptom", "appointment_symptom.appointment_id=appointment.id", "INNER");
        $query = $this->db->get();
        return $query->row_array();
    }

    public function appointment_cancel_model($id) {
        $query = $this->db->get_where("appointment", ['id' => $id]);
        if ($query->num_rows() > 0) {
            $this->db->where("id", $id);
            $this->db->update("appointment", ['status' => 2]);
            return true;
        } else {
            return false;
        }
    }

    // sotre the current date
    public function appointment_booking_later_today_model($data) {

        $this->db->insert("later_today_appointment", ['appointment_id' => $data['appointment_id'], "created_date" => $this->config->item("date")]);
        //$this->db->where("id", $id);
        // $this->db->update("appointment", ['schedule_date' => $this->config->item("date")]);
    }

}

?>