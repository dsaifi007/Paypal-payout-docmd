<?php

require(APPPATH . '/libraries/REST_Controller.php');

//use Restserver\Libraries\REST_Controller

class Doctors extends REST_Controller {

    private $doctor_data = [];
    private $language_file = ["doctors/doctor", "doctors/spn_doctor"];
    private $response_send = ["status" => false, "message" => "Bad response 401."];
    private $doctor_table = "doctors";
    private $doctor_professional_info = [];
    private $doctor_info = [];
    private $doctor_address_data = [];
    private $doctor_profile_folder = "assets/doctor/profile";
    private $profile_field = "profile_image";
    private $headers;
    private $otp_data = [];
    private $user_profile = [];
    private $email_phone_keys = ["email", "phone"];
    private $create_account_keys = ['first_name', 'last_name', 'email', 'phone', 'password'];
    private $doctor_info_keys = ['id', 'date_of_birth', 'gender', 'address', 'city', 'state', 'zip_code', 'undergraduate_university', 'medical_school', 'residency', 'medical_license_number','speciality_id','degree_id','additional_info'];
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
      | This Function use for load doctor model
      * @param none
      * @return only load model of current class
      |--------------------------------------------------------------------------------
     */
    private function _loadModel() {
        try {
            $this->load->model("api/doctor_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function will check the existance of User Email and Phone in Our Database
      * @param none
      * @return boolean true or false
      |--------------------------------------------------------------------------------
     */

    public function check_email_and_phone_get() {
        try {
            $this->doctor_data = $this->input->get();
            if (check_form_array_keys_existance($this->doctor_data, $this->email_phone_keys) && check_user_input_values($this->doctor_data)) {
                if (filter_var($this->doctor_data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->_loadModel();
                    $response = $this->doctor_model->check_email_phone_existance(trim($this->doctor_data['email']), trim($this->doctor_data['phone']));
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
      * @param none
      * @return array (user information)
      |-----------------------------------------------------------------------------------------
     */

    public function createaccount_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true);
            if (check_user_input_values($this->doctor_data) && check_form_array_keys_existance($this->doctor_data, $this->create_account_keys)) {
                if (filter_var($this->doctor_data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->_loadModel();
                    $this->doctor_data['created_date'] = date("Y-m-d h:i:s");
                    $response = $this->doctor_model->create_doctor_account_data_insert($this->doctor_data);
                    if ($response != false) {
                        $this->response_send = ['doctor' => $response, "status" => $this->config->item("status_true")];
                        $this->email_verification($this->doctor_data['email']);
                    } else {
                        $this->response_send = ["message" => $this->lang->line('email_or_phone_exist'), "status" => $this->config->item("status_false")];
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('wrong_email'), "status" => $this->config->item("status_false")];
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
      |-----------------------------------------------------------------------------------------
      | This Function will insert the basic information of  doctor in database
      * @param none
      * @return array 
      |-----------------------------------------------------------------------------------------
     */

    public function adddoctorinformation_post() {
        try {
            $this->_loadModel();
            $this->doctor_data = json_decode(file_get_contents('php://input'), true);
            
            if(trim($this->doctor_data['additional_info']) == '' || trim($this->doctor_data['additional_info']) == null)
            { 
              unset($this->doctor_data['additional_info']);
              $doctor_value = $this->doctor_data;
            }
            else
            {
              $doctor_value = $this->doctor_data;
            }

            if (check_form_array_keys_existance($this->doctor_data, $this->doctor_info_keys) != false && check_user_input_values($doctor_value)) {
                check_acces_token(@$this->headers['Authorization'], $this->doctor_data['id'], $this->doctor_table);

                $this->update_doctor_info($this->doctor_data);
                $this->add_doctor_address($this->doctor_data);
                $this->add_doctor_professional_info($this->doctor_data);

                $this->response_send = ["message" => $this->lang->line('doctor_added_successfull'), "status" => $this->config->item("status_true")];
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
      |-----------------------------------------------------------------------------------------
      | This function is used for updating the data of doctor inofrmation
      * @param doctor_data , doctor_id 
      * @return true 
      |-----------------------------------------------------------------------------------------
     */

    public function update_doctor_info($doctor_data, $doctor_id = '') {
        try {
            $this->doctor_info = [
                "date_of_birth" => $doctor_data['date_of_birth'],
                "gender" => $doctor_data['gender'],
                "created_date" => date("Y-m-d h:i:s")];
            $this->doctor_model->update_doctor_info_model($this->doctor_info, $doctor_data['id']);
            return true;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for adding the doctor address
      * @param doctor_address_data
      * @return true 
      |-----------------------------------------------------------------------------------------
     */

    public function add_doctor_address($doctor_address) {
        try {
            $this->doctor_address_data = [
                "address" => $doctor_address['address'],
                "city" => $doctor_address['city'],
                "state" => $doctor_address['state'],
                "zip_code" => $doctor_address['zip_code']];
            $this->doctor_model->doctor_address_inserted($this->doctor_address_data, $doctor_address['id']);
            return true;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for display the profile of users only based on id and token
      * @param doctor_info
      * @return null
      |-----------------------------------------------------------------------------------------
     */

    private function add_doctor_professional_info($doctor_info) {
        try {
            $this->doctor_professional_info = [
                'undergraduate_university' => $doctor_info['undergraduate_university'],
                'medical_school' => $doctor_info['medical_school'],
                'residency' => $doctor_info['residency'],
                'medical_license_number' => $doctor_info['medical_license_number'],
                'doctor_id' => $doctor_info['id'],
                'speciality_id' => $doctor_info['speciality_id'],
                'degree_id' => $doctor_info['degree_id'],
                'additional_info' => $doctor_info['additional_info']
            ];
            $this->doctor_model->doctor_professional_info_insert($this->doctor_professional_info);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
    
    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for display the profile of Doctor only based on id and token
      * @param none
      * @return doctor profile in json format
      |-----------------------------------------------------------------------------------------
     */
    public function doctorprofile_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if (check_user_input_values($this->doctor_data)  && check_form_array_keys_existance($this->doctor_data,["doctor_id"])) {
                check_acces_token(@$this->headers['Authorization'], $this->doctor_data['doctor_id'], $this->doctor_table);
                $this->_loadModel();
                $doctor_profile = $this->doctor_model->get_doctor_profile($this->doctor_data['doctor_id']);
                $this->response_send = ["doctor_info" => $doctor_profile, "status" => $this->config->item("status_true")];
                $this->response($this->response_send);
                //$doc_complete_profile = ($doctor_profile != false) ? [$doctor_profile ,["status" => $this->config->item("status_true")]] : ["status" => $this->config->item("status_true")]; 
                //echo json_encode($doc_complete_profile);
            } else {
                $this->response_send = ["message" => $this->lang->line('user_id_or_key_value_missing'), "status" => $this->config->item("status_false")];
                $this->response($this->response_send);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This function is used for make a array(json) accourding to ios and android
      * @param user information
      * @return user info array
      |-----------------------------------------------------------------------------------------
     */
    public function user_profile_array($user_array) {
        try {
            if (!empty($user_array)) {
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
      | This Function will update the profile image of individual doctor
      * @param none
      * @return image upload response
      |--------------------------------------------------------------------------------
     */

    public function doctor_profile_img_update_post() {
        try {
            $this->doctor_data = $this->input->post();
            if (!empty($this->doctor_data['doctor_id']) && check_form_array_keys_existance($this->doctor_data,["doctor_id"])) {
                check_acces_token(@$this->headers['Authorization'], $this->doctor_data['doctor_id'], $this->doctor_table);
                $img_response = $this->doctor_profile_img_upload($_FILES, $this->doctor_data['doctor_id']);
                if ( $img_response ) {
                   $this->response_send = $img_response;
                }else{
                    $this->response_send =  ["message" => $this->lang->line('file_not_upload'),"status"=>$this->config->item("status_false")];
                }
                
            } else {
                $this->response_send = ["message" => $this->lang->line('no_user_found'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function will upload the doctor profile image
      * @param file ,doctor_id
      * @return response json array
      |--------------------------------------------------------------------------------
     */

    private function doctor_profile_img_upload($file, $doctor_id) {
        try {
            $file_name = (!empty($file) && count($file)>0) ? $file['profile_image']['name'] : "";
            $this->load->library("common");
            $this->load->helper('string');
            if ($file_name != '' && !empty($file)) {
                $rename_image = (random_string('numeric') + time()) . random_string();
                $img_data = $this->common->file_upload($this->doctor_profile_folder, $this->profile_field, $rename_image);
                if (isset($img_data["upload_data"]['file_name'])) {
                    remove_existing_img($doctor_id, $this->doctor_table, $this->profile_field, $this->doctor_profile_folder);
                    $new_file_name = $img_data["upload_data"]['file_name'];
                    $file_url = base_url() . $this->doctor_profile_folder . "/" . $new_file_name;
                    $this->_loadModel();
                    $this->doctor_model->doctor_profile_img_update_model($new_file_name, $file_url, $doctor_id);
                    return ["message" => $this->lang->line('profile_image_updated'), "profile_image_url" => $file_url, "status" => $this->config->item("status_true")];
                } else {
                    return array_merge(["message"=>strip_tags($img_data['error']),"status" => $this->config->item("status_false")]);
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
      |----------------------------------------------------------------------------------------------------------
      | This function is used for submited edit inoformation  of user profile with some additional inofmation
      * @param null
      * @return response json array
      |--------------------------------------------------------------------------------------------------------
     */

    public function doctoreditsubmitted_put() {
        try {
            $this->doctor_data = $this->put();
            dd($this->doctor_data);
            if (check_form_array_keys_existance($this->doctor_data, $this->doctor_info_keys) && check_user_input_values($this->doctor_data)) {
                $this->_loadModel();
                if (is_numeric($this->doctor_data['user_id'])) {

                    $address_id = $this->user_address($this->doctor_data, $this->doctor_data['user_id']);
                    $this->patient_info($this->doctor_data, $this->doctor_data['user_id']);
                    $this->medical_profile($this->doctor_data, $this->doctor_data['user_id']);
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
    |--------------------------------------------------------------------------------
    | This Function will send the new OTP to the doctor 
    |--------------------------------------------------------------------------------
    */   
    public function send_new_otp_to_doctor_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            $this->load->library('twilio');
            
            $this->_loadModel();
            
            check_acces_token(@$this->headers['Authorization'],@$this->doctor_data['doctor_id'],$this->doctor_table);                           
            
            $expire_time = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            
            $new_otp_number = mt_rand(1000, 10000);
            
            $sms_sender = trim($this->config->item("number"));
            
            if (check_form_array_keys_existance($this->doctor_data,["doctor_id"]) 
                    && check_user_input_values($this->doctor_data)) {
                
                $get_phone_number = $this->doctor_model->check_phone_existance($this->doctor_data['doctor_id']);
                if($get_phone_number != false)
                {
                    $sms_reciever = trim($get_phone_number->phone); //$get_phone_number->phone';
                    $sms_message =  sprintf($this->lang->line("doctor_otp_label") , $new_otp_number);

                    $from = $this->plus_sign . $sms_sender; //trial account twilio number
                    
                    $to =   $sms_reciever; //sms recipient number

                    $response = $this->twilio->sms($from, $to, $sms_message);
                    //dd($response);
                    if ($response->IsError) {
                        $this->response_send = ['message' => $response->ErrorMessage, "status" => $this->config->item("status_false")];
                    } else {
                    $this->doctor_model->delete_otp($this->doctor_data['doctor_id']);    
                    $this->otp_data = [
                        "doctor_id" => $this->doctor_data['doctor_id'],
                        "otp_number" => $new_otp_number,
                        "expire_time" => $expire_time
                    ];
                    $this->doctor_model->store_new_otp($this->otp_data);

                        $this->response_send = ['message' => "Otp successfully sent to ".$sms_reciever, "status" => $this->config->item("status_true")];
                    }
                }
                else
                {
                   $this->response_send = ["message" => $this->lang->line('phone_not_exist'), "status" => $this->config->item("status_false")];
                }
            }
            else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
        /*
      |----------------------------------------------------------------------------------------------------------
      | This function is used for get data of speciality and degree
      * @param null
      * @return response json array
      |--------------------------------------------------------------------------------------------------------
     */
      public function get_doctor_speciality_and_degree_get()
      {

         try {
            $this->doctor_data = $this->get();
            $this->_loadModel();
            $data  = $this->doctor_model->get_doct_speciality_and_degree_model();
            $this->response(
              array_merge(["status" => $this->config->item("status_true")],$data));

          } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
      }

    public function verifiy_doctor_otp_post() {
        try {
            $this->doctor_data = json_decode(file_get_contents('php://input'), true); //$this->input->post();
            if (check_form_array_keys_existance($this->doctor_data, ["doctor_id", "otp"]) && check_user_input_values($this->doctor_data)) {
                check_acces_token(@$this->headers['Authorization'], $this->doctor_data['doctor_id'], $this->doctor_table);
                $this->_loadModel();
                $this->otp_data = [
                    "doctor_id" => $this->doctor_data['doctor_id'],
                    "otp_number" => $this->doctor_data['otp']
                ];
                $get_response = $this->doctor_model->get_otp($this->otp_data);
                if ($get_response) {
                    $current_data_time = date("Y-m-d H:i:s");
                    if (strtotime($current_data_time) <= strtotime($get_response->expire_time)) {
                        $this->response_send = ["message" => $this->lang->line('verfiy_otp'), "status" => $this->config->item("status_true")];
                        $this->doctor_model->delete_otp($this->doctor_data['doctor_id']);
                        $this->doctor_model->update_phone_status($this->doctor_data['doctor_id']);                       
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
            $email_encoded = base64_encode($email);
            $response = $this->doctor_model->email_verification_code(trim($email), $email_encoded);
            if ($response) {
                $this->load->library("email_setting");
                $from = $this->config->item("from");
                $subject = $this->config->item("email_verified"); // language file is not working
                $message = $this->config->item("email_link") . base_url() . "api/nonapi/doctoremailauth/" . $email_encoded;
                $this->email_setting->send_email($email, $from, $message, $subject);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

}

?>