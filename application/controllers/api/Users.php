<?php

require(APPPATH . '/libraries/REST_Controller.php');

//use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller {

    private $user_data = [];
    private $language_file = ["users/users", "users/spn_users"];
    private $response_send = ["status" => false, "message" => "Bad response 401."];
    private $medical_data = [];
    private $patnt_info = [];
    private $address_data = [];
    private $user_image_folder = "assets/users/profile";
    private $patient_tbl = "patient_info";
    private $profile_field = "profile_image";
    private $headers;
    private $medical_profile_data = [];
    private $health_match_data = [];
    private $user_profile = [];
    private $email_phone_keys = ["email", "phone"];
    private $user_info_keys = ['first_name', 'last_name', 'date_of_birth', 'gender', 'address', 'city', 'state', 'zip_code'];
    private $plus_sign = "+";

    public function __construct() {
        try {
            $this->headers = apache_request_headers();
            parent::__construct();
            //$this->config->load('twilio');
            content_type($this->headers);
            change_languge($this->headers, $this->language_file);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function use for load model
      |--------------------------------------------------------------------------------
     */

    private function _loadModel() {
        try {
            $this->load->model("api/user_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function will check the existance of User Email and Phone in Our Database
      |--------------------------------------------------------------------------------
     */

    public function check_email_and_phone_get() {
        try {
            $this->user_data = $this->input->get();
            if (check_form_array_keys_existance($this->user_data, $this->email_phone_keys) && check_user_input_values($this->user_data)) {
                if (filter_var($this->user_data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->_loadModel();
                    $response = $this->user_model->check_email_phone_existance(trim($this->user_data['email']), trim($this->user_data['phone']));
                    if ($response == false) {
                        $this->response_send = ["status" => $this->config->item("status_true")];
                    } else {
                        $this->response_send = ["message" => $this->lang->line('email_or_phone_exist'), "status" => $this->config->item("status_false")];
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('wrong_email'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This Function will insert the data of user in database (only user information add in DB)
      |-----------------------------------------------------------------------------------------
     */

    public function createaccount_post() {
        try {
            $this->user_data = json_decode(file_get_contents('php://input'), true);
            $user_input_array = ["email", "phone", "password"];
            if (check_form_array_keys_existance($this->user_data, $user_input_array) && check_user_input_values($this->user_data)) {
                if (filter_var($this->user_data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->_loadModel();
                    $this->user_data['created_date'] = date("Y-m-d h:i:s");
                    $response = $this->user_model->create_user_account_data_insert($this->user_data);
                    if ($response != false) {
                        $this->response_send = ['user' => $response, "status" => $this->config->item("status_true")];
                        $this->email_verification($this->user_data['email']);
                    } else {
                        $this->response_send = ["message" => $this->lang->line('email_or_phone_exist'), "status" => $this->config->item("status_false")];
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('wrong_email'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This Function will insert the basic information of  user in database (only user/patient information add in DB)
      |-----------------------------------------------------------------------------------------
     */

    public function adduserinformation_post() {
        try {
            $this->_loadModel();
            $this->user_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->user_data, $this->user_info_keys) != false) {
                check_acces_token(@$this->headers['Authorization'], $this->user_data['user_id']);

                $p_id = $this->patient_info($this->user_data);

                $this->user_model->user_patient_map($p_id, $this->user_data['user_id']);

                $address_id = $this->user_address($this->user_data);

                $this->user_model->user_address_map($address_id, $p_id,@$this->user_data['state']);

                $this->medical_profile_add($this->user_data, $p_id);

                $this->addUserOnBraintree($this->user_data);

                $this->response_send = ["message" => $this->lang->line('singup_success'), "status" => $this->config->item("status_true")];
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
      |--------------------------------------------------------------------------------
      | This function is used for submited edit inoformation  of user profile with some additional inofmation
      |--------------------------------------------------------------------------------
     */

    public function usereditsubmited_put() {
        try {
            $this->user_data = $this->put();
            if (check_form_array_keys_existance($this->user_data, $this->user_info_keys)) {
                //&& check_user_input_values($this->user_data)
                $this->_loadModel();
                if (is_numeric($this->user_data['patient_id'])) {
                    
                    check_acces_token(@$this->headers['Authorization']);
                    
                    $this->patient_info($this->user_data, $this->user_data['patient_id']);
                    
                    $address_id = $this->user_address($this->user_data, $this->user_data['patient_id']);
                    
                    $this->ptnt_medical_profile_update($this->user_data, $this->user_data['patient_id']);
                                        
                    $this->response_send = ["message" => $this->lang->line('user_information_updated'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('user_id_invalid'), "status" => $this->config->item("status_false")];
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
     * This function is used for add the medical profile
     * @param medical profile data
     * @return add the medical profile
     */
    public function medical_profile_add($medcl_data, $p_id) {
        try {
            $medical_keys = ['medications', 'allergies', 'past_medical_history', 'social_history', 'family_history'];
            $this->medical_profile_data = form_input_filter(form_array_key_inersection($medcl_data, $medical_keys));
            if (count($this->medical_profile_data) > 0) {
                $this->medical_profile_data['created_at'] = date("Y-m-d h:i:s");
                $this->medical_profile_data['patient_id'] = $p_id;
                $this->user_model->user_medical_profile_add($this->medical_profile_data);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
    

     /*
     * This function is used for add/update patient information
     * @param patient info
     * @return update/add the patient information
     */
    public function patient_info($patient_data, $patnt_id = '') {
        try {
            $user_health_key = ['provider', 'member_id','ins_group'];
            $this->health_match_data = form_input_filter(form_array_key_inersection($patient_data, $user_health_key));
            
            $this->patnt_info = [
                "first_name" => $patient_data['first_name'],
                "last_name" => $patient_data['last_name'],
                "date_of_birth" => $patient_data['date_of_birth'],
                "gender" => $patient_data['gender'],
                "user_id" => (isset($patient_data['user_id']))?$patient_data['user_id']:'',
                "created_date" => date("Y-m-d h:i:s")
            ];
            $final_array = (is_array($this->health_match_data) && !empty($this->health_match_data)) ? $this->health_match_data : [];
            $patient_id = $this->user_model->signup_data_inserted(@array_merge($this->patnt_info,$final_array), $patnt_id);
            return $patient_id;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    
     /*
     * This function is used for add/update address
     * @param address info
     * @return update/add the user address information
     */
    public function user_address($add_data, $user_id = '') {
        try {
            $this->address_data = ["address" => $add_data['address'], "city" => $add_data['city'], "state" => $add_data['state'], "zip_code" => $add_data['zip_code']];
            $add_id = $this->user_model->address_data_inserted($this->address_data, $user_id);
            return $add_id;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function ptnt_medical_profile_update($medcl_data, $ptnt_id = '') {
        try {           
            $this->medical_data = ["patient_id","medications","allergies","past_medical_history","social_history", "family_history"];
            $medical_info = form_array_key_inersection($medcl_data , $this->medical_data);
            if(count($medical_info) > 1 ){
                $this->user_model->user_medical_profile_update($medical_info, $ptnt_id);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
    /*
      |--------------------------------------------------------------------------------
      | This Function will be used for get all the patients
      |--------------------------------------------------------------------------------
    */
      public function get_all_patients_get()
      {
          try {
            $this->user_data = $this->input->get();      
            if ($this->user_data['user_id'] != '') {
                $this->_loadModel();    
                $all_patient_data = $this->user_model->get_all_patients_model($this->user_data['user_id']);
               $this->response_send = ["patients" => $all_patient_data, "status" => $this->config->item("status_true")];
            }else{
                $this->response_send = ["message" => $this->lang->line("user_id"), "status" => $this->config->item("status_false")];
            }
             } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];         
        }
        $this->response($this->response_send);
      }

    /*
      |--------------------------------------------------------------------------------
      | This Function will update the profile image of individual user
      |--------------------------------------------------------------------------------
     */

    public function user_profile_img_update_post() {
        try {
            $this->user_data = $this->input->post();
            if (!empty($this->user_data['patient_id']) && check_form_array_keys_existance($this->user_data, ["patient_id"])) {
                $img_response = $this->user_file_upload($_FILES, $this->user_data['patient_id']);
                 check_acces_token(@$this->headers['Authorization']);
                if ($img_response) {
                    $this->response_send = $img_response;
                } else {
                    $this->response_send = ["message" => $this->lang->line('file_not_upload'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('patient_key_value_id_not_exist'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function will upload the user profile
      |--------------------------------------------------------------------------------
     */

    private function user_file_upload($file, $patientid) {
        try {
            $file_name = (!empty($file)) ? $file['profile_image']['name'] : "";
            $this->load->library("common");
            $this->load->helper('string');
            if ($file_name != '') {
                $rename_image = (random_string('numeric') + time()) . random_string();
                $img_data = $this->common->file_upload($this->user_image_folder, $this->profile_field, $rename_image);
                if (isset($img_data["upload_data"]['file_name'])) {
                    remove_existing_img($patientid, $this->patient_tbl, $this->profile_field, $this->user_image_folder);
                    $file_url = base_url() . $this->user_image_folder . "/" . $img_data["upload_data"]['file_name'];
                    $new_file_name = $img_data["upload_data"]['file_name'];
                    $this->_loadModel();
                     $profile_url = $this->user_model->user_image_update($new_file_name, $file_url, $patientid);
                    return [
                        "message" => $this->lang->line('profile_image_file'), 
                        "profile_image_url" => $profile_url['profile_url'],
                        "status" => $this->config->item("status_true")];
                } else {
                    return array_merge($img_data, ["status" => $this->config->item("status_false")]);
                }
            } else {
                return FALSE;
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for display the profile of users only based on id and token
      |-----------------------------------------------------------------------------------------
     */

    public function userprofile_post() {
        try {
            $this->user_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if ($this->user_data['user_id'] != '' && check_form_array_keys_existance($this->user_data, ["user_id"])) {
                //check_acces_token(@$this->headers['Authorization'], $this->user_data['user_id']);
                $this->_loadModel();
                $user_profile = $this->user_model->get_user_profile_only($this->user_data['user_id']);       
                $this->response_send = ["patient_info" => $user_profile, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('user_id_or_value_missing'), "status" => $this->config->item("status_false")];    
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for make a array accourding to ios and android
      |-----------------------------------------------------------------------------------------
     */

    public function user_profile_array($user_array) {
        try {
            if (!empty($user_array)) {
                echo json_encode($user_array);die;
                $this->user_profile['status'] = true;
                $this->user_profile['users'] = array_slice($user_array, 0, 7);
                $this->user_profile['users']["patient_info"] = array_slice($user_array, 9, 13);
                $this->user_profile['users']["health_insurance"] = array_slice($user_array, 7, 2);
                $this->user_profile['users']["family_patient"] = array_slice($user_array, 22);
                return $this->user_profile;
            } else {
                return $this->lang->line('no_data_found');
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function will send the new OTP to the doctor
      |--------------------------------------------------------------------------------
     */

    public function send_new_otp_to_user_post() {
        try {
            $this->user_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            $this->load->library('twilio');
            $this->_loadModel();

            check_acces_token(@$this->headers['Authorization'], @$this->user_data['user_id']);

            $expire_time = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $new_otp_number = mt_rand(1000, 10000);

            $sms_sender = trim($this->config->item("number"));

            if (check_form_array_keys_existance($this->user_data, ["user_id"]) && check_user_input_values($this->user_data)) {

                $get_phone_number = $this->user_model->check_phone_existance($this->user_data['user_id']);

                if ($get_phone_number != false) {
                    $sms_reciever = trim($get_phone_number->phone); //$get_phone_number->phone';
                    $sms_message = sprintf($this->lang->line("user_otp_label"), $new_otp_number);

                    $from = $this->plus_sign . $sms_sender; //trial account twilio number

                    $to = $sms_reciever; //sms recipient number

                    $response = $this->twilio->sms($from, $to, $sms_message);

                    if ($response->IsError) {
                        $this->response_send = ['message' => $response->ErrorMessage, "status" => $this->config->item("status_false")];
                    } else {

                        $this->user_model->delete_otp($this->user_data['user_id']);

                        $this->otp_data = [
                            "user_id" => $this->user_data['user_id'],
                            "otp_number" => $new_otp_number,
                            "expire_time" => $expire_time
                        ];
                        $this->user_model->store_new_otp($this->otp_data);
                        $this->response_send = ['message' => "Verification Code successfully sent to " . $sms_reciever, "status" => $this->config->item("status_true")];
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('phone_not_exist'), "status" => $this->config->item("status_false")];
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

    public function verifiy_user_otp_post() {
        try {
            $this->user_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if (check_form_array_keys_existance($this->user_data, ["user_id", "otp"]) && check_user_input_values($this->user_data)) {
                check_acces_token(@$this->headers['Authorization'], $this->user_data['user_id']);
                $this->_loadModel();
                $this->otp_data = [
                    "user_id" => $this->user_data['user_id'],
                    "otp_number" => $this->user_data['otp']
                ];
                $get_response = $this->user_model->get_otp($this->otp_data);
                if ($get_response) {
                    $current_data_time = date("Y-m-d H:i:s");
                    if (strtotime($current_data_time) <= strtotime($get_response->expire_time)) {
                        $this->response_send = ["message" => $this->lang->line('verfiy_otp'), "status" => $this->config->item("status_true")];
                        $this->user_model->delete_otp($this->user_data['user_id']);
                        $this->user_model->update_phone_status($this->user_data['user_id']);
                    } else {
                        $this->response_send = ["message" => $this->lang->line('otp_expire'), "status" => $this->config->item("status_false")];
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('invalid_otp'), "status" => $this->config->item("status_false")];
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
      |--------------------------------------------------------------------------------
      | This Function will be used for the user email verification only
      |--------------------------------------------------------------------------------
     */

    public function email_verification($email) {
        try {
            $this->_loadModel();
            $this->config->load('shared');
            $email_encoded = generateEncryptedString($email);
            $response = $this->user_model->email_verification_code(trim($email), $email_encoded);
            if ($response) {
                $this->load->library("email_setting");
                $from = $this->config->item("from");
                $subject = $this->config->item("email_verified"); // language file is not working
                $link['link'] = base_url() . "api/nonapi/emailauth?id=".$email_encoded."";
                $message = $this->load->view("email_template",$link,TRUE);
                //$message = $this->config->item("email_link") . base_url() . "api/nonapi/emailauth/" . $email_encoded;
                $this->email_setting->send_email($email, $from, $message, $subject);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
 /*
      |--------------------------------------------------------------------------------
      | This Function will change email user/doctor
      |--------------------------------------------------------------------------------
     */
    public function change_email_put() {
        try {
            $this->user_data=  $this->put();
            if (@$this->user_data['type'] == "user") {
            check_acces_token(@$this->headers['Authorization']);
            } else {
              check_acces_token(@$this->headers['Authorization'],null,"doctors");  
            }

            if (check_form_array_keys_existance($this->user_data, ["new_email", "password","type"]) && check_user_input_values($this->user_data)) {
                $this->_loadModel();
                $result = $this->user_model->change_email($this->user_data,$this->headers['Authorization']);
                if($result){
                    $this->email_verification($this->user_data['new_email']);
                    $this->response_send = ["message" => $this->lang->line('email_updated'), "status" => $this->config->item("status_true")];
                }else{
                   $this->response_send = ["message" => $this->lang->line('wrong_password'), "status" => $this->config->item("status_false")];
                }           
            }else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    /*
      |--------------------------------------------------------------------------------
      | This Function will change password user/doctor
      |--------------------------------------------------------------------------------
     */
    public function change_password_put() {
        try {
            $this->user_data = $this->put();

            if (check_form_array_keys_existance($this->user_data, ["old_password", "new_password", "type"]) && check_user_input_values($this->user_data)) {
                $this->_loadModel();
                $result = $this->user_model->change_password($this->user_data, $this->headers['Authorization']);
                if ($result == false) {
                    $this->response_send = ["message" => $this->lang->line('old_password_not_matched'), "status" => $this->config->item("status_false")];
                } else {
                   $this->response_send = ["message" => $this->lang->line('password_updated'), "status" => $this->config->item("status_true")];                 
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }   
         /*
      |--------------------------------------------------------------------------------
      | This Function will check user/doctor loggedin or not
      |--------------------------------------------------------------------------------
     */
    public function is_user_loggedin_get() {
        try {
            $this->user_data = $this->get();

            if (check_form_array_keys_existance($this->user_data, ["id", "type"]) && check_user_input_values($this->user_data)) {
                $this->_loadModel();
                $result = $this->user_model->is_user_loggedin_model($this->user_data);
                $this->response_send = ["status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
        // Delete the user
    // Delete patient only not main user
    public function delete_patient_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->user_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if (check_form_array_keys_existance($this->user_data, ["user_id", "patient_id"]) && check_user_input_values($this->user_data)) {
                $this->_loadModel();
                $result = $this->user_model->only_patient_delete($this->user_data);
                if ($result) {
                    $this->response_send = ["status" => $this->config->item("status_true"),"message"=>$this->lang->line("patient_deleted")];
                } else {
                    $this->response_send = ["status" => $this->config->item("status_false"),"message"=>$this->lang->line("patient_not_deleted")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    public function makePatientToUser_post() {
         try {
            check_acces_token(@$this->headers['Authorization']);
            $this->user_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if (check_form_array_keys_existance($this->user_data, ["user_id", "patient_id"]) && check_user_input_values($this->user_data)) {
                
                $this->_loadModel();
                $result = $this->user_model->makeUserToPatient_model($this->user_data);
                if ($result) {
                    $this->response_send = ["status" => $this->config->item("status_true"),"message"=>$this->lang->line("user_change")];
                } else {
                    $this->response_send = ["status" => $this->config->item("status_false"),"message"=>$this->lang->line("user_can_not_change")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function braintreeSetup()
    {
        require_once APPPATH . "third_party/lib/Braintree.php";
            // Instantiate a Braintree Gateway either like this:
            $gateway = new Braintree_Gateway([
                'environment' => $this->config->item("environment"),
                'merchantId' => $this->config->item("merchantId"),
                'publicKey' => $this->config->item("publicKey"),
                'privateKey' => $this->config->item("privateKey")
            ]);
            return $gateway;
    }

    private function addUserOnBraintree($basic_info)
    {
        try {
            
            $user_info = $this->user_model->getUserInfo($basic_info['user_id']);
            $gateway = $this->braintreeSetup();
            $result = $gateway->customer()->create([
                'firstName' => $basic_info['first_name'],
                'lastName' => $basic_info['last_name'],
                'email' => $user_info['email'],
                'phone' => $user_info['phone'],               
            ]);
            
            if($result->success){
                $this->user_model->updateCustomerId($basic_info['user_id'],$result->customer->id);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }
    public function genrateClientToken_post()
    {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->user_data = json_decode(file_get_contents('php://input'), true); 
            if (check_form_array_keys_existance($this->user_data, ["user_id", "cust_id"]) && check_user_input_values($this->user_data)) {              
                $gateway = $this->braintreeSetup();

                $clientToken = $gateway->clientToken()->generate([
                    "customerId" => $this->user_data['cust_id']
                ]);
                 $this->response_send = ["token" =>$clientToken , "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
}

?>