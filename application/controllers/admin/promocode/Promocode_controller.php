<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Promocode_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "promocode/promocode";
    protected $model = 'admin/promocode/promocode_model';
    protected $is_model = "promocode_model";
    private $input_data = [];
    protected $emails = [];

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
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->data['existing_users_list'] = $this->{$this->is_model}->get_all_users();
        $this->input_data = $this->input->post();
        if (isset($this->input_data['edit_id']) && (int) $this->input_data['edit_id'] > 0) {
            $this->edit_promocode_info($this->input_data);
        } else {
            $this->add_promocode_info($this->input_data);
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/promocode/promocode_view";
        $this->displayview($this->data);
    }

    function getdata() {
        // log_message("info",  json_encode($_POST));
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->{$this->is_model}->count_all($post),
            "recordsFiltered" => $this->{$this->is_model}->count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);

        echo json_encode($output);
    }

    function process_get_data() {
        $post = $this->get_post_input_data();
        //$post['where'] = array( 'order_date >= ' => date('Y-m-d',strtotime("-30 days")));
        //$post['where_in'] = array('status' => array('Pending', 'Cancelled', 'Completed'));
        $post['column_order'] = array(null, 'name', 'code', 'discount', 'expiry', 'description');
        $post['column_search'] = array('name', 'code', 'discount', 'expiry', 'description');

        $list = $this->{$this->is_model}->get_order_list($post);
        $data = array();
        $no = $post['start'];
        //dd($list);	
        foreach ($list as $order_list) {
            $no++;
            $row = $this->table_data($order_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_post_input_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');
        $post['external_filtering'] = $this->input->post('filter_data');
        return $post;
    }

    function table_data($order_list, $no) {
        $row = array();
        $row[] = $order_list->id;
        //dd($order_list);
        $row[] = $order_list->name;
        $row[] = $order_list->code;
        $row[] = $order_list->discount;
        $row[] = $order_list->expiry;
        $row[] = substr($order_list->description,0,10)."..";

        $is_sent= ($order_list->is_sent == 0)?"<span data-toggle='modal' data-target='#send'   send-id ='" . $order_list->id . "'  class='label label-sm label-success row-id'>Send</span>":"<span  class='label label-sm label-danger'>Sent</span>";
        $row[] = "".$is_sent."  <span edit-id ='" . $order_list->id . "' data-toggle='modal' data-target='#add_new'  class='label label-sm label-info edit_promocode'>Edit</span> 
                <a class='label label-sm label-danger'  href='" . base_url() . "admin/promocode/promocode_controller/delete/" . $order_list->id . "'>delete</a>";
        return $row;
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- this function will use for pharmacy view info
      |	@return -- json data
      |-----------------------------------------------------------------------------
     */

    public function promocode_view($pharmacy_id) {
        try {
            if (!empty($doctor_id)) {
                // get the pharmacy info 
                $this->data['pharmacy_info'] = $this->{$this->is_model}->get_promocode_info($pharmacy_id);
                $this->data['view'] = "admin/promocode/promocode_info_view";
                $this->displayview($this->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new promocode
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_promocode_info($promocode_data) {
        //dd($promocode_data);
        //$this->data['page_title'] = $this->lang->line("add_title");
        if ($this->input_validations()) {

            $result = $this->{$this->is_model}->add_promocode($promocode_data);
            if ($result) {
                $this->session->set_flashdata("flashsuccess", $this->lang->line("success_promocode_save"));
            } else {
                $this->session->set_flashdata("flashsuccess", $this->lang->line("error_promocode_save"));
            }
            redirect("admin/promocode/promocode_controller");
        }
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function edit_promocode_info($data) {
        //echo isset($data['id'])?"set":"";
        if (isset($data['edit_id'])) {

            $unique_code = $this->{$this->is_model}->get_promocode_info($data['edit_id']);
            if ($this->input_validations($unique_code->code, $unique_code->id, $data['code'])) {
                $this->{$this->is_model}->update_promocode($data);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("success_promocode_edit_save"));

                redirect("admin/promocode/promocode_controller");
            }
        }
    }

    /*
      |--------------------------------------------------------------
      | Work - validation of form input
      | @return - true/false
      |---------------------------------------------------------------
     */

    public function input_validations($code = null, $id = null, $user_input_code = null) {
        $this->load_validation_lib();
        $is_unique = ($id > 0 && (strtolower($user_input_code) == strtolower($code))) ? '' : "|is_unique[promocode.code]";
        $this->form_validation->set_rules("name", "Promocode Name", "required|trim|min_length[4]|max_length[30]");
        $this->form_validation->set_rules("code", "Promocode", "required|trim|min_length[2]|max_length[24]" . $is_unique);
        $this->form_validation->set_rules("discount", "Discount", "required|trim|min_length[2]|max_length[4]");
        $this->form_validation->set_rules("expiry", "Expiry Date", "required|trim");
        $this->form_validation->set_rules("description", "Description", "required|trim|min_length[6]");
        return $this->form_validation->run();
    }

    public function get_input_values() {
        $id = $this->input->post();
        $result = $this->{$this->is_model}->get_promocode_info($id['id']);
        if ($result) {
            echo json_encode($result);
        }
    }

    public function get_email_information() {
        try {
            $data = $this->input->post();
            //dd($data);
            $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
            $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
            //dd($data);
            $this->load_validation_lib();
            if (isset($data['user_list']) && @$data['user_list'] < 3) {
                $this->form_validation->set_rules("user_list", "Users", "required|min_length[1]|max_length[1]");
            } else {
                $this->form_validation->set_rules("user_ids[]", "Select Users", "required");
            }
            $this->form_validation->set_rules("send_id", "Id", "required|numeric");
            if (isset($data['save']) && $this->form_validation->run()) {
                $this->data['expiry'] = $this->{$this->is_model}->get_exipre_date($data['send_id']);
                if ($data['user_list'] == 1) {
                    // all users
                    $this->emails = $this->{$this->is_model}->get_all_users_emails();
                } elseif ($data['user_list'] == 2) {
                    // 1 -- it means we will get those emails, which is not made any appointment
                    $this->emails = $this->{$this->is_model}->get_all_users_emails(1);
                } else {
                    //dd($data['user_ids']);
                    $this->emails = $data['user_ids'];
                }
                $this->email_sent($this->emails);
                // when email sent then update the si_sent=1 for  hide the sent button 
                $this->{$this->is_model}->update_is_sent_status($data['send_id']);
                $this->session->set_flashdata("flashsuccess", $this->lang->line("promocode_sent"));
                redirect("admin/promocode/promocode_controller/index");
            }
            $this->BuildFormEnv(["template_helper"]);
            $this->data['view'] = "admin/promocode/promocode_view";
            $this->displayview($this->data);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function email_sent($emails) {
        $this->config->load('shared');
        $this->load->library("email_setting");
        $from = $this->config->item("from");
        $subject = "New Promocode";
        $message = $this->load->view("promocode_template", $this->data, true);
        $this->email_setting->send_email($emails, $from, $message, $subject);
        return true;
    }

    public function delete($id) {
        $this->db->where("id", $id);
        $this->db->delete("promocode");
        $this->session->set_flashdata("flashsuccess", $this->lang->line("delete_promocode"));
        redirect("admin/promocode/promocode_controller");
    }

}

?>
