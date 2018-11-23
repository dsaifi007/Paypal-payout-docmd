<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Manage_other_information extends REST_Controller {

    private $response_send = [];
    private $is_model = "api/manage_other_information_model";
    private $language_file = ["api_message", "spn_api_message"];
    private $headers;

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
     * This function is used to get medications
     * @parma none
     * @return consent data in json format
     */

    public function medications_get() {
        try {

            $data = $this->get();

            

            $result = $this->manage_other_information_model->get_allmedications($this->headers['Accept-Language']);
            //dd($result);
            if ($result != false) {
                $this->response_send = ["medications" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_record_found'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
    /*
     * This function is used to get allergies
     * @parma none
     * @return consent data in json format
     */

    public function allergies_get() {
        try {

            $data = $this->get();

            

            $result = $this->manage_other_information_model->get_allallergies($this->headers['Accept-Language']);
            //dd($result);
            if ($result != false) {
                $this->response_send = ["allergies" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_record_found'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
    /*
     * This function is used to get diagnosis
     * @parma none
     * @return consent data in json format
     */

    public function diagnosis_get() {
        try {

            $data = $this->get();

            check_acces_token(@$this->headers['Authorization'],null,"doctors");

            $result = $this->manage_other_information_model->get_alldiagnosis($this->headers['Accept-Language']);
            //dd($result);
            if ($result != false) {
                $this->response_send = ["diagnosis" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_record_found'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
   
}
?>



