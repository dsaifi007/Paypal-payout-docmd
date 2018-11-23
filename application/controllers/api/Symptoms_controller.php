<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Symptoms_controller extends REST_Controller {

    protected $response_send = [];
    protected $language_file = ["symptoms/index", "symptoms/spn_index"];
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
      |--------------------------------------------------------------------------------
      | This Function use for load model
      |--------------------------------------------------------------------------------
     */
    private function _loadModel() {
      $this->load->model($this->config->item("symptoms_model"));
    }
    /*
    |--------------------------------------------------------------------------------
    | This Function use to get the all symptoms data
    | @return -- json data 
    |--------------------------------------------------------------------------------
    */
    public function get_all_symptoms_get()
      {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->_loadModel();
            $allsymptoms = $this->symptoms_model->get_all_symptoms_model($this->headers['Accept-Language']);
            $this->response_send = ["symptoms" => $allsymptoms,"status" => $this->config->item("status_true")];
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];     
        }
        $this->response($this->response_send);
    }
    /*
    |-------------------------------------------------------------------------------------------
    | This Function use to get the Severity of Symptoms
    | @return -- json data 
    |-------------------------------------------------------------------------------------------
    */
      public function get_severity_symptoms_get()
      {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->_loadModel();
            $severity_symptoms = $this->symptoms_model->get_all_severity_symptoms_model($this->headers['Accept-Language']);
            $this->response_send = ["severity_of_symptoms" => $severity_symptoms,"status" => $this->config->item("status_true")];
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];     
        }
        $this->response($this->response_send);
    }
}
?>