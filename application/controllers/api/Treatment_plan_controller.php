<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Treatment_plan_controller extends REST_Controller {

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
        $this->load->model($this->config->item("treatment_plan_model"));
    }

    /*
      |--------------------------------------------------------------------------------
      | This Function use to get all treatment plan
      |--------------------------------------------------------------------------------
     */

    public function get_all_treatment_plan_get() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->_loadModel();
            $alltreatment_plan = $this->treatment_plan_model->get_all_treatment_plan(@$this->headers['Accept-Language']);
            $this->response_send = ["provider_plan" => $alltreatment_plan, "status" => $this->config->item("status_true")];
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function get_all_treatment_plan_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->_loadModel();

            $data = json_decode(file_get_contents('php://input'), true);

            if (check_form_array_keys_existance($data, ['symptom_ids']) && count($data['symptom_ids']) > 0) {
                //dd($data);
                $alltreatment_plan = $this->treatment_plan_model->get_all_treatment_plan_based_on_symptoms($data);
                if ($alltreatment_plan) {
                    $this->response_send = ["provider_plan" => $alltreatment_plan, "status" => $this->config->item("status_true")];
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

}

?>