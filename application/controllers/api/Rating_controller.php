<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Rating_controller extends REST_Controller {

    private $response_send = ["status" => false];
    private $is_model = "api/rating_model";
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

    // add rating of the user or provider
    public function add_rating_post() {
        try {
            $data = form_input_filter(json_decode(file_get_contents('php://input'), true));
            if ($data['who_rate'] == "user") {
                check_acces_token(@$this->headers['Authorization']);
            } else {
                check_acces_token(@$this->headers['Authorization'], null, "doctors");
            }
            if (check_form_array_keys_existance($data, ['who_rate', 'rating_given_by_id', 'rating_given_to_id', 'rating','app_rating']) && check_user_input_values($data)) {
                $result = $this->rating_model->add_rating_model($data);
                if ($result) {
                    $this->response_send = ["message" => $this->lang->line('rating_added'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('user_or_provider_not_exist'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function add_app_rating_post() {
        try {
            $data = form_input_filter(json_decode(file_get_contents('php://input'), true));
            if ($data['type'] == "user") {
                check_acces_token(@$this->headers['Authorization']);
            } else {
                check_acces_token(@$this->headers['Authorization'], null, "doctors");
            }
            if (check_form_array_keys_existance($data, ['type', 'rating', 'review']) && check_user_input_values($data) && (@$data['user_id'] != '' || @$data['doctor_id'] != '')) {
                $result = $this->rating_model->add_app_rating_model($data);
                $this->response_send = ["message" => $this->lang->line('rating_added'), "status" => $this->config->item("status_true")];
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
