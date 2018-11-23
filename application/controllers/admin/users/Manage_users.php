<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_users extends MY_Controller {

    protected $data = [];
    private $language_file = "users/users";
    private $add_css = "users/formlayout.css";
    protected $model = 'admin/users/manage_user_model';
    protected $is_model = "user_model";
    private $status_response = [];

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
        $this->BuildFormEnv(["template_helper"]);

        $this->data['view'] = "admin/users/manage_user_view";

        $this->data['filterdata'] = ($this->input->post('address_city') ||
                $this->input->post('address_state') || $this->input->post('ptnt_provider') || $this->input->post('ptnt_gender') || $this->input->post('avg_rating')) ? $this->input->post() : '';


        $this->data['add_datatable_js'] = "users/manage_users.js";
        ;
        $this->isModelload();

        //$this->data['state'] = $this->user_model->get_all_state();
        $this->data['city'] = $this->user_model->get_all_city();
        
        $this->data['rating'] = $this->user_model->getrating();

        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->displayview($this->data);
    }

    function getdata() {
        // log_message("info",  json_encode($_POST));
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->user_model->count_all($post),
            "recordsFiltered" => $this->user_model->count_filtered($post),
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
        $post['column_order'] = array(null, 'med_id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'date_of_birth', null);
        $post['column_search'] = array('first_name', 'last_name', 'email', 'phone', 'gender', 'date_of_birth');

        $list = $this->user_model->get_order_list($post);
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
        $row[] = "<input type='checkbox' id='chek' class='chek' name='sltd_emails[]' value='" . $order_list->id . "' />";
        $row[] = $order_list->med_id;

        $row[] = "<a href='".base_url("admin/users/manage_users/user_view/$order_list->id")."'  >" . $order_list->first_name . "</a>";
        $row[] = "<a href='".base_url("admin/users/manage_users/user_view/$order_list->id")."'>".$order_list->last_name."</a>";
        $row[] = "<a href = 'mailto:" . $order_list->email . "'>$order_list->email</a>";;
        $row[] = $order_list->phone;
        $row[] = $order_list->gender;
        $row[] = date("m-d-Y",strtotime($order_list->date_of_birth));
        $row[] = ($order_list->avg_rating) ? $order_list->avg_rating : "N/A";
        $is_blocked = ($order_list->is_blocked == 1) ? "checked" : "";
        $row[] = "<label class='switchToggle' style='top:15px;'>
            <input type='checkbox' id='user_block' name='user_block[]' value='" . $order_list->is_blocked . "'  data-id='" . $order_list->id . "' " . $is_blocked . ">
            <span class='slider red round'></span>
            </label><a href=" . base_url('admin/users/manage_users/user_view/') . $order_list->id . " class='btn btn-info btn-custm' style='margin-left:10px;width: 44px;'><i class='fa fa-eye'></i></a> ";
        //$row[] = "<input type='checkbox' id='chek' class='chek' name='sltd_emails[]' value='".$order_list->id."' checked /><a href=".base_url('admin/users/users/user_view/'). $order_list->id." class='btn btn-info btn-custm'><i class='fa fa-eye'></i></a>";

        return $row;
    }

    /*
      |-----------------------------------------------------------------------------
      | Work -- this function will use for user view data
      | @return -- json data
      |-----------------------------------------------------------------------------
     */

    public function user_view($user_id) {
        try {
            if (!empty($user_id)) {

                $this->data['user_info'] = $this->{$this->is_model}->get_user_data($user_id);

                $past_date = "patient_availability_date_and_time <='" . date("Y-m-d h:i:s") . "'";
                $this->data['past_appointment'] = get_user_total_appointment($user_id, $past_date, [1, 6, 4, 5]);

                $up_date = "patient_availability_date_and_time >='" . date("Y-m-d h:i:s") . "'";
                $this->data['upcoming_appointment'] = get_user_total_appointment($user_id, $up_date, $this->config->item("upcoming_status"));

                $this->data['cancel_appointment'] = get_user_total_appointment($user_id, null, $this->config->item("cancel_status"));

                $this->data['view'] = "admin/users/user_info_view";
                $this->displayview($this->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      -------------------------------------------------------------------------------------------
      Work -- Send email to users in bulk or individual users
      @return -- true/false
      -------------------------------------------------------------------------------------------
     */

    public function send_email_to_users() {
        try {
            $user_data = $this->input->post();   
           
            if (isset($user_data["sltd_emails"]) && $user_data["sltd_emails"] != "") {
                
                if (is_numeric($this->input->post("subject"))) {
                    $email_content = get_email_templates(["id" => $this->input->post("subject")]);
                    $this->sent_email($email_content[0]['subject'], $email_content[0]['message'], $user_data['sltd_emails'], $email_content[0]['email_attechment']);
                } else {
                    $data['htmldata'] = $this->input->post("message", NULL, FALSE);
                    $email_template = $this->load->view("admin/email_template/email_html", $data, TRUE);
                   
                    $this->sent_email($this->input->post("subject"), $email_template, $user_data['sltd_emails']);
                }
                $this->session->set_flashdata('flashsuccess', "Email sent successfully");
            }
            redirect("admin/users/manage_users/index");
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      |---------------------------------------------------------------------------------------
      |                 This Function will be used for the bulk email
      |---------------------------------------------------------------------------------------
     */

    public function sent_email($subject, $message, $selected_id = null, $attach = null) {
        try {
            $this->isModelload();
            $this->config->load('shared');
            $emails = $this->user_model->get_all_emails($selected_id);
            $this->load->library("email_setting");
            $from = $this->config->item("from");
            $this->email_setting->send_email($emails, $from, $message, $subject, null, null, $attach);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      Work -- update the user status active/unactive based on ID
      @return -- json response
     */

    public function update_user_status() {
        $user_data = $this->input->post();
        $this->isModelload();
        $this->{$this->is_model}->update_user_status_model($user_data);
        if ($user_data['status'] == 0) {
            $this->status_response["active"] = $this->lang->line("users_active");
        } else {
            $this->status_response["unactive"] = $this->lang->line("users_unactive");
        }
        echo json_encode($this->status_response);
        exit();
    }

    public function user_medical_history($user_id) {

        $this->isModelload();
        $this->data['patient_medical_history'] = $this->{$this->is_model}->get_user_medical_history($user_id);

        $this->data['view'] = "admin/users/user_medical_history";
        $this->displayview($this->data);
    }

    public function get_all_user_patient() {
        $user_id = $this->input->post();
        //dd($user_id);
        $result = $this->{$this->is_model}->get_all_users_patient_list_model($user_id["user_id"]);
        //dd($result);
        if (!empty($result)) {
            echo "<table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Med Id</th>
                            <th>Name</th>
                            <th>Date Of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead><tbody>";
            foreach ($result as $key => $value) {
                echo "<tr>
                    <td>" . $value['med_id'] . "</td>
                    <td>" . $value['ful_name'] . "</td>
                    <td>" . $value['date_of_birth'] . "</td>
                    <td>" . $value['gender'] . "</td>
                 </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>NO Information is available at this time</p>";
        }
        exit();
    }

}

?>
