<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Get_notifications_controller extends REST_Controller {

    protected $response_send = ["status" => false];
    protected $language_file = ["api_message", "spn_api_message"];
    protected $headers;
    protected $appoinment_data;
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
            $this->load->model("api/get_notifications_model", "notifications_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function get_user_notifications_list_get() {
        try {
            $user_id = $this->get();
            check_acces_token(@$this->headers['Authorization']);
            if (check_form_array_keys_existance($user_id, ["user_id"]) && check_user_input_values($user_id)) {
                $result = $this->notifications_model->get_user_notification($user_id['user_id']);
                if ($result) {
                    $this->response_send = ["notifications" =>$result , "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    public function get_doctor_notifications_list_get() {
        try {
            $doctor_id = $this->get();
            check_acces_token(@$this->headers['Authorization'],null,"doctors");
            if (check_form_array_keys_existance($doctor_id, ["doctor_id"]) && check_user_input_values($doctor_id)) {
                $result = $this->notifications_model->get_doctor_notification($doctor_id['doctor_id']);
                if ($result) {
                    $this->response_send = ["notifications" =>$result , "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
        // get user create/reschedule appointment notification
    public function getUserRescheduleAppointmentNotificationList_get() {
        try {
            $user_id = $this->get();
            check_acces_token(@$this->headers['Authorization']);
            if (check_form_array_keys_existance($user_id, ["user_id"]) && check_user_input_values($user_id)) {
                $result = $this->notifications_model->get_users_notification($user_id['user_id']);
                
                if ($result) {
                    $this->response_send = ["notifications" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    public function notificationSeen_get() {
        try {
            $data = $this->get();         
            if($data['action'] == "doctor"){
                check_acces_token(@$this->headers['Authorization'],null,"doctors");
            }else{
                check_acces_token(@$this->headers['Authorization']);
            }
            if (check_form_array_keys_existance($data, ["notification_id","action"]) && check_user_input_values($data) && (int)$data['notification_id']) {
                $result = $this->notifications_model->update_user_is_read_status($data);
                $this->response_send = ["status" => $this->config->item("status_true")];
                
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    public function notificationDelete_get() {
        try {
            $data = $this->get();
            if ($data['action'] == "doctor") {
                check_acces_token(@$this->headers['Authorization'], null, "doctors");
            } else {
                check_acces_token(@$this->headers['Authorization']);
            }
            if (check_form_array_keys_existance($data, ["notification_id", "action"]) && check_user_input_values($data) && (int) $data['notification_id']) {
                $this->notifications_model->delete_notification($data);
                $this->response_send = ["message" =>$this->lang->line("notification_deleted"), "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
}

?>