<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Prescriptions_controller extends REST_Controller {

    protected $response_send = ["status" => false];
    protected $language_file = ["api_message", "spn_api_message"];
    protected static $exam_fields = ['patient_id', 'doctor_id', 'name', 'details', 'appointment_id'];
    protected $headers;
    protected $appoinment_data;
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
            $this->load->model("api/prescription_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work --  This function is used for get users prescription
      | @return -- jeson response
      |--------------------------------------------------------------------------------
     */

    public function user_prescriptions_get() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $user_id = $this->get();
            $data = array();
            $result = $this->prescription_model->user_prescritpions_model($user_id['user_id'], $offset = null, $this->headers['Accept-Language']);

            if ($result) {
                if ($result != 'no_data') {
                    foreach ($result as $k => $v) {
                        $data[$k]['symptoms'] = explode(",", $v['symptoms']);
                        if (isset($v['medications']) && $v['medications'] != '') {
                            $data[$k]['medications'] = [$v['medications']];
                        } else {
                            $data[$k]['medications'] = null;
                        }
                        $data[$k]['name'] = $v['name'];
                        $data[$k]['appointment_id'] = $v['appointment_id'];
                        $data[$k]['prescription_id'] = $v['prescription_id'];
                        $data[$k]['doctor_age'] = $v['doctor_age'];
                        $data[$k]['doctor_gender'] = $v['doctor_gender'];
                    }
                    $this->response_send = ["prescritpions" => $data, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line("appointment_not_exist"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function doctor_prescriptions_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_id = $this->get();

            $data = array();
            $result = $this->prescription_model->doctor_prescritpions_model($doctor_id['doctor_id'], $offset = null, $this->headers['Accept-Language']);
            //dd($result);
            if ($result) {
                if ($result != 'no_data') {
                    foreach ($result as $k => $v) {
                        //echo $k
                        $result[$k]['symptoms'] = explode(",", $v['symptoms']);
                        if (isset($v['medications']) && $v['medications'] != '') {
                            $result[$k]['medications'] = [$v['medications']];
                        } else {
                            $result[$k]['medications'] = null;
                        }
                        $result[$k]['patient'] = array_combine(["id", "name", "gender", "age"], explode("|", $v['patient']));
                        $result[$k]['appointment_id'] = $v['appointment_id'];
                    }
                    $this->response_send = ["prescritpions" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line("appointment_not_exist"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function doctor_prescriptions_past_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_id = $this->get();

            $data = array();
            $result = $this->prescription_model->doctor_prescritpions_past_model($doctor_id['doctor_id'], $offset = null, $this->headers['Accept-Language']);
            //dd($result);
            if ($result) {
                if ($result != 'no_data') {
                    foreach ($result as $k => $v) {
                        //echo $k
                        $data[$k]['symptoms'] = explode(",", $v['symptoms']);
                        if (isset($v['medications']) && $v['medications'] != '') {
                            $data[$k]['medications'] = explode("||||", $v['medications']);
                        } else {
                            $data[$k]['medications'] = null;
                        }
                        $data[$k]['patient'] = array_combine(["id", "name", "gender", "age"], explode("|", $v['patient']));
                        $data[$k]['appointment_id'] = $v['appointment_id'];
                        $data[$k]['patient_availability_date_and_time'] = $v['patient_availability_date_and_time'];
                        $data[$k]['type'] = $v['type'];
                    }
                    $this->response_send = ["past_appointments" => $data, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line("appointment_not_exist"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // when prescription will get or not from database then show the appointment data
    public function doctor_to_patient_prescriptions_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_id = $this->get();

            $data = array();
            $result = $this->prescription_model->doctor_to_patient_prescritpions_model(@$doctor_id['doctor_id'], @$doctor_id['patient_id'], @$doctor_id['appointment_id'], $this->headers['Accept-Language']);
            //dd($result);
            if ($result) {
                if ($result != 'no_data') {
                    foreach ($result as $k => $v) {
                        //echo $k
                        $result[$k]['symptoms'] = explode(",", $v['symptoms']);
                        if (isset($v['medications']) && $v['medications'] != '') {
                            $result[$k]['medications'] = explode("||||", $v['medications']);
                        } else {
                            $result[$k]['medications'] = null;
                        }
                        $result[$k]['patient'] = array_combine(["id", "name", "gender", "age"], explode("|", $v['patient']));
                        $result[$k]['appointment_id'] = $v['appointment_id'];
                    }
                    $this->response_send = ["prescritpions" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line("appointment_not_exist"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // add the data of exam for making the same url
    public function exam_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = form_input_filter(json_decode(file_get_contents('php://input'), true));
            if (check_form_array_keys_existance($data, self::$exam_fields) && check_user_input_values($data)) {
                $result = $this->prescription_model->add_exam_model($data);
                $this->response_send = ["message" => $this->lang->line('add_exam'), "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get the data of exam for making the same url
    public function exam_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");

            $data = $this->get();
            if (check_form_array_keys_existance($data, ['patient_id', 'doctor_id', 'appointment_id']) && check_user_input_values($data)) {
                $result = $this->prescription_model->get_exam_model($data);
                if ($result) {
                    $this->response_send = ["exams" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get the data of diagnosis for making the same url
    public function diagnosis_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");

            $data = form_input_filter(json_decode(file_get_contents('php://input'), true));
            if (check_form_array_keys_existance($data, self::$exam_fields) && check_user_input_values($data)) {
                $result = $this->prescription_model->add_diagnosis_model($data);
                $this->response_send = ["message" => $this->lang->line('add_diagnosis'), "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get the data of diagnosis for making the same url
    public function diagnosis_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = $this->get();

            if (check_form_array_keys_existance($data, ['patient_id', 'doctor_id', 'appointment_id']) && check_user_input_values($data)) {
                $result = $this->prescription_model->get_diagnosis_model($data);
                if ($result) {
                    $this->response_send = ["diagnosis" => $result, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function diagnosis_put() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = $this->put();
            if (check_form_array_keys_existance($data, ['diagnosis_id', 'name', 'details']) && check_user_input_values($data)) {
                $result = $this->prescription_model->add_diagnosis_model($data, $data['diagnosis_id']);
                $this->response_send = ["message" => $this->lang->line('update_diagnosis'), "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // get doctor with patient prescription screen number 35 
    public function doctor_patient_prescritpions_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = $this->get();

            if (check_form_array_keys_existance($data, ['patient_id', 'doctor_id']) && check_user_input_values($data)) {
                $result = $this->prescription_model->get_doctor_patient_prescritpions($data);
                $this->response_send = ["patient_info" => $result[0], "upcomming_appointments" => $result[1], "past_appointments" => $result[2], "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    /*
     * Add prescription by the doctor 
     */

    public function doctor_prescriptions_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $input_data = json_decode(file_get_contents('php://input'), true);
            //&& check_user_input_values_null($input_data)
            if (count($input_data['medication']) > 0 && check_form_array_keys_existance($input_data, ['appointment_id', 'doctor_id', 'patient_id', 'diagnosis_id', 'medication']) && check_user_input_values(['doctor_id' => $input_data['doctor_id'], "appointment_id" => $input_data['appointment_id'], "patient_id" => $input_data['patient_id']])) {
                $result = $this->prescription_model->addprescription_model($input_data, @$this->headers['Accept-Language']);
                $result['notify_time'] = $this->config->item("date");
                if ($result) {
                    $response = send_notification($this->lang->line('new_prescription_title'),sprintf($this->lang->line('prescription_body'),$result['name']),$this->lang->line('precpt_constant'),$result['device_token'],$result);
                    $this->prescription_model->add_new_prescptn_data($response["title"],$input_data['appointment_id'],$result,$response['fcm_resp']);
                    $this->response_send = ["message" => $this->lang->line('added_prescription'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function get_medication_detail_get() {
        try {

            //check_acces_token(@$this->headers['Authorization'], null, 'doctors');
            $data = $this->get();
            if (check_form_array_keys_existance($data, ["prescription_id"]) && check_user_input_values($data)) {
                $result = $this->prescription_model->get_single_medication($data['prescription_id']);
                if ($result) {
                    $this->response_send = ["status" => $this->config->item("status_true"), "medication" => $result];
                } else {
                    $this->response_send = ["status" => $this->config->item("status_true"), "message" => $this->lang->line("no_data_found")];
                }
            } else {
                $this->response_send = ["status" => false, "message" => $this->lang->line("all_field_required")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

}

?>