<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Registered_doctors extends MY_Controller {

    protected $data = [];
    private $language_file = "doctors/registered_doctors";
    protected $model = 'admin/doctors/registered_doctor_model';
    protected $is_model = "registered_model";
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

    public function index() {

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/register_doctor_view";
        $this->data['active_class'] = "active open";
        $this->data['open_class'] = "open";
        $this->data['add_datatable_js'] = "doctors/register_doctors.js";

        $this->data['state'] = $this->{$this->is_model}->get_all_state();
        $this->data['city'] = $this->{$this->is_model}->get_all_city();
        $this->data['specility'] = $this->{$this->is_model}->get_all_specilities();

        //if (count($this->input->post()) > 0) {
        $this->data['filtering_data'] = $this->security->xss_clean(array_filter($this->input->post()));

        //}
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
        $post['column_order'] = array(null, "id", "med_id", 'first_name', 'email', 'phone', 'gender', 'date_of_birth');
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
        $row[] = $order_list->med_id;
        $row[] = "<a href='" . base_url("admin/doctors/registered_doctors/doctor_view/$order_list->id") . "'>" . $order_list->first_name . "</a>";
        $row[] = "<a href='" . base_url("admin/doctors/registered_doctors/doctor_view/$order_list->id") . "'>" . $order_list->last_name . "</a>";
        $row[] = "<a href = 'mailto:" . $order_list->email . "'>$order_list->email</a>";
        ;
        $row[] = $order_list->phone;
        $row[] = $order_list->gender;
        $row[] = date_format(date_create($order_list->date_of_birth), "m-d-Y"); //$order_list->date_of_birth;
        $row[] = $order_list->name;
        $row[] = ($order_list->is_loggedin) ? "Yes" : "No";
        $is_blocked = ($order_list->is_blocked == 1) ? "checked" : "";
        $row[] = "<a href='" . base_url("admin/doctors/registered_doctors/doctor_view/") . $order_list->id . "' style='color:white'><span class='label label-danger label-mini' style='top:-20px;position:relative;right:4px;'><i class='fa fa-eye'></i></span></a><label class='switchToggle'>
			<input type='checkbox' id='user_block' name='user_block[]' value='" . $order_list->is_blocked . "'  data-id='" . $order_list->id . "' " . $is_blocked . ">
			<span class='slider red round'></span>
			</label>";
        return $row;
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- this function will use for doctor view info
      |	@return -- json data
      |-----------------------------------------------------------------------------
     */

    public function doctor_view($doctor_id, $status = null) {
        try {
            $this->BuildFormEnv(["template_helper"]);
            $this->data['successs'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
            $this->data["appointment"] = $this->{$this->is_model}->doctor_appointment($doctor_id);
            $data = $this->input->post();
            if (!empty($doctor_id)) {
                // get the doctor info 
                $this->data['degree'] = $this->{$this->is_model}->get_all_degree();
                $this->data['speciality'] = $this->{$this->is_model}->get_all_speciality();
                $this->data['doctor_info'] = $this->{$this->is_model}->get_doctor_info($doctor_id);
                // update the sataus block/unblock of the doctor 
                if ($status != null && ( $status == 1 || $status == 0 )) {
                    $doctor_data = ['doctor_id' => $doctor_id, 'status' => $status];
                    $this->{$this->is_model}->update_doctor_status_model($doctor_data);
                    $this->data['success'] = $this->lang->line("doctor_status_updated");
                } elseif (isset($data['save'])) {

                    if ($this->edit_register_form()) {

                        $this->{$this->is_model}->edit_doctor_info_model($data, $doctor_id);
                        $this->session->set_flashdata('flashsuccess', "Information updated successfully");
                        redirect("admin/doctors/registered_doctors/doctor_view/" . $doctor_id);
                    }
                }
                $this->data['view'] = "admin/doctors/register_doctor_info_view";
                $this->displayview($this->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function edit_register_form() {
        $this->load_validation_lib();
        $this->load->helper('security');
        $this->form_validation->set_rules("commission", "Commission", "required|min_length[1]|max_length[6]");
        $this->form_validation->set_rules("rating", "Rating", "required|min_length[1]|max_length[3]");
        $this->form_validation->set_rules("speciality_id[]", "Speciality", "required|min_length[1]|integer|is_natural_no_zero");
        $this->form_validation->set_rules("degree_id[]", "Degree", "required|min_length[1]|integer|is_natural_no_zero");
        return $this->form_validation->run();
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
            dd($user_data);
            if (isset($user_data["selectall"]) && $user_data["selectall"] == "all") {
                $email_sent = $this->sent_email($this->input->post("subject"), $this->input->post("message"));
                //$this->lang->line("emails_sent") lang file not working
                // send email in bulk 
                $this->session->set_flashdata('flashsuccess', "Email sent successfully");
                //redirect("admin/users/manage_users/index");					
            } else {
                if (is_numeric($this->input->post("subject"))) {
                    $email_content = get_email_templates(["id" => $this->input->post("subject")]);
                    //dd($email_content);
                    // $this->input->post("subject") is id
                    $this->sent_email($email_content[0]['subject'], $email_content[0]['message'], $user_data['sltd_emails'], null, $email_content[0]['email_attechment']);
                } else {
                    $this->sent_email($this->input->post("subject"), $this->input->post("message"), $user_data['sltd_emails']);
                }
                $this->session->set_flashdata('flashsuccess', "Email sent successfully");
            }
            redirect("admin/doctors/registered_doctors/index");
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      |---------------------------------------------------------------------------------------
      |		This Function will be used for send the bulk email
      |		$selected_id = null that means send email to all doctors
      |     $selected_id != null only send selected email send
      |---------------------------------------------------------------------------------------
     */

    public function sent_email($subject, $message, $selected_id = null, $emails = null, $attach = null) {
        try {
            $this->isModelload();
            $this->config->load('shared');
            if ($emails == null) {
                $emails = $this->{$this->is_model}->get_all_emails($selected_id);
            }
            $this->load->library("email_setting");
            $from = $this->config->item("from");
            //$msg = $this->load->view("auto_email_template",$this->data,true);

            $this->email_setting->send_email($emails, $from, $message, $subject, null, null, $attach);
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

    public function update_doctor_status() {
        $user_data = $this->input->post();
        $this->{$this->is_model}->update_doctor_status_model($user_data);
        if ($user_data['status'] == 0) {
            $this->status_response["active"] = "Provider Successfully Unblocked";
        } else {
            $this->status_response["unactive"] = "Provider Successfully Blocked";
        }
        echo json_encode($this->status_response);
        exit();
    }

}

?>
