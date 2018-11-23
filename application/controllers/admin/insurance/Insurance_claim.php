<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Insurance_claim extends MY_Controller {

    protected $data = [];
    private $language_file = "insurance/insurance";
    protected $model = 'admin/insurance/insurance_model';
    protected $is_model = "insurance_model";
    protected $insurance_status;

    public function __construct() {
        parent::__construct();
        $this->user_not_loggedin();
        $this->lang->load('api_message');
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
        if (count($this->input->post()) > 0) {
            $this->data['filetration'] = $this->security->xss_clean(array_map("trim", $this->input->post()));
            
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['filter_data'] = $this->{$this->is_model}->get_filter_data();
        $this->data['view'] = "admin/insurance/insurance_claim_view";
        $this->data['add_datatable_js'] = "insurance/insurance_claim.js";
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
            "recordsTotal" => $this->insurance_model->count_all($post),
            "recordsFiltered" => $this->insurance_model->count_filtered($post),
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
        $post['column_order'] = array('appointment.id', 'all_appointment.patient_med_id', 'patient_info.first_name', 'patient_info.last_name', 'doctors.first_name', 'doctors.last_name', 'all_appointment.type', 'all_appointment.patient_availability_date_and_time', 'all_appointment.patient_availability_date_and_time', 'provider_plan.amount', "appointment.payment_method_id", "patient_info.provider", "patient_info.member_id", "patient_info.ins_group", null, null, "appointment.insurance_status");
        $post['column_search'] = array('all_appointment.patient_med_id', 'patient_info.first_name', 'patient_info.last_name', 'doctors.first_name', 'doctors.last_name', 'all_appointment.type', 'all_appointment.patient_availability_date_and_time', 'all_appointment.patient_availability_date_and_time', 'provider_plan.amount', "appointment.payment_method_id", "patient_info.provider", "patient_info.member_id", "patient_info.ins_group", "appointment.insurance_status");

        $list = $this->insurance_model->get_order_list($post);
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

    public function table_data($order_list, $no) {


        if ($order_list->insurance_status == 1) {
            $this->insurance_status = "pending";
        } elseif ($order_list->insurance_status == 2) {
            $this->insurance_status = "Action Required";
        } elseif ($order_list->insurance_status == 3) {
            $this->insurance_status = "Claim Submited";
        } elseif ($order_list->insurance_status == 4) {
            $this->insurance_status = "Rejected";
        } elseif ($order_list->insurance_status == 5) {
            $this->insurance_status = "Accepted";
        } else {
            $this->insurance_status = "Completed";
        }
        $doct_name = explode(" ", $order_list->doctor);
       
        $row = array();
        $row[] =  $order_list->id;
        $row[] = $order_list->patient_med_id;
        $row[] = $order_list->first_name;
        $row[] = $order_list->last_name;
        $row[] = $order_list->doctor_first_name;
        $row[] = $order_list->doctor_last_name;
        $row[] = $order_list->type;
        $row[] = $order_list->date;
        $row[] = $order_list->time;
        $row[] = $order_list->amount;
        //$row[] = $order_list->payment_method_id;
        $row[] = $order_list->provider;
        $row[] = $order_list->member_id;
        $row[] = $order_list->ins_group;
        $row[] = "Claim Requested";
        $row[] = "<div class='btn-group'>
                   <button type='button' class='btn btn-default'>" . $this->insurance_status . "</button>
		   <button type='button' class='btn btn-default dropdown-toggle m-r-20' data-toggle='dropdown' aria-expanded='false'>
                   <i class='fa fa-angle-down'></i>
                    </button>
                    <ul class='dropdown-menu' role='menu' x-placement='bottom-start' style='position: absolute; transform: translate3d(83px, 34px, 0px); top: 0px; left: 0px; will-change: transform;'>
                        <li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' action-id='" . $order_list->id . "' status-id='1'>Pending</a></li>
                        <li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' status-id='2' >Action Required!</a></li>                   
                        <li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' status-id='3'>Claim Submited</a></li>
                        <li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' status-id='4' >Rejected</a></li>    
                        <li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' status-id='5'>Accepted</a></li>			
			<li><a href='#' class='insurance-status' patient_id= '" . $order_list->patient_id . "' user_id= '" . $order_list->user_id . "' action-id='" . $order_list->id . "' status-id='6' >Completed</a></li>			
			
			</ul>
		  </div>";
        return $row;
    }

    public function update_insurance_action_status() {
        if (!empty($this->input->post())) {
            $data = $this->input->post();

            $device_token = $this->insurance_model->update_insurance_action_status_model($this->input->post());
            $data['notifiy_time'] = $this->config->item("date");
            $r = send_notification($this->lang->line('insurance_title'), sprintf($this->lang->line('insurance_body'), $device_token['name']), $this->lang->line('insurance_constant'), $device_token['device_token'], $data);
            $this->insurance_model->insert_notificationdata($r, $data);
            echo json_encode(["message" => "Status Successfully updated"]);
            die;
            exit();
        }
    }

}

?>
