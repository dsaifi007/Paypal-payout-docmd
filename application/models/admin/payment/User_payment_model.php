<?php

class User_payment_model extends CI_Model {

    var $order = array('appointment.id' => 'desc'); // default order
    protected $patient_table = 'patient_info';
    protected $user_table = "users";
    protected $user_patient = "user_patient";
    protected $address_map = "patient_address";
    protected $address = "address";
    protected $emails = [];

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

    function _get_order_list_query() {
       
        $this->db->select(
                "appointment.id,patient_info.med_id,
                CONCAT(
                     patient_info.first_name,
                     ' ',
                     patient_info.last_name
                 ) AS username,
                 CONCAT(
                     doctors.first_name,
                     ' ',
                     doctors.last_name
                 ) AS provider_name,
                 provider_plan.title,
                 appointment.patient_availability_date,
                 appointment.patient_availability_time,
                 appointment.amount,
                 user_payment_methods.payment_method_type,
                 appointment.status AS apt_status,user_transactions.payment_type,
                 appointment_status.status,user_transactions.transaction_status"
        );
        $this->db->from("appointment");
        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "INNER");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $this->db->join("provider_plan", "appointment.treatment_provider_plan_id = provider_plan.id", "INNER");
        $this->db->join("user_payment_methods", "user_payment_methods.id = appointment.payment_method_id", "LEFT");
        $this->db->join("appointment_status", "appointment_status.id = appointment.status", "INNER");
        $this->db->join("user_transactions", "user_transactions.appointment_id = appointment.id", "INNER");
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
        $this->db->from('patient_info');
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
        //echo $this->db->last_query();die;
        $row = $q->row_array();
        return $row;
    }
    public function update_payment_status($id) {
        $this->db->Where("appointment_id", $id['id']);
        $this->db->update("user_transactions", ["transaction_status" => "Refund"]);
    }
}

?>
