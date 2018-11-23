<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_payment extends MY_Controller {

    protected $data = [];
    private $language_file = "payment/user_payment";
    protected $model = 'admin/payment/user_payment_model';
    protected $is_model = "user_payment";
    protected $insurance_status;
    protected $apt_status = 0;
    protected $gateway;

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
        $this->BuildFormEnv(["template_helper"]);

        //$this->data['add_datatable_js'] = "user_payment/user_payment.js";
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->data['auto_class1'] = "active";
        $this->data['auto1'] = "open";
        $this->data['view'] = "admin/payment/user_payment_view";
        $this->displayview($this->data);
    }

    function getdata() {
        // log_message("info",  json_encode($_POST));
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->user_payment->count_all($post),
            "recordsFiltered" => $this->user_payment->count_filtered($post),
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
        $post['column_order'] = array('all_appointment.patient_med_id', 'all_appointment.name', 'all_appointment.doctor', 'all_appointment.type', 'all_appointment.patient_availability_date_and_time', 'all_appointment.patient_availability_date_and_time', 'provider_plan.amount', "appointment.payment_method_id", "patient_info.provider", "patient_info.member_id", "patient_info.ins_group", null, "appointment.insurance_status");
        $post['column_search'] = array('all_appointment.patient_med_id', 'all_appointment.name', 'all_appointment.doctor', 'all_appointment.type', 'all_appointment.patient_availability_date_and_time', 'all_appointment.patient_availability_date_and_time', 'provider_plan.amount', "appointment.payment_method_id", "patient_info.provider", "patient_info.member_id", "patient_info.ins_group", "appointment.insurance_status");

        $list = $this->user_payment->get_order_list($post);
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
        //$post['external_filtering'] = $this->input->post('filter_data');
        return $post;
    }

    public function table_data($order_list, $no) {
        $row = array();
       
        $row[] = $order_list->med_id;
        $row[] = $order_list->username;
        $row[] = $order_list->provider_name;
        $row[] = $order_list->title;
        $row[] = $order_list->patient_availability_date;
        $row[] = $order_list->patient_availability_time;
        $row[] = $order_list->amount;
        $row[] = $order_list->payment_type;
        if ((int) $order_list->apt_status == 2) {
            if ($order_list->transaction_status == "succeeded") {
                $row[] = $order_list->status . " <a href='" . base_url("admin/payment/user_payment/getpayment/$order_list->id/$order_list->apt_status") . "'>Refund</a>";
            } else {
                $row[] = "Refunded";
            }
        } elseif ((int) $order_list->apt_status == 3) {
            if ($order_list->transaction_status == "succeeded") {
                $row[] = $order_list->status . " <a href='" . base_url("admin/payment/user_payment/getpayment/$order_list->id/$order_list->apt_status") . "'>Refund</a>";
            } else {
                $row[] = "Refunded";
            }
            //$row[] = $order_list->status . " <a href='#'>Refund</a>";
        } else {
            $row[] = $order_list->status;
        }
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

    public function getPayment($id, $status) {
        try {
            if ((int) $status == 2) { // Cancel By Patient
                $this->apt_status = $status;
            } elseif ((int) $status == 3) { // Cancel By Doctor
                $this->apt_status = $status;
            }
            $amount_data = $this->user_payment->getAppintmentFees($id, $this->apt_status);
            if ($amount_data) {
                if ($amount_data['payment_type'] == "stripe") {
                    //Payment refund by the stripe
                    $amount_data['amount'] = ($status == 3) ? (($amount_data['amount'] / 100) * 50) : $amount_data['amount'];
                    $this->paymentRefundByStripe($amount_data);
                } else {               
                    // Payment refund  by the braintree
                    $amount_data['amount'] = ($status == 3) ? (($amount_data['amount'] / 100) * 50) : $amount_data['amount'];
                    $this->paymentRefundByBraintree($amount_data);
                }
            } else {
                $this->session->set_flashdata("flasherror", "NO data found");
                redirect("admin/payment/user_payment");
            }
        } catch (Exception $exc) {
            $this->session->set_flashdata("flasherror", $exc->getMessage());
        }
    }

    private function paymentRefundByStripe($amount_data) {
        try {
            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe = \Stripe\Stripe::setApiKey($this->config->item("stripe_sk"));
            $refund = \Stripe\Refund::create([
                        'charge' => $amount_data['charge_id'],
                        'amount' => 100 * $amount_data['amount']
            ]);
            if ($refund->status == "succeeded") {
                $this->user_payment->update_payment_status($amount_data);
                $this->session->set_flashdata("flashsuccess", "Payment Successfull Refund");
            } else {
                $this->session->set_flashdata("flashsuccess", $refund->failure_reason);
            }
        } catch (Exception $exc) {
            $this->session->set_flashdata("flasherror", $exc->getMessage());
        }
        redirect("admin/payment/user_payment");
    }

    private function paymentRefundByBraintree($data) {
        try {
            require_once APPPATH . "third_party/lib/Braintree.php";
            // Instantiate a Braintree Gateway either like this:
            $this->gateway = new Braintree_Gateway([
                'environment' => $this->config->item("environment"),
                'merchantId' => $this->config->item("merchantId"),
                'publicKey' => $this->config->item("publicKey"),
                'privateKey' => $this->config->item("privateKey")
            ]);
            $refund = $this->gateway->transaction()->refund($data['transaction_id'], $data['amount']);
           
            if($refund->success){
                 $this->user_payment->update_payment_status($data);
                  $this->session->set_flashdata("flashsuccess", "Payment Successfull Refund");
            }else{
                $this->session->set_flashdata("flashsuccess", $refund->message);
            }
            redirect("admin/payment/user_payment");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }

    public function sendmoneydoctor() {
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
