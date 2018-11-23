<?php

class Appointment_detail_model extends CI_Model {

    protected $provider_plan = 'provider_plan';

    //get all state
    public function get_all_provider_plan_type() {
        $query = $this->db->select("id,title")->from($this->provider_plan)->get();
        return $query->result_array();
    }

    public function get_appointment_detail($appointment_id) {

        $this->db->where("appointment.id", $appointment_id);
        $this->db->group_by("appointment.id");
        $this->db->select(" 
               appointment.id, 
               appointment.user_id,
                DATE(
                  all_appointment.patient_availability_date_and_time
                ) AS booking_date,
                TIME(
                  all_appointment.patient_availability_date_and_time
                ) AS booking_time,
                provider_plan.title AS type,
                appointment.amount,
                user_payment_methods.payment_method_type,
                appointment.insurance_status,
                promocode.code,
                all_appointment.name,
                all_appointment.patient_med_id,
                all_appointment.symptoms,
                appointment.symptom_start_date,
                all_appointment.doctor,
                all_appointment.med_id,
                (SELECT GROUP_CONCAT(`name`) AS exam FROM `exam` AS a  GROUP BY `appointment_id` HAVING appointment_id='" . $appointment_id . "') AS exam_name,
                (SELECT GROUP_CONCAT(`name`) AS diagnosis FROM `diagnosis` AS a  GROUP BY `appointment_id` HAVING appointment_id='" . $appointment_id . "') AS diagnosis_name");

        $this->db->from("all_appointment");
        $this->db->join("appointment", "all_appointment.appointment_id = appointment.id", "INNER");
        $this->db->join("user_payment_methods", "user_payment_methods.id = appointment.payment_method_id", "LEFT");
        $this->db->join("promocode", "promocode.id = appointment.promocode_id", "LEFT");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    public function get_appointment_prescription_detail($appointment_id) {

        $this->db->where("prescriptions.appointment_id", $appointment_id);
        $this->db->group_by("doctor_medication_instructions.medication_id");
        $this->db->select("prescriptions.prescription_id,
                prescriptions.appointment_id,
                medication.name,
                medication.id,
                medication_info.quantity,medication_info.dosage,
                medication_info.refill,medication_info.unit,
                medication_info.frequency,medication_info.route,
                doctor_medication_instructions.medication_id,
                GROUP_CONCAT(`doctor_medication_instructions`.`medication_instruction`) AS medication_instruction,
                medication.additional_info
                ");

        $this->db->from("medication_info");
        $this->db->join("prescription_medication", "medication_info.id = prescription_medication.medication_info_id", "INNER");
        $this->db->join("prescriptions", "prescriptions.prescription_id = prescription_medication.prescription_id", "INNER");
        $this->db->join("medication", "medication.id =medication_info.medication_id", "INNER");
        $this->db->join("doctor_medication_instructions", "doctor_medication_instructions.medication_id = medication_info.id", "LEFT");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }
    public function get_user_name($user_id) {
       $this->db->where("id IN(SELECT patient_id FROM user_patient WHERE user_id ='".$user_id."')"); 
       $q = $this->db->select("CONCAT(first_name,' ',last_name) AS user_name")->from("patient_info")->get(); 
       return $q->row_array();
    }
}

?>