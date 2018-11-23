<?php

class Doctor_payment_model extends CI_Model {

    var $order = array('appointment.id' => 'desc'); // default order
    protected $patient_table = 'patient_info';
    protected $user_table = "users";
    protected $user_patient = "user_patient";
    protected $address_map = "patient_address";
    protected $address = "address";
    protected $emails = [];
    protected $pay_status = 0;

    function __construct() {
        parent::__Construct();
    }

    function get_order_list($post) {

        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->result();
    }

    function _get_order_list_query($post) {

        $this->db->where(["appointment.status" => "6", "appointment.is_payment_released" => 0, "doctor_payment_methods.take_payment_status" => "yes"]);
        $this->db->group_by("appointment.doctor_id");
        $this->db->select(
                "appointment.id,doctors.id AS doctor_id,
                    doctors.med_id,
                      doctors.first_name,
                      doctors.last_name,
                      doctors.payment_option,
                    GROUP_CONCAT(appointment.id) AS appointment_ids,
                    doctors.commission,
                    SUM(provider_plan.amount) AS due_amount,
                    COUNT(appointment.id) AS completed_appointment,
                    GROUP_CONCAT(appointment.status) AS appointment_status,
                    GROUP_CONCAT(
                      appointment.is_payment_released
                    ) AS payment_released,
                    doctor_payment_methods.payment_method_type,
                    doctor_payment_methods.paypal_email,
                    doctor_payment_methods.take_payment_status,
                    stripe_accounts.stripe_account_id,
                    appointment.status"
        );
        $this->db->from("appointment");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $this->db->join("doctor_payment_methods", "doctor_payment_methods.doctor_id = doctors.id", "INNER");
        $this->db->join("stripe_accounts", "stripe_accounts.id = doctor_payment_methods.stripe_account_table_id", "LEFT");

        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }


        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all($post) {
        $this->_count_all_bb_order($post);
        $query = $this->db->count_all_results();

        return $query;
    }

    public function _count_all_bb_order($post) {
        //print_r($post);die;
        $this->db->from('appointment');
        //$this->db->where($post['where']);
        //foreach ($post['where_in'] as $index => $value){
        //$this->db->where_in($index, $value);
        //}
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_insurance_action_status_model($data) {
        $this->db->Where("id", $data['appointment_id']);
        $this->db->update("appointment", ["insurance_status" => $data['status']]);
        #get user device token
        return $this->db->select("device_token ,(SELECT CONCAT(first_name,' ',last_name) as n FROM patient_info WHERE id ='" . $data['patient_id'] . "' ) AS name")->from("users")->where("id", $data['user_id'])->get()->row_array();
        //return true;
    }

    public function getAppintmentFees($id, $status) {
        $this->db->where(["appointment.id" => $id, "appointment.status" => $status]);
        $this->db->select("appointment.id,appointment.amount,user_transactions.transaction_id,user_transactions.charge_id,user_transactions.payment_type")->from("appointment");
        $this->db->join("user_transactions", "user_transactions.appointment_id=appointment.id", "INNER");
        $q = $this->db->get();
        $row = $q->row_array();
        return $row;
    }

    public function update_payment_status($id) {
        $this->db->Where("appointment_id", $id['id']);
        $this->db->update("user_transactions", ["transaction_status" => "Refund"]);
    }

    public function getDoctorTotalPayment() {
        $this->db->limit(20);
        $this->db->where(["appointment.status" => 6, "appointment.is_payment_released" => 0, "doctor_payment_methods.take_payment_status" => "yes"]);
        $this->db->group_by("appointment.doctor_id");
        $this->db->select(
                "appointment.id,doctors.id AS doctor_id,
                    doctors.med_id,
                      doctors.first_name,
                      doctors.last_name,
                      doctors.payment_option,
                    GROUP_CONCAT(appointment.id) AS appointment_ids,
                    doctors.commission,
                    SUM(provider_plan.amount) AS due_amount,
                    COUNT(appointment.id) AS completed_appointment,
                    GROUP_CONCAT(appointment.status) AS appointment_status,
                    GROUP_CONCAT(
                      appointment.is_payment_released
                    ) AS payment_released,
                    doctor_payment_methods.payment_method_type,
                    doctor_payment_methods.paypal_email,
                    doctor_payment_methods.take_payment_status,
                    stripe_accounts.stripe_account_id,
                    appointment.status,doctor_last_payment.payment_date"
        );
        $this->db->from("appointment");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $this->db->join("doctor_last_payment", "doctor_last_payment.doctor_id = doctors.id", "LEFT");
        $this->db->join("doctor_payment_methods", "doctor_payment_methods.doctor_id = doctors.id", "INNER");
        $this->db->join("stripe_accounts", "stripe_accounts.id = doctor_payment_methods.stripe_account_table_id", "LEFT");
        $q = $this->db->get();
        //echo $this->db->last_query();die;
        return $q->result_array();
    }

    public function getpaySchedule() {
        $q = $this->db->get("payment_schedule");
        $row = $q->result_array();
        return $row;
    }

    public function pay_status_update($data) {
        $this->db->where("id", $data['doctor_id']);
        $this->db->update("doctors", ["payment_option" => $data['payment_option']]);

        $r = $this->db->insert("doctor_last_payment", ["payment_date" => date("Y-m-d H:i:s"), "doctor_id" => $data['doctor_id']]);
        if (!$r) {
            $this->db->where("doctor_id", $data['doctor_id']);
            $this->db->update("doctor_last_payment", ["payment_date" => date("Y-m-d H:i:s")]);
        }
    }

    public function update_doctor_payment_status($doctor_id, $appt_id, $response) {
        $this->db->trans_start(); # Starting Transaction
        #update the appointment status
        $this->db->where("id IN($appt_id)");
        $this->db->where("doctor_id", $doctor_id);
        $this->db->update("appointment", ["is_payment_released" => 1]); // 1-> payment has been released
        # update doctor transcation      
        $input_data = [
            "doctor_id" => $doctor_id,
            "transcation_id" => $response->id,
            "amount" => ($response->amount / 100),
            "payment_date" => date("Y-m-d H:i:s"),
            "payment_status" => "success",
            "payment_json" => json_encode($response),
            "payment_method_type" => "stripe"
        ];
        $this->db->insert("doctor_payment_history", $input_data);
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function update_payment_failed($id, $msg, $pay_method = "stripe") {
        $this->db->insert("doctor_payment_failed_histroy", ["doctor_id" => $id, "message" => $msg, "payment_method_type" => ($pay_method == "stripe") ? "stripe" : "paypal"]);
    }

    public function update_doctor_last_payment_date($doctor_id, $day = 14) {
        if ($day == 1) {
            $date = date("Y-m-d H:i:s", strtotime("+1 day"));
        } elseif ($day == 7) {
            $date = date("Y-m-d H:i:s", strtotime("+7 day"));
        } elseif ($day == 14) {
            $date = date("Y-m-d H:i:s", strtotime("+14 day"));
        } elseif ($day == 30) {
            $date = date("Y-m-d H:i:s", strtotime("+30 day"));
        }
        $this->db->where("doctor_id", $doctor_id);
        $this->db->update("doctor_last_payment", ["payment_date" => $date]);
    }

    public function update_one_time_payment_option($doctor_id) {
        $this->db->where("id", $doctor_id);
        $this->db->update("doctors", ["payment_option" => NULL]);
    }

    public function update_doctor_paypal_payment_status($doctor_id, $appt_id, $response,$payment) {
        $this->db->trans_start(); # Starting Transaction
        #update the appointment status
        $this->db->where("id IN($appt_id)");
        $this->db->where("doctor_id", $doctor_id);
        $this->db->update("appointment", ["is_payment_released" => 1]); // 1-> payment has been released
        # update doctor transcation      
        $input_data = [
            "doctor_id" => $doctor_id,
            "transcation_id" => $response->batch_header->payout_batch_id,
            "amount" => $payment,
            "payment_date" => date("Y-m-d H:i:s"),
            "payment_status" => "success",
            "payment_json" => json_encode($response),
            "payment_method_type" => "stripe"
        ];
        $this->db->insert("doctor_payment_history", $input_data);
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            # Everything is Perfect. 
            # Committing data to the database.
            $this->db->trans_commit();
            return TRUE;
        }
    }

}

?>
