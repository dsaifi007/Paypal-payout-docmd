<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rejected_doctors extends MY_Controller {

    protected $data = [];
    private $language_file = "doctors/rejected_doctors";
    protected $model = 'admin/doctors/rejected_doctor_model';
    protected $is_model = "rejected_model";
    private $status_response = [];

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

    public function index($commisson = null) {

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/rejected_doctor_view";
        $this->data['active_class'] = "active open";
        $this->data['rejected_class'] = "open";
        
        $this->data['add_datatable_js'] = "doctors/rejected_doctors.js";
        // $this->data['add_js'] = "doctors/jquery-rating.js";
        //$this->data['add_css'] = "common/model-2.css";
        $this->data['state'] = $this->{$this->is_model}->get_all_state();
        $this->data['city'] = $this->{$this->is_model}->get_all_city();
        $this->data['specility'] = $this->{$this->is_model}->get_all_specilities();
        if ($commisson != 1) {
            $this->data['filtering_data'] = $this->security->xss_clean(array_filter($this->input->post()));
        }

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
        $post['column_order'] = array(null, "id", 'first_name', 'email', 'phone', 'gender', 'date_of_birth', null);
        $post['column_search'] = array('first_name', 'email', 'phone', 'gender', 'date_of_birth');

        $list = $this->{$this->is_model}->get_order_list($post);
        $data = array();
        $no = $post['start'];

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
        $row[] = $order_list->id;

        $row[] = "<a href='" . base_url("admin/doctors/rejected_doctors/doctor_view/$order_list->id") . "'>" . $order_list->first_name . "</a>";
        $row[] = "<a href='" . base_url("admin/doctors/rejected_doctors/doctor_view/$order_list->id") . "'>" . $order_list->last_name . "</a>";
        $row[] = $order_list->email;
        $row[] = $order_list->phone;
        $row[] = $order_list->gender;
        $row[] = date("m-d-Y",strtotime($order_list->date_of_birth));
        $row[] = "<a href='" . base_url("admin/doctors/rejected_doctors/doctor_view/") . $order_list->id . "'
              style='color:white'><span class='label label-danger label-mini'>
                <i class='fa fa-eye'></i></span></a> 
              <span class='label label-info label-mini' style='background-color:#56d396 !important;padding:8px 10px'> 
              <a href='#'  class='approve' data-toggle='modal' data-target='#myModal1' data-id='" . $order_list->id . "' style='color:white'>
                Approve</a>  
</span>";
        return $row;
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- this function will use for user view data
      |	@return -- json data
      |-----------------------------------------------------------------------------
     */

    public function doctor_view($doctor_id) {
        try {
            if (!empty($doctor_id)) {
                $this->data['doctor_info'] = $this->{$this->is_model}->get_doctor_info($doctor_id);
                $this->data['degree'] = $this->{$this->is_model}->get_all_degree();
                $this->data['speciality'] = $this->{$this->is_model}->get_all_speciality();
                   
                 // waiting 
//                $input_data = $this->input->post();
//                if(isset($input_data['edit_save'])){
//                    $this->{$this->is_model}->update_degree_speciality($input_data,$doctor_id);
//                }
                
                $this->data['view'] = "admin/doctors/rejected_doctor_info_view";
                $this->displayview($this->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function rating_commission_form_validation() {
        $this->load_validation_lib();
        $this->load->helper('security');
        $this->form_validation->set_rules("rating", "Rating", "required|min_length[1]|max_length[1]");
        $this->form_validation->set_rules("doctor_id", "Docotor Id", "required");
        $this->form_validation->set_rules("commission", "Doctor Commission", "required|trim|min_length[1]|max_length[3]|integer|is_natural_no_zero");

        return $this->form_validation->run();
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- this function will accept the doctor and status accept
      |	@return -- null only update the status
      |-----------------------------------------------------------------------------
     */

    public function update_doctors_status($id=null) {
        //$redirect_url = ($id)?"doctor_view/".$id:'';
        $doctor_data = $this->input->post();
        
        if ($this->rating_commission_form_validation()) {
            $response = $this->{$this->is_model}->update_doctor_rating_commission($doctor_data);
            
            if ($response) {
                $this->session->set_flashdata('flashsuccess', $this->lang->line("accepted_doctor"));
            } else {
                $this->session->set_flashdata('flasherror', $this->lang->line("accepted_rejected"));
            }
            redirect("admin/doctors/rejected_doctors");
        }
        $this->index(1);
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- update the doctor degree and speciality
      |		@return -- json response
      -------------------------------------------------------------------------------------------
     */

    public function update_degree_speciality() {
        $data = $this->input->post();
        // dd($data);
        $this->{$this->is_model}->update_degree_speciality_model($data);

        echo json_encode(["status" => "Record Successfully updated"]);
        exit();
    }

    /*
      -------------------------------------------------------------------------------------------
      Work -- Send email to users in bulk or individual users
      @return -- true/false
      -------------------------------------------------------------------------------------------
     */

    public function send_email_to_doctors() {
        try {
            $user_data = $this->input->post();
            
            if (isset($user_data["sltd_emails"]) && count($user_data["sltd_emails"])>0) {
                //$email_sent = $this->sent_email($this->input->post("subject"), $this->input->post("message"), $user_data['sltd_emails']);
                 if (is_numeric($this->input->post("subject"))) {
                    $email_content = get_email_templates(["id" => $this->input->post("subject")]);
                    // $this->input->post("subject") is id
                    $this->sent_email($email_content[0]['subject'], $email_content[0]['message'], $user_data['sltd_emails'], null, $email_content[0]['email_attechment']);
                } else {
                    $this->sent_email($this->input->post("subject"), $this->input->post("message"), $user_data['sltd_emails']);
                }
                $this->session->set_flashdata('flashsuccess', "Email sent successfully");
            }
            redirect("admin/doctors/rejected_doctors/index");
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      |---------------------------------------------------------------------------------------
      |     This Function will be used for send the bulk email
      |     $selected_id = null that means send email to all doctors
      |     $selected_id != null only send selected email send
      |---------------------------------------------------------------------------------------
     */

    public function sent_email($subject, $message, $selected_id = null, $emails = null,$attach = null) {
        try {
            $this->isModelload();
            $this->config->load('shared');
            if ($emails == null) {
                $emails = $this->{$this->is_model}->get_all_emails($selected_id);
            }
            $this->load->library("email_setting");
            $from = $this->config->item("from");
            $a = $this->email_setting->send_email($emails, $from, $message, $subject,null,null,$attach);
            return $a;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- update the user status active/unactive based on ID
      |		@return -- json response
      -------------------------------------------------------------------------------------------
     */

    public function update_user_status() {
        $user_data = $this->input->post();

        $this->{$this->is_model}->update_user_status_model($user_data);
        if ($user_data['status'] == 0) {
            $this->status_response["active"] = $this->lang->line("users_active");
        } else {
            $this->status_response["unactive"] = $this->lang->line("users_unactive");
        }
        echo json_encode($this->status_response);
        exit();
    }

    /*
      -------------------------------------------------------------------------------------------
      |						Work -- Send email to doctor rejected Email with reason
      |									@return -- true/false
      -------------------------------------------------------------------------------------------
     */

    public function send_rejected_mail_to_doctor() {
        $doctor_input = $this->security->xss_clean($this->input->post());
        $this->isModelload();
    
        if (isset($doctor_input['submit'])) {
            unset($doctor_input['submit']);
            // some pending work is remaining that doctor deleted or not
            $result = $this->sent_email(trim($doctor_input['subject']), trim($doctor_input['message']), null, trim($doctor_input['email']));
            if($result){
                $this->{$this->is_model}->update_rejected_status_model($doctor_input);
                $this->session->set_flashdata('flashsuccess', "Email sent successfully");
            }else{
                $this->session->set_flashdata('flasherror', "Email not sent successfully");
            }
            // lang file is not working in email case          
        }
        redirect("admin/doctors/rejected_doctors/index");
    }

}

?>
