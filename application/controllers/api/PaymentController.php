<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class PaymentController extends REST_Controller {

    protected $headers;
    protected $language_file = ["api_message", "spn_api_message"];
    private $response_send = ["status" => false, "message" => "Bad response 401."];
    private $stripe_payment_method_keys = ['payment_method_type', 'card_number', 'card_name', 'exp_month', 'exp_year', 'cvc'];
    private $bank_payment_method_keys = ['payment_method_type', 'bank_name', 'bank_account_name', 'bank_account_number', 'bank_short_code'];
    private $paypal_payment_method_keys = ['payment_method_type', 'paypal_email'];
    private $return_result_key = ['result_key'];
    private $payment_required_keys = ['payment_method_id', 'amount', 'appointment_id'];
    private $takepayment_status_keys = ['payment_method_id', 'doctor_id'];

    public function __construct() {

        try {
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            change_languge($this->headers, $this->language_file);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |-----------------------------------------------------------------------------------------
      | This Function is used for add payment method for users

     * @param none
     * @return json 
      |-----------------------------------------------------------------------------------------
     */

      public function addPaymentMethod_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $user_info = getUserInfoByToken($this->headers['Authorization']);
            if (count($user_info) > 0) {
                $user_id = $user_info->id;
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                return $this->response($this->response_send);
            }

            $this->post_data = json_decode(file_get_contents('php://input'), true);
            if ($this->post_data['payment_method_type'] == 'paypal') {

                if (check_form_array_keys_existance($this->post_data, $this->paypal_payment_method_keys) != false && check_user_input_values($this->post_data)) {
                    $result = $this->_savedPaypalMethod($user_id, $this->post_data);
                    $result_key = (isset($this->post_data['result_key'])) ? $this->post_data['result_key'] : 'data';
                    $this->response_send = ["message" => $this->lang->line('payment_method_save'), "status" => $this->config->item("status_true"), $result_key => $result];
                } else {
                    $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                }
            } else {

                if (check_form_array_keys_existance($this->post_data, $this->stripe_payment_method_keys) != false && check_user_input_values($this->post_data)) {

                    $this->_savedCardMethod($user_info, $this->post_data);
                } else {
                    $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                }
            }
            return $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }

    /**
     * this is private function to save user cards
     * @param $user_info object required
     * @param $data array required
     *
     * @return json
     */
    Private function _savedCardMethod($user_info, $data) {
        $this->load->model('api/Payment_model');
        $data['user_id'] = $user_info->id;
        require_once APPPATH . "third_party/stripe/stripe-php/init.php";
        $stripe_sk = $this->config->item("stripe_sk");
        $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
        try {
            $token = \Stripe\Token::create([
                'card' => [
                    'number' => $data['card_number'],
                    'exp_month' => $data['exp_month'],
                    'exp_year' => $data['exp_year'],
                    'cvc' => $data['cvc'],
                ],
            ]);
            $card = $this->createStripeCustomer($user_info, $token->id);
            //dd($card);
            if ($card['status'] == "false") {
                $card['status'] = false;
                return $this->response($card);
            }
            return $result = $this->_savesavedCardWithOutCharge($card, $user_info, $data);
        } catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();

            $err = $body['error'];
            $this->response_send = ["message" => $err['message'], "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }
    public function updateUserCard_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $this->post_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->post_data, ['id', 'user_id', 'card_name', 'exp_month', "exp_year"]) != false && check_user_input_values($this->post_data)) {
                $this->load->model('api/Payment_model');
                $result = $this->Payment_model->getCardAndCustomerId($this->post_data);
                if (!empty($result)) {
                    $this->_updateUserCardDetail($result, $this->post_data);
                    $this->response_send = ["status" => $this->config->item("status_true")];
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

    private function _updateUserCardDetail($card_detail, $update_detail) {
        try {
            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe_sk = $this->config->item("stripe_sk");
            $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
            $customer = \Stripe\Customer::retrieve($card_detail['cust_id']);
            $card = $customer->sources->retrieve($card_detail['stripe_card_id']);
            $card->name = $update_detail['card_name'];
            $card->exp_month = $update_detail['exp_month'];
            $card->exp_year = $update_detail['exp_year'];
            $response = $card->save();
            $this->Payment_model->updateCardDetail($this->post_data);
        } catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();

            $err = $body['error'];
            $this->response_send = ["message" => $err['message'], "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }
    /**
     * this is private function to save paypal method
     * @param $user_id integer required
     * @param $data array required
     *
     * @return json
     */
    Private function _savedPaypalMethod($user_id, $data) {
        $this->load->model('api/Payment_model');
        $data['user_id'] = $user_id;
        return $result = $this->Payment_model->insertPaypalPaymentMethod($data);
    }

    /**
     * this is function use to save user stripe customer Id
     * @param $user_info object required
     * @param $stripetoken string required
     *
     * @return array
     */
    public function createStripeCustomer($user_info, $stripetoken) {

        $this->load->model('api/Payment_model');

        try {
            $result = $this->Payment_model->checkAlreadyCustomer($user_info->id);
            $count_customer = $this->Payment_model->checkAlreadyCustomer($user_info->id);
            if (count($count_customer) == 0) {
                // Create a Customer:
                $customer = \Stripe\Customer::create([
                    'source' => $stripetoken,
                    'email' => $user_info->email,
                ]);
                $currentDate = time(); // get current date
                $current_timestamp = date("Y-m-d H:i:s", $currentDate);
                $stripe_customer_data['user_id'] = $user_info->id;
                $stripe_customer_data['stripe_customer_id'] = $customer->id;
                $stripe_customer_data['created_at'] = $current_timestamp;
                $stripe_customer_data['updated_at'] = $current_timestamp;
                $this->Payment_model->inserUserStripeCustomerId($stripe_customer_data);
                return $customer->sources->data[0];
            } else {
                $stripe = \Stripe\Customer::retrieve($count_customer->stripe_customer_id);
                return $stripe->sources->create(array("source" => $stripetoken));
            }
        } catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err = $body['error'];
            return $result = ["message" => $err['message'], "status" => $this->config->item("status_false")];
        }
    }

    /**
     * This Private  function use to insert card info into db return from stripe 
     * @param $user_info object required
     * @param $card object required
     * @param $data array required
     *
     * @return json
     */
    private function _savesavedCardWithOutCharge($card, $user_info, $data) {

        $this->load->model('api/Payment_model');
        $user_id = $user_info->id;
        $check_card_allready = $this->Payment_model->getSavedCardByCardNumber($card->last4, $user_id);
        $card_data['user_id'] = $user_id;
        $card_data['stripe_card_id'] = $card->id;
        $card_data['card_number'] = substr($data['card_number'], -4);//$card->last4;
        $card_data['card_name'] = $data['card_name'];
        $card_data['brand'] = $card->brand;
        $card_data['exp_month'] = $card->exp_month;
        $card_data['exp_year'] = $card->exp_year;

        $result = $this->Payment_model->insertStripePaymentMethod($card_data);
        $result_key = (isset($data['result_key'])) ? $this->post_data['result_key'] : 'data';
        $result->card_number = substr($data['card_number'], -4);
        $this->response_send = ["message" => $this->lang->line('card_saved'), "status" => $this->config->item("status_true"), $result_key => $result];
        return $this->response($this->response_send);
    }

    public function getUserAllPaymentMethods_get() {

        check_acces_token(@$this->headers['Authorization']);
        $this->load->model('api/Payment_model');
        $user_info = getUserInfoByToken($this->headers['Authorization']);
        if (count($user_info) > 0) {
            $this->post_data = $this->input->get();
            if (check_form_array_keys_existance($this->post_data, $this->return_result_key) != false && check_user_input_values($this->post_data)) {
                $user_id = $user_info->id;
                $results = [];
                $paypal_methods = $this->Payment_model->getAllMethodByType($user_id, 'paypal');
                $card_methods = $this->Payment_model->getAllMethodByType($user_id, 'stripe');
                if (count($paypal_methods) > 0) {
                    $results['paypal_methods'] = $paypal_methods;
                } else {
                    $results['paypal_methods'] = NULL;
                }
                if (count($card_methods) > 0) {
                    $results['card_methods'] = $card_methods;
                } else {
                    $results['card_methods'] = NULL;
                }

                $total_count = count($results['card_methods']) + count($results['paypal_methods']);

                if ($total_count > 0) {
                    $this->response_send = ["message" => $this->lang->line('payment_method_found'), "status" => $this->config->item("status_true"), $this->post_data['result_key'] => $results];
                } else {
                    $this->response_send = ["message" => $this->lang->line('payment_method_not_found'), "status" => $this->config->item("status_true"), $this->post_data['result_key'] => $results];
                }

                return $this->response($this->response_send);
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                return $this->response($this->response_send);
            }
        } else {
            $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }

    /**
     * This function is use pay appointment fee 
     *
     * @return json
     */
    public function payAppointmentFee_post() {

        try {
            check_acces_token(@$this->headers['Authorization']);
            $user_info = getUserInfoByToken($this->headers['Authorization']);
            if (count($user_info) > 0) {
                $user_id = $user_info->id;
            } else {
                $this->response_send = ["message" => $this->lang->line('user_id'), "status" => $this->config->item("status_false")];
                return $this->response($this->response_send);
            }

            $this->post_data = json_decode(file_get_contents('php://input'), true);

            if (check_form_array_keys_existance($this->post_data, $this->payment_required_keys) != false && check_user_input_values($this->post_data)) {

                //load payment method
                $this->load->model('api/Payment_model');

                //get payment_method card data by payment_method_id
                $payment_method_info = $this->Payment_model->getPaymentMethodByIdUserId($this->post_data['payment_method_id'], $user_id);

                if (count($payment_method_info) > 0) {

                    if ($payment_method_info->payment_method_type == 'stripe') {

                        return $this->_payoutByStripe($user_info, $payment_method_info, $this->post_data);
                    }
                    if ($payment_method_info->payment_method_type == 'paypal') {

                        return $this->payoutByPaypal();
                    }
                } else {
                    $this->response_send = ["message" => $this->lang->line('payment_method_not_found'), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err = $body['error'];
            $this->response_send = ["message" => $err['message'], "status" => $this->config->item("status_false")];
        }

        return $this->response($this->response_send);
    }

    /**
     * This function is use pay payment by stripe payment gateway 
     * @param $user_info object required
     * @param $data array required
     * @param $payment_metod_info object required 
     *
     * @return json
     */
    private function _payoutByStripe($user_info, $payment_metod_info, $data) {

        require_once APPPATH . "third_party/stripe/stripe-php/init.php";
        $stripe_sk = $this->config->item("stripe_sk");
        $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
        try {

            //key for send response
            $result_key = ($data['result_key']) ? $this->post_data['result_key'] : 'data';
            $user_id = $user_info->id;
            $this->load->model('api/Payment_model');
            //get stripe customer id
            $stripe_customer = $this->Payment_model->getCustomerByUserId($user_id);

            if (count($stripe_customer) > 0) {


                //create charge (payment) 
                $charge = \Stripe\Charge::create([
                    "amount" => 100 * $data['amount'],
                    "currency" => "usd",
                    "description" => "Appointment fixed",
                    "customer" => $stripe_customer->stripe_customer_id,
                    "source" => $payment_metod_info->stripe_card_id,
                ]);

                $transaction_data = $this->_saveTransactionInfo($user_info, $data, $charge);

                if ($charge->status == 'succeeded') {
                    $this->response_send = ["message" => $this->lang->line('transcation_succeeded'), "status" => $this->config->item("status_true"), $result_key => $transaction_data];
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
        return $this->response($this->response_send);
    }

    /**
     * This function is use to store transactioninfo 
     * @param $user_info object required
     * @param $data array required
     * @param $charge object required 
     *
     * @return object inserted
     */
    private function _saveTransactionInfo($user_info, $data, $charge) {

        $this->load->model('api/Payment_model');
        $currentDate = time(); // get current date
        $current_timestamp = date("Y-m-d H:i:s", $currentDate);
        $transaction_array = [];
        $transaction_array['user_id'] = $user_info->id;
        $transaction_array['appointment_id'] = $data['appointment_id'];
        $transaction_array['transaction_id'] = $charge->balance_transaction;
        $transaction_array['amount'] = $charge->amount;
        $transaction_array['charge_id'] = $charge->balance_transaction;
        $transaction_array['transaction_status'] = $charge->status;
        $transaction_array['created_at'] = $current_timestamp;
        //insert transaction info
        return $transaction_info = $this->Payment_model->inserTransctionInfo($transaction_array);
    }

    /*     * ----------- payment code for doctors------------------------- */

    /**
     * This function is used to add payment method for doctor
     *
     * @return json
     */
    public function addDoctorPaymentMethod_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_info = getDoctorInfoByToken($this->headers['Authorization']);
            if (count($doctor_info) > 0) {
                $doctor_id = $doctor_info->id;
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                return $this->response($this->response_send);
            }

            $this->post_data = json_decode(file_get_contents('php://input'), true);
            if ($this->post_data['payment_method_type'] == 'paypal') {

                if (check_form_array_keys_existance($this->post_data, $this->paypal_payment_method_keys) != false && check_user_input_values($this->post_data)) {
                    $result = $this->_savedDpoctorPaypalMethod($doctor_id, $this->post_data);
                    $result_key = (array_key_exists("result_key", $this->post_data)) ? $this->post_data['result_key'] : 'data';
                    $this->response_send = ["message" => $this->lang->line('payment_method_save'), "status" => $this->config->item("status_true"), $result_key => ["id"=>$result,"payment_method_type"=>"paypal"]];

                } else {
                    $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                }
            } else {

                if (check_form_array_keys_existance($this->post_data, $this->bank_payment_method_keys) != false && check_user_input_values($this->post_data)) {

                    $result = $this->_savedBankMethod($doctor_info, $this->post_data);
                    $result_key = (array_key_exists("result_key", $this->post_data)) ? $this->post_data['result_key'] : 'data';
                    $this->response_send = ["message" => $this->lang->line('payment_method_save'), "status" => $this->config->item("status_true"), $result_key => $result];
                } else {
                    $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                }
            }
            return $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }

    /**
     * this is private function to save paypal method for doctor
     * @param $doctor_id integer required
     * @param $data array required
     *
     * @return json
     */
    Private function _savedDpoctorPaypalMethod($doctor_id, $data) {
        $this->load->model('api/Payment_model');
        $paypal_data['doctor_id'] = $doctor_id;
        $paypal_data['paypal_email'] = $data['paypal_email'];
        $paypal_data['payment_method_type'] = "paypal";
        $this->db->insert("doctor_payment_methods", $paypal_data);
        return $this->db->insert_id();
        //return $result = $this->Payment_model->insertDoctorPaypalPaymentMethod($paypal_data);
    }
    /**
     * this is private function to save user cards
     * @param $user_info object required
     * @param $data array required
     *
     * @return json
     */
    Private function _savedBankMethod($doctor_info, $data) {
        $this->load->model('api/Payment_model');
        $bank_data = $data;
        $bank_data['doctor_id'] = $doctor_info->id;
        $bank_data['paypal_email'] = Null;
        return $result = $this->Payment_model->insertBankAccountPaymentMethod($bank_data);
    }

    public function getDoctorAllPaymentMethods_get() {

        check_acces_token(@$this->headers['Authorization'], null, "doctors");
        $this->load->model('api/Payment_model');
        $doctor_info = getDoctorInfoByToken($this->headers['Authorization']);
        if (count($doctor_info) > 0) {

            $this->post_data = $this->input->get();
            if (check_form_array_keys_existance($this->post_data, $this->return_result_key) != false && check_user_input_values($this->post_data)) {
                $doctor_id = $doctor_info->id;
                $results = [];
                $paypal_methods = $this->Payment_model->getAllDoctorMethodByType($doctor_id, 'paypal');
                $bank_accounts = $this->Payment_model->getAllDoctorMethodByType($doctor_id, 'bank_account');
                if (count($paypal_methods) > 0) {
                    $results['paypal_methods'] = $paypal_methods;
                } else {
                    $results['paypal_methods'] = NULL;
                }
                if (count($bank_accounts) > 0) {
                    $results['bank_methods'] = $bank_accounts;
                } else {
                    $results['bank_methods'] = NULL;
                }

                $total_count = count($results['bank_methods']) + count($results['paypal_methods']);

                if ($total_count > 0) {
                    $this->response_send = ["message" => $this->lang->line('payment_method_found'), "status" => $this->config->item("status_true"), $this->post_data['result_key'] => $results];
                } else {
                    $this->response_send = ["message" => $this->lang->line('payment_method_not_found'), "status" => $this->config->item("status_true"), $this->post_data['result_key'] => $results];
                }

                return $this->response($this->response_send);
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
                return $this->response($this->response_send);
            }
        } else {
            $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }

    /**
     * this function is use to set take payment method for doctor
     *
     * @return json
     */
    public function setTakePaymentStatus_post() {

        try {

            check_acces_token(@$this->headers['Authorization'], null, "doctors");

            $this->post_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($this->post_data, $this->takepayment_status_keys) != false && check_user_input_values($this->post_data)) {

                $result_key = (array_key_exists("result_key", $this->post_data)) ? $this->post_data['result_key'] : 'data';

                $this->load->model('api/Payment_model');
                $payment_method_id = $this->post_data['payment_method_id'];
                $doctor_id = $this->post_data['doctor_id'];
                $method_info = $this->Payment_model->getDoctorMethodByIdUserId($payment_method_id, $doctor_id);
                if (count($method_info) > 0) {
                    //$this->Payment_model->unsetLastActiveStatus($doctor_id);
                    $this->Payment_model->updateDoctorData($payment_method_id, $doctor_id);
                    $this->response_send = ["message" => $this->lang->line('Method_update_take_payment'), "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('payment_method_not_found'), "status" => $this->config->item("status_true"), $result_key => null];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }

            return $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            return $this->response($this->response_send);
        }
    }

    function getAllCardOfUser_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $id = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($id, ["user_id"]) && check_user_input_values($id)) {
                $this->load->model('api/Payment_model');
                $response = $this->Payment_model->getUserCardDetail($id);
                if (count($response) > 0) {

                    $this->response_send = ["payment_method" => $response, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_true"), "payment_method" => []];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function deleteUserCard_post() {
        try {
            check_acces_token(@$this->headers['Authorization']);
            $id = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($id, ["user_id", "payment_method_id"]) && check_user_input_values($id)) {
                $this->load->model('api/Payment_model');
                $result = $this->Payment_model->getUsecardId($id);

                if (count($result) > 0) {
                    $deleted_card = $this->_delete_user_card($result);
                    $this->Payment_model->deleteusercard($id);
                    $this->response_send = ["status" => $this->config->item("status_true")];
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

    private function _delete_user_card($card_id) {
        try {

            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe_sk = $this->config->item("stripe_sk");
            $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
            $card_list = \Stripe\Customer::retrieve($card_id['stripe_customer_id']); // ->sources->all(array('object' => 'card'));
            $response = $card_list->sources->retrieve($card_id['stripe_card_id'])->delete();
            return $response;
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }

    // create doctor account
    public function createCustomAccount_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $this->post_data = $this->input->post();
            if (check_form_array_keys_existance($this->post_data, ["doctor_id", "account_holder_name", "account_holder_type", "routing_number", "account_number"]) && count($_FILES) > 0) {
                $this->load->model('api/Payment_model');
                $doctor_data = $this->Payment_model->getDoctorDetail($this->post_data['doctor_id']);

                $result = $this->user_file_upload($_FILES);
                if (isset($result['upload_data']['full_path'])) {

                    require_once APPPATH . "third_party/stripe/stripe-php/init.php";
                    $country = 'US';
                    $stripe_sk = $this->config->item("stripe_sk");
                    $stripe = \Stripe\Stripe::setApiKey($stripe_sk);

                    $account = \Stripe\Account::create(array(
                                //"managed" => true,
                                "country" => "US", //$country,
                                "email" => $doctor_data->email,
                                "type" => 'custom'
                            ));
                    //dd($account);
                    $currentDate = time(); // get current date
                    $current_timestamp = date("Y-m-d H:i:s", $currentDate);
                    $account_array['doctor_id'] = $this->post_data['doctor_id'];
                    $account_array['stripe_account_id'] = $account->id;
                    $account_array['stripe_pK'] = $account->keys->publishable;
                    $account_array['stripe_sK'] = $account->keys->secret;
                    $account_array['stripe_response'] = json_encode($account);
                    $account_array['created_at'] = $current_timestamp;
                    $account_array['updated_at'] = $current_timestamp;
                    $account_array['account_number'] = $this->post_data['account_number'];
                    //$account_array['type'] = "doctor";
                    //$new_insert_id = $this->Payment_model->insertAccounts($account_array);

                    $document_image = $result['upload_data']['full_path'];
                    $this->_uploadDocument($account->id, $document_image, $doctor_data, $account_array);

                    $this->response_send = ["status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $result['error'], "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    private function _uploadDocument($account_id, $document_image, $doctor_data, $account_array) {
        try {
            $address = explode("|", $doctor_data->doctor_add);
            $date_of_birth = explode("-", $doctor_data->date_of_birth);
            $stripe_sk = $this->config->item("stripe_sk");
            $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
            $this->load->model('api/Payment_model');
            $uplodaed_array = [];
            $fp = fopen($document_image, 'r');
            $uplodaed = \Stripe\FileUpload::create([
                'file' => $fp,
                'purpose' => 'identity_document',
            ], array("stripe_account" => $account_id));

            $new_insert_id = $this->Payment_model->insertAccounts($account_array);
            $currentDate = time(); // get current date
            $current_timestamp = date("Y-m-d H:i:s", $currentDate);
            $uplodaed_array['account_id'] = $new_insert_id;
            $uplodaed_array['uploaded_file_id'] = $uplodaed->id;
            $uplodaed_array['filename'] = $uplodaed->filename;
            $uplodaed_array['stripe_response'] = json_encode($uplodaed);
            $uplodaed_array['purpose'] = $uplodaed->purpose;
            $uplodaed_array['created_at'] = $current_timestamp;
            $this->Payment_model->insertUploadedDocument($uplodaed_array);

            $account = \Stripe\Account::retrieve($account_id);

            $bank = array(
                "object" => "bank_account",
                "country" => "US",
                "currency" => "USD",
                "account_holder_name" => $this->post_data['account_holder_name'],
                "account_holder_type" => $this->post_data['account_holder_type'], //'individual',
                "routing_number" => $this->post_data['routing_number'], //"110000000",
                "account_number" => $this->post_data['account_number'] //"000123456789"
            );
            $account->external_accounts->create(array("external_account" => $bank));
            // Update additional owners
            $account->legal_entity->first_name = $doctor_data->first_name;
            $account->legal_entity->last_name = $doctor_data->last_name;
            $account->legal_entity->address->city = $address[1];
            $account->legal_entity->address->line1 = $address[0];
            $account->legal_entity->address->postal_code = $address[3];
            $account->legal_entity->address->state = $address[2];
            //$account->legal_entity->business_tax_id = 11111;
            //$account->legal_entity->ssn_last_4 = $this->post_data['ssn_number'];  //1111;
            //$account->legal_entity->personal_id_number = $this->post_data['pan_id'];  //"111111111";
            $account->legal_entity->verification->document = $uplodaed->id;

            $account->legal_entity->dob->day = $date_of_birth[2];
            $account->legal_entity->dob->month = $date_of_birth[1];
            $account->legal_entity->dob->year = $date_of_birth[0];

            $account->legal_entity->type = 'individual';
            $account->tos_acceptance->date = time();
            $account->tos_acceptance->ip = $_SERVER['SERVER_ADDR'];
            $account->legal_entity->business_name = "Provider Appointment";
            //$account->external_account=$bank;
            $account->save();
            $account = json_encode($account);
            $account_insert_info = array();
            $account_info = json_decode($account);
            $account_insert_info['stripe_account_table_id'] = $new_insert_id;
            $account_insert_info['doctor_id'] = $this->post_data['doctor_id'];
            $account_insert_info['payment_method_type'] = $account_info->external_accounts->data[0]->object;
            //$account_insert_info['paypal_email'] = $account_info->email;
            $account_insert_info['bank_name'] = $account_info->external_accounts->data[0]->bank_name;
            $account_insert_info['bank_account_name'] = $account_info->external_accounts->data[0]->account_holder_name;
            $account_insert_info['bank_account_number'] = $account_info->external_accounts->data[0]->last4;
            $account_insert_info['json_text'] = $account;
            $this->Payment_model->insertDoctorPaymentMethod($account_insert_info);
            $this->response(["status" => $this->config->item("status_true")]);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    private function user_file_upload($file) {
        try {
            $file_name = $file['file']['name'];
            $this->load->library("common");
            $this->load->helper('string');
            $rename_image = (random_string('numeric') + time()) . random_string();
            $img_data = $this->common->file_upload("assets/doctor/doctor_document", "file", $rename_image);
            if (isset($img_data["upload_data"]['file_name'])) {
                $file_url = base_url() . "assets/doctor/doctor_document/" . $img_data["upload_data"]['file_name'];
                return $img_data;
            } else {
                return $img_data;
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    public function deleteDoctorBankAccount_post() {
        try {
            $id = json_decode(file_get_contents('php://input'), true);
            require_once APPPATH . "third_party/stripe/stripe-php/init.php";
            $stripe_sk = $this->config->item("stripe_sk");
            $stripe = \Stripe\Stripe::setApiKey($stripe_sk);
            $this->load->model('api/Payment_model');
            $data = $this->Payment_model->getDoctorAccountid($id);
            $this->Payment_model->deletedoctorAccount($id);
            if ($data) {
                $account = \Stripe\Account::retrieve($data['stripe_account_id']);
                $account->delete();        
            } 
            $this->response(["status" => $this->config->item("status_true")]);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    function getAllCardOfDoctor_post() {
        try {
            check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $id = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($id, ["doctor_id"]) && check_user_input_values($id)) {
                $this->load->model('api/Payment_model');
                $response = $this->Payment_model->getDoctorCardDetail($id);
                if (count($response) > 0) {
                    foreach ($response as $key => $value) {
                        $response[$key]['is_selected'] = ($value['is_selected'] == "yes") ? true : false;
                    }
                    $this->response_send = ["payment_method" => $response, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["message" => $this->lang->line('no_data_found'), "status" => $this->config->item("status_true"), "payment_method" => []];
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