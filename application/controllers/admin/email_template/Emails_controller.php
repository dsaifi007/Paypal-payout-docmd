<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Emails_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "email_template/emails";
    protected $model = 'admin/email_template/email_model';
    protected $is_model = "email_model";
    protected $filter = [];

    public function __construct() {

        parent::__construct();
        $this->user_not_loggedin();
        language_helper($this->language_file);
        $this->isModelload();
    }

    /*
      |-----------------------------------------------------------------------------
      | Work -- render home page listing
      | @return -- null
      |-----------------------------------------------------------------------------
     */

    public function index() {

        $this->data['page_title'] = $this->lang->line("manual_title");
        $this->data['items'] = $this->{$this->is_model}->get_all_email_templates();
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->data['auto_class'] = "active open";
        $this->data['auto'] = "open";
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/email_template/automatic_view";
        $this->displayview($this->data);
    }

    public function edit_automatic_email() {
        
        if ($this->input_validations()) {
            $input_data = $this->input->post();
           
            if (isset($input_data['save'])) {
                //$input_data['email_attechment'] = $this->uploade_file($_FILES);
                $file_data = $this->uploade_file($_FILES, $input_data['id']);
                 
                unset($input_data['save']);
                if (isset($file_data['error'])) {
                    $this->session->set_flashdata("flasherror", $file_data['error']);
                } else {
                    if (!empty($file_data)) {
                        $input_data['email_attechment'] = $file_data[0];
                        $input_data['file_name'] = $file_data[1];
                    }
                    $this->{$this->is_model}->update_automatic_email_template($input_data);
                    $this->session->set_flashdata("flashsuccess", $this->lang->line("success_email_save"));
                }
            }
            redirect("admin/email_template/emails_controller/email_auto_edit/".$input_data['id']);
        }
        $this->index();
    }

    private function uploade_file($file, $id = null) {

        if (!empty($file['email_attechment']['name'])) {
            $file_name = $file['email_attechment']['name'];

            $this->load->library("common");

            $this->load->helper('string');

            $rename_image = (random_string('numeric') + time()) . random_string();
            $file_type = "zip|rar|tar|gzip|mp3|mpga|wav|jpeg|jpg|png|gif|bmp|txt|doc|docx|xls|xlsx|pdf";

            $data = $this->common->file_upload("assets/admin/img/email_template", "email_attechment", $rename_image, $file_type);

            if (isset($data["upload_data"]['file_name'])) {
                if ($id != null) {
                    remove_existing_img($id, "email_templates", "file_name", "assets/admin/img/email_template");
                }
                $new_file_name = $data["upload_data"]['file_name'];
                //$file_url = base_url() . "assets/admin/img/email_template/" . $new_file_name;
                $file_url =$data["upload_data"]['full_path'];
                return [$file_url, $new_file_name];
            } else {
                return $data;
            }
        }
    }

    /*
      |-----------------------------------------------------------------------------
      | Work -- render home page listing
      | @return -- null
      |-----------------------------------------------------------------------------
     */

    public function manual_email_template() {
        $this->data['filter'] = 'accept';
        if (count($this->input->post() > 0) && !empty($this->input->post())) {
            $this->data['filter'] = $this->input->post();
        }
        $this->data['auto_class'] = "active open";
        $this->data['manual_class'] = "open";
        $this->data['page_title'] = $this->lang->line("manual_title");
        $this->data['items'] = $this->{$this->is_model}->get_all_manual_email_templates($this->data['filter']);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/email_template/manual_view";
        $this->displayview($this->data);
    }

    /*
      -------------------------------------------------------------------------------------------
      |     Work -- Add new Manual emails
      |     @return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_manual_email_info() {
        if ($this->input_validations(1)) {
          
            $input_data = $this->input->post();
            
            $data['htmldata'] = $this->input->post("message", FALSE);
            $input_data['message'] = $this->load->view("admin/email_template/email_html", $data, TRUE);
            $file = $this->uploade_file($_FILES, $input_data['id']);
            if ($file == NULL) {

                $this->{$this->is_model}->add_update_manual_emails($input_data, $input_data['id']);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("success_email_added"));
            } else {
                if (isset($file['error'])) {
                    $this->session->set_flashdata("flasherror", $file['error']);
                } else {
                    $file_array = ["email_attechment" => $file[0], "file_name" => $file[1]];
                    $post_data = array_merge($input_data, $file_array);
                    $this->{$this->is_model}->add_update_manual_emails($post_data, $input_data['id']);
                    $this->session->set_flashdata("flashsuccess", $this->lang->line("success_email_added"));
                }
            }

            $redirect_function = (isset($input_data['id']) && $input_data['id']!='') ? "email_edit/" . $input_data['id'] : "manual_email_template";
            redirect("admin/email_template/emails_controller/$redirect_function");
        }
        $this->manual_email_template();
    }

    /*
      |--------------------------------------------------------------
      | Work - validation of form input
      | @return - true/false
      |---------------------------------------------------------------
     */

    public function input_validations($id = null) {
        $this->load_validation_lib();

        // $is_unique_name = ($id > 0 && strtolower($this->input->post("name")) == strtolower($input_data->name)) ? '' : "|is_unique[symptom.name]";

        $this->form_validation->set_rules("subject", "Subject", "required|trim|min_length[2]|max_length[255]");

        $this->form_validation->set_rules("message", "Message", "required|trim");
        if ($id == null) {
            $this->form_validation->set_rules("id", "Row Id", "required|numeric");
        } else {
            $this->form_validation->set_rules("type", "Email type", "required|trim");
        }
        return $this->form_validation->run();
    }

    public function email_edit($id) {
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->data['item'] = $this->{$this->is_model}->edit_email_detail($id);
        $this->data['view'] = "admin/email_template/edit_email";
        $this->BuildFormEnv(["template_helper"]);
        $this->displayview($this->data);
    }
    
    
    public function email_auto_edit($id) {
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->data['item'] = $this->{$this->is_model}->edit_email_detail($id);
        $this->data['view'] = "admin/email_template/edit_auto_email";
        $this->BuildFormEnv(["template_helper"]);
        $this->displayview($this->data);
    }
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete("email_templates");
        $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_email"));
        redirect("admin/email_template/emails_controller/manual_email_template");
    }

}

?>
