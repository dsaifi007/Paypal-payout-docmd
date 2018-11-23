<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Consentcare extends REST_Controller {

    private $response_send = [];
    private $is_model = "api/consentcare_model";
    private $language_file = ["consent_care/consent", "consent_care/spn_consent"];
    private $headers;
    private $user_id;
    
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

    public function consent_text_post() {
        try {
           $this->user_id = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->user_id, ["user_id"]) && check_user_input_values($this->user_id)) {
                check_acces_token(@$this->headers['Authorization'], $this->user_id['user_id']);
                
                $consent_text = $this->consentcare_model->get_alltext(trim(@$this->headers['Accept-Language']));               
                if ($consent_text != false) {
                    $this->response_send = array_merge($consent_text, ["status" => $this->config->item("status_true")]);           
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_faq_found'), "status" => $this->config->item("status_false")];
                    
                }
                $this->response($this->response_send);          
            }
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
    public function is_read_consent_care_update_post() {
        try {
           $this->user_id = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->user_id, ["user_id"]) && check_user_input_values($this->user_id)) {
                check_acces_token(@$this->headers['Authorization'], $this->user_id['user_id']);
                
                $consent_text = $this->consentcare_model->read_consent_care_update(trim($this->user_id['user_id']));               
                $this->response_send = ["status" => $this->config->item("status_true")];           
                $this->response($this->response_send);          
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
}
?>



