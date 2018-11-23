<?php

require(APPPATH . '/libraries/REST_Controller.php');

class On_call_appointment_controller extends REST_Controller {

    protected $response_send = ["status" => false];
    protected $language_file = ["api_message", "spn_api_message"];
    protected $headers;
    protected $appoinment_data;
    protected $appointment_request;

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will check the content type and change the language
      |------------------------------------------------------------------------------------------------------------
     */

    public function __construct() {
        try {
            //die("ddd");
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            change_languge($this->headers, $this->language_file);
            $this->load->model("api/on_call_appoinment_model", "appoinment_model");
        } catch (Exception $exc) {
            //$this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work -- we are create appoinment of patient based on dcotor state and specility
      | @return -- insert the data with doctor id
      |--------------------------------------------------------------------------------
     */

    public function create_on_call_patient_appoinment_post() {
        try {
            $this->appoinment_data = json_decode(file_get_contents('php://input'), true);
            check_acces_token(@$this->headers['Authorization']);
            $field_name = ["user_id", "patient_id", "doc_speciality_id", "symptom_ids", "symptom_start_date", "severity_of_symptom_id", "treatment_provider_plan_id", "payment_method_id", "latitude", "longitude","payment_method_type"];
            if (check_form_array_keys_existance($this->appoinment_data, $field_name) && check_user_input_values($this->appoinment_data)) {
                if ($this->appoinment_data['payment_method_type'] == "stripe") {
                    $response = $this->payAppointmentFee_post($this->appoinment_data['user_id'], $this->appoinment_data['payment_method_id'], $this->appoinment_data['amount']);
                    if ($response->status == "succeeded") {
                        $appint_id = $this->appoinment_model->on_call_insert_appoinment_detail($this->appoinment_data);
                         $this->_saveTransactionInfo($this->appoinment_data['user_id'], $appint_id, $response, $this->appoinment_data);


                        $av_doctor = $this->appoinment_model->getCurrentlyAvalDoctorOnTimeSlot($this->appoinment_data, @$this->headers['Accept-Language']);
                        $current_doct = $this->appoinment_model->GetToggleOnDoctor($this->appoinment_data, @$this->headers['Accept-Language']);
                        $doctor_ids = $this->finalBroadCastingDoctor($av_doctor, $current_doct);
                        if (!empty($doctor_ids) && count($doctor_ids) > 0) {
                            $get_doctor_device_tokens = $this->appoinment_model->get_doctor_device_token($doctor_ids);
                            $patient_info = $this->appoinment_model->get_user_notification_data($appint_id, $this->appoinment_data['patient_id']);
                            $total_device_token = explode("||||", $get_doctor_device_tokens['device_token']);
                            // send the notification to all the doctor
                            $response = $this->send_appointment_notification($total_device_token, $patient_info['data'], $patient_info['notification']);
                            // store the notifaication data
                            $this->appoinment_model->storeNotificationData($patient_info, $doctor_ids, $response, $appint_id);

                            $this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => (string) $appint_id, "wait_for_physian" => true, "message" => $this->lang->line("searching_physican")];
                        } else {
                            $this->response_send = ["is_token_expire" => false, "status" => true, "wait_for_physian" => false, "appointment_id" => (string) $appint_id, "message" => $this->lang->line("searching_physican")];
                        }
                    }
                } elseif (($this->appoinment_data['payment_method_type'] == "Paypal" || $this->appoinment_data['payment_method_type'] == "Venmo") && isset($this->appoinment_data['payment_method_nonce'])) {
                    $braintree_response = $this->pay_by_braintree($this->appoinment_data['amount'], $this->appoinment_data['payment_method_nonce']);
                    if ($braintree_response->success) {
                        $appint_id = $this->appoinment_model->on_call_insert_appoinment_detail($this->appoinment_data);
                        
                        $this->_saveBraintreeTransactionInfo($this->appoinment_data['user_id'], $appint_id, $braintree_response, $this->appoinment_data);

                        $av_doctor = $this->appoinment_model->getCurrentlyAvalDoctorOnTimeSlot($this->appoinment_data, @$this->headers['Accept-Language']);
                        $current_doct = $this->appoinment_model->GetToggleOnDoctor($this->appoinment_data, @$this->headers['Accept-Language']);
                        $doctor_ids = $this->finalBroadCastingDoctor($av_doctor, $current_doct);
                        if (!empty($doctor_ids) && count($doctor_ids) > 0) {
                            $get_doctor_device_tokens = $this->appoinment_model->get_doctor_device_token($doctor_ids);
                            $patient_info = $this->appoinment_model->get_user_notification_data($appint_id, $this->appoinment_data['patient_id']);
                            $total_device_token = explode("||||", $get_doctor_device_tokens['device_token']);
                            // send the notification to all the doctor
                            $response = $this->send_appointment_notification($total_device_token, $patient_info['data'], $patient_info['notification']);
                            // store the notifaication data
                            $this->appoinment_model->storeNotificationData($patient_info, $doctor_ids, $response, $appint_id);

                            $this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => (string) $appint_id, "wait_for_physian" => true, "message" => $this->lang->line("searching_physican")];
                        } else {
                            $this->response_send = ["is_token_expire" => false, "status" => true, "wait_for_physian" => false, "appointment_id" => (string) $appint_id, "message" => $this->lang->line("searching_physican")];
                        }
                    } else {
                        $this->response_send = ["message" => $braintree_response->message, "status" => $this->config->item("status_false")];
                    }
                }
            } else {
                $this->response_send = ["message" => $this->lang->line("field_key_missing"), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function payAppointmentFee_post($user_id, $pay_method_id, $amount) {

        try {
            $this->load->model('api/Payment_model');
            //get payment_method card data by payment_method_id
            $payment_method_info = $this->Payment_model->getPaymentMethodByIdUserId($pay_method_id, $user_id);

            if (count($payment_method_info) > 0) {

                if ($payment_method_info->payment_method_type == 'stripe') {

                    return $this->_payoutByStripe($user_id, $payment_method_info, $amount);
                }
                if ($payment_method_info->payment_method_type == 'paypal') {

                    return $this->payoutByPaypal();
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('payment_method_not_found'), "status" => $this->config->item("status_false")];
            }
        } catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err = $body['error'];
            $this->response_send = ["message" => $err['message'], "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function pay_by_braintree($amount, $method_nonce, $device_data = '') {
        try {
            //echo $method_nonce;die;
            require_once APPPATH . "third_party/lib/Braintree.php";
            // Instantiate a Braintree Gateway either like this:
            $this->gateway = new Braintree_Gateway([
                'environment' => $this->config->item("environment"),
                'merchantId' => $this->config->item("merchantId"),
                'publicKey' => $this->config->item("publicKey"),
                'privateKey' => $this->config->item("privateKey")
            ]);
            //$clientToken = $this->gateway->clientToken()->generate();
            //echo $clientToken;die;
            $result = $this->gateway->transaction()->sale([
                'amount' => $amount,
                'paymentMethodNonce' => $method_nonce,
                'options' => ['submitForSettlement' => true]
            ]);

            return $result;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /**
     * This function is use pay payment by stripe payment gateway 
     * @param $user_info object required
     * @param $data array required
     * @param $payment_metod_info object required 
     *
     * @return json
     */
    private function _payoutByStripe($user_id, $payment_metod_info, $amount) {


        require_once APPPATH . "third_party/stripe/stripe-php/init.php";
        $stripe_sk = $this->config->item("stripe_sk");
        $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
        try {

            //key for send response
            //$result_key = ($data['result_key']) ? $this->post_data['result_key'] : 'data';
            $user_id = $user_id;
            $this->load->model('api/Payment_model');
            //get stripe customer id
            $stripe_customer = $this->Payment_model->getCustomerByUserId($user_id);

            if (count($stripe_customer) > 0) {
                //create charge (payment) 
                $charge = \Stripe\Charge::create([
                            "amount" => 100 * $amount,
                            "currency" => "usd",
                            "description" => "Appointment Fixed",
                            "customer" => $stripe_customer->stripe_customer_id,
                            "source" => $payment_metod_info->stripe_card_id,
                ]);

                if ($charge->status == 'succeeded') {
                    //$transaction_data = $this->_saveTransactionInfo($user_info, $data, $charge);
                    return $charge;
                    //$this->response_send = ["message" => $this->lang->line('transcation_succeeded'), "status" => $this->config->item("status_true"), $result_key => $transaction_data];
                } else if ($charge->status == 'pending') {
                    $this->response_send = ["message" => $this->lang->line('transaction_pending'), "status" => $this->config->item("status_true"), $result_key => $transaction_data];
                } else {
                    $this->response_send = ["message" => $this->lang->line('transaction_failed'), "status" => $this->config->item("status_true"), $result_key => $transaction_data];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('stripe_customer_not_found'), "status" => $this->config->item("status_false"), $result_key => $transaction_data];
            }
        } catch (Stripe_CardError $e) {
            $error1 = $e->getMessage();
            $this->response_send = ["message" => $error1, "status" => $this->config->item("status_false")];
        } catch (Stripe_InvalidRequestError $e) {
            // Invalid parameters were supplied to Stripe's API
            $error2 = $e->getMessage();
            $this->response_send = ["message" => $error2, "status" => $this->config->item("status_false")];
        } catch (Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            $error3 = $e->getMessage();
            $this->response_send = ["message" => $error3, "status" => $this->config->item("status_false")];
        } catch (Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
            $error4 = $e->getMessage();
            $this->response_send = ["message" => $error4, "status" => $this->config->item("status_false")];
        } catch (Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $error5 = $e->getMessage();
            $this->response_send = ["message" => $error5, "status" => $this->config->item("status_false")];
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $error6 = $e->getMessage();
            $this->response_send = ["message" => $error6, "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    /**
     * This function is use to store transactioninfo 
     * @param $user_info object required
     * @param $data array required
     * @param $charge object required 
     *
     * @return object inserted
     */
    private function _saveTransactionInfo($user_id, $appointment_id, $charge, $pay_method) {
        // dd($charge);
        $this->load->model('api/Payment_model');
        $currentDate = time(); // get current date
        $current_timestamp = date("Y-m-d H:i:s", $currentDate);
        $transaction_array = [];
        $transaction_array['user_id'] = $user_id;
        $transaction_array['appointment_id'] = $appointment_id;
        $transaction_array['transaction_id'] = $charge->balance_transaction;
        $transaction_array['amount'] = ($charge->amount / 100);
        $transaction_array['charge_id'] = $charge->id;
        $transaction_array['paypal_email'] = isset($pay_method['paypal_email'])?$pay_method['paypal_email']:NULL;
        $transaction_array['venmo_id'] = isset($pay_method['venmo_id'])?$pay_method['venmo_id']:NULL;
        $transaction_array['transaction_status'] = $charge->status;
        $transaction_array['payment_json'] = json_encode($charge);
        $transaction_array['created_at'] = $current_timestamp;
        //insert transaction info
        return $transaction_info = $this->Payment_model->inserTransctionInfo($transaction_array);
    }

    private function _saveBraintreeTransactionInfo($user_id, $appointment_id, $repsonse,$pay_method) {
        $this->load->model('api/Payment_model');
        //$current_timestamp = date("Y-m-d H:i:s");
        $transaction_array = [];
        $transaction_array['user_id'] = $user_id;
        $transaction_array['appointment_id'] = $appointment_id;
        $transaction_array['transaction_id'] = $repsonse->transaction->id;
        $transaction_array['amount'] = $repsonse->transaction->amount;
        $transaction_array['payment_type'] = $pay_method['payment_method_type'];
        $transaction_array['paypal_email'] = (isset($pay_method['paypal_email']))?$pay_method['paypal_email']:'';
        $transaction_array['venmo_id'] = (isset($pay_method['venmo_id']))?$pay_method['venmo_id']:'';

        $transaction_array['transaction_status'] = "succeeded";
        $transaction_array['created_at'] = date("Y-m-d H:i:s");
        $transaction_array['payment_json'] = json_encode($repsonse);
        $this->Payment_model->inserTransctionInfo($transaction_array);
    }

    public function acceptAppointmentByDoctor_post() {
        try {

            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $input_data = $this->appoinment_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($input_data, ["appointment_id", "doctor_id"]) && check_user_input_values($input_data)) {

                $result = $this->appoinment_model->getDoctorIdAndCreateDate($input_data);
            
                if ($result['doctor_id'] == '' && $result['created_date'] > date("Y-m-d H:i:s")) {

                    $this->appoinment_model->updateDoctorIdAndTime($input_data);
                    $device_token = $this->appoinment_model->get_user_device_token(null, $input_data['appointment_id']);

                    // Send the notification to the particular user along with the doctor information
                    $notification_data = $this->appoinment_model->get_doctor_notification_data($input_data);
                    $notification_data['data']['notify_time'] = $this->config->item("date");

                    // send the notification to the user that appointment accepted by the doctor
                    $response = $this->send_appointment_notification($device_token['device_token'], $notification_data['data'], $notification_data['notification']);
                    //dd($response );
                    // store the notification data
                    $this->appoinment_model->userStoreNotificationData($notification_data, $notification_data['data']['user_id'], $response, $input_data['appointment_id'], $input_data['doctor_id']);


                    // delete the entry from later_today_appointment case                  
                    $this->db->where("appointment_id", $input_data['appointment_id']);
                    $this->db->delete("later_today_appointment");

                    // delete the oncall appointment notification  after accept the on call appointment 
                    $this->db->where("appointment_id", $input_data['appointment_id']);
                    $this->db->delete("doctor_on_call_notification");

                    // off the toggle
                    $this->db->where("doctor_id", $input_data['doctor_id']);
                    $this->db->update("doctors", ['is_loggedin' => '0']);

                    $this->response_send = [
                        "is_token_expire" => false,
                        "status" => true,
                        "message" => $this->lang->line("appointment_accepted")
                    ];
                } else {
                    $this->response_send = ["message" => $this->lang->line("appointment_rejected"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    /*
      |--------------------------------------------------------------------------------
      | Work -- Send the FCM Notification
      | @return -- none
      |--------------------------------------------------------------------------------
     */

    private function send_appointment_notification($device_token, $message, $title) {
        try {

            $this->load->library("pushnotification");

            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($device_token, $message, $title);
            return $response;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function finalBroadCastingDoctor($setavldoctor, $toggledoctor) {
        $toggleOnDoctor = array();
        $final_doctor = array();
        if (!empty($toggledoctor['doctor_id'])) {
            $toggleOnDoctor = explode(",", $toggledoctor['doctor_id']);
        }
        //dd($setavldoctor);
        $current_avl_doctor = array();
        $booked_doctor = array();
        if (!empty($setavldoctor) && count($setavldoctor) > 0) {
            foreach ($setavldoctor as $k => $v) {
                if ($v['status'] == 1) {
                    $booked_doctor[] = $v['id'];
                } else {
                    $current_avl_doctor[] = $v['id'];
                }
            }
        }
        $final_doctor = array_unique(array_merge($current_avl_doctor, array_diff($toggleOnDoctor, $booked_doctor)));
        return $final_doctor;
    }

    public function oncallAction_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $input_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($input_data, ["appointment_id", "action"]) && check_user_input_values($input_data)) {
                if ($input_data['action'] == "searching") {

                    $appointment_data = $this->appoinment_model->getAppointmentData($input_data['appointment_id']);
                    $appointment_data['symptom_ids'] = explode(",", $appointment_data['symptom_ids']);
                    $this->FindCurrentDcotorByAction($appointment_data);
                    $this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => $this->appoinment_data['id'], "wait_for_physian" => true, "message" => $this->lang->line("searching_physican")];
                } elseif ($input_data['action'] == "schedule_today") {
                    // set for crone job
                    $this->appoinment_model->appointment_booking_later_today_model($input_data);
                    $this->response_send = ["is_token_expire" => false, "message" => $this->lang->line("later_today"), "status" => $this->config->item("status_true")];
                } else {

                    $this->appoinment_model->appointment_cancel_model($input_data['appointment_id']);
                    $this->response_send = ["is_token_expire" => false, "message" => $this->lang->line("app_cancel"), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function FindCurrentDcotorByAction($data) {

        $this->appoinment_data = $data;


        $av_doctor = $this->appoinment_model->getCurrentlyAvalDoctorOnTimeSlot($this->appoinment_data);
        $current_doct = $this->appoinment_model->GetToggleOnDoctor($this->appoinment_data);

        $doctor_ids = $this->finalBroadCastingDoctor($av_doctor, $current_doct);

        //$doctor_ids = array_unique(array_merge(explode(",",$av_doctor['doctors_id']),explode(",",$current_doct['doctor_id'])));

        if (!empty($doctor_ids) && count($doctor_ids) > 0) {
            $get_doctor_device_tokens = $this->appoinment_model->get_doctor_device_token($doctor_ids);
            $patient_info = $this->appoinment_model->get_user_notification_data($this->appoinment_data['id'], $this->appoinment_data['patient_id']);
            $patient_info['data']['notify_time'] = $this->config->item("date");
            $total_device_token = explode("||||", $get_doctor_device_tokens['device_token']);

            // send the notification to all the doctor
            $response = $this->send_appointment_notification($total_device_token, $patient_info['data'], $patient_info['notification']);

            // store the notifaication data
            $this->appoinment_model->storeNotificationData($patient_info, $doctor_ids, $response, $this->appoinment_data['id']);

            $this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => $this->appoinment_data['id'], "wait_for_physian" => true, "message" => "DOC MD is searching for a Physician near you..."];
        } else {
            $this->response_send = ["is_token_expire" => false, "status" => true, "wait_for_physian" => false, "appointment_id" => $this->appoinment_data['id'], "message" => "Sorry! Doctor not found...."];
        }
        //return $this->response_send;
    }

    //On call appoitnent is rejcetd by the doctor(it means doctor don't want to accept the on call appointemnt )
    public function appointment_reject_by_docotor_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $input_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($input_data, ["appointment_id", "doctor_id", "is_rejected"]) && check_user_input_values($input_data)) {
                $this->db->insert("reject_on_call_appointment_by_doctor", $input_data);
                $this->response_send = ["status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

}

?>