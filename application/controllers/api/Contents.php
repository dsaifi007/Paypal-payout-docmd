<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Contents extends REST_Controller {

    protected $response_send = [];
    protected $questions = [];
    protected $is_model = "api/content_model";
    private $language_file = ["api_message", "spn_api_message"];
    private $headers;

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

//    public function get_faq_list_get() {
//        try {
//            //die("dd");
//            $data = $this->get();
//            if ($data['type'] == "user") {
//                check_acces_token(@$this->headers['Authorization']);
//            } else {
//                check_acces_token(@$this->headers['Authorization'], null, "doctors");
//            }
//            $this->load->model($this->is_model);
//
//            $faq_records = $this->content_model->get_allrecords($data['type'], $this->headers['Accept-Language']);
//            if ($faq_records != false) {
//            $main_array = [];
//            $main_array2 = [];
//            foreach ($faq_records as $faq_record) {
//                $q = explode("----", $faq_record['question']);
//                $ans = explode("----", $faq_record['answer']);
//                $sub_array = [];
//                foreach ($q as $k => $v) {
//                    $array = ["question" => $v,
//                        "answer" => $ans[$k]
//                    ];
//                    array_push($sub_array, $array);
//                }
//                $main_array['category_name'] = $faq_record['category'];
//                $main_array['questions'] = $sub_array;
//                array_push($main_array2, $main_array);
//            }
//
//                $this->response_send = ["faqs" => $main_array2, "status" => $this->config->item("status_true")];
//            } else {
//                $this->response_send = ["message" => $this->lang->line('no_faq_found'), "status" => $this->config->item("status_false")];
//            }
//        } catch (Exception $exc) {
//            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
//        }
//        $this->response($this->response_send);
//    }
    public function get_faq_list_get() {
        try {
            $data = $this->get();

            if (@$data['type'] == "user") {
                check_acces_token(@$this->headers['Authorization']);
            } else {
                check_acces_token(@$this->headers['Authorization'], null, "doctors");
            }
            $field = ($this->headers['Accept-Language'] == "spn") ? "faq_category.sp_category AS category_name,GROUP_CONCAT(CONCAT(faq.sp_question,'||',sp_answer) SEPARATOR '||||') as questions" : "faq_category.category AS category_name,GROUP_CONCAT(CONCAT(faq.question,'||',answer) SEPARATOR '||||') as questions";
            $this->db->where("faq_category.type", $data['type']);
            $this->db->group_by("faq.faq_cat_id");
            $this->db->order_by("faq.id", "ASC");
            $this->db->select($field);
            $this->db->from("faq");
            $this->db->join("faq_category", "faq_category.id=faq.faq_cat_id", "INNER");
            $query = $this->db->get();
            $result = $query->result_array();
            $i = 0;
            $arr = array();
            // Will start from monday 04/june/2018 ----------------------------------------------------
            if ($result) {
                foreach ($query->result_array() as $key => $value) {
                    $result[$key]['questions'] = explode("||||", $value['questions']);
                    //$result[$key]['qus_ans_spn'] = explode("||||", $value['qus_ans_spn']);
                    foreach ($result[$key]['questions'] as $k => $v) {
                        unset($result[$key]['questions'][$k]);
                        //$result[$key]['questions'][$k] = (object)explode("||", $v);
                        $arr = explode("||", $v);
                        $result[$key]['questions'][$k] = ["question" => $arr[0], "answer" => $arr[1]];
                    }
                    $i = 0;
                }
            }
            $this->response_send = ["faqs" => ($result) ? $result : null, "status" => $this->config->item("status_true")];
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function about_us_get() {
        try {
            $select_options = ($this->headers['Accept-Language'] == "spn" ) ? "sp_title as  title,sp_description as description" : "title,description";
            $query = $this->db->select($select_options)->from("content")->get();

            if (!empty($query->result_array()) && count($query->result_array()) > 0) {
                $this->response_send = ["abouts" => $query->result_array(), "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('no_about_found'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

}
?>



