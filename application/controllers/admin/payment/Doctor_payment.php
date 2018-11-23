<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_payment extends MY_Controller {

    protected $data = [];
    private $language_file = "payment/doctor_payment";
    protected $model = 'admin/payment/doctor_payment_model';
    protected $is_model = "doctor_payment";
    protected $insurance_status;
    protected $apt_status = 0;
    protected $gateway;

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

        //$this->data['add_datatable_js'] = "user_payment/user_payment.js";
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->data['auto_class1'] = "active";
        $this->data['auto1'] = "open";
        $this->data['payment_option'] = $this->doctor_payment->getpaySchedule();
        $this->data['view'] ="admin/payment/doctor_payment_view";
        $this->displayview($this->data);
    }

    function getdata() {
        // log_message("info",  json_encode($_POST));
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->doctor_payment->count_all($post),
            "recordsFiltered" => $this->doctor_payment->count_filtered($post),
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
        $post['column_order'] = array('doctors.med_id', 'doctors.first_name', 'doctors.last_name', 'doctor_payment_methods.payment_method_type');
        $post['column_search'] = array('doctors.med_id', 'doctors.first_name', 'doctors.last_name', 'doctor_payment_methods.payment_method_type');

        $list = $this->doctor_payment->get_order_list($post);
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

    public function table_data($order_list, $no) {
        $row = array();

        $row[] = $order_list->med_id;
        $row[] = $order_list->first_name;
        $row[] = $order_list->last_name;
        $row[] = $order_list->completed_appointment;
        $row[] = "USD " . (($order_list->commission * $order_list->due_amount) / 100);
        $row[] = ucwords(str_replace("_", " ", $order_list->payment_method_type));
        $row[] = "Pending";
        $row[] = "<i class='fa fa-money' aria-hidden='true'></i>  <a href='#' data-toggle='modal' class='pay_option' payment_option_id='" . $order_list->payment_option . "'  data-id='" . $order_list->doctor_id . "'  data-target='#payModal'>Pay</a>";
        return $row;
    }

    public function update_insurance_action_status() {
        if (!empty($this->input->post())) {
            $data = $this->input->post();
            $device_token = $this->user_payment->update_insurance_action_status_model($this->input->post());
            $data['notifiy_time'] = $this->config->item("date");
            $r = send_notification($this->lang->line('insurance_title'), sprintf($this->lang->line('insurance_body'), $device_token['name']), $this->lang->line('insurance_constant'), $device_token['device_token'], $data);
            $this->insurance_model->insert_notificationdata($r, $data);
            //  $this->db->insert("user_on_call_notification",["user_id"=>$data['user_id'],"notification_data"=>$r['']]);

            echo json_encode(["message" => "Status Successfully updated"]);
            exit();
        }
    }

    public function setpaymentmethod() {
        if (!empty($this->input->post("doctor_id")) && !empty($this->input->post("payment_option"))) {
            $this->doctor_payment->pay_status_update($this->input->post());
            $this->session->set_flashdata("flashsuccess", "Payment Status successfully updated");
            redirect("admin/payment/doctor_payment");
        }
    }

    public function sendPayToDoctor() {
        try {
            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe = \Stripe\Stripe::setApiKey($this->config->item("stripe_sk"));
            echo "A";
            // Create a payout to the specified recipient
            $payout = \Stripe\Transfer::create(array(
                        "amount" => 1000, // amount in cents
                        "currency" => "usd",
                        "destination" => "acct_1DGRCDJIMys30m00"
                            )
            );
            dd($payout);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

?>
