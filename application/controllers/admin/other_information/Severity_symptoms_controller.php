<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Severity_symptoms_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "other_information/severity_symptoms";
    protected $model = 'admin/other_information/severity_symptom_model';
    protected $is_model = "severity_symptom";

    public function __construct() {

        parent::__construct();
        $this->user_not_loggedin();
        language_helper($this->language_file);
        $this->isModelload();
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- render home page listing
      |	@return -- null
      |-----------------------------------------------------------------------------
     */

    public function index() {
        $this->data['items'] = $this->{$this->is_model}->get_all_severity_symptoms();

        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/other_information/severity_symptoms/severity_symptoms_view";
        $this->data['add_datatable_js'] = "other_information/severity_symptoms.js";
        //$this->data['add_datatable_js'] = "other_information/medications.js";
        $this->data['active_class1'] = "active";
        $this->data['open3'] = "open";
        $this->displayview($this->data);
    }


    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_severity_symptom_info() {
        $this->data['page_title'] = $this->lang->line("add_title");


        if (isset($_FILES['severity_symptoms_csv']['name']) && $_FILES['severity_symptoms_csv']['name'] != '') {
            $response = $this->severity_symptom_csv_upload($_FILES);
            if (isset($response['error'])) {
                $this->data['error'] = $response['error'];
            }else{
                $result = insertCsvData($response,"severity_of_symptoms"); // making in helper function
                
                if($result != NULL || $result == false){
                    
                    if($result){
                        $this->data['success'] = $this->lang->line("csv_uploaded");
                    }else{
                        $this->data['error'] = $this->lang->line("csv_error");
                    }
                }else{
                    $this->data['error'] = $this->lang->line("csv_already_uploaded");
                }
            }
        } else {
            if ($this->input_validations()) {
                $input_data = $this->security->xss_clean($this->input->post());
                $this->item_id = $this->{$this->is_model}->add_and_update_severity_symptoms($input_data);
                  $this->session->set_flashdata("flashsuccess", $this->lang->line("success_severity_symptom_save"));
                redirect("admin/other_information/severity_symptoms_controller/edit_severity_symptom_info/" . $this->item_id);
            }
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['active_class1'] = "active";
        $this->data['open3'] = "open";
        $this->data['add_datatable_js'] = "other_information/severity_symptoms.js";
        $this->data['view'] = "admin/other_information/severity_symptoms/add_severity_symptom_view";
        $this->displayview($this->data);
    }

    private function severity_symptom_csv_upload($file, $id = null) {
        
            $this->load->library("common");
            $this->load->helper('string');
            $rename_image = (random_string('numeric') + time()) . random_string();
            $img_data = $this->common->file_upload("assets/admin/img/other_information", "severity_symptoms_csv", $rename_image,$fileformat = "csv");
            if (isset($img_data["upload_data"]['file_name'])) {
                $new_file_name = $img_data["upload_data"]['file_name'];
                $file_url = base_url() . "assets/admin/img/other_information/" . $new_file_name;
                return  $file_url;
            } else {
                return $img_data;
            }
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function edit_severity_symptom_info($id) {
        $this->data['page_title'] = $this->lang->line("edit_title");
        $this->data['items'] = $this->{$this->is_model}->get_severity_symptom_info($id);
        $this->data['success'] = ($this->session->flashdata("flashsuccess")) ? $this->session->flashdata("flashsuccess") : '';

        if ($this->input_validations($id, $this->data['items'])) {
            $input_data = $this->security->xss_clean($this->input->post());
            $this->{$this->is_model}->add_and_update_severity_symptoms($input_data, $id);
            //$this->data['success'] = $this->lang->line("success_severity_symptom_edit_save");
            $this->session->set_flashdata("flashsuccess",$this->lang->line("success_severity_symptom_edit_save"));
            redirect("admin/other_information/severity_symptoms_controller/edit_severity_symptom_info/$id");
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['active_class1'] = "active";
        $this->data['open3'] = "open";
        $this->data['view'] = "admin/other_information/severity_symptoms/edit_severity_symptom_view";
        $this->displayview($this->data);
    }

    /*
      |--------------------------------------------------------------
      | Work - validation of form input
      | @return - true/false
      |---------------------------------------------------------------
     */

    public function input_validations($id = null, $input_data = null) {
        $this->load_validation_lib();
        $this->load->helper('security');

        $is_unique_name = ($id > 0 && strtolower($this->input->post("name")) == strtolower($input_data->name)) ? '' : "|is_unique[symptom.name]";

        $is_unique_spn_name = ($id > 0 && strtolower(trim($this->input->post("sp_name"))) == strtolower($input_data->sp_name)) ? '' : "|is_unique[symptom.sp_name]";

        $this->form_validation->set_rules("name", "English Name", "required|trim|xss_clean|min_length[2]|max_length[45]" . $is_unique_name);

        $this->form_validation->set_rules("additional_info", "Additional Info", "required|trim|xss_clean");

        $this->form_validation->set_rules("sp_name", "Spanish Name", "required|trim|xss_clean|min_length[2]|max_length[45]" . $is_unique_spn_name);

        $this->form_validation->set_rules("sp_additional_info", "Spanish Additional Info", "required|trim|xss_clean");
        return $this->form_validation->run();
    }

    /*
      for testing purpose
     */

    public function delete($id) {   
       $this->db->where('id', $id);
       $this->db->update("severity_of_symptoms",["is_deleted"=>1]); 
       $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_severity_symptom"));
       redirect("admin/other_information/severity_symptoms_controller/index");
    }

}

?>
