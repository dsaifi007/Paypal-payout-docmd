<?php

class Registered_doctor_model extends CI_Model {

    var $order = array('doctors.id' => 'desc'); // default order
    protected $patient_table = 'patient_info';
    protected $doctor_table = "doctors";
    protected $doc_profes_info = "doc_professional_info";
    protected $user_patient = "user_patient";
    protected $address_map = "doctor_address";
    protected $address = "address";
    protected $specility_table = "spacility";
    protected $emails = [];
    protected $doctor_pending_status = "pending";
    protected $doctor_accept_status = "accept";
    protected $doctor_reject_status = "reject";

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
        $final_filter =array();
        // This if condition is work when external filtering is in used  
        if ($post['external_filtering'] != '') {
            
            $filter_data = (array) json_decode($post['external_filtering']);
           
            foreach ($filter_data as $key => $value) {
                $data[] = str_replace("_", ".", $key);
            }
            $final_filter = array_combine($data, $filter_data);
          
            if(isset($final_filter['is.loggedin'])){
                
                $this->db->where(["doctors.is_loggedin"=>($final_filter['is.loggedin']=='1')?'1':'0']);
                unset($final_filter['is.loggedin']);
            }
            
            $this->db->where($final_filter);
        }

        $this->db->select(
                "doctors.id,
                doctors.med_id,
                doctors.is_loggedin,
                language.name,
                doctors.first_name,doctors.last_name,
                doctors.email,
                doctors.phone,
                doctors.gender,
                doctors.date_of_birth,
                
                doctors.is_blocked"
        );
        $this->db->from("$this->doctor_table as doctors");
        $this->db->join("language","language.language_id=doctors.language_id","INNER");
        $this->db->where("status", $this->doctor_accept_status);
        //$this->db->get();
        //echo  $this->db->last_query();die;

        /*
          -------------------------------------------------------------------------------------
          |       when Filtering is on
          -------------------------------------------------------------------------------------
         */
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $this->db->join("$this->address_map as address_map", "address_map.doctor_id = doctors.id", "LEFT");
            $this->db->join("$this->address as address", "address.id = address_map.address_id", "LEFT");
            $this->db->join("$this->doc_profes_info as doc_profes_info", "doc_profes_info.doctor_id = doctors.id", "LEFT");
            $this->db->join("$this->specility_table as splct", "splct.id = doc_profes_info.speciality_id", "LEFT");
            //$this->db->get();
        }
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
        $this->db->from($this->doctor_table);
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

    // get all email 
    public function get_all_emails($user_id = null) {
        if ($user_id != null) {
            $this->db->where_in("id", $user_id);
        }
        $this->db->select("email")->from($this->doctor_table);
        $query = $this->db->get();
        foreach ($query->result_array() as $key => $value) {
            $this->emails[] = trim($value['email']);
        }
        return $this->emails;
    }

   // Get individual doctor data
    public function get_doctor_info($id) {
        $this->db->where("doctor.id", $id);
        //$this->db->where(["doctor_avg_rating.doctor_id" => $id]);
        $this->db->select(
                "doctor.id,
                CONCAT(doctor.first_name,' ',doctor.last_name) AS name,
                doctor.gender,
                doctor.med_id,
                doctor.is_loggedin,
                doctor.commission,
                doctor.date_of_birth,
                doctor.email,
                doctor.phone,
                doctor.profile_url,
                doctor.is_blocked,
                address.address,
                address.city,
                address.state,
                address.zip_code,
                doc_profes_info.undergraduate_university,
                doc_profes_info.medical_school,
                doc_profes_info.residency,
                doc_profes_info.medical_license_number,
                doctor_avg_rating.avg_rating,
                doctor.mal_practice_information,
                (SELECT COUNT(doctor_id) FROM `reject_on_call_appointment_by_doctor` GROUP BY doctor_id HAVING doctor_id='" . $id . "') AS total_decline_appointments,
                (SELECT COUNT(doctor_id) FROM `appointment` WHERE treatment_provider_plan_id =4 AND doctor_id='".$id."') AS accepted_on_call_appointment,
                (SELECT `name` FROM `language` WHERE language_id = (SELECT language_id FROM doctors WHERE id='" . $id . "') LIMIT 1) AS language,
                (SELECT GROUP_CONCAT(degree_id) FROM doctor_degree_mapping WHERE doctor_id='" . $id . "') AS degree_id,
                (SELECT GROUP_CONCAT(spacility_id)FROM doctor_speciality WHERE doctor_id='" . $id . "') AS spacility_id
                ");
        $this->db->from("$this->doctor_table as doctor");
        $this->db->join("$this->doc_profes_info as doc_profes_info", "doc_profes_info.doctor_id = doctor.id", "LEFT");
        $this->db->join("$this->address_map as address_mapping", "address_mapping.doctor_id = doctor.id", "LEFT");
        $this->db->join("$this->address as address", "address.id = address_mapping.address_id", "LEFT");
        $this->db->join("doctor_avg_rating", "doctor_avg_rating.doctor_id = doctor.id", "LEFT");

        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->row_array();
        //echo $this->db->last_query();die;
    }


    //update_dcotor_pending_status
    public function update_doctor_status($id) {
        $this->db->where("id", $id);
        $this->db->update($this->doctor_table, ["status" => $this->doctor_accept_status]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function edit_doctor_info_model($data, $id) {
        $specaility = array();
        $degree = array();
        
        $this->db->delete('doctor_speciality',["doctor_id"=>$id]);
        $this->db->delete('doctor_degree_mapping',["doctor_id"=>$id]);
       
        if(!empty($data['speciality_id'])){
            foreach ($data['speciality_id'] as $k => $v) {
               $specaility[]= ["doctor_id"=>$id,"spacility_id"=>$v];  
            }
            $this->db->insert_batch("doctor_speciality",$specaility);
        }
        if(!empty($data['degree_id'])){
            foreach ($data['degree_id'] as $key => $deg) {
               $degree[]= ["doctor_id"=>$id,"degree_id"=>$deg];  
            }
            $this->db->insert_batch("doctor_degree_mapping",$degree);
        }    

        // update commission
        $this->db->where("id", $id);
        $this->db->update($this->doctor_table, ["commission" => $data['commission']]);

        // update commission
        $this->db->where("doctor_id", $id);
        $this->db->update("doctor_avg_rating", ["avg_rating" => $data['rating']]);
        return true;
    }

    // block/unblock of the users based on id
    public function update_doctor_status_model($doctor_data) {
        $this->db->where("id", trim($doctor_data['doctor_id']));
        $this->db->update($this->doctor_table, ["is_blocked" => trim($doctor_data['status'])]);
        return true;
    }

    //get all state
    public function get_all_state() {
        $query = $this->db->select("state_code AS state")->from("state_list")->get();
        return $query->result_array();
    }

    //get all city
    public function get_all_city() {
        $query = $this->db->select("DISTINCT(city) AS city")->from($this->address)->get();
        return $query->result_array();
    }

    // get all get_all_specilities
    public function get_all_specilities() {
        $query = $this->db->select("id,name")->from($this->specility_table)->get();
        return $query->result_array();
    }

    public function get_all_degree() {
        $query = $this->db->select("id,degree")->from("doctor_degree")->get();
        return $query->result_array();
    }

    public function get_all_speciality() {
        $query = $this->db->select("id,name")->from("spacility")->get();
        return $query->result_array();
    }

    public function doctor_appointment($id) {
        $sql = "SELECT
  DISTINCT(doctor_id),
(
SELECT
  SUM(provider_plan.amount)
FROM
  appointment
INNER JOIN
  provider_plan ON provider_plan.id = appointment.payment_method_id
    WHERE appointment.doctor_id='" . $id . "'
) AS earning,
(
SELECT
  COUNT(id)
FROM
  `appointment`
     WHERE appointment.doctor_id='" . $id . "'
) AS total_appointment,
(
SELECT
  COUNT(id)
FROM
  `appointment`
WHERE
  patient_availability_date_and_time >= '" . $this->config->item("date") . "' AND STATUS IN(1,
  4,
  5)  AND appointment.doctor_id='" . $id . "'
) AS upcoming_appointment,
(
SELECT
  COUNT(id)
FROM
  `appointment`
WHERE STATUS
  IN(2,
  3)  AND appointment.doctor_id='" . $id . "'
) AS cancel_appointment,
(
SELECT
  COUNT(id)
FROM
  `appointment`
WHERE
  patient_availability_date_and_time <= '" . $this->config->item("date") . "'  AND STATUS IN(1,
  4,
  5,
  6) AND   appointment.doctor_id='" . $id . "'
) AS past_appointment
FROM
  `appointment` WHERE doctor_id ='" . $id . "'";
        $query = $this->db->query($sql);

        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

}

?>