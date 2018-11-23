<?php

/*
  class name : Payment model
 */

class Payment_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * This function is used to insert user stripe customer id in db 
     * @param $data array required
     *
     * @return true
     */
    public function insertCustomer($data) {

        return $this->db->insert("stripe_customers", $data);
    }

    /**
     * This function is used to insert and update user stripe card into db 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function insertStripePaymentMethod($data) {
        $already_insert = $this->checkCardAlreadyInsert($data);
        if (count($already_insert) > 0) {
            return $this->updateData($data, $already_insert->id);
        } else {
            return $this->inserData($data);
        }
    }

    /**
     * This function is used to insert and update user paypal method into db 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function insertPaypalPaymentMethod($data) {
        $already_insert = $this->checkPaypalEmailAlreadyInsert($data);
        if (count($already_insert) > 0) {
            return $this->updateData($data, $already_insert->id);
        } else {
            return $this->inserData($data);
        }
    }

    public function insertAccounts($data) {

        $this->db->insert("stripe_accounts", $data);
        return $this->db->insert_id();
    }

    public function getDoctorDetail($id) {
        $this->db->where("id",$id);
        $this->db->select("first_name,last_name,email,date_of_birth,(SELECT CONCAT(address ,'|',city,'|',state,'|',zip_code) FROM address WHERE id IN(SELECT address_id FROM `doctor_address` WHERE doctor_id='" . $id . "')) AS doctor_add");
        $this->db->from("doctors");
        $q = $this->db->get();
        return $q->row();
    }

    /**
     * This function is used to insert data into payment method  
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function inserData($data) {
        
        $this->db->insert("user_payment_methods", $data);
        $id = $this->db->insert_id();
        $q = $this->db->get_where('user_payment_methods', array('id' => $id));
        return $q->row();
    }

    /**
     * This function is used to update data into payment method  
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function updateData($data, $id) {
        $data['is_deleted'] = 0;
        $this->db->where("id", $id);
        $this->db->update('user_payment_methods', $data);
        $q = $this->db->get_where('user_payment_methods', array('id' => $id));
        return $q->row();
    }

    /**
     * This function is used to check card already inserted or not 
     * @param $data array required
     *
     * @return  object
     */
    public function checkCardAlreadyInsert($data) {

        $this->db->where(["user_id" => $data['user_id'], 'card_number' => $data['card_number']]);
        return $this->db->select("id")->from("user_payment_methods")->get()->row();
    }

    /**
     * This function is used to check paypal email already inserted or not 
     * @param $data array required
     *
     * @return  object
     */
    public function checkPaypalEmailAlreadyInsert($data) {

        return $query = $this->db->where(["user_id" => $data['user_id'], 'paypal_email' => $data['paypal_email']])->select("id")->from("user_payment_methods")->get()->row();
    }

    /**
     * This function is used to check customer  already inserted or not 
     * @param $data array required
     *
     * @return  object
     */
    public function checkAlreadyCustomer($user_id) {

        return $query = $this->db->where(["user_id" => $user_id])->select("*")->from("stripe_customers")->get()->row();
    }

    /**
     * This function is used to insert user stripe customer id 
     * @param $data array required
     *
     * @return  object
     */
    public function inserUserStripeCustomerId($data) {
        $this->db->insert("stripe_customers", $data);
        $id = $this->db->insert_id();
        $q = $this->db->get_where('user_payment_methods', array('id' => $id));
        return $q->row();
    }

    /**
     * This function is use to get saved card by user id and card number 
     * @param $card_number integer required
     * @param $user_id integer required
     *
     * @return  object
     */
    public function getSavedCardByCardNumber($card_number, $user_id) {
        return $query = $this->db->where(["card_number" => $card_number, 'user_id' => $user_id])->select("*")->from("user_payment_methods")->get()->row();
    }

    /**
     * This function is use to get user all payment methods 
     * @param $user_id integer
     * @param $type string required
     *
     * @return  array
     */
    public function getAllMethodByType($user_id, $type) {
        return $query = $this->db->where(["payment_method_type" => $type, 'user_id' => $user_id])->select("*")->from("user_payment_methods")->get()->result_array();
    }

    /**
     * This function is used to get stripe customer id
     * @param $data array required
     *
     * @return  object
     */
    public function getCustomerByUserId($user_id) {

        return $query = $this->db->where(["user_id" => $user_id])->select("*")->from("stripe_customers")->get()->row();
    }

    /**
     * This function is use to get payment methods info by id and user id
     * @param $user_id integer required
     * @param $id integer required
     *
     * @return  array
     */
    public function getPaymentMethodByIdUserId($id, $user_id) {
        return $query = $this->db->where(["id" => $id, 'user_id' => $user_id])->select("*")->from("user_payment_methods")->get()->row();
    }

    /**
     * This function is used to insert transaction info 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function inserTransctionInfo($data) {
        $this->db->insert("user_transactions", $data);
        $id = $this->db->insert_id();
        $q = $this->db->get_where('user_transactions', array('id' => $id));
        return $q->row();
    }

    /** --------- doctor function ------------------------------* */

    /**
     * This function is used to insert and update doctor paypal method into db 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function insertDoctorPaypalPaymentMethod($data) {
        $already_insert = $this->checkDoctorPaypalEmailAlreadyInsert($data);
        if (count($already_insert) > 0) {
            return $this->updateDoctorData($data, $already_insert->id);
        } else {
            return $this->inserDoctorData($data);
        }
    }

    /**
     * This function is used to insert and update user bank account into db 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function insertBankAccountPaymentMethod($data) {
        $already_insert = $this->checkBankAccountAlreadyInsert($data);
        if (count($already_insert) > 0) {
            return $this->updateDoctorData($data, $already_insert->id);
        } else {
            return $this->inserDoctorData($data);
        }
    }

    /**
     * This function is used to insert data into payment method for doctor 
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function inserDoctorData($data) {
        $this->db->insert("doctor_payment_methods", $data);
        $id = $this->db->insert_id();
        $q = $this->db->get_where('doctor_payment_methods', array('id' => $id));
        return $q->row();
    }

    /**
     * This function is used to update data into payment method  
     * @param $data array required
     *
     * @return current insert data in object
     */
    public function updateDoctorData($id,$doctor_id) {
        $this->db->where("doctor_id", $doctor_id);
        $this->db->update('doctor_payment_methods', ["take_payment_status"=>"no"]);   
        #------------------------------------------
        $this->db->where("id", $id);
        $this->db->update('doctor_payment_methods', ["take_payment_status"=>"yes"]);
        $q = $this->db->get_where('doctor_payment_methods', array('id' => $id));
        return $q->row();
    }

    /**
     * This function is used to check bank account  already inserted or not for 
     * doctor
     * @param $data array required
     *
     * @return  object
     */
    public function checkBankAccountAlreadyInsert($data) {

        $this->db->where(["doctor_id" => $data['doctor_id'], 'bank_account_number' => $data['bank_account_number']]);
        return $this->db->select("id")->from("doctor_payment_methods")->get()->row();
    }

    /**
     * This function is used to check paypal email already inserted or not 
     * in doctor_payment_methods for doctor 
     * @param $data array required
     *
     * @return  object
     */
    public function checkDoctorPaypalEmailAlreadyInsert($data) {

        return $query = $this->db->where(["doctor_id" => $data['doctor_id'], 'paypal_email' => $data['paypal_email']])->select("id")->from("doctor_payment_methods")->get()->row();
    }

    /**
     * This function is use to get doctor all payment methods 
     * @param $doctor_id integer
     * @param $type string required
     *
     * @return  array
     */
    public function getAllDoctorMethodByType($doctor_id, $type) {
        return $query = $this->db->where(["payment_method_type" => $type, 'doctor_id' => $doctor_id])->select("*")->from("doctor_payment_methods")->get()->result_array();
    }

    /**
     * This function is used to un set last avtive method
     * @paran $doctor_id integer
     *
     * @return true
     */
    public function unsetLastActiveStatus($id) {
        $data['take_payment_status'] = 'no';
        $this->db->where("id", $id['doctor_id']);
        return $this->db->update('doctor_payment_methods', $data);
    }

    /**
     * This function is use to get payment methods info by id and doctor id
     * @param $doctor_id integer required
     * @param $id integer required
     *
     * @return  array
     */
    public function insertUploadedDocument($data) {

        $this->db->insert("stripe_upload_document", $data);
        return $insert_id = $this->db->insert_id();
    }

    public function getDoctorMethodByIdUserId($id) {
       return  $this->db->where(["id" => $id])->select("*")->from("doctor_payment_methods")->get()->row();
        
    }

    public function getUserCardDetail($id) {
        return $query = $this->db->where(["user_id" => $id['user_id'],"is_deleted"=>0])->select("*")->from("user_payment_methods")->get()->result_array();
    }

    public function getDoctorCardDetail($id) {
        return $query = $this->db->where(["doctor_id" => $id['doctor_id'],"is_deleted"=>0])->select("id,bank_account_number AS account_number,take_payment_status AS is_selected,paypal_email")->from("doctor_payment_methods")->get()->result_array();
    }
    public function getDoctorAccountid($id) {
        return $query = $this->db->where(["doctor_id" => $id['doctor_id'],"id"=>$id['payment_method_id']])->select("id,stripe_account_id")->from("stripe_accounts")->get()->row_array();
    }
   function deletedoctorAccount($id) {
        $this->db->where(["doctor_id" => $id['doctor_id'],"id"=>$id['payment_method_id']]);
        $this->db->update("doctor_payment_methods",["is_deleted"=>1]);
    }
    public function getUsecardId($data) {

        $this->db->where(["user_id" => $data['user_id'], "payment_method_type" => "stripe", "id" => $data['payment_method_id']]);
        $this->db->select("user_id,(SELECT stripe_customer_id FROM `stripe_customers` where user_id = '" . $data['user_id'] . "') AS stripe_customer_id,stripe_card_id");
        $this->db->from("user_payment_methods");
        $q = $this->db->get();
        return $q->row_array();
    }

    public function deleteusercard($data) {
        $this->db->where(["user_id" => $data['user_id'], "payment_method_type" => "stripe", "id" => $data['payment_method_id']]);
        $this->db->update("user_payment_methods",["is_deleted"=>1]);
        //echo $this->db->last_query();die;
    }
    public function insertDoctorPaymentMethod($data) {
        $this->db->insert("doctor_payment_methods", $data);
    }
    public function getCardAndCustomerId($data) {
        $this->db->where(['id' => $data['id'], "user_id" => $data['user_id']]);
        $this->db->select("id,exp_month,exp_year,card_name,stripe_card_id,(SELECT stripe_customer_id FROM stripe_customers WHERE user_id='" . $data['user_id'] . "') AS cust_id")->from("user_payment_methods");
        $query = $this->db->get();
        return $query->row_array();
    }

    public function updateCardDetail($data) {
        $this->db->where(["id" => $data['id']]);
        $this->db->update("user_payment_methods", $data);
    }
}

?>