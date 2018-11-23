<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Userlogin extends REST_Controller {

    protected $user_data = [];
    protected $response_send = [];
    //protected $language_file = ["users/login", "users/spn_login"];
    
    protected $language_file = ["api_message", "spn_api_message"];
    protected $data = [];
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
            $this->load->model("api/login_model");
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

    public function userlogin_post() {
        try {
            $this->user_data = json_decode(file_get_contents('php://input'), true);
            if ($this->user_data['email'] != '' && $this->user_data['password'] != '' && $this->user_data['device_token'] != '') {
                $this->_loadModel();
                $response = $this->login_model->check_email_and_pass_existence($this->user_data,$this->user_data['device_token']);
                if ($response === "wrong_credential") {
                    $this->response_send = ["message" => $this->lang->line('login_failed'), "status" => $this->config->item("status_false")];
                } elseif ($response === "email_not_verified") {
                    $this->response_send = ["message" => $this->lang->line('em_not_verified'), "status" => $this->config->item("status_false")];
                } elseif ($response === "phone_not_verified") {
                    $this->response_send = ["message" => $this->lang->line('phone_not_verified'), "status" => $this->config->item("status_false")];
                } elseif ($response === "user_blocked") {
                    $this->response_send = ["message" => $this->lang->line('user_blocked'), "status" => $this->config->item("status_false")];
                } else {
                    $this->response_send = ["user" => $response, "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will Send Temprary password to user incase for forget password
      |------------------------------------------------------------------------------------------------------------
     */

    public function forget_password_get() {
        try {
            $this->_loadModel();
            $this->user_data = $this->input->get();
            if ($this->user_data['email'] != '') {
                $response = $this->login_model->check_email_existence(trim($this->user_data['email']));
                if ($response == false) {
                    $this->response_send = ["message" => $this->lang->line('email_not_found'), "status" => $this->config->item("status_false")];
                } else {

                    $email_response = $this->send_temp_password($this->user_data['email']);
                    $this->response_send = ["message" => $this->lang->line('sent_temp_email'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
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
            $response = $this->login_model->updating_pass(trim($email), $temp_pass);
         
            if ($response) {
                $this->load->library("email_setting");
                $from = $this->config->item("from");
                $subject = $this->config->item("subject"); // language file is not working
                //$message = $this->config->item("forget_message") . " --- " . "<mark>" . $temp_pass . "</mark>";
              $message = "Hi ".ucfirst($response['first_name']).",<br>
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
            $id = json_decode(file_get_contents('php://input'), true);
            if (count($id) > 0) {
                $this->_loadModel();
                $response = $this->login_model->auto_login_model($id);
                if ($response != false) {
                    check_acces_token($this->headers['Authorization'], $id['user_id']);
                    $this->response_send = ["user" => $response, "status" => $this->config->item("status_true")];
                } else {

                    $this->response_send = ["message" => $this->lang->line('in_valid_id'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('id_not_found'), "status" => $this->config->item("status_false")];
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
      $response=$this->login_model->rest_password(trim($email),$email_encoded);
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