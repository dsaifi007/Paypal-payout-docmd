<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Consultant_fees_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "consultant_fees/index_lang";

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
        $this->data['items'] = $this->db->select("id,title,amount")->from("provider_plan")->get()->result_array();
        //dd($this->data['items']);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/consultant_fees/index";
        //$this->data['add_datatable_js'] = "other_information/degree.js";
        $this->displayview($this->data);
    }

    public function input_validations() {
        $this->load_validation_lib();
        $this->form_validation->set_rules("title_0", "Message", "required|trim|min_length[1]|max_length[5]|numeric");
        $this->form_validation->set_rules("title_1", "Audio", "required|trim|min_length[1]|max_length[5]|numeric");
        $this->form_validation->set_rules("title_2", "Video", "required|trim|min_length[1]|max_length[5]|numeric");
        $this->form_validation->set_rules("id_0", "Id", "required|trim|numeric");
        $this->form_validation->set_rules("id_1", "Id", "required|trim|numeric");
        $this->form_validation->set_rules("id_2", "Id", "required|trim|numeric");
        return $this->form_validation->run();
    }

    public function form_submited() {
        if ($this->input_validations()) {
            $insert_array = [];
            $data = $this->input->post();
            $i = 0;
            $total_record = ceil(count($data) / 2);
            foreach ($data as $key => $value) {
                if ($i < $total_record) {
                    $insert_array[] = ['amount' => $data["title_" . $i . ""], "id" => $data["id_" . $i . ""]];
                    $i++;
                }
            }
            $this->db->update_batch("provider_plan", $insert_array, "id");
            $this->session->set_flashdata("flashsuccess", "Records Updated successfully");
            redirect("admin/consultant_fees/consultant_fees_controller/index");
        }
        $this->index();
    }

}

?>
