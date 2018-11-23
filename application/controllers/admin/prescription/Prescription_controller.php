<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "prescription/prescription";
    protected $model = 'admin/prescription/prescription_model';
    protected $is_model = "prescription_model";
    private $status_response = [];
    protected $pres_status = "Action Required";

    //private $pharmacy_table = "pharmacies";

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
        if (count($this->input->post()) > 0) {
            $this->data['filetration'] = $this->security->xss_clean(array_map("trim", $this->input->post()));
        }
        $this->data['filter_data'] = $this->{$this->is_model}->get_filter_data();
        //$this->data['city'] = $this->{$this->is_model}->get_all_city();
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/prescription/prescription_view";
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
        $post['column_order'] = array('patient_med_id', 'name', 'date_of_birth', 'med_id', 'doctor', 'date', 'time', 'type');
        $post['column_search'] = array('a.patient_med_id', 'a.name', 'patient_info.date_of_birth', 'a.med_id', 'a.doctor', 'a.patient_availability_date_and_time', 'a.patient_availability_date_and_time', 'a.type');

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

        if ($order_list->status == 1) {
            $this->pres_status = "Requested";
        } elseif ($order_list->status == 2) {
            $this->pres_status = "Pending";
        } elseif ($order_list->status == 3) {
            $this->pres_status = "Filled";
        } elseif ($order_list->status == 4) {
            $this->pres_status = "Completed";
        } elseif ($order_list->status == 5) {
            $this->pres_status = "On Hold";
        } elseif ($order_list->status == 6) {
            $this->pres_status = "Denied";
        } elseif ($order_list->status == 7) {
            $this->pres_status = "Contact Patient!";
        } elseif ($order_list->status == 8) {
            $this->pres_status = "Contact Prescriber!";
        } elseif ($order_list->status == 9) {
            $this->pres_status = "Contact Pharmacy!";
        } elseif ($order_list->status == 10) {
            $this->pres_status = "Prior Authorization Needed";
        } elseif ($order_list->status == 11) {
            $this->pres_status = "Too Soon";
        } elseif ($order_list->status == 0) {
            $this->pres_status = "Action Required!";
        }
        $row = array();
        $row[] = $order_list->patient_med_id;
        $url = base_url() . "admin/users/manage_users/user_view/$order_list->user_id";
        $row[] = "<a href='" . $url . "'  >" . $order_list->first_name . "</a>";
        $row[] = $order_list->last_name;
        $row[] = date("d-m-Y", strtotime($order_list->date_of_birth));
        $row[] = $order_list->med_id;
        $doctorurl = base_url() . "admin/doctors/registered_doctors/doctor_view/$order_list->doctor_id";
        $row[] = "<a href='" . $doctorurl . "'  >" . $order_list->doctor_first_name . "</a>";
        $row[] = $order_list->doctor_last_name;
        $row[] = date("d-m-Y", strtotime($order_list->date));
        $row[] = $order_list->time;
        $row[] = ucfirst($order_list->title);
        $row[] = "<div class='btn-group'>
                   <button type='button' class='btn btn-danger btn-sm' style='min-width:160px;'>" . $this->pres_status . "</button>
		   <button type='button' class='btn btn-default dropdown-toggle m-r-20' data-toggle='dropdown' aria-expanded='false'>
                   <i class='fa fa-angle-down'></i>
                    </button>
                    <ul class='dropdown-menu' role='menu' x-placement='bottom-start' style='position: absolute; transform: translate3d(83px, 34px, 0px); top: 0px; left: 0px; will-change: transform;'>
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='0'>Action Required</a></li>
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='1'>Requested</a></li>
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='2' >Pending</a></li>                   
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='3'>Filled</a></li>
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='4' >Completed</a></li>    
                        <li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='5'>On Hold</a></li>			
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='6' >Denied</a></li>			
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='7' >Contact Patient!</a></li>			
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='8' >Contact Prescriber!</a></li>						
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='9' >Contact Pharmacy!</a></li>						
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='10' >Prior Authorization Needed</a></li>						
			<li><a href='#' class='p-status' action-id='" . $order_list->appointment_id . "' status-id='11' >Too Soon</a></li>						
			</ul>
		  </div>";
        $row[] = "
                <a href=" . base_url('admin/doctors/appointment_detail_controller/detail_past_appointment/') . $order_list->appointment_id . " style='color:white'><span class='label label-danger label-mini btn-custm'>
                    <i class='fa fa-eye'></i></span></a>&nbsp;<span class='label label-danger label-mini' style='cursor: pointer'><a href='#' class='admin_note' appt_id = '" . $order_list->appointment_id . "' data-toggle='modal' data-target='#add_note' style='color:white'>
                    <i class='fa fa-sticky-note'></i></a></span>";

        return $row;
    }

    public function update_appt_presc_status() {
        $data = $this->input->post();

        $response = $this->{$this->is_model}->update_appt_presc_status_model($data);
        $msg = ["message" => "Status updated successfully"];
        echo json_encode($msg);
        exit();
    }

    #---------------------------------------end here ---------------------------------------------
    

    public function get_admin_note_detail() {
        $appt_id = $this->input->post("appointment_id");
        if ($appt_id) {
            $row = $this->{$this->is_model}->get_admin_note($appt_id);
           
            if (!empty($row)) {
                foreach ($row as $key => $v) {
                    echo "<tr><td>" . $v['note'] . "</td>
                    <td>" . $v['admin_name'] . "</td>
                    <td>" . $v['updated_at'] . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3'><center>No Information available at this time</center></td></tr>";
            }
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
