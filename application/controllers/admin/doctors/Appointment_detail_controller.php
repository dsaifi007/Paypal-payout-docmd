<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_detail_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "users/appointment_detail";
    protected $model = 'admin/doctors/Appointment_detail_model';
    protected $is_model = "appointment_detail";
    private $filtering_data = array();
    private static $status = [1, 4, 5, 6];
    private static $cancel_status = [2, 3];
    private static $all_status = [1, 2, 3, 4, 5, 6];

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

    public function upcoming_appointments($doctorid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }

        $upcoming_app_date = [
            "appointment.patient_availability_date_and_time >= " => date("Y-m-d H:i:s")
        ];
        // get_user_appointments for both user/doctor
        $this->data['upcoming_appointment'] = get_user_appointments($doctorid, $upcoming_app_date, self::$status, $this->filtering_data, "doctors");

        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();


        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/upcoming_appointments"; // we will take the file from user because file are same 
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    function view_transcation($userid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }

        $this->data['items'] = get_user_appointments($userid, null, self::$all_status, $this->filtering_data, "doctors");
        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();
        $this->data['doctor_name'] = $this->{$this->is_model}->getusername($userid);
        //dd($this->date['appointment_type']);

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/doctor_view_transcation";
        $this->data['add_datatable_js'] = "users/user_transcation.js";
        $this->displayview($this->data);
    }

    public function past_appointments($doctorid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }
        $past_app_date = [
            "appointment.patient_availability_date_and_time <= " => date("Y-m-d H:i:s")
        ];
        $this->data['past_appointment'] = get_user_appointments($doctorid, $past_app_date, self::$status, $this->filtering_data, "doctors");

        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();

        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/past_appointments";
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    public function cancel_appointments($doctorid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }
        $this->data['cancel_appointment'] = get_user_appointments($doctorid, null, self::$cancel_status, $this->filtering_data, "doctors");
        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();


        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/doctors/cancel_appointments";
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    public function detail_past_appointment($appt_id,$id=null) {
       
        $this->data['appointment_data'] = $this->{$this->is_model}->get_appointment_detail($appt_id);
        $this->data['prescription_data'] = $this->{$this->is_model}->get_appointment_prescription_detail($appt_id);
        $this->data['admin_note'] = $this->{$this->is_model}->get_admin_note($appt_id);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';

        $this->data['page_title'] = "Appointment Prescription Detail";
        $this->data['view'] = "admin/doctors/detail_past_appointments";
        $this->BuildFormEnv(["template_helper"]);
        $this->displayview($this->data);
    }

    public function get_pharmacy_detail() {
        $pharmacy_id = $this->input->post();
        if ($pharmacy_id) {
            $row = $this->{$this->is_model}->get_pharmacy_detail($pharmacy_id);
            echo "<td>" . ucwords($row->pharmacy_name) . "</td>
            <td>" . $row->phone . "</td>
            <td>" . $row->address . "</td>
            <td>" . $row->city . "</td>
            <td>" . $row->state . "</td>
            <td>" . $row->zip . "</td>";
        }
        exit();
    }

    public function add_admin_note() {
        $data = $this->input->post();

        if ($this->add_admin_note_validations() && isset($data['save'])) {
            unset($data['save']);

            $data['admin_name'] = $this->session->userdata('name');
            $this->db->insert("admin_appointment_note", $data);
            $this->session->set_flashdata("flashsuccess", "Admin Note Successfully Added/Updated");
            redirect("admin/doctors/appointment_detail_controller/detail_past_appointment/" . $data['appointment_id'] . "");
        }
        $this->index();
    }

    public function add_admin_note_validations() {
        $this->load_validation_lib();
        $this->form_validation->set_rules("note", "Admin Note", "required|trim|min_length[2]");
        $this->form_validation->set_rules("appointment_id", "Appointment Id", "required|integer");
        return $this->form_validation->run();
    }

}

?>
