<?php

class Dashboard_model extends CI_Model {

    protected $tbl = 'users';
    protected $fields = '';

    public function users_doctors_graph_model($id) {
        $this->fields = "MONTHNAME(created_date) AS label,COUNT(id) AS value";
        $group_by = "MONTH(created_date)";

        if ($id != null && $id == 1) {
            $this->tbl = "doctors";
        } elseif ($id == 2) {
            $this->fields = "SUM(provider_plan.amount) AS value,
  MONTHNAME(appointment.created_date) AS label";
            $this->tbl = "appointment";
        } elseif ($id == 3) {
            $this->tbl = "appointment";
        } elseif ($id == 4) {
            $this->tbl = "appointment";
            $this->db->where("status IN(2,3)");
        } elseif ($id == 5) {
            // completed
            $this->tbl = "appointment";
            $this->db->where("status IN(1,4,5,6)");
            $this->db->where("patient_availability_date_and_time <=", $this->config->item("appointment_date"));
        } elseif ($id == 6) {
            // upcomimg
            $this->tbl = "appointment";
            $this->db->where("status IN(1,4,5)");
            $this->db->where("patient_availability_date_and_time >=", $this->config->item("appointment_date"));
        }
        $this->db->select($this->fields);
        $this->db->from($this->tbl);
        if ($id == 2) {
            $this->db->join("provider_plan", "provider_plan.id=appointment.payment_method_id", "INNER");
        }
        $this->db->group_by($group_by);
        $query = $this->db->get();
//        if($id == 6){
//        echo $this->db->last_query();die;}
        return (!empty($query->row_array())) ? $query->result_array() : array();
    }
    public function get_statistic_view_model() {
        $sql = "SELECT COUNT(id) AS doctors ,(SELECT COUNT(id) FROM `users`) AS users ,(SELECT SUM(provider_plan.amount) FROM appointment INNER JOIN provider_plan ON provider_plan.id=appointment.treatment_provider_plan_id) AS earning,(SELECT COUNT(id) FROM `appointment` ) AS total_appointment,(SELECT COUNT(id) FROM `appointment` WHERE patient_availability_date_and_time >='".$this->config->item('appointment_date')."' AND status IN(1,4,5)) AS upcoming_appointment,(SELECT COUNT(id) FROM `appointment` WHERE  status IN(2,3)) AS cancel_appointment,(SELECT COUNT(id) FROM `appointment` WHERE patient_availability_date_and_time <='".$this->config->item('appointment_date')."' AND status IN(1,4,5,6)) AS past_appointment FROM `doctors`";
        $query = $this->db->query($sql);
  
        return ($query->num_rows()>0)?$query->row_array():false;
    }
}

?>