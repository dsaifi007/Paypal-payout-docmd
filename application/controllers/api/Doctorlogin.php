<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Doctorlogin extends REST_Controller {

    protected $doctor_data = [];
    protected $response_send = [];
    //protected $language_file = ["doctors/doctor_login", "doctors/spn_doctor_login"];
    protected $language_file = ["api_message", "spn_api_message"];
    protected $data = [];
    protected $doctor_table = "doctors";
    protected $headers;

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
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will use load model
      |------------------------------------------------------------------------------------------------------------
     */

    private function _loadModel() {
        try {
            $this->load->model("api/doctor_login_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will loggedin based email and password and also check user existance in DB
      |------------------------------------------------------------------------------------------------------------
     */

    public function doctorlogin_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true);
            if (check_user_input_values($this->doctor_data)) {
                $this->_loadModel();
                $response = $this->doctor_login_model->check_email_and_pass_existence($this->doctor_data,$this->doctor_data['device_token']);
                //dd($response);
                if ($response === "wrong_credential") {
                    $this->response_send = ["message" => $this->lang->line('login_failed'), "status" => $this->config->item("status_false")];
                } elseif ($response === "email_not_verified") {
                    $this->response_send = ["message" => $this->lang->line('em_not_verified'), "status" => $this->config->item("status_false")];
                } elseif ($response === "phone_not_verified") {
                    $this->response_send = ["message" => $this->lang->line('phone_not_verified'), "status" => $this->config->item("status_false")];
                } elseif ($response === "user_blocked") {
                    $this->response_send = ["message" => $this->lang->line('user_blocked'), "status" => $this->config->item("status_false")];
                } else {
                    $this->response_send = ["doctor" => $response, "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            echo 'Message: ' . $exc->getMessage();
            $this->exceptionhandler->handle($exc);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will Send Temprary password to user in case for forget password
      |------------------------------------------------------------------------------------------------------------
     */

    public function forget_password_get() {
        try {
            $this->_loadModel();
            $this->doctor_data = $this->input->get();
            if ($this->doctor_data['email'] != '') {
                $response = $this->doctor_login_model->check_email_existence(trim($this->doctor_data['email']));
                if ($response == false) {
                    $this->response_send = ["message" => $this->lang->line('email_not_found'), "status" => $this->config->item("status_false")];
                } else {
                    $email_response = $this->send_temp_password($this->doctor_data['email']);
                    $this->response_send = ["message" => $this->lang->line('sent_temp_email'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            echo 'Message: ' . $exc->getMessage();
        }
    }

    private function _encrypt_string() {
        $this->load->library('encrypt');
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This function is using send temp password to user
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function send_temp_password($email) {
        try {
            $this->_loadModel();
            $this->load->helper('string');
            $temp_pass = random_string('alnum', 8);
            $response = $this->doctor_login_model->update_pass(trim($email), $temp_pass);
            if ($response) {
                $this->load->library("email_setting");
                $from = $this->config->item("from");
                $subject = $this->config->item("subject"); // language file is not working
                //$message = $this->config->item("forget_message") . " --- " . "<mark>" . $temp_pass . "</mark>";
                $message = "Hi ".$response['first_name'].",<br>
                        We’ve reset your password to: " .$temp_pass. ". Please use this temporary password to
                        log in to your DOC MD account. You may change your password at any time in the Account
                        Information section. If you did not initiate this request, please contact our Support team at
                        support@docmdapp.com";

                $this->email_setting->send_email($email, $from, $message, $subject);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will auto in app suppose user is loggedin suddenly app is shutdown/close then user will auto login as same page
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function autologin_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->doctor_data, ["doctor_id"]) && check_user_input_values($this->doctor_data)) {
                check_acces_token(@$this->headers['Authorization'], $this->doctor_data['doctor_id'], $this->doctor_table);
                $this->_loadModel();
                $response = $this->doctor_login_model->auto_doctor_login_model($this->doctor_data);
                if ($response != false) {
                    $this->response_send = ["doctor" => $response, "status" => $this->config->item("status_true")];
                } else {

                    $this->response_send = ["message" => $this->lang->line('id_not_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('docto_key_or_id_missing'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            echo 'Message: ' . $exc->getMessage();
            $this->exceptionhandler->handle($exc);
        }
    }

// for password reset following the reset link currently we are not using this function
    /* public function password_reset($email)
      {
      $this->_encrypt_string();
      $email_encoded=$this->encrypt->encode($email);
      $response=$this->doctor_login_model->rest_password(trim($email),$email_encoded);
      if ($response) {
      $this->load->library("email_setting");
      $from="danishk@chromeinfotech.com";
      $subject="Forget Password"; // language file is not working
      $message= "Please click Below the link for reset the password<br>"."<br>--".base_url()."api/update_password/index/".$email_encoded;
      $email_sent=$this->email_setting->send_email($email,$from,$message,$subject);
      if ($email_sent) {
      return true;
      }else{
      return false;
      }
      }
      } */
}

?>