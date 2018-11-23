<?php

class Insurance_model extends CI_Model {

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

    function _get_order_list_query($post) {
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $filter_data = array_filter(json_decode($post['external_filtering'], true));
            if (isset($filter_data['patient_availability_date'])) {
                $filter_data['appointment.patient_availability_date'] = $filter_data['patient_availability_date'];
                unset($filter_data['patient_availability_date']);
            }
            if (isset($filter_data['title'])) {
                $filter_data['provider_plan.title'] = $filter_data['title'];
                unset($filter_data['title']);
            }
            if (isset($filter_data['status'])) {
                $filter_data['appointment.insurance_status'] = $filter_data['status'];
                unset($filter_data['status']);
            }
            $this->db->where($filter_data);
        }
        $this->db->where("appointment.insurance_status IS NOT NULL");
        $this->db->select(
                "appointment.id,
                appointment.user_id,
                 appointment.patient_id,
                all_appointment.`patient_med_id`,
                patient_info.`first_name`,
                patient_info.`last_name`,
                doctors.`first_name` AS doctor_first_name,
                doctors.`last_name` AS  doctor_last_name,
                all_appointment.`doctor`,
                all_appointment.`type`,
                DATE(all_appointment.`patient_availability_date_and_time`) AS date,
                TIME(all_appointment.`patient_availability_date_and_time`) AS time,
                provider_plan.amount,
                appointment.payment_method_id,
                patient_info.provider,
                patient_info.member_id,
                patient_info.ins_group,
                appointment.insurance_status"
        );
        $this->db->from("all_appointment");
        $this->db->join("patient_info", "patient_info.id = all_appointment.patient_id", "INNER");
        $this->db->join("doctors", "doctors.id = all_appointment.doctor_id", "LEFT");
        $this->db->join("appointment", "appointment.id = all_appointment.appointment_id", "INNER");
        $this->db->join("provider_plan", "appointment.treatment_provider_plan_id = provider_plan.id", "INNER");


        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }

        /* foreach ($post['where_in'] as $index => $value){

          $this->db->where_in($index, $value);
          } */

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

    //get all state
    public function get_filter_data() {
        $this->db->select("appointment.id,appointment.patient_availability_date AS date ,provider_plan.title")->from("appointment");
        $this->db->join("provider_plan", "provider_plan.id=appointment.treatment_provider_plan_id", "INNER");
        $query = $this->db->get();
        return $query->result_array();
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

    public function insert_notificationdata($data, $r) {
        $notification_data = json_encode(array_merge(["notification" => $data['title'], "data" => $r]));
        $r = $this->db->insert("user_on_call_notification", ["user_id" => $r['user_id'], "notification_data" => $notification_data, "fcm_response" => $data['fcm_resp']]);
        vd($r);
    }

}

?>
