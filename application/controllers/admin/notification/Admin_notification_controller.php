<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_notification_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "notification/notification";
    protected $model = 'admin/notification/notification_model';
    protected $is_model = "notification_model";
    protected $input_data = '';

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
        $this->data['items'] = $this->{$this->is_model}->get_all_notification();
        $this->data['schedule_list'] = $this->{$this->is_model}->get_all_notification_schedule();
        $this->data['users_list'] = $this->{$this->is_model}->get_all_users();
        $this->data['new_users_list'] = $this->{$this->is_model}->GetAllNewUsers();
        $this->data['new_doctor_list'] = $this->{$this->is_model}->GetAllNewDoctors();   
        $this->data['doctors_list'] = $this->{$this->is_model}->get_all_doctors();
        
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/notification/notification_view";
        $this->data['add_datatable_js'] = "other_information/notification.js";
        $this->displayview($this->data);
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_notification_info() {
        $this->data['page_title'] = $this->lang->line("add_title");
        if ($this->input_validations()) {
            $input_data = $this->security->xss_clean($this->input->post());

            $this->item_id = $this->{$this->is_model}->add_and_update_notification($input_data);
            $this->session->set_flashdata("flashsuccess", $this->lang->line("success_notification_save"));
            redirect("admin/notification/admin_notification_controller/edit_notification_info/" . $this->item_id);
        }

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/notification/add_notification_view";
        $this->displayview($this->data);
    }

    // update the user ids and notification type
    public function update_user_ids() {
        $data = $this->input->post();
        if ($this->update_user_ids_validation()) {

            $result = $this->{$this->is_model}->update_user_ids_model($this->input->post());
            if ($result) {
                $this->session->set_flashdata("flashsuccess", $this->lang->line("record_updated"));
                redirect("admin/notification/admin_notification_controller");
            }
        }
        $this->index();
    }

    public function update_user_ids_validation() {
        $this->load_validation_lib();
        //dd($this->input->post());
        if (count($this->input->post("doctor_ids")) > 0) {
            $this->form_validation->set_rules("doctor_ids[]", "Doctor Id", "required");
        }
        if (count($this->input->post("user_ids")) > 0) {
            $this->form_validation->set_rules("user_ids[]", "User Id", "required");
        }
        $this->form_validation->set_rules("notification_type", "Notification Type", "required|min_length[1]|numeric");
        $this->form_validation->set_rules("id", "Record Id", "required|numeric");
        return $this->form_validation->run();
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function edit_notification_info($id) {
        $this->data['page_title'] = $this->lang->line("edit_title");
        $this->data['items'] = $this->{$this->is_model}->get_notification_info($id);
        $this->data['success'] = ($this->session->flashdata("flashsuccess")) ? $this->session->flashdata("flashsuccess") : '';

        if ($this->input_validations($id, $this->data['items'])) {
            $input_data = $this->security->xss_clean($this->input->post());
            $this->{$this->is_model}->add_and_update_notification($input_data, $id);
            //$this->data['success'] = $this->lang->line("success_notification_edit_save");
            $this->session->set_flashdata("flashsuccess",$this->lang->line("success_notification_edit_save"));
            redirect("admin/notification/admin_notification_controller/edit_notification_info/$id");
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/notification/edit_notification_view";
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

        $is_unique_name = ($id > 0 && strtolower($this->input->post("name")) == strtolower($input_data->name)) ? '' : "|is_unique[notification_content.name]";

        $is_unique_spn_name = ($id > 0 && strtolower(trim($this->input->post("sp_name"))) == strtolower($input_data->sp_name)) ? '' : "|is_unique[notification_content.sp_name]";

        $this->form_validation->set_rules("name", "English Name", "required|trim|xss_clean|min_length[5]|max_length[45]" . $is_unique_name);

        $this->form_validation->set_rules("additional_info", "Additional Info", "required|trim|xss_clean");

        $this->form_validation->set_rules("sp_name", "Spanish Name", "required|trim|xss_clean|min_length[5]|max_length[45]" . $is_unique_spn_name);

        $this->form_validation->set_rules("sp_additional_info", "Spanish Additional Info", "required|trim|xss_clean");
        return $this->form_validation->run();
    }

    /*
      for deleting
     */

    public function delete($id) {
      
        $this->db->where('id', $id);
        $this->db->delete("notification_content");
        $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_notification"));
        redirect("admin/notification/admin_notification_controller/index");
    }

    // daily ,weekly,monthly,6 month
    public function updatescheduler_id() {
        $this->input_data = $this->input->post();
        //dd($this->input_data);
        if (isset($this->input_data['confirm'])) {
            if ($this->updatescheduler_id_validation()) {
                $this->{$this->is_model}->updatescheduler_id_model($this->input_data);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("record_updated"));
                redirect("admin/notification/admin_notification_controller");
            }
        }
        $this->index();
    }

    public function updatescheduler_id_validation() {
        $this->load_validation_lib();
        if (isset($this->input_data['schedule_time'])) {
            $this->form_validation->set_rules("schedule_time", "Schedule Time", "required");
        }
        $this->form_validation->set_rules("item_id", "Item Id", "required|min_length[1]|numeric");
        $this->form_validation->set_rules("notification_scheduler_id", "Notification Id", "required|min_length[1]|numeric");

        return $this->form_validation->run();
    }

}

?>
