<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Appointment_controller extends REST_Controller {

    protected $response_send = ["status" => false, "message" => "Something went to wrong Please try again.."];
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
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            change_languge($this->headers, $this->language_file);
            $this->load->model("api/appoinment_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work -- we are create appoinment of patient based on dcotor state and specility
      | @return -- insert the data with doctor id
      |--------------------------------------------------------------------------------
     */

    public function create_patient_appoinment_post() {
        try {
            $this->appoinment_data = json_decode(file_get_contents('php://input'), true);
            check_acces_token(@$this->headers['Authorization']);
            $field_name = [
                "patient_id",
                "doc_speciality_id",
                "symptom_ids",
                "symptom_start_date",
                "severity_of_symptom_id",
                "treatment_provider_plan_id",
                "patient_availability_date",
                "patient_availability_time",
                "doctor_id",
                "slot_id",
                "user_id",
                "latitude",
                "longitude",
                "time_abbreviation",
                "amount",
                "payment_method_type"
            ];

            if (check_form_array_keys_existance($this->appoinment_data, $field_name) && check_user_input_values($this->appoinment_data)) {
                if ($this->appoinment_data['payment_method_type'] == "stripe") {

                    $response = $this->payAppointmentFee_post($this->appoinment_data['user_id'], $this->appoinment_data['payment_method_id'], $this->appoinment_data['amount']);
                    if ($response->status == "succeeded") {
                        $api_response = $this->appoinment_model->insert_appoinment_detail($this->appoinment_data);
                        $this->_saveTransactionInfo($this->appoinment_data['user_id'], $api_response->id, $response, $this->appoinment_data);

                        $this->sendEmailToUser($api_response->id);
                        // get the current login user device token from database
                        $device_token = $this->appoinment_model->get_device_token($this->appoinment_data['doctor_id']);
                        $this->send_appointment_notification_to_doctor($device_token->device_token, @$api_response->id);
                        // send the response
                        $this->response_send = ["appointment" => $api_response, "status" => $this->config->item("status_true")];
                    }
                } elseif (($this->appoinment_data['payment_method_type'] == "Paypal" || $this->appoinment_data['payment_method_type'] == "Venmo") && isset($this->appoinment_data['payment_method_nonce'])) {

                    $braintree_response = $this->pay_by_braintree($this->appoinment_data['amount'], $this->appoinment_data['payment_method_nonce']);

                    if ($braintree_response->success) {
                        $api_response = $this->appoinment_model->insert_appoinment_detail($this->appoinment_data);
                        $this->_saveBraintreeTransactionInfo($this->appoinment_data['user_id'], $api_response->id, $braintree_response, $this->appoinment_data);
                        $this->sendEmailToUser($api_response->id);
                        $device_token = $this->appoinment_model->get_device_token($this->appoinment_data['doctor_id']);
                        $this->send_appointment_notification_to_doctor($device_token->device_token, @$api_response->id);
                        // send the response
                        $this->response_send = ["appointment" => $api_response, "status" => $this->config->item("status_true")];
                    } else {
                        $this->response_send = ["message" => $braintree_response->message, "status" => $this->config->item("status_false")];
                    }
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
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
        $transaction_array['paypal_email'] = (isset($pay_method['paypal_email'])) ? $pay_method['paypal_email'] : '';
        $transaction_array['venmo_id'] = (isset($pay_method['venmo_id'])) ? $pay_method['venmo_id'] : '';
        $transaction_array['transaction_status'] = $charge->status;
        $transaction_array['payment_json'] = json_encode($charge);
        $transaction_array['created_at'] = $current_timestamp;
        //insert transaction info
        return $transaction_info = $this->Payment_model->inserTransctionInfo($transaction_array);
    }

    private function _saveBraintreeTransactionInfo($user_id, $appointment_id, $repsonse, $pay_method) {
        $this->load->model('api/Payment_model');
        //$current_timestamp = date("Y-m-d H:i:s");
        $transaction_array = [];
        $transaction_array['user_id'] = $user_id;
        $transaction_array['appointment_id'] = $appointment_id;
        $transaction_array['transaction_id'] = $repsonse->transaction->id;
        $transaction_array['amount'] = $repsonse->transaction->amount;
        $transaction_array['payment_type'] = $pay_method['payment_method_type'];
        $transaction_array['paypal_email'] = (isset($pay_method['paypal_email'])) ? $pay_method['paypal_email'] : '';
        $transaction_array['venmo_id'] = (isset($pay_method['venmo_id'])) ? $pay_method['venmo_id'] : '';
        $transaction_array['transaction_status'] = "succeeded";
        $transaction_array['created_at'] = date("Y-m-d H:i:s");
        $transaction_array['payment_json'] = json_encode($repsonse);
        $this->Payment_model->inserTransctionInfo($transaction_array);
    }

    private function pay_by_braintree($amount, $method_nonce, $device_data = '') {
        try {
            require_once APPPATH . "third_party/lib/Braintree.php";
            // Instantiate a Braintree Gateway either like this:
            $this->gateway = new Braintree_Gateway([
                'environment' => $this->config->item("environment"),
                'merchantId' => $this->config->item("merchantId"),
                'publicKey' => $this->config->item("publicKey"),
                'privateKey' => $this->config->item("privateKey")
            ]);
            $result = $this->gateway->transaction()->sale([
                'amount' => $amount,
                'paymentMethodNonce' => $method_nonce,
                'options' => ['submitForSettlement' => true]
            ]);
            return $result;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work -- Send the FCM Notification
      | @return -- none
      |--------------------------------------------------------------------------------
     */

    private function send_appointment_notification_to_doctor($device_token, $apnt_id) {
        try {
            $this->load->library("pushnotification");

            // get notification structure
            $message = $this->appoinment_model->get_notification_data($apnt_id);
            if ($message) {

                $title = array(
                    'title' => $this->lang->line("new_appointment_create"), //'Appointment',
                    'body' => $this->lang->line("new_appointment_body"), //'New Appointment',
                    "type" => $this->lang->line("new_appointment_body_constant")
                );
                $message['notify_time'] = $this->config->item("date");
                // send the notification to FCM
                $response = $this->pushnotification->sendPushNotificationToFCMSever($device_token, $message, $title);

                $insert_data = [
                    "appointment_id" => $message['appointment_id'],
                    "doctor_id" => $this->appoinment_data['doctor_id'],
                    "notification_data" => json_encode(array_merge($title, ["data" => $message])),
                    "fcm_response" => $response,
                    "created_date" => $this->config->item("date")
                ];
                $this->appoinment_model->appointment_data_insert($insert_data);

                $r = json_decode($response);
                //dd($r);
                if (!$r->success) {
                    return true;
                    //echo json_encode(["error"=>$r->results[0]->error,"status"=>$this->config->item("status_false")]);die;
                }
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work --  Get the upcoming/recent/past appointment of the doctor
      | @return -- json data
      | Note - Only 3 recent record
      |--------------------------------------------------------------------------------
     */

    public function get_all_doctor_appointment_get() {
        try {

            $this->appointment_request = $this->get();
            check_acces_token(@$this->headers['Authorization'], $this->appointment_request['doctor_id'], 'doctors');
            if ($this->appointment_request['doctor_id'] != '' && count($this->appointment_request) == 1) {

                // Recent  appointment(past)
                $recent_condition = [
                    "patient_availability_date_and_time <=" => $this->config->item("appointment_date"), "doctor_id" => $this->appointment_request['doctor_id']];
                $recent_status = $this->config->item("recent_status");
                // when we want to prescription of past appointment send prescription
                $recent_data = get_all_appointment($recent_condition, $recent_status, null, "prescription", $this->headers['Accept-Language']);


                $recent_appoint = appointment_array($recent_data);
                //dd($recent_appoint);
                //$recent_appointment =  (count(appointment_array($recent_data))>0)?array_merge(["prescriptions"=>["A","B"]],appointment_array($recent_data)):[];
                //Cancel appointment
                $cancel_condition = ["doctor_id" => $this->appointment_request['doctor_id']];
                $status = $this->config->item("cancel_status");
                $cancel_data = get_all_appointment($cancel_condition, $status);
                $cancel_appointment = appointment_array($cancel_data);

                //upcoming appointment
                $upcoming_condition = [
                    "patient_availability_date_and_time >=" => $this->config->item("appointment_date"), "doctor_id" => $this->appointment_request['doctor_id']];
                $status = $this->config->item("upcoming_status");
                $upcmng_data = get_all_appointment($upcoming_condition, $status);
                $up_appointment = appointment_array($upcmng_data);

                //missed appointment
                $missed_condition = [
                    "doctor_id" => $this->appointment_request['doctor_id']];
                $status = $this->config->item("missed_status");
                $missed_data = get_all_appointment($missed_condition, $status);
                $missed_appointment = appointment_array($missed_data);

                $this->response_send = [
                    "upcomming_appointments" => $up_appointment,
                    "recent_appointments" => $recent_appoint,
                    "cancel_appointments" => $cancel_appointment,
                    "missed_appointments" => $missed_appointment,
                    "status" => $this->config->item("status_true")
                ];
                //dd($cancel_appointment);
            } else {
                $this->doctor_appointment();
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    /*
      |--------------------------------------------------------------------------------
      | Work --  Get the upcoming/recent/past appointment based on action and doctor id
      | @return -- json data
      |--------------------------------------------------------------------------------
     */

    public function doctor_appointment() {
        if ($this->appointment_request['action'] == "upcoming") {
            $upcoming_condition = [
                "DATE_ADD(all_appointment.patient_availability_date_and_time,INTERVAL 23 MINUTE) >=" => $this->config->item("appointment_date"), "all_appointment.doctor_id" => $this->appointment_request['doctor_id']];
            $status = $this->config->item("upcoming_status");
            $upcmng_data = get_all_appointment($upcoming_condition, $status, "all_up_appointment");
            $up_appointment = appointment_array($upcmng_data);
            $this->response_send = [
                "appointments" => $up_appointment,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "recent") {
            // Recent  appointment(past)
            //$recent_condition = ["patient_availability_date_and_time <=" => $this->config->item("appointment_date"), "doctor_id" => $this->appointment_request['doctor_id']];
            $recent_condition = ["doctor_id" => $this->appointment_request['doctor_id']];
            $recent_status = $this->config->item("past_status");
            $recent_data = get_all_appointment($recent_condition, $recent_status, "all_recent_appointment", "prescription", $this->headers['Accept-Language']);
            //$recent_appointment =  (count(appointment_array($recent_data))>0)?array_merge(["prescriptions"=>["A","B"]],appointment_array($recent_data)):[];
            $recent_app = appointment_array($recent_data);
            $this->response_send = [
                "appointments" => $recent_app,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "cancel") {
            //Cancel appointment

            $cancel_condition = ["doctor_id" => $this->appointment_request['doctor_id']];
            $status = $this->config->item("cancel_status");
            $cancel_data = get_all_appointment($cancel_condition, $status, "all_cancel_appointment");
            $cancel_appointment = appointment_array($cancel_data);
            //dd($cancel_appointment);
            $this->response_send = [
                "appointments" => $cancel_appointment,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "missed") {
            $missed_condition = [
                "doctor_id" => $this->appointment_request['doctor_id']];
            $status = $this->config->item("missed_status");
            $missed_data = get_all_appointment($missed_condition, $status, "all_missed_appointment");
            $missed_appointment = appointment_array($missed_data);
            $this->response_send = [
                "appointments" => $missed_appointment,
                "status" => $this->config->item("status_true")
            ];
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work --  Get the upcoming/recent/past appointment of the users/patient
      | @return -- json data
      | Note - Only 3 recent record
      |--------------------------------------------------------------------------------
     */

    public function get_all_users_appointment_get() {
        try {
            $this->appointment_request = $this->get();
            check_acces_token(@$this->headers['Authorization']);
            if ($this->appointment_request['user_id'] != '' && count($this->appointment_request) == 1) {
                
                // Recent  appointment(past)
                $recent_condition = [
                    "a.patient_availability_date_and_time <=" => $this->config->item("appointment_date"), "a.user_id" => $this->appointment_request['user_id']];
                $recent_status = $this->config->item("recent_status");

                $recent_data = get_user_appointment($recent_condition, $recent_status, null, "prescription", $this->headers['Accept-Language']);

                $recent_user_app = appointment_array($recent_data);
                //$recent_appointment =  (count(appointment_array($recent_data))>0)?array_merge(["prescriptions"=>["A","B"]],appointment_array($recent_data)):[];
                // upcoming appointment
                $upcoming_condition = [
                    "DATE_ADD(a.patient_availability_date_and_time ,INTERVAL 23 MINUTE)>= " => $this->config->item("appointment_date"), "a.user_id" => $this->appointment_request['user_id']];
                $status = $this->config->item("upcoming_status");
                $upcmng_data = get_user_appointment($upcoming_condition, $status, null);
                // 3->only three record,"users"=>all users appointment based on user id
                $up_appointment = appointment_array($upcmng_data);

                //Cancel appointment
                $cancel_condition = ["a.user_id" => $this->appointment_request['user_id']];
                $status = $this->config->item("cancel_status");
                $cancel_data = get_user_appointment($cancel_condition, $status, null);
                $cancel_appointment = appointment_array($cancel_data);

                // missed appointment
                $missed_condition = [
                    "a.user_id" => $this->appointment_request['user_id']];
                $status = $this->config->item("missed_status");
                $missed_data = get_user_appointment($missed_condition, $status, null);
                // 3->only three record,"users"=>all users appointment based on user id
                $missed_appointment = appointment_array($missed_data);




                $this->response_send = [
                    "upcomming_appointments" => $up_appointment,
                    "recent_appointments" => $recent_user_app,
                    "cancel_appointments" => $cancel_appointment,
                    "missed_appointments" => $missed_appointment,
                    "status" => $this->config->item("status_true")
                ];
                //dd($cancel_appointment);
            } else {
                $this->users_appointment();
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    /*
      |--------------------------------------------------------------------------------
      | Work --  Get the upcoming/recent/past appointment based on action and users/patient id
      | @return -- json data
      |--------------------------------------------------------------------------------
     */

    public function users_appointment() {
        if ($this->appointment_request['action'] == "upcoming") {
            //echo date("Y-m-d H:i:s",strtotime($this->config->item("appointment_date"))+(22*60));die;
            //$upcoming_condition = ["a.patient_availability_date_and_time >=" => $this->config->item("appointment_date"), "b.user_id" => $this->appointment_request['user_id']];
            $upcoming_condition = ["DATE_ADD(a.patient_availability_date_and_time,INTERVAL 22 MINUTE) >=" => $this->config->item("appointment_date"), "a.user_id" => $this->appointment_request['user_id']];

            $status = $this->config->item("upcoming_status");
            $upcmng_data = get_user_appointment($upcoming_condition, $status, "all_up_appointment");
            $up_appointment = appointment_array($upcmng_data);
            $this->response_send = [
                "appointments" => $up_appointment,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "recent") {
            // Recent  appointment(past)
            //$recent_condition = ["a.patient_availability_date_and_time <=" => $this->config->item("appointment_date"), "b.user_id" => $this->appointment_request['user_id']];
            $recent_condition = ["a.user_id" => $this->appointment_request['user_id']];
            $recent_status = $this->config->item("recent_status");

            $recent_data = get_user_appointment($recent_condition, $recent_status, "all_recent_appointment", "prescritpion", $this->headers['Accept-Language'], "a.doctor_id IS NOT NULL");
            $recent_user_appoint = appointment_array($recent_data);

            //$recent_appointment =  (count(appointment_array($recent_data))>0)?array_merge(["prescriptions"=>["A","B"]],appointment_array($recent_data)):[];
            $this->response_send = [
                "appointments" => $recent_user_appoint,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "cancel") {
            //Cancel appointment

            $cancel_condition = ["a.user_id" => $this->appointment_request['user_id']];
            $status = $this->config->item("cancel_status");
            $cancel_data = get_user_appointment($cancel_condition, $status, "all_cancel_appointment");
            $cancel_appointment = appointment_array($cancel_data);
            //dd($cancel_appointment);
            $this->response_send = [
                "appointments" => $cancel_appointment,
                "status" => $this->config->item("status_true")
            ];
        } elseif ($this->appointment_request['action'] == "missed") {
            $missed_condition = [
                "b.user_id" => $this->appointment_request['user_id']];
            $status = $this->config->item("missed_status");
            $missed_data = get_user_appointment($missed_condition, $status, "all_missed_appointment");
            $missed_appointment = appointment_array($missed_data);
            $this->response_send = [
                "appointments" => $missed_appointment,
                "status" => $this->config->item("status_true")
            ];
        }
    }

    /*
     * This function is used for get all free slot of doctor
     * @param date/state/specility info
     * @return json array
     */

    public function user_appointment_booking_post() {
        try {
            $api_array = array();
            $booking_data = json_decode(file_get_contents('php://input'), true);
            $booking_keys = [
                "spacility",
                "date",
                "state",
                "time_abbreviation"
            ];
            check_acces_token(@$this->headers['Authorization']);
            if (check_form_array_keys_existance($booking_data, $booking_keys) && check_user_input_values($booking_data)) {
                // dd($booking_data);
                $free_slot = $this->appoinment_model->user_appointment_booking_model($booking_data, @$this->headers['Accept-Language']);
                if ($free_slot) {
                    // dd($free_slot);

                    $this->response_send = ['status' => $this->config->item("status_true"), "slots" => $free_slot];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_doctor_available'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    // appointment is completed by the doctor
    public function appointment_completed_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $appointment_id = $this->get();
            if (count($appointment_id) > 0) {
                $result = $this->appoinment_model->appointment_completed_model($appointment_id['appointment_id']);
                if ($result) {

                    $this->response_send = ["status" => $this->config->item("status_true")];

                    // Send Notification of User after completing the appointment
                    $result_data = $this->appoinment_model->get_notification_user_data($appointment_id['appointment_id']);

                    $response = send_notification($this->lang->line('appointment_summary'), sprintf($this->lang->line('appointment_summary_body'), $result_data['user_name']), $this->lang->line('appointment_constant'), $result_data['user_device_token'], $result_data);

                    $this->appoinment_model->insert_notification_user_data($appointment_id['appointment_id'], $response, $result_data);

                    // if any appointment come for later today                   
                    $this->db->where("DATE(created_date)", date("Y-m-d"));
                    $this->db->where("is_broadcasting", 0);
                    $query = $this->db->select("appointment_id,is_broadcasting,created_date")->from("later_today_appointment")->get();

                    //echo $this->db->last_query();die;
                    if ($query->num_rows() > 0) {
                        $this->load->model("api/on_call_appoinment_model");
                        $row = $query->row_array();

                        $appointment_data = $this->on_call_appoinment_model->getAppointmentData($row['appointment_id']);
                        //dd($appointment_data);
                        $appointment_data['symptom_ids'] = explode(",", $appointment_data['symptom_ids']);
                        $this->FindCurrentDcotorByAction($appointment_data);
                    } else {

                        // delete the prevoius day record
                        $this->db->where("DATE(created_date) !=", date("Y-m-d"));
                        $this->db->delete("later_today_appointment");

                        // update the record 
                        $this->db->update("later_today_appointment", ['is_broadcasting' => 0]);
                        //----------------------------------------------------------------
                        $this->db->where("DATE(created_date)", date("Y-m-d"));
                        $this->db->where("is_broadcasting", 0);
                        $query = $this->db->select("appointment_id,is_broadcasting,created_date")->from("later_today_appointment")->get();

                        if ($query->num_rows() > 0) {
                            $this->load->model("api/on_call_appoinment_model");
                            $row = $query->row_array();

                            $appointment_data = $this->on_call_appoinment_model->getAppointmentData($row['appointment_id']);
                            //dd($appointment_data);
                            $appointment_data['symptom_ids'] = explode(",", $appointment_data['symptom_ids']);
                            $this->FindCurrentDcotorByAction($appointment_data);
                        }
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
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

        //dd($this->appoinment_data);

        $av_doctor = $this->on_call_appoinment_model->getCurrentlyAvalDoctorOnTimeSlot($this->appoinment_data, null);
        $current_doct = $this->on_call_appoinment_model->GetToggleOnDoctor($this->appoinment_data, @$this->headers["Accept-Language"]);
        $doctor_ids = $this->finalBroadCastingDoctor($av_doctor, $current_doct);

        //dd($doctor_ids);
        //$doctor_ids = array_unique(array_merge(explode(",",$av_doctor['doctors_id']),explode(",",$current_doct['doctor_id'])));

        if (!empty($doctor_ids) && count($doctor_ids) > 0) {
            $doctor_ids = reset($doctor_ids);

            $get_doctor_device_tokens = $this->on_call_appoinment_model->get_doctor_device_token($doctor_ids);

            $patient_info = $this->on_call_appoinment_model->get_user_notification_data($this->appoinment_data['id'], $this->appoinment_data['patient_id']);

            $patient_info['data']['notify_time'] = $this->config->item("date");

            $total_device_token = explode("||||", $get_doctor_device_tokens['device_token']);

            //dd($patient_info);
            // send the notification to all the doctor
            $response = $this->send_appointment_notification($total_device_token, $patient_info['data'], $patient_info['notification']);
//            $update_id = ['appointment_id'=>$this->appoinment_data['id'],$doctor_ids[0]];
//            $this->appoinment_model->updateDoctorIdAndTime($update_id);

            $this->db->where("appointment_id", $this->appoinment_data['id']);
            $this->db->update("later_today_appointment", ['is_broadcasting' => 1]);

            // store the notifaication data
            $this->on_call_appoinment_model->storeNotificationData($patient_info, [$doctor_ids], $response, $this->appoinment_data['id']);

            //$this->response_send = ["is_token_expire" => false, "status" => $this->config->item("status_true"), "appointment_id" => $this->appoinment_data['id'], "wait_for_physian" => true, "message" => "DOC MD is searching for a Physician near you..."];
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

    private function send_appointment_notification($device_token, $message, $title) {
        try {

            $this->load->library("pushnotification");

            // send the notification to FCM
            $response = $this->pushnotification->sendPushNotificationToFCMSever($device_token, $message, $title);
            return $response;
        } catch (Exception $exc) {
            //$this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            //$this->response($this->response_send);
        }
    }

    public function sendEmailToUser($id, $template_id = 21) {
        try {
            $this->db->where("appointment_id", $id);
            $query = $this->db->select("(SELECT email from doctors WHERE id = (SELECT doctor_id from appointment WHERE id = '" . $id . "' )) AS doctor_email,(SELECT email from users WHERE id = (SELECT user_id from appointment WHERE id = '" . $id . "' )) AS user_email,DATE(patient_availability_date_and_time) AS date,TIME(patient_availability_date_and_time) AS time,type")->from("all_appointment")->get();
            $row1 = $query->row_array();

            if (!empty($row1)) {
                $this->config->load('shared');
                $data = get_email_templates(["id" => $template_id]); // template fixed on 18 id
                $data['message'] = $data[0]['message'];
                $data['appointment_detail'] = $row1;
                $message = $this->load->view("api_email_template/new_appointment_user_template", $data, TRUE);

                $this->load->library("email_setting");
                $from = $this->config->item("from");

                $response = $this->email_setting->send_email([$row1['doctor_email'], $row1['user_email']], $from, $message, $data[0]["subject"]);

                //return $response;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    // Get 30 days doctor free slot
    public function getThirtyDaysDoctorFreeSlot_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $data = json_decode(file_get_contents('php://input'), true);
            $data_keys = [
                "speciality_id",
                "state",
                "from ",
                "to"
            ];
            if (check_form_array_keys_existance($data, $data_keys) && check_user_input_values($data)) {

                $result = $this->appoinment_model->getThirtyDaysSlotModel($data, @$this->headers['Accept-Language']);
                $this->response_send = ["avaliable_dates" => $result, "status" => $this->config->item("status_true")];
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function submitedPrescriptionByTheDoctor_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = $this->get();

            if (check_form_array_keys_existance($data, ["appointment_id"]) && check_user_input_values($data)) {
                $result = $this->appoinment_model->submited_prescription_model($data);
                if ($result) {
                    $result1 = $this->appoinment_model->get_doctor_notification_data($data);
                    $this->response_send = ["status" => $this->config->item("status_true")];
                    $response = send_notification($this->lang->line('prescription_submit_title'), sprintf($this->lang->line('prescription_submit_body'), $result1['name']), $this->lang->line('prescription_submit_constant'), $result1['device_token'], $result1);
                    $this->appoinment_model->insert_notification_user_data($data["appointment_id"], $response, $result1);
                } else {
                    $this->response_send = ["message" => $this->lang->line('appt_not_exist'), "status" => $this->config->item("status_true")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // Appointment is initiative by the doctor
    public function appointment_initiative_get() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $appointment_id = $this->get();

            if (check_form_array_keys_existance($appointment_id, ["appointment_id"]) && check_user_input_values($appointment_id)) {

                $result = $this->appoinment_model->appointment_initiative_model($appointment_id['appointment_id']);
                if ($result) {
                    $r = $this->appoinment_model->get_doctor_notification_data($appointment_id);
                    $title = array("notification" => array(
                            "title" => "Apppointment Initiative!",
                            "body" => "Your Apppointment Request Accepted by " . $r['name'] . "",
                            "type" => "APPT_INITIATIVE",
                        )
                    );
                    $message = ["data" => $r];
                    $data = $this->send_appointment_notification($r['device_token'], $message, $title);
                    $this->response_send = ["status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = [
                        "message" => $this->lang->line('appt_on_time_only'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function addApptVisitInstructionByDoctor_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($data, ["appointment_id", "visit_instruction"]) && check_user_input_values($data)) {
                $result = $this->appoinment_model->appointment_visit_instruction_insert($data);
                if ($result) {
                    $this->response_send = ["message" => $this->lang->line('visit_instruction_added'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('invalid_id'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    // doctor is calling to user send notification in case of app killing
    public function doctor_calling_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($data, ["user_id", "doctor_id"]) && check_user_input_values($data)) {
                $result = $this->appoinment_model->get_info($data);

                if ($result) {
                    $title = array(
                        'title' => 'DOCMD',
                        'body' => $result['doctor_name'] . " is calling",
                        "type" => "APPT_CALL",
                    );
                    $message = $result;
                    $r = $this->send_appointment_notification($result['user_device_token'], $message, $title);
                    $this->response_send = ["status" => $this->config->item("status_true"), "message" => $r];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_false")];
                }
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