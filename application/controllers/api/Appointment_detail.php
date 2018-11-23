<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Appointment_detail extends REST_Controller {

    protected $response_send = ["status" => false];
    protected $language_file = ["api_message", "spn_api_message"];
    protected $headers;
    protected $appointment_request;

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will check the content type and change the language
      |------------------------------------------------------------------------------------------------------------
     */

    public function __construct() {
        try {
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            change_languge($this->headers, $this->language_file);
            $this->load->model("api/appointment_detail_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will check the content type and change the language
      |------------------------------------------------------------------------------------------------------------
     */

    public function get_appointment_by_user_get() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $appintment_data = $this->get();
            //dd($appintment_data);
            if (count($appintment_data) > 0) {
                $result = $this->appointment_detail_model->get_appointment_by_user_model($appintment_data, @$this->headers['Accept-Language']);
                // dd($result);
                if ($result) {
                    //dd($result);
                    $result['doctor'] = array_combine(["id", "first_name", "last_name", "profile_image_url", "state", "med_id", "zip_code", "gender"], explode("|", $result['doctor']));
                    $result['doctor']["age"] = $result["age"];
                    $result['doctor']['degree'] = $result['degree'];
                    $result['doctor']['doctor_speciality'] = $result['doctor_speciality'];
                    $result['appointment_status'] = array_combine(["id", "status"], explode("|", $result['appointment_status']));
                    $result['symptoms'] = explode("_", $result['symptoms']);
                    $result['provider_plan'] = array_combine(["title", "amount", "is_recommended", "type"], explode("|", $result['provider_plan']));
                    $result['provider_plan']['is_recommended'] = ($result['provider_plan']['is_recommended'] == 1) ? true : false;
                    $result['patient'] = array_combine(["patient_id", "med_id", "first_name", "last_name", "profile_url"], explode("||", $result['patient']));
                    $result['patient']['state'] = $result["state"];
                    $result['appointment_instruction'] = array_filter(explode('||', $result['appointment_instruction'] . '||' . $result['visit_inst']));
                    $result['payment_method'] = (!empty($result['payment_method']))?array_combine(["card_number","brand","card_name"],explode('|', $result['payment_method'])):["paypal_email"=>$result["paypal_email"],"venmo_id"=>$result["venmo_id"]];
                    //dd($result);
                    unset($result["age"]);
                    unset($result["state"]);
                    unset($result["visit_inst"]);
                    unset($result["degree"]);
                    unset($result["doctor_speciality"]);
                    unset($result['paypal_email']);
                    unset($result['venmo_id']);
                    $this->response_send = ["appointment" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["status" => false, "message" => $this->lang->line("user_id_not_exist")];
                }
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function get_appointment_by_doctor_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $appintment_data = $this->get();
            //dd($appintment_data);
            if (count($appintment_data) > 0) {
                $result = $this->appointment_detail_model->get_appointment_by_doctor_model($appintment_data, @$this->headers['Accept-Language']);
                if ($result) {
                    $result['patient'] = array_merge(array_combine(["id", "first_name", "last_name", "gender", "zip_code", "state", "profile_image_url", "med_id"], explode("|", $result['patient'])), ["age" => $result['age']]);
                    unset($result['age']);

                    $result['appointment_status'] = array_combine(["id", "status"], explode("|", $result['appointment_status']));
                    $result['symptoms'] = explode("_", $result['symptoms']);
                    $result['provider_plan'] = array_combine(["title", "amount", "is_recommended", "type"], explode("|", $result['provider_plan']));
                    $result['provider_plan']['is_recommended'] = ($result['provider_plan']['is_recommended'] == 1) ? true : false;
                    $result['appointment_instruction'] = array_filter(explode("||", $result['appointment_instruction'] . '||' . $result['visit_inst']));
                    unset($result['visit_inst']);
                    $this->response_send = ["appointment" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["status" => false, "message" => $this->lang->line("user_id_not_exist")];
                }
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function cancel_appointment_by_user_get() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $cancel_data = $this->get();
            if (count($cancel_data) > 0) {
                $query = $this->db->get_where('appointment', array('id' => $cancel_data['appointment_id']));
                if ($query->num_rows() == 1) {
                    $this->db->where("id", $cancel_data['appointment_id']);
                    $this->db->update("appointment", ['status' => $this->config->item("appointment_cancel_by_patient")]);

                    $this->db->where("id IN(SELECT slot_id from appointment where id = '" . $cancel_data['appointment_id'] . "')", NULL, FALSE);
                    $this->db->update("doctor_slots", ['status' => 0]);
                    $this->response_send = ['status' => true];

                    # Send Notification
                    $this->sendNotificationProvider($cancel_data['appointment_id']);
                    $this->sendNotificationUser($cancel_data['appointment_id']);

                    $this->sendEmailToUser($cancel_data['appointment_id']);
                    $this->sendEmailToDoctor($cancel_data['appointment_id']);
                } else {
                    $this->response_send = ['status' => false, "message" => $this->lang->line("user_id_not_exist")];
                }
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function cancel_appointment_by_doctor_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, 'doctors');
            $cancel_data = $this->get();
            if (count($cancel_data) > 0) {
                $query = $this->db->get_where('appointment', array('id' => $cancel_data['appointment_id']));

                if ($query->num_rows() == 1) {
                    $this->db->where("id", $cancel_data['appointment_id']);
                    $this->db->update("appointment", ['status' => $this->config->item("appointment_cancel_by_doctor")]);


                    $this->db->where("id IN(SELECT slot_id from appointment where id = '" . $cancel_data['appointment_id'] . "')", NULL, FALSE);
                    $this->db->update("doctor_slots", ['status' => 0]);

                    $this->response_send = ['status' => true];
                    
                    $this->sendNotificationProvider($cancel_data['appointment_id']);
                    $this->sendNotificationUser($cancel_data['appointment_id']);

                    $this->sendEmailToUser($cancel_data['appointment_id']);
                    $this->sendEmailToDoctor($cancel_data['appointment_id']);
                } else {
                    $this->response_send = ['status' => false, "message" => $this->lang->line("user_id_not_exist")];
                }
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function sendNotificationProvider($appt_id) {
        $notification_data = array();
        $data = $this->appointment_detail_model->getUserDoctorNotficationData($appt_id);      
        $result = ["appointment_id"=>$data['id'],"name"=>$data['doctor_name'],"profile_url"=>$data['doctor_profile_url'],"notifiy_time"=>$this->config->item("date")];
        $response = send_notification($this->lang->line('appt_canceled'), sprintf($this->lang->line('appt_canceled_body'), $data['doctor_name']), $this->lang->line('appt_canceled_constant'), $data['doctor_device_token'], $result);
        $inser_array = json_encode(["notification"=>$response['title'],"data"=>$result]);
        $this->db->insert("doctor_on_call_notification",["appointment_id"=>$appt_id,"doctor_id"=>$data['doctor_id'],"notification_data"=>$inser_array,"fcm_response"=>$response['fcm_resp'],"created_date"=>$this->config->item("date")]);
    }
    private function sendNotificationUser($appt_id) {
        $notification_data = array();
        $data1 = $this->appointment_detail_model->getUserDoctorNotficationData($appt_id);      
        $result1 = ["appointment_id"=>$data1['id'],"name"=>$data1['user_name'],"profile_url"=>$data1['user_profile_url'],"notifiy_time"=>$this->config->item("date")];
        $response1 = send_notification($this->lang->line('appt_canceled'), sprintf($this->lang->line('appt_canceled_body'), $data1['user_name']), $this->lang->line('appt_canceled_constant_by_user'), $data1['user_device_token'], $result1);
        $inser_array1 = json_encode(["notification"=>$response1['title'],"data"=>$result1]);
        $this->db->insert("user_on_call_notification",["appointment_id"=>$appt_id,"user_id"=>$data1['user_id'],"notification_data"=>$inser_array1,"fcm_response"=>$response1['fcm_resp'],"created_date"=>$this->config->item("date")]);
    }
    public function appointment_reschedule_by_user_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $reschedule_data = json_decode(file_get_contents('php://input'), true);
            $field_name = ['doctor_id', 'slot_id', "appointment_id", "last_appointment_date"];
            if (check_form_array_keys_existance($reschedule_data, $field_name) && check_user_input_values($reschedule_data)) {

                $result = $this->appointment_detail_model->appointment_reschedule_by_user_model($reschedule_data);
                if ($result) {
                    // send notification 
                    $notification_title = array(
                        "title" => $this->lang->line("app_reschedule") ,//"Appointment Rescheduled",
                        "body" => $this->lang->line("app_reschedule_body") ,//"You received Rescheduled Appointment",
                        "type" => $this->lang->line("app_reschedule_constant") //"RESCH_APPT_BY_PT"
                    );
                    $user_data = explode("||", $result['doctor_profile']);
                    $message = [
                        "appointment_id" => $reschedule_data['appointment_id'],
                        "name" => $user_data[0],
                        "profile_url" => ($user_data[1]) ? $user_data[1] : null,
                        "type" => $result['type'],
                        "notify_time" => $this->config->item("date")];
                    //dd($message);
                    $fcm_response = $this->send_appointment_notification($result['device_token'], $message, $notification_title);

                    $notification_data = json_encode(array_merge(["notification" => $notification_title], ["data" => $message]));
                    $insert_data = array(
                        "appointment_id" => $reschedule_data['appointment_id'],
                        "notification_data" => $notification_data,
                        "fcm_response" => $fcm_response,
                        "doctor_id" => $reschedule_data['doctor_id'],
                        "created_date" => $this->config->item("date")
                    );

                    $this->db->insert("doctor_on_call_notification", $insert_data);

                    //------------------------------------------------------------
                    $this->sendEmailToUser($reschedule_data['appointment_id'], 19);
                    $this->sendEmailToDoctor($reschedule_data['appointment_id'], 19);
                    $this->response_send = ["status" => true];
                } else {
                    $this->response_send = ["status" => false, "message" => $this->lang->line("no_data_found")];
                }
            } else {
                $this->response_send = ["status" => false, "message" => $this->lang->line("all_field_required")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function appointment_reschedule_by_doctor_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $reschedule_data = json_decode(file_get_contents('php://input'), true);
            $field_name = ['slot_id', "appointment_id", "last_appointment_date"];
            if (check_form_array_keys_existance($reschedule_data, $field_name) && check_user_input_values($reschedule_data)) {
                $result = $this->appointment_detail_model->appointment_reschedule_by_doctor_model($reschedule_data);

                if ($result) {
                    $user_data = explode("||", $result['user_profile']);
                    // send notification 
                    $notification_title = array(
                        "title" =>  $this->lang->line("app_reschedule"), //"Appointment Rescheduled",
                        "body" => sprintf($this->lang->line("app_reschedule_body_doct"),$user_data[0]),//"('" . $user_data[0] . "'), Your upcoming DOC MD appointment has been re-scheduled",
                        "type" => $this->lang->line("app_reschedule_constant_doct") //"RESCH_APPT_BY_DOCT"
                    );
                    $message = [
                        "appointment_id" => $reschedule_data['appointment_id'],
                        "notify_time" => $this->config->item("date"),
                        "name" => $user_data[0],
                        "profile_url" => ($user_data[1]) ? $user_data[1] : null,
                        "type" => $result['type'],
                    ];

                    $fcm_response = $this->send_appointment_notification($result['device_token'], $message, $notification_title);

                    $notification_data = json_encode(array_merge(["notification" => $notification_title], ["data" => $message]));
                    $insert_data = array(
                        "appointment_id" => $reschedule_data['appointment_id'],
                        "notification_data" => $notification_data,
                        "fcm_response" => $fcm_response,
                        "created_date" => $this->config->item("date")
                    );

                    $this->db->insert("user_on_call_notification", $insert_data);
                    //=========================================================
                    $this->response_send = ["status" => true];
                    $this->sendEmailToUser($reschedule_data['appointment_id'], 20);
                    $this->sendEmailToDoctor($reschedule_data['appointment_id'], 20);
                } else {
                    $this->response_send = ["status" => false, "message" => $this->lang->line("no_data_found")];
                }
            } else {
                $this->response_send = ["status" => false, "message" => $this->lang->line("all_field_required")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
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

    public function sendEmailToUser($id, $template_id = 17) {
        try {
            $this->db->where("appointment_id", $id);
            $query = $this->db->select(
                            "(SELECT email from users WHERE id = (SELECT user_id from appointment WHERE id = '" . $id . "' )) AS email,
                name,doctor,patient_availability_date_and_time,appointment_status")->from("all_appointment")->get();

            $row1 = $query->row_array();

            if (!empty($row1)) {
                $this->config->load('shared');
                $data = get_email_templates(["id" => $template_id]); // template fixed on 17 id
                $data['message'] = $data[0]['message'];
                $data['appointment_detail'] = $row1;
                $message = $this->load->view("api_email_template/appointment_user_cancel_template", $data, TRUE);

                $this->load->library("email_setting");
                $from = $this->config->item("from");

                $response = $this->email_setting->send_email($row1['email'], $from, $message, $data[0]["subject"]);

                //return $response;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function sendEmailToDoctor($id, $template_id = 18) {
        try {
            $this->db->where("appointment_id", $id);
            $query = $this->db->select(
                            " (SELECT email from doctors WHERE id = (SELECT doctor_id from appointment WHERE id = '" . $id . "' )) AS email,
                    name,doctor,patient_availability_date_and_time,appointment_status")->from("all_appointment")->get();

            $row = $query->row_array();

            if (!empty($row)) {
                $this->config->load('shared');
                $data = get_email_templates(["id" => $template_id]);
                $data['message'] = $data[0]['message'];
                $data['appointment_detail'] = $row;
                $message = $this->load->view("api_email_template/appointment_doctor_cancel_template", $data, TRUE);
                $this->load->library("email_setting");
                $from = $this->config->item("from");

                $response = $this->email_setting->send_email($row['email'], $from, $message, $data[0]["subject"]);
                return $response;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    // get All appointment patient info of the doctor
    public function getAllDoctorPatientInfo_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, 'doctors');
            $doctor_id = $this->get();
            if (check_form_array_keys_existance($doctor_id, ["doctor_id"]) && check_user_input_values($doctor_id)) {
                $result = $this->appointment_detail_model->getAllDoctorPatientInfo_model($doctor_id);
                if ($result) {
                    $this->response_send = ["status" => $this->config->item("status_true"), "patient_info" => $result];
                } else {
                    $this->response_send = ["status" => $this->config->item("status_false"), "message" => $this->lang->line("no_data_found")];
                }
            } else {
                $this->response_send = ["status" => false, "message" => $this->lang->line("all_field_required")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function getAllAppointmentDoctorWithPatient_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, 'doctors');
            $data = $this->get();

            if (check_form_array_keys_existance($data, ["doctor_id", "patient_id"]) && check_user_input_values($data)) {
                // Recent  appointment(past)

                $recent_condition = [
                    "doctor_id" => $data['doctor_id'],
                    "patient_id" => $data['patient_id']
                ];
                $recent_status = 6;
                // when we want to prescription of past appointment send prescription
                $recent_data = get_all_appointment($recent_condition, $recent_status, "total_record", "prescription", $this->headers['Accept-Language']);
                if (!empty($recent_data)) {
                    $recent_appoint = appointment_array($recent_data);
                    $this->response_send = ["status" => $this->config->item("status_true"), "past_appointments" => $recent_appoint];
                } else {
                    $this->response_send = ["status" => $this->config->item("status_false"), "message" => $this->lang->line("no_data_found")];
                }
            } else {
                $this->response_send = ["status" => $this->config->item("status_false"), "message" => $this->lang->line("all_field_required")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

}

?>