<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Promocode_controller extends REST_Controller {

    protected $response_send = ["status" => false];
    protected $language_file = ["api_message", "spn_api_message"];
    protected $headers;
    protected $data;
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
            $this->load->model("api/promocode_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function getPromocode_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->data, ['promocode', "user_id", "treatment_provider_plan_id"]) && check_user_input_values($this->data)) {
                $result = $this->promocode_model->promocode_model($this->data);
                if ($result === true) { // already used promocode
                    $this->response_send = ["message" => $this->lang->line('promocode_already_used'), "status" => $this->config->item("status_false")];
                } elseif (count($result) > 0 && is_array($result)) {
                    $this->response_send = ["promocode" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('promocode_expired'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function promocodeList_get() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $result = $this->promocode_model->listPromocode_model();
            if ($result) {
                $this->response_send = ["promocodes" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

}

?>