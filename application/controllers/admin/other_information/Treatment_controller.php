<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Treatment_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "other_information/treatment";
    protected $model = 'admin/other_information/treatment_model';
    protected $is_model = "treatment_model";

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
        $this->data['items'] = $this->{$this->is_model}->get_all_treatment();
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/other_information/treatment/treatment_view";
        $this->data['add_datatable_js'] = "other_information/treatment.js";
        $this->data['active_class1'] = "active";
        $this->data['open9'] = "open";
        $this->displayview($this->data);
    }


    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_treatment_info() {
        $this->data['page_title'] = $this->lang->line("add_title");
        $this->data['symptoms_list'] = $this->{$this->is_model}->get_all_symptoms();

        if (isset($_FILES['treatment_csv']['name']) && $_FILES['treatment_csv']['name'] != '') {
            $response = $this->treatment_csv_upload($_FILES);
            if (isset($response['error'])) {
                $this->data['error'] = $response['error'];
            }else{
                $result = insertCsvData($response,"treatment_option"); // making in helper function
                
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
                
                $this->item_id = $this->{$this->is_model}->add_and_update_treatment($input_data);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("success_treatment_save"));
                redirect("admin/other_information/treatment_controller/edit_treatment_info/" . $this->item_id);
            }
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['add_datatable_js'] = "other_information/treatment.js";
        $this->data['view'] = "admin/other_information/treatment/add_treatment_view";
        $this->data['active_class1'] = "active";
        $this->data['open9'] = "open";
        $this->displayview($this->data);
    }

    private function treatment_csv_upload($file, $id = null) {
        
            $this->load->library("common");
            $this->load->helper('string');
            $rename_image = (random_string('numeric') + time()) . random_string();
            $img_data = $this->common->file_upload("assets/admin/img/other_information", "treatment_csv", $rename_image,$fileformat = "csv");
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
      |		Work -- Add new treatment
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function edit_treatment_info($id) {
        $this->data['page_title'] = $this->lang->line("edit_title");
        $this->data['items'] = $this->{$this->is_model}->get_treatment_info($id);
        $this->data['success'] = ($this->session->flashdata("flashsuccess")) ? $this->session->flashdata("flashsuccess") : '';
        $this->data['symptoms_list'] = $this->{$this->is_model}->get_all_symptoms();
        if ($this->input_validations($id, $this->data['items'])) {
            $input_data = $this->security->xss_clean($this->input->post());
            $this->{$this->is_model}->add_and_update_treatment($input_data, $id);
            $this->data['success'] = $this->lang->line("success_treatment_edit_save");
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/other_information/treatment/edit_treatment_view";
        $this->data['active_class1'] = "active";
        $this->data['open9'] = "open";
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

        $is_unique_name = ($id > 0 && strtolower($this->input->post("title")) == strtolower($input_data->title)) ? '' : "|is_unique[provider_plan.title]";

        $is_unique_spn_name = ($id > 0 && strtolower(trim($this->input->post("title_spn"))) == strtolower($input_data->title_spn)) ? '' : "|is_unique[provider_plan.title_spn]";

        $this->form_validation->set_rules("title", "English Name", "required|trim|xss_clean|min_length[5]|max_length[45]" . $is_unique_name);

        $this->form_validation->set_rules("description", "Additional Info", "required|trim|xss_clean");
        $this->form_validation->set_rules("treatment_plan_id[]", "Treatment Option", "required|trim|xss_clean");

        $this->form_validation->set_rules("title_spn", "Spanish Name", "required|trim|xss_clean|min_length[5]|max_length[45]" . $is_unique_spn_name);

        $this->form_validation->set_rules("description_spn", "Spanish Additional Info", "required|trim|xss_clean");
        return $this->form_validation->run();
    }

    /*
      for testing purpose
     */

    public function delete($id) { 
       $this->db->where('id', $id);
       $this->db->update("provider_plan",['is_deleted'=>1]); 
       $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_treatment"));
       redirect("admin/other_information/treatment_controller/index");
    }
    function update_rcmd_status() {
        $id = $this->input->post("id");
        $this->{$this->is_model}->update_status($id);
        echo json_encode(["message"=>"Status Updated"]);
        exit();
    }
}

?>
