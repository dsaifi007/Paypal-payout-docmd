<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Pharmacies_controller extends REST_Controller {

    private $response_send = [];
    private $is_model = "api/pharmacies_model";
    private $language_file = ["api_message", "spn_api_message"];
    private $headers;
    private static $pharmacy_fields = ["pharmacy_name", "address", "type", "id"];
    private static $edit_pharmacy_fields = ["pharmacy_name", "address", "pharmacy_id"];
    private static $primary_fields = ['user_id', 'pharmacy_id', 'is_primary'];

    public function __construct() {
        try {
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            $this->load->model($this->is_model);
            change_languge($this->headers, $this->language_file);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
     * This function is used to get consent text 
     * @parma none
     * @return consent data in json format
     */

    public function user_pharmacies_get() {
        try {

            $data = $this->get();

            check_acces_token(@$this->headers['Authorization']);

            $result = $this->pharmacies_model->get_allrecords();
            //dd($result);
            if ($result != false) {
                foreach ($result as $k => $value) {
                    if (!empty($value['location'])) {
                        $result[$k]['location'] = array_combine(['latitude', 'longtitude'], explode("||", $value['location']));
                    }
                }
                //dd($result);
                $this->response_send = ["pharmacies" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_record_found'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function add_pharmacy_post() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $values = $data;
            unset($values['pharmacy_timing']); // unset value for check the phramacy timing
            if (isset($data['type']) && $data['type'] == "doctor") {
                $field_key = array_merge(self::$pharmacy_fields, ['user_id']);
                //check_acces_token(@$this->headers['Authorization'], null, "doctors");
            } else {
                unset($data['user_id']);
                $field_key = self::$pharmacy_fields;
                //check_acces_token(@$this->headers['Authorization']);
            }
           
            if (check_form_array_keys_existance($data, $field_key) && check_user_input_values($values)) {
                
                $result = $this->pharmacies_model->add_pharmacy_model($data);

                if ($result) {
                    $this->response_send = ['message' => $this->lang->line("pharmacy_added"), "status" => $this->config->item("status_true"), "id" => (string)$result];
                } else {
                    $this->response_send = ['message' => $this->lang->line("something_wrong"), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ['message' => $this->lang->line("all_field_required"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    //edit the pharmacy
    public function edit_pharmacy_put() {
        try {
            $data = $this->put();
            if (@$data['type'] == "doctor") {
                //check_acces_token(@$this->headers['Authorization'], null, "doctors");
            } else {
                //check_acces_token(@$this->headers['Authorization']);
            }
            
            $values = $data;
            unset($values['pharmacy_timing']); // unset value for not check the phramacy timing in check_user_input_values()
            if (check_form_array_keys_existance($data, self::$edit_pharmacy_fields) && check_user_input_values($values)) {
               
                $result = $this->pharmacies_model->update_pharmacy_model($data);
                if ($result) {
                    $this->response_send = ['message' => $this->lang->line("pharmacy_updated"), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ['message' => $this->lang->line("pharmacy_not_updated"), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ['message' => $this->lang->line("all_field_required"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // make primary/prefered pharmacy 
    public function make_user_primary_pharmacy_post() {
        try {
            // if prime then is_primary = true other false
            $data = json_decode(file_get_contents('php://input'), true);
            check_acces_token(@$this->headers['Authorization']);
            $values = array_merge($data, ["is_primary" => boolean_parse($data['is_primary'])]);
            if (check_form_array_keys_existance($data, self::$primary_fields) && check_user_input_values($values)) {


                $result = $this->pharmacies_model->make_prefer_pharmacy_model($data);
                if ($result) {
                    $this->response_send = ['message' => $this->lang->line("pharmacy_updated"), "status" => $this->config->item("status_true")];
                }else{
               $this->response_send = ['message' => $this->lang->line("invalid_id"), "status" => $this->config->item("status_false")];

                }
            } else {
                $this->response_send = ['message' => $this->lang->line("all_field_required"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function uplode_pharmacy_logo_post() {
        try {
            $data = $this->input->post();

            //check_acces_token(@$this->headers['Authorization']);
            if (!empty($data['pharmacy_id']) && check_form_array_keys_existance($data, ["pharmacy_id"]) && count($_FILES) > 0 && is_numeric($data['pharmacy_id'])) {

                $img_response = $this->pharmacy_file_upload($_FILES, $data['pharmacy_id']);
                //dd($img_response);
                if (isset($img_response['error'])) {
                    $this->response_send = ["url" => strip_tags($img_response['error']), "status" => $this->config->item("status_true")];
                } else {
                    $result = $this->pharmacies_model->pharmacy_image_update($img_response, $data['pharmacy_id']);

                    if ($result) {
                        $this->response_send = ["pharmacy_image_url" => $img_response, "status" => $this->config->item("status_true")];
                    } else {
                        $this->response_send = ["message" => $this->lang->line('img_not_update'), "status" => $this->config->item("status_false")];
                    }
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function pharmacy_file_upload($file, $id) {
        try {
            $file_name = (!empty($file)) ? $file['pharmacy_img']['name'] : "";
            $this->load->library("common");
            $this->load->helper('string');
            if ($file_name != '') {
                $rename_image = (random_string('numeric') + time()) . random_string();
                $img_data = $this->common->file_upload("assets/file", "pharmacy_img", $rename_image);

                if (isset($img_data["upload_data"]['file_name'])) {
                    remove_existing_img($id, "pharmacies", "pharmacy_img", "assets/file");
                    $file_url = base_url() . "assets/file/" . $img_data["upload_data"]['file_name'];
                    $new_file_name = $img_data["upload_data"]['file_name'];

                    return $file_url;
                } else {
                    return $img_data;
                }
            } else {
                return FALSE;
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    //get user prefered and prime pharmacy
    public function get_user_prefered_pharmacy_get() {
        try {
            $user_id = $this->get();
            if (isset($user_id['user_id']) && $user_id['user_id'] != '') {
                $result = $this->pharmacies_model->get_user_prefered_pharmacy_model($user_id['user_id']);
                if ($result) {
                    $this->response_send = array_merge($result, ["status" => $this->config->item("status_true")]);
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get users pharmacy
    public function get_pharmacy_get() {
        try {

            $id = $this->get();

            check_acces_token(@$this->headers['Authorization']);
            if (isset($id['pharmacy_id']) && $id['pharmacy_id'] != '' && isset($id['user_id']) && $id['user_id'] != '') {
                $result = $this->pharmacies_model->get_user_pharmacy_model($id);
                
                if ($result) {
                    $result['is_primary'] = ($result['is_primary'] == "1") ? true : false;
                    $result['is_editable'] = ($result['created_by'] == $id['user_id'] && $result['place_id'] =='') ? true : false;

                     if($result['place_id'] && isset($result['place_id'])){
                        $result['google_timing'] = $result['pharmacy_timing'];
                        unset($result['pharmacy_timing']);                  
                    }else{
                        $result['pharmacy_timing'] = json_decode($result['pharmacy_timing']);
                    }

                    //$result['pharmacy_timing'] = ($result['place_id'])?$result['pharmacy_timing']:json_decode($result['pharmacy_timing']);
                    //$result['place_id'] = ($result['place_id']) ? false : true;
                    unset($result['created_by']);
                    
                    $this->response_send = ["pharmacy_detail" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get doctor pharmacy
    public function get_doctor_pharmacy_get() {
        try {

            $id = $this->get();

            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            if (isset($id['pharmacy_id']) && $id['pharmacy_id'] != '' && isset($id['doctor_id']) && $id['doctor_id'] != '') {
                $result = $this->pharmacies_model->get_doctor_pharmacy_model($id);
                if ($result) {
                    $result['is_editable'] = ($result['created_by'] == $id['doctor_id'] && $result['place_id'] =='' ) ? true : false;
                    if($result['place_id'] && isset($result['place_id'])){
                        $result['google_timing'] = $result['pharmacy_timing'];
                        unset($result['pharmacy_timing']);                  
                    }else{
                        $result['pharmacy_timing'] = json_decode($result['pharmacy_timing']);
                    }
                    unset($result['created_by']);
                    $this->response_send = ["pharmacy_detail" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // user pharmacy delete(status change)
    public function user_pharmacy_deleted_get() {
        try {
            $id = $this->get();

            check_acces_token(@$this->headers['Authorization']);
            if (check_form_array_keys_existance($id, ["pharmacy_id", "user_id"]) && check_user_input_values($id)) {
                $result = $this->pharmacies_model->user_pharmacy_deleted_model($id);
                if ($result) {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_deleted'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_id_not_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // doctor pharmacy delete(status change 1)
    public function doctor_pharmacy_deleted_get() {
        try {
            $id = $this->get();
            //dd($id);
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            if (check_form_array_keys_existance($id, ["pharmacy_id", "doctor_id"]) && check_user_input_values($id)) {
                $result = $this->pharmacies_model->doctor_pharmacy_deleted_model($id);
                if ($result) {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_deleted'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_id_not_found'), "status" => $this->config->item("status_false")];
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
     *  -- get user pharmacy list based on lang and latitude and open/close
     *  and near by around 25 miles(40km) of all pharmacy list
     * 
     */

    public function get_user_pharmacy_list_get() {
        try {
            $data = $this->get();
            if (check_form_array_keys_existance($data, ["latitude", "longitude", "is_open"]) && check_user_input_values($data)) {
                $result = $this->pharmacies_model->get_user_pharmacy_list_model($data);
                if ($result) {
                    $this->response_send = ["pharmacies" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_pharmacy_exist'), "status" => $this->config->item("status_false")];
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
     * Make a  doctor  prefered pharmacy for the users  by doctor
     * 
     */

    public function make_prefered_pharmacy_for_user_post() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($data, ["doctor_id", "user_id", "pharmacy_id"]) && check_user_input_values($data)) {
                $result = $this->pharmacies_model->make_prefered_pharmacy_for_user_model($data);
                if ($result) {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_added'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('pharmacy_already_added'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // searching the 
    public function searching_post() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data['search'] != '' && isset($data['search'])) {
                $result = $this->pharmacies_model->searching_model($data['search']);
                if ($result) {
                    $this->response_send = ["pharmacies" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    // add pharmacy by the third party or it means google
    public function add_pharmacy_by_third_party_post() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $values = $data;
            
            
            if (isset($data['type']) && $data['type'] == "doctor") {
                $field_key = array_merge(self::$pharmacy_fields, ['user_id']);
                //check_acces_token(@$this->headers['Authorization'], null, "doctors");
            } else {
                unset($data['user_id']);
                $field_key = self::$pharmacy_fields;
                //check_acces_token(@$this->headers['Authorization']);
            }
            $field_key[]= "place_id";
           
            if (check_form_array_keys_existance($data, $field_key) && check_user_input_values($values)) {                 
                $result = $this->pharmacies_model->add_pharmacy_third_party_model($data);
                if($result) {
                    $this->response_send = ['message' => $this->lang->line("pharmacy_added"), "status" => $this->config->item("status_true"), "id" => (string)$result];
                }
                else {
                    $this->response_send = ['message' => $this->lang->line("something_wrong"), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ['message' => $this->lang->line("all_field_required"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
}
?>



