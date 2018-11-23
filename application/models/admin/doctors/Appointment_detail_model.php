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
               appointment.doctor_id,
                DATE(
                  all_appointment.patient_availability_date_and_time
                ) AS booking_date,
                TIME(
                  all_appointment.patient_availability_date_and_time
                ) AS booking_time,
                provider_plan.title AS type,
                appointment.amount,
                user_payment_methods.payment_method_type,
                promocode.code,
                appointment.insurance_status,
                all_appointment.name,
                all_appointment.patient_med_id,
                all_appointment.symptoms,
                appointment.symptom_start_date,
                all_appointment.doctor,
                all_appointment.med_id,
                (SELECT CONCAT(user_pharmacies.pharmacies_id , '|',pharmacies.pharmacy_name,'|',pharmacies.phone) AS pharmacy_name FROM`user_pharmacies` INNER JOIN pharmacies ON pharmacies.id = user_pharmacies.pharmacies_id WHERE user_id IN(SELECT user_id FROM `appointment` WHERE id = '" . $appointment_id . "') AND user_pharmacies.is_primary = 0 ORDER BY user_pharmacies.id DESC LIMIT 1) AS user_preferred_pharmacy,
                (SELECT GROUP_CONCAT(`name`) AS exam FROM `exam` AS a  GROUP BY `appointment_id` HAVING appointment_id='" . $appointment_id . "') AS exam_name,
                (SELECT GROUP_CONCAT(`name`) AS diagnosis FROM `diagnosis` AS a  GROUP BY `appointment_id` HAVING appointment_id='" . $appointment_id . "') AS diagnosis_name");

        $this->db->from("all_appointment");
        $this->db->join("appointment", "all_appointment.appointment_id = appointment.id", "INNER");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $this->db->join("user_payment_methods", "user_payment_methods.id = appointment.payment_method_id", "LEFT");
        $this->db->join("promocode", "promocode.id = appointment.promocode_id", "LEFT");
        $query = $this->db->get();

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

    public function get_pharmacy_detail($pharmacy_id) {
        $this->db->where("id",$pharmacy_id['id']);
        $this->db->select("id,pharmacy_name,phone,city,state,zip,address");
        $this->db->from("pharmacies");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : false;
    }
    public function get_admin_note($appt_id) {
        $this->db->where("admin_name",$this->session->userdata('name'));
        $this->db->where("appointment_id",$appt_id);
        $this->db->order_by("updated_at","DESC");
        $this->db->select("id,admin_name,appointment_id,note,CONVERT_TZ(updated_at,'+00:00','-08:00') AS updated_at");
        $this->db->from("admin_appointment_note");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }
    function getusername($id) {
        $this->db->where("id",$id);
        $query = $this->db->select("CONCAT(first_name,' ',last_name) AS doctor_name")->from("doctors")->get();
        return $query->row_array();
    }
}

?>