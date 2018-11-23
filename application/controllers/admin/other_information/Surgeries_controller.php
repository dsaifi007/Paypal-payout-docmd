<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Surgeries_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "other_information/surgeries";
    protected $model = 'admin/other_information/surgeries_model';
    protected $is_model = "surgeries_model";

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
        $this->data['items'] = $this->{$this->is_model}->get_all_surgeries_list();

        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/other_information/surgeries/surgeries_view";
        $this->data['add_datatable_js'] = "other_information/surgeries.js";
                       $this->data['active_class1'] = "active";
        $this->data['open8'] = "open";
        $this->displayview($this->data);
    }


    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_surgeries_info() {
        $this->data['page_title'] = $this->lang->line("add_title");
        if (isset($_FILES['surgeries_csv']['name']) && $_FILES['surgeries_csv']['name'] != '') {
            $response = $this->surgeries_csv_upload($_FILES);
            if (isset($response['error'])) {
                $this->data['error'] = $response['error'];
            }else{
                $result = insertCsvData($response,"surgeries"); // making in helper function
                
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
                $this->item_id = $this->{$this->is_model}->add_and_update_surgeries($input_data);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("success_surgeries_save"));
                redirect("admin/other_information/surgeries_controller/edit_surgeries_info/" . $this->item_id);
            }
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['add_datatable_js'] = "other_information/surgeries.js";
        $this->data['view'] = "admin/other_information/surgeries/add_surgeries_view";
         $this->data['active_class1'] = "active";
        $this->data['open8'] = "open";
        $this->displayview($this->data);
    }

    private function surgeries_csv_upload($file, $id = null) {
        
            $this->load->library("common");
            $this->load->helper('string');
            $rename_image = (random_string('numeric') + time()) . random_string();
            $img_data = $this->common->file_upload("assets/admin/img/other_information", "surgeries_csv", $rename_image,$fileformat = "csv");
            if (isset($img_data["upload_data"]['file_name'])) {
//                if ($id != null) {
//                    remove_existing_img($id, $this->symptoms_csv, "medication_csv", "assets/admin/img/other_information");
//                }
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

    public function edit_surgeries_info($id) {
        $this->data['page_title'] = $this->lang->line("edit_title");
        $this->data['items'] = $this->{$this->is_model}->get_surgeries_info($id);
        $this->data['success'] = ($this->session->flashdata("flashsuccess")) ? $this->session->flashdata("flashsuccess") : '';

        if ($this->input_validations($id, $this->data['items'])) {
            $input_data = $this->security->xss_clean($this->input->post());
            $this->{$this->is_model}->add_and_update_surgeries($input_data, $id);
            //$this->data['success'] = $this->lang->line("success_surgeries_edit_save");
            $this->session->set_flashdata("flashsuccess",$this->lang->line("success_surgeries_edit_save"));
            redirect("admin/other_information/surgeries_controller/edit_surgeries_info/$id");
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/other_information/surgeries/edit_surgeries_view";
         $this->data['active_class1'] = "active";
        $this->data['open8'] = "open";
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
       $this->db->update("surgeries",["is_deleted"=>1]); 
       $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_surgeries"));
       redirect("admin/other_information/surgeries_controller/index");
    }

}

?>
