<?php

/**
 * 
 */
class Nonapi extends CI_Controller {

    protected $response_send;
    protected $language_file = "users/users";
    protected $pass_length = 7;

    function __construct() {
        parent::__construct();
        $this->lang->load([$this->language_file, "api_message"]);
    }

    public function index($reset_string) {
        $this->load->library('encrypt');
        $this->load->model("api/login_model");
        $this->load->library("session");
        $this->data["email_key"] = $reset_string;
        $this->load->view("updatepassword/update_pass", $this->data);
    }

    public function updtingpassword() {
        try {
            $this->load->library("session");
            $this->user_data = $this->input->post();
            if ($this->user_data['password'] != '' && $this->user_data['passconf'] != '' && $this->user_data['reset_code'] != '') {
                if ($this->user_data['password'] == $this->user_data['passconf']) {
                    if (strlen($this->user_data['password']) >= $this->pass_length) {
                        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $this->user_data['password'])) {
                            $this->load->library('encrypt');
                            $this->load->model("api/login_model");
                            $user_email = $this->encrypt->decode($this->user_data['reset_code']);
                            $return_resp = $this->login_model->checking_reset_code($user_email, $this->user_data['reset_code']);
                            if ($return_resp) {
                                $this->login_model->updating_pass($user_email, $this->user_data['password']);
                                $this->response_send = $this->lang->line('success_updated_pass');
                            } else {
                                $this->response_send = $this->lang->line('email_or_reset_key_invalid');
                            }
                        } else {
                            $this->response_send = $this->lang->line('pass_character_invalid');
                        }
                    } else {
                        $this->response_send = $this->lang->line('pass_length_invalid');
                    }
                } else {
                    $this->response_send = $this->lang->line('pass_not_match');
                }
            } else {
                $this->response_send = $this->lang->line('all_field_required');
            }
            $this->session->set_flashdata('message', $this->response_send);
            redirect("api/update_password/index/" . $this->user_data['reset_code']);
        } catch (Exception $exc) {
            //new Error($exc);
            echo 'Message: ' . $exc->getMessage();
            $this->exceptionhandler->handle($exc);
        }
    }

    public function emailauth($reset_code='') {
        //$this->load->library('encrypt');
        //echo $this->input->get('id');die;
        $this->load->model("api/user_model");
        $email = generateDecryptedString($this->input->get('id'));
        
        $response = $this->user_model->email_verified($email);
        
        if ($response) {
            $this->sent_create_account_mail($email, 15); // 15 is id of User approval 
            echo $this->lang->line('email_verifed');
        } else {
            echo $this->lang->line('wrong_url');
        }
        exit;
    }

    public function doctoremailauth($reset_code='') {
        //echo $this->input->get('id');die;
        //$this->load->library('encrypt');
        $this->load->model("api/doctor_model");
        $email = generateDecryptedString($this->input->get('id'));
        
        $response = $this->doctor_model->email_verified($email);
        if ($response) {
            $this->sent_create_account_mail($email, 14); // 14 is id of Provider approval 
            echo $this->lang->line('email_verifed');
        } else {
            echo $this->lang->line('wrong_url');
        }
        exit;
    }

    // after email verified successfully ,sent create account email to user/doctor  
    private function sent_create_account_mail($email, $id) {
        try {
            //$this->isModelload();
            $this->config->load('shared');

            $data = get_email_templates(["id" => $id]);
            $data['message'] = $data[0]['message'];
            $message = $this->load->view("auto_email_template", $data, TRUE);
            //$message = $data[0]['message'];
            $this->load->library("email_setting");
            $from = $this->config->item("from");

            $response = $this->email_setting->send_email($email, $from, $message, $data[0]["subject"]);
            return $response;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    // When user is inactive from last six month then send reminder email to user
    // set the crone job
    public function sendReminderEmailToUser() {
        $this->db->having("last_update_date", date("Y-m-d"));
        //$this->db->having("last_update_date","2019-01-15");
        $query = $this->db->select("email,DATE_ADD(DATE(last_update_date),INTERVAL 180 DAY) as last_update_date")->from("users")->get();
        $result = $query->result_array();
        //echo $this->db->last_query();die;
        if (!empty($result) && count($result) > 0) {
            foreach ($result as $key => $value) {
                $this->emails[] = $value['email'];
            }
            $this->sent_create_account_mail($this->emails, 16);
        }
    }

    // send email to user and doctor before the 15 minute of the appintment
    public function SendEmailToUserBeforeFifteenMinuteOfAppointment() {
        $this->db->insert("notification_sent_after_fifteen_minute",['appointment_id'=>573]);
        //die;
        $this->db->where("DATE(all_appointment.patient_availability_date_and_time)", date("Y-m-d"));
        $this->db->where("is_email_notf_sent", 0);
        $this->db->select("all_appointment.appointment_id,appointment.time_abbreviation,appointment.`patient_availability_date_and_time` AS utc_slot_time,CONVERT_TZ(appointment.`patient_availability_date_and_time`,'+00:00','-08:00') AS patient_availability_date_and_time,all_appointment.name,all_appointment.doctor,all_appointment.type,users.email,doctors.email AS doctor_email,DATE_SUB(all_appointment.`patient_availability_date_and_time`, INTERVAL 15 MINUTE) AS date_time");
        $this->db->from("all_appointment");
        $this->db->join("appointment", "appointment.id = all_appointment.appointment_id", "INNER");
        $this->db->join("users", "users.id = all_appointment.user_id", "INNER");
        $this->db->join("doctors", "doctors.id = all_appointment.doctor_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        $result = $query->result_array();
       
        if (!empty($result) && count($result) > 0) {
            foreach ($result as $key => $value) {
                $slot_time = get_time_zone($value['time_abbreviation'], $value['date_time']);
                
                if (strtotime($slot_time) == strtotime(date("Y-m-d H:i"))) {  
                    //$value['patient_availability_date_and_time'] = $value['patient_availability_date_and_time'];            
                    $a = $this->sendEmailToUser($value, 23);
                    $this->sendEmailToDoctor($value, 24);
                    //-------------- Update Status --------------------------------------//
                    $this->db->where("id", $value['appointment_id']);
                     $this->db->update("appointment", ["is_email_notf_sent" => 1]);
                }
            }
        }
    }

    public function sendEmailToUser($appointment_data, $template_id = 23) {
        try {

            $email_content = get_email_templates(["id" => $template_id]); // template fixed on 23 id
            $data2['message'] = $email_content[0]['message'];
            $data2['appointment_detail'] = $appointment_data;

            $message = $this->load->view("api_email_template/appointment_before_fifteen_user_template", $data2, TRUE);
            
            $this->load->library("email_setting");

            $response = $this->email_setting->send_email($appointment_data['email'], '', $message, $email_content[0]["subject"]);
            return $response;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function sendEmailToDoctor($appointment_data, $template_id = 24) {
        try {
            $email_content = get_email_templates(["id" => $template_id]); // template fixed on 23 id
            $data2['message'] = $email_content[0]['message'];
            $data2['appointment_detail'] = $appointment_data;

            $message = $this->load->view("api_email_template/appointment_before_fifteen_doctor_template", $data2, TRUE);

            $this->load->library("email_setting");

            $response = $this->email_setting->send_email($appointment_data['doctor_email'], '', $message, $email_content[0]["subject"]);
            return $response;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    // Send notification to the user/doctor if appointment is not initiative(Within 22-Minute) by the doctor/user  appointment date time
//    public function sendNotificationToUserProvider() {
//
//        // $this->db->insert("notification_sent_after_fifteen_minute",['appointment_id'=>475]);
//        // die;
//        $this->db->where("date(appointment.patient_availability_date_and_time) >=",date("Y-m-d"));
//        $this->db->where("appointment.status IN(1,4,5)");
//        $this->db->order_by("appointment.id", "DESC");
//        $this->db->select("appointment.id,appointment.status,appointment.doctor_id,appointment.user_id,DATE_ADD(appointment.patient_availability_date_and_time, INTERVAL 22 MINUTE) AS date_time,users.device_token AS user_device_token,doctors.device_token AS doctor_device_token");
//        $this->db->from("appointment");
//        $this->db->join("users", "users.id = appointment.user_id", "INNER");
//        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
//        $query = $this->db->get();
//
//
//        if ($query->num_rows() > 0) {
//            //echo $this->config->item("date") . "<br>";
//            $results = $query->result_array();
//            foreach ($results AS $result) {
//                //echo date("Y-m-d H:i",strtotime($result['date_time'])) ."==".date("Y-m-d H:i",strtotime($this->config->item("date")))."<br>";
//                if (date("Y-m-d H:i", strtotime($result['date_time'])) == date("Y-m-d H:i", strtotime($this->config->item("date")))) {
//                    //dd($results);
//                    # update the status
//                    $this->db->where("id", $result['id']);
//                    $this->db->update("appointment", ['status' => 7]);
//                    #End
//                    $this->send_appointment_notification_to_doctor($result);
//                    $this->send_appointment_notification_to_user($result);
//                }
//            }
//        }
//    }
    // Send notification to the user/doctor if appointment is not initiative(Within 22-Minute) by the doctor/user  appointment date time
    public function sendNotificationToUserProvider() {

         
        $this->db->where("date(appointment.patient_availability_date_and_time) =", date("Y-m-d"));
        $this->db->where("appointment.status IN(1,4,5)");
        $this->db->order_by("appointment.id", "DESC");
        $this->db->select("appointment.id,appointment.status,appointment.doctor_id,appointment.user_id,DATE_ADD(appointment.patient_availability_date_and_time, INTERVAL 23 MINUTE) AS date_time,users.device_token AS user_device_token,appointment.time_abbreviation,doctors.device_token AS doctor_device_token");
        $this->db->from("appointment");
        $this->db->join("users", "users.id = appointment.user_id", "INNER");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            //echo $this->config->item("date") . "<br>";
            $results = $query->result_array();

            foreach ($results AS $result) {
                $slot_time = get_time_zone($result['time_abbreviation'], $result['date_time']);
                
                if ($slot_time == date("Y-m-d H:i")) {
                    # update the status
                    $this->db->where("id", $result['id']);
                    $this->db->update("appointment", ['status' => 7]);
                    #End
                    $this->send_appointment_notification_to_doctor($result);
                    $this->send_appointment_notification_to_user($result);
                }
            }
        }
    }

    private function send_appointment_notification_to_doctor($data) {
        try {
            $this->load->library("pushnotification");

            $notification_data = array();
            $title = array(
                'title' => 'Missed Appointment',
                'body' => "Uh oh! It looks like you missed your recent DOC MD appointment. ",
                "type" => "MISSED_APPT_DOCTOR"
            );
            $message = [
                "appointment_id" => $data['id'],
                "status" => $data['status'],
                "notify_time" => $this->config->item("date")
            ];
            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($data['doctor_device_token'], $message, $title);

            $insert_data = [
                "appointment_id" => $data['id'],
                "doctor_id" => $data['doctor_id'],
                "notification_data" => json_encode(array_merge(["notification" => $title], ["data" => $message])),
                "fcm_response" => $response,
                "created_date" => $this->config->item("date")
            ];
            $this->db->insert("doctor_on_call_notification", $insert_data);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    private function send_appointment_notification_to_user($data1) {
        try {
            $this->load->library("pushnotification");

            $user_message = array();
            $title1 = array(
                'title' => 'Missed Appointment',
                'body' => ((int) $data['status'] == 8) ? "Oops! It looks like you missed your DOC MD appointment" : 'Oops! It looks like your Doctor was not available for your DOC MD appointment',
                "type" => "MISSED_APPT_USER"
            );
            $user_message = [
                "appointment_id" => $data1['id'],
                "status" => $data1['status'],
                "notify_time" => $this->config->item("date")
            ];
            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($data1['user_device_token'], $user_message, $title1);

            $insert_data = [
                "appointment_id" => $data1['id'],
                "doctor_id" => $data1['doctor_id'],
                "user_id" => $data1['user_id'],
                "notification_data" => json_encode(array_merge($title1, ["data" => $user_message])),
                "fcm_response" => $response,
                "created_date" => $this->config->item("date")
            ];
            $this->db->insert("user_on_call_notification", $insert_data);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
     * Send notification before the five minute from the starting time of the appointment created by the user send to the doctor 
     */

    public function sendNotificationToDoctorBeforeFiveMinuteOfApppointment() {
        // under construction
        $this->db->where("appointment.status IN(1,4,5)");
        $this->db->where("date(appointment.patient_availability_date_and_time) >=", date("Y-m-d"));
        $this->db->order_by("appointment.id", "DESC");
        $this->db->select("appointment.id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS patient_name,patient_info.profile_url AS patient_profile_url,CONCAT(doctors.first_name,' ',doctors.last_name) AS doctor_name,doctors.profile_url AS doctor_profile,appointment.time_abbreviation,provider_plan.title AS type,appointment.doctor_id,appointment.user_id,DATE_SUB(appointment.patient_availability_date_and_time, INTERVAL 5 MINUTE) AS date_time,doctors.device_token AS doctor_device_token,users.device_token");
        $this->db->from("appointment");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $this->db->join("users", "users.id = appointment.user_id", "INNER");
        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "INNER");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if ($query->num_rows() > 0) {
            //echo $this->config->item("date") . "<br>";
            $results = $query->result_array();
            foreach ($results AS $result) {
                $slot_time = get_time_zone($result['time_abbreviation'], $result['date_time']);
                //echo date("Y-m-d H:i")."<br>".$slot_time;
                if (date("Y-m-d H:i") == $slot_time) {
                    $this->load->library("pushnotification");
                    $this->send_notf_to_doctor($result);
                    $this->send_notf_to_users($result);
                }
            }
        }
    }

    private function send_notf_to_doctor($data1) {
        try {
            $user_message = array();
            $title1 = array(
                    'title' => 'Upcoming Appointment',
                    'body' => 'Just a quick heads up.  Your DOC MD appointment is in 5 minutes!',
                    "type" => "UPCMG_APPT"
            );
            $user_message = [
                "appointment_id" => $data1['id'],
                "type" => $data1['type'],
                "name" => $data1['patient_name'],
                "profile_url" => $data1['patient_profile_url'],
                "notify_time" => $this->config->item("date")
            ];
            // send the notification to FCM
            $response1 = $this->pushnotification->sendPushNotificationToFCMSever($data1['doctor_device_token'], $user_message, $title1);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    private function send_notf_to_users($data1) {
        try {
            $doctor_message = array();
            $title2 = array(
                    'title' => 'Upcoming Appointment',
                    'body' => 'Reminder: DOC MD appointment in 5 minutes!',
                    "type" => "UPCMG_USER_APPT"
            );
            $doctor_message = [
                "appointment_id" => $data1['id'],
                "type" => $data1['type'],
                "name" => $data1['doctor_name'],
                "profile_url" => $data1['doctor_profile'],
                "notify_time" => $this->config->item("date")
            ];
            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($data1['device_token'], $doctor_message, $title2);
           
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    // Send Notification To the doctor if doctor did not set availability from last 15 days
    function sendNotificationToProvider() {
        $this->db->where("id NOT IN(SELECT doctor_id FROM doctor_slots_list WHERE date_available >= DATE_ADD(CURRENT_DATE() ,INTERVAL -15 DAY) GROUP BY doctor_id)");
        $result = $this->db->select("id ,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url")->from("doctors")->get();
        if ($result->num_rows() > 0) {
            $r = $result->result_array();
            foreach ($r as $k => $v) {
                send_notification($this->lang->line('avail_missing_title'), sprintf($this->lang->line('avail_missing_body'), $v['name']), $this->lang->line('avail_missing_constant'), $v['device_token'], $r[$k]);
            }
        }
    }

    // Send Notification To the doctor if doctor did not set availability from last 30 days
    function sendNotificationToProviderAfterThirtyDays() {
        $this->db->where("id NOT IN(SELECT doctor_id FROM doctor_slots_list WHERE date_available >= DATE_ADD(CURRENT_DATE() ,INTERVAL -30 DAY) GROUP BY doctor_id)");
        $result = $this->db->select("id ,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url")->from("doctors")->get();
        if ($result->num_rows() > 0) {
            $r = $result->result_array();
            foreach ($r as $k => $v) {
                send_notification($this->lang->line('avail_missing_title'), sprintf($this->lang->line('avail_missing_body'), $v['name']), $this->lang->line('avail_missing_constant1'), $v['device_token'], $r[$k]);
            }
        }
    }

    // Send Notification To the doctor if doctor did not set availability from last 30 days
    function sendNotificationToProviderAfterSixtyDays() {
        $this->db->where("id NOT IN(SELECT doctor_id FROM doctor_slots_list WHERE date_available >= DATE_ADD(CURRENT_DATE() ,INTERVAL -60 DAY) GROUP BY doctor_id)");
        $result = $this->db->select("id ,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url")->from("doctors")->get();
        if ($result->num_rows() > 0) {
            $r = $result->result_array();
            foreach ($r as $k => $v) {
                send_notification($this->lang->line('avail_missing_title'), sprintf($this->lang->line('avail_missing_body'), $v['name']), $this->lang->line('avail_missing_constant2'), $v['device_token'], $r[$k]);
            }
        }
    }

    // Send Notification To the doctor if doctor did not set availability from last 90 days
    function sendNotificationToProviderAfterNinetyDays() {
        $this->db->where("id NOT IN(SELECT doctor_id FROM doctor_slots_list WHERE date_available >= DATE_ADD(CURRENT_DATE() ,INTERVAL -90 DAY) GROUP BY doctor_id)");
        $result = $this->db->select("id ,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url")->from("doctors")->get();
        if ($result->num_rows() > 0) {
            $r = $result->result_array();
            foreach ($r as $k => $v) {
                send_notification($this->lang->line('avail_missing_title'), sprintf($this->lang->line('avail_missing_body'), $v['name']), $this->lang->line('avail_missing_constant3'), $v['device_token'], $r[$k]);
            }
        }
    }

    // Send Notification To the doctor if doctor did not set availability from last 180 days
    function sendNotificationToProviderAfterOneEightytyDays() {
        $this->db->where("id NOT IN(SELECT doctor_id FROM doctor_slots_list WHERE date_available >= DATE_ADD(CURRENT_DATE() ,INTERVAL -180 DAY) GROUP BY doctor_id)");
        $result = $this->db->select("id ,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url")->from("doctors")->get();
        if ($result->num_rows() > 0) {
            $r = $result->result_array();
            foreach ($r as $k => $v) {
                send_notification($this->lang->line('avail_missing_title'), sprintf($this->lang->line('avail_missing_body'), $v['name']), $this->lang->line('avail_missing_constant4'), $v['device_token'], $r[$k]);
            }
        }
    }

    // If provider not set profile image within 7 days send notification
    function providerNotsetProfileImageWithinSevenDays() {
        $this->db->where("date(DATE_ADD(created_date, INTERVAL 7 DAY)) <= CURRENT_DATE() AND profile_url IS NULL");
        $q = $this->db->select("id,CONCAT(first_name,' ',last_name) AS name,device_token,profile_url,created_date")->from("doctors")->get();
        //echo $this->db->last_query();die;
        if ($q->num_rows() > 0) {
            $r = $q->result_array();
            foreach ($r as $k => $v) {
                $response = send_notification($this->lang->line('profile_title'), sprintf($this->lang->line('profile_body'), $v['name']), $this->lang->line('profile_constant'), $v['device_token'], $r[$k]);                                          
            }
        }
    }
}

?>