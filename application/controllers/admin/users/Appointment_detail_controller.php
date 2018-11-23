<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_detail_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "users/appointment_detail";
    protected $model = 'admin/users/Appointment_detail_model';
    protected $is_model = "appointment_detail";
    private $filtering_data = array();
    private static $status = [1, 4, 5, 6,7];
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

    public function upcoming_appointments($userid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }

        $upcoming_app_date = [
            "appointment.patient_availability_date_and_time >= " => date("Y-m-d H:i:s")
        ];
        $this->data['upcoming_appointment'] = get_user_appointments($userid, $upcoming_app_date, self::$status, $this->filtering_data);

        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();
        $this->data['user_name'] = $this->{$this->is_model}->get_user_name($userid);
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/users/upcoming_appointments";
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    function view_transcation($userid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }

        $this->data['items'] = get_user_appointments($userid, null, self::$all_status, $this->filtering_data);
        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();
        $this->data['user_name'] = $this->{$this->is_model}->get_user_name($userid);
       
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/users/user_view_transcation";
        $this->data['add_datatable_js'] = "users/user_transcation.js";
        $this->displayview($this->data);
    }

    public function past_appointments($userid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }
        $past_app_date = [
            "appointment.patient_availability_date_and_time <= " => date("Y-m-d H:i:s")
        ];
        $this->data['past_appointment'] = get_user_appointments($userid, $past_app_date, self::$status, $this->filtering_data);
        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();
        $this->data['user_name'] = $this->{$this->is_model}->get_user_name($userid);
        
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/users/past_appointments";
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    public function cancel_appointments($userid) {
        if (count($this->input->post()) > 0) {
            $this->filtering_data = $this->security->xss_clean(array_map("trim", array_filter($this->input->post())));
            //dd($this->filtering_data);           
        }
        $this->data['cancel_appointment'] = get_user_appointments($userid, null, self::$cancel_status, $this->filtering_data);
        $this->data['appointment_type'] = $this->{$this->is_model}->get_all_provider_plan_type();
        $this->data['user_name'] = $this->{$this->is_model}->get_user_name($userid);


        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/users/cancel_appointments";
        $this->data['add_datatable_js'] = "users/appointment_detail.js";
        $this->displayview($this->data);
    }

    public function detail_past_appointment($appt_id,$id=null) {
        
        $this->data['appointment_data'] = $this->{$this->is_model}->get_appointment_detail($appt_id);
        $this->data['prescription_data'] = $this->{$this->is_model}->get_appointment_prescription_detail($appt_id);

        $this->data['view'] = "admin/users/detail_past_appointments";
        $this->BuildFormEnv(["template_helper"]);
        $this->displayview($this->data);
    }

}

?>
