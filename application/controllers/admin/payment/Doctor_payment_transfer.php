<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_payment_transfer extends MY_Controller {

    protected $data = [];
    protected $model = 'admin/payment/doctor_payment_model';
    protected $is_model = "doctor_payment";

    public function __construct() {
        parent::__construct();
        $this->isModelload();
    }

    //Set ths function for Crone jop for transfering the payment by stripe or brinatree
    public function index() {
        $list = $this->doctor_payment->getDoctorTotalPayment(); /// one time only 20 people payment set 
        
        $day = 14;
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                if (!empty($v['stripe_account_id']) && $v['stripe_account_id'] != '') { // Payment From stripe Payment gateway 
                    if ($v['payment_option'] == 1) { // One Time Payment 
                        if ($v['due_amount'] > 0) {
                            $this->doctor_payment->update_one_time_payment_option($v['doctor_id']);
                        }
                    } elseif ((int) $v['payment_option'] == 2 && (date("Y-m-d") == date("Y-m-d", strtotime($v['payment_date'])))) {
                        if ($v['due_amount'] > 0) {
                            $day = 1; //
                        }
                    } elseif ((int) $v['payment_option'] == 3 && (date("Y-m-d") == date("Y-m-d", strtotime($v['payment_date'])))) {
                        if ($v['due_amount'] > 0) {
                            $day = 7; //
                        }
                    } elseif ((int) $v['payment_option'] == 4 && (date("Y-m-d") == date("Y-m-d", strtotime($v['payment_date'])))) {
                        if ($v['due_amount'] > 0) {
                            $day = 14; //
                        }
                    } elseif ((int) $v['payment_option'] == 5 && (date("Y-m-d") == date("Y-m-d", strtotime($v['payment_date'])))) {
                        if ($v['due_amount'] > 0) {
                            $day = 30;
                        }
                    }
                    //$total_amount = ($v['due_amount'] - ( ($v['commission'] * $v['due_amount']) / 100));
                    $total_amount =  $v['due_amount'] -(($v['commission'] * $v['due_amount']) / 100);
                    $response = $this->sendPayToDoctor($v['stripe_account_id'], $total_amount);
                    if (isset($response->id) || @$response->id) {
                        $this->doctor_payment->update_doctor_payment_status($v['doctor_id'], $v['appointment_ids'], $response);
                        $this->doctor_payment->update_doctor_last_payment_date($v['doctor_id'], $day);
                    } else {
                        $this->doctor_payment->update_payment_failed($v['doctor_id'], $response);
                    }
                } else {
                   
                    // Payment from Paypal
                    $total_amount =  $v['due_amount'] -(($v['commission'] * $v['due_amount']) / 100);
                    $response = $this->sendPaymentTodDoctorByPaypal($v['paypal_email'], $total_amount);
                    
                    if (isset($response->batch_header->payout_batch_id) && @$response->batch_header->payout_batch_id) {
                        $this->doctor_payment->update_doctor_paypal_payment_status($v['doctor_id'], $v['appointment_ids'], $response, $total_amount);
                        $this->doctor_payment->update_doctor_last_payment_date($v['doctor_id'], $day);
                    } else {
                        $this->doctor_payment->update_payment_failed($v['doctor_id'], $response, "paypal");
                    }
                }
            }
        }
    }

    private function sendPaymentTodDoctorByPaypal($paypa_email, $amount) {
        require_once APPPATH . "third_party/paypal/bootstrap.php";
        $payouts = new \PayPal\Api\Payout();
        $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())->setEmailSubject("You have a new payment");
        $senderItem = new \PayPal\Api\PayoutItem(
                array("recipient_type" => "EMAIL",
            "receiver" => $paypa_email,
            "note" => "Thank you",
            "sender_item_id" => uniqid(),
            "amount" => array(
                "value" => $amount,
                "currency" => "USD")
                )
        );
        $payouts->setSenderBatchHeader($senderBatchHeader)
                ->addItem($senderItem);
        try {
            $output = $payouts->create(null, $apiContext);
            return $output;
            //echo $output->batch_header->payout_batch_id;
            //echo $output->batch_header->batch_status;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    // send payment to doctor paymenthod is stripe
    private function sendPayToDoctor($stripe_id, $amount) {
        try {
            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe = \Stripe\Stripe::setApiKey($this->config->item("stripe_sk"));
            // Create a payout to the specified recipient
            $payout = \Stripe\Transfer::create(array(
                        "amount" => $amount * 100, // amount in cents
                        "currency" => "usd",
                        "destination" => $stripe_id, //"accto_1DOfKRG9WXy8mBJH",
                        "description" => 'Doctor Payment'
                            )
            );
            return $payout;
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
    }

}

?>
