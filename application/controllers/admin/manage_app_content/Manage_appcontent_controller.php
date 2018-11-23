<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_appcontent_controller extends MY_Controller {

    protected $data = [];
    protected $language_file = "manage_app_content/index_lang";

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
        $this->data['items'] = $this->db->select("*")->from("content")->get()->result_array();
        //dd($this->data['items']);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/manage_appcontent/index";
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
        $data = $this->input->post();
        if (isset($data['save'])) {

            unset($data['save']);
            $this->db->where("content_id", $data['content_id']);
            $this->db->update("content", $data);
            $this->session->set_flashdata("flashsuccess", $this->lang->line("content_added"));
            redirect("admin/manage_app_content/manage_appcontent_controller/index");
        }
    }

    public function faq_display($view_id = null, $edit_id = null) {
        //dd($this->data['items']);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        if ($view_id == 1) {
            $this->data['items'] = $this->db->select("id,category,sp_category")->from("faq_category")->where("id", $edit_id)->get()->row_array();

            $this->data['view'] = "admin/manage_appcontent/faq_category";
        } elseif ($view_id == 2) {
            $this->data['faq_cat'] = $this->db->select("id,category")->from("faq_category")->get()->result_array();
            $this->data['view'] = "admin/manage_appcontent/add_faq_view";
        } elseif ($view_id == 3) {
            $this->data['items'] = $this->faq_index(null, $edit_id);
            //dd($this->data['items']);
            $this->data['faq_cat'] = $this->db->select("id,category")->from("faq_category")->get()->result_array();
            $this->data['view'] = "admin/manage_appcontent/edit_faq";
        } elseif ($view_id == 5) {
            $this->data['items'] = $this->db->select("id,category,sp_category")->from("faq_category")->get()->result_array();
            $this->data['view'] = "admin/manage_appcontent/faq_category_display";
        } elseif ($view_id == 4) {
            $this->data['items'] = $this->db->select("id,consent_eng_text,consent_spn_text")->from("consent_care")->get()->row_array();
            ;
            $this->data['view'] = "admin/manage_appcontent/consent_to_care";
        } else {
            $data = $this->input->post();
            $this->data['filter_data'] = null;
            if (isset($data['type']) && $data['type']) {
                $this->data['filter_data'] = $data['type'];
            }
            $this->data['view'] = "admin/manage_appcontent/faq";
            $this->data['add_datatable_js'] = "faq/faq.js";
            $this->data['items'] = $this->faq_index($this->data['filter_data']);
        }

        $this->displayview($this->data);
    }

    public function faq_submited($i = null,$id=null) {
        
        $data = $this->input->post();
        if (isset($data['save'])) {
            unset($data['save']);
            if($id) {
                $this->db->where("id",$id);
                $this->db->update("faq_category",$data);
                
            } else {
                $this->db->insert("faq_category", $data);
            }
            $this->session->set_flashdata("flashsuccess", "Category Successfully added/updated");
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display/5");
        } elseif (isset($data['faq_save'])) {
            unset($data['faq_save']);
            $this->db->insert("faq", $data);
            $this->session->set_flashdata("flashsuccess", $this->lang->line("content_added"));
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display/2");
        }
    }

    public function faq_edit_submited($view_id, $id) {
        $update_data = $this->input->post();
        if ($id != '' || isset($update_data['edit_faq_save'])) {
            unset($update_data['edit_faq_save']);
            $this->db->where("id", $id);
            $this->db->update("faq", $update_data);
            $this->session->set_flashdata("flashsuccess", $this->lang->line("content_added"));
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display/3/" . $id);
        } else {
            echo "id can not be null";
            die;
        }
    }

    public function faq_index($filter = null, $edit_id = null) {
        $field = "faq.id,faq_category.id as cat_id,faq_category.type,faq_category.category,faq_category.sp_category,GROUP_CONCAT(CONCAT(faq.question,'||',answer) SEPARATOR '||||') as qus_ans_eng,GROUP_CONCAT(CONCAT(faq.sp_question,'||',sp_answer) SEPARATOR '||||') as qus_ans_spn";
        if ($filter != null) {
            $this->db->where("faq_category.type", $filter);
        }
        if ($edit_id != null) {
            $this->db->where("faq.id", $edit_id);
        }
        $this->db->group_by("faq.faq_cat_id");
        $this->db->order_by("faq.id", "DESC");
        $this->db->select($field);
        $this->db->from("faq");
        $this->db->join("faq_category", "faq_category.id=faq.faq_cat_id", "INNER");
        $query = $this->db->get();
        $result = $query->result_array();

        //dd($result);
        $i = 0;
        if ($result) {
            foreach ($query->result_array() as $key => $value) {
                $result[$key]['qus_ans_eng'] = explode("||||", $value['qus_ans_eng']);
                $result[$key]['qus_ans_spn'] = explode("||||", $value['qus_ans_spn']);
                foreach ($result[$key]['qus_ans_eng'] as $k => $v) {
                    unset($result[$key]['qus_ans_eng'][$k]);

                    $result[$key]['qus_ans_eng'][$k][] = explode("||", $v);
                    $i++;
                    $result[$key]['qus_ans_eng'][$k][] = explode("||", $result[$key]['qus_ans_spn'][$k]);

                    unset($result[$key]['qus_ans_spn'][$k]);
                }
                $i = 0;
            }
        }
        return $result;
    }

    public function consent_submit() {
        $data = $this->input->post();
        if (isset($data['save'])) {
            unset($data['save']);
            $this->db->where("id", 1);
            $this->db->update("consent_care", $data);
            $this->session->set_flashdata("flashsuccess", "Item has been successfully Updated");
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display/4");
        }
    }

    public function delete_faq($id) {
        if ($id) {
            //echo $id;die;
            $this->db->where("faq_cat_id", $id);
            $this->db->delete("faq");
            $this->session->set_flashdata("flashsuccess", "Item has been successfully deleted!");
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display");
        }
    }

    public function delete_cat($id) {
        if ($id) {
            $this->db->where("id", $id);
            $this->db->delete("faq_category");
            $this->session->set_flashdata("flashsuccess", "Item has been successfully deleted!");
            redirect("admin/manage_app_content/manage_appcontent_controller/faq_display/5");
        }
    }

}

?>
