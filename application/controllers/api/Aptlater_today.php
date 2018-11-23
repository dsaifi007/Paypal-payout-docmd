<?php

/**
  Set Crone job and also not need to authenticate of the user
 */
class Aptlater_today extends CI_Controller {

    protected $response_send;

    public function __construct() {
        parent::__construct();
    }

    // When request come from user for later today(Today 12pm only) appointment then
    // set the crone job daily (interval 15 minute)
    public function SetAppointmentToday() {
        //$this->
        $appointment_data = array();
        $this->db->where("DATE(schedule_date) = '" . date("Y-m-d") . "' AND doctor_id IS NULL");
        $query = $this->db->select("id,doctor_id,DATE(schedule_date) AS schedule_date")->from("appointment")->get();
        $result = $query->result_array();
        //dd($result);
        //echo $this->db->last_query();die;    
        if (count($result) > 0 && !empty($result)) {
            $this->load->model("api/on_call_appoinment_model", "appoinment_model");
            foreach ($result as $k => $v) {
                $appointment_data = $this->appoinment_model->getAppointmentData($v['id']); // id = appointment_id
                $appointment_data['symptom_ids'] = explode(",", $appointment_data['symptom_ids']);
                $this->FindCurrentDcotorByAction($appointment_data);
            }
        }
        dd($appointment_data);
        //$this->on_call_appoinment_model->getAppointmentData();
    }

    private function FindCurrentDcotorByAction($data) {

        $this->appoinment_data = $data;


        $av_doctor = $this->appoinment_model->getCurrentlyAvalDoctorOnTimeSlot($this->appoinment_data);
        $current_doct = $this->appoinment_model->GetToggleOnDoctor($this->appoinment_data);

        $doctor_ids = $this->finalBroadCastingDoctor($av_doctor, $current_doct);

        //dd($doctor_ids);
        //$doctor_ids = array_unique(array_merge(explode(",",$av_doctor['doctors_id']),explode(",",$current_doct['doctor_id'])));

        if (!empty($doctor_ids) && count($doctor_ids) > 0) {
            $get_doctor_device_tokens = $this->appoinment_model->get_doctor_device_token($doctor_ids);
            $patient_info = $this->appoinment_model->get_user_notification_data($this->appoinment_data['id'], $this->appoinment_data['patient_id']);
            $patient_info['data']['notify_time'] = $this->config->item("date");
            $total_device_token = explode("||||", $get_doctor_device_tokens['device_token']);

            // send the notification to all the doctor
            $response = $this->send_appointment_notification($total_device_token, $patient_info['notification'], $patient_info['data']);

            // store the notifaication data
            $this->appoinment_model->storeNotificationData($patient_info, $doctor_ids, $response, $this->appoinment_data['id']);

            //$this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => $this->appoinment_data['id'], "wait_for_physian" => true, "message" => "DOC MD is searching for a Physician near you..."];
        }
//        else {
//            $this->response_send = ["is_token_expire" => false, "status" => true, "wait_for_physian" => false, "appointment_id" => $this->appoinment_data['id'], "message" => "Sorry! Doctor not found...."];
//        }
        //return $this->response_send;
    }

    public function finalBroadCastingDoctor($setavldoctor, $toggledoctor) {
        $toggleOnDoctor = array();
        $final_doctor = array();
        if (!empty($toggledoctor['doctor_id'])) {
            $toggleOnDoctor = explode(",", $toggledoctor['doctor_id']);
        }
        //dd($setavldoctor);
        $current_avl_doctor = array();
        $booked_doctor = array();
        if (!empty($setavldoctor) && count($setavldoctor) > 0) {
            foreach ($setavldoctor as $k => $v) {
                if ($v['status'] == 1) {
                    $booked_doctor[] = $v['id'];
                } else {
                    $current_avl_doctor[] = $v['id'];
                }
            }
        }
        $final_doctor = array_unique(array_merge($current_avl_doctor, array_diff($toggleOnDoctor, $booked_doctor)));
        return $final_doctor;
    }

    private function send_appointment_notification($device_token, $message, $title) {
        try {

            $this->load->library("pushnotification");

            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($device_token, $message, $title);
            return $response;
        } catch (Exception $exc) {
            //$this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            //$this->response($this->response_send);
        }
    }

    // set crone job when doctor not recive the appointment within 15 minute
    // 15 minute crone
    public function DoctorNotRecivedAppointment() {
        $notification_data = array();
        
      
        //$this->db->insert("notification_sent_after_fifteen_minute",["appointment_id"=>324,"is_sent_notification"=>1]);


        $this->db->where("a.is_sent_notification",0);
        $this->db->where("appointment.doctor_id IS NULL AND DATE(appointment.created_date) = '" . date("Y-m-d") . "'");
        $this->db->select("appointment.id AS appointment_id,users.id AS user_id,appointment.patient_id,users.device_token,appointment.doctor_id,DATE_ADD(appointment.created_date,INTERVAL 1 MINUTE) AS created_date")->from("appointment");
        $this->db->join("users", "users.id=appointment.user_id", "INNER");
        $this->db->join("notification_sent_after_fifteen_minute AS a", "a.appointment_id=appointment.id", "INNER");

        $query = $this->db->get();

        //echo $this->db->last_query();die;
        $result = $query->result_array();
        //dd($result);
        //echo $this->db->last_query();die;    
        if (count($result) > 0 && !empty($result)) {
            $this->load->model("api/on_call_appoinment_model", "appoinment_model");
            foreach ($result as $k => $v) {
                //dd($result);
                if (($v['doctor_id'] == "" || $v['doctor_id'] == null ) && $v['created_date'] < $this->config->item("date")) {
                    $notification_data = $this->appoinment_model->get_user_notification_data($v['appointment_id'], $v['patient_id']); // id = appointment_id  
                                  
                    //$notification_data['notification']['title'] = "DOCMD";
                    $notification_data['notification']['body'] = "Your On-Call Request was not Accepted";
                    $notification_data['notification']['type'] = "ON_CALL_EXPIRE_APPT";
                    
                    unset($notification_data['data']['name']);
                    unset($notification_data['data']['age']);
                    unset($notification_data['data']['gender']);
                    unset($notification_data['data']['symptoms']);
                    //dd($notification_data);
                    $response = $this->send_appointment_notification($v['device_token'], $notification_data['data'], $notification_data['notification']);

                    $this->appoinment_model->userStoreNotificationData($notification_data, $v['user_id'], $response, $v['appointment_id']);

                    // change notification status for not sent again the notification
                    $this->db->where("appointment_id",$v['appointment_id']);
                    $this->db->update("notification_sent_after_fifteen_minute",['is_sent_notification'=>1]);
                    //$appointment_data['symptom_ids'] = explode(",", $appointment_data['symptom_ids']);
                }
            }
        }
        //dd($notification_data);
    }
    public function testing()
    {
      $this->db->insert("notification_sent_after_fifteen_minute",["appointment_id"=>508,"is_sent_notification"=>1]);die;
    }
}

?>