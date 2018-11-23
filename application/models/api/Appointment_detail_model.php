<?php

/*
  class name : Appoinment Model
 */

class Appointment_detail_model extends CI_Model {

    protected $update_data = [];

    function __construct() {
        parent::__construct();
    }

    public function get_appointment_by_user_model($data, $language) {

        $severity_of_symptoms_by_user = ($language == "spn") ? "severity_of_symptoms.sp_name AS severity_of_symptom" : "severity_of_symptoms.name AS severity_of_symptom";


        $this->db->where("appointment.id", $data['appointment_id']);
        $this->db->group_by("appointment_symptom.appointment_id");
        $degree = "(SELECT
                        GROUP_CONCAT(doctor_degree.degree)
                      FROM
                        `doctor_degree`
                      INNER JOIN
                        doctor_degree_mapping ON doctor_degree_mapping.degree_id = doctor_degree.id
                      GROUP BY
                        doctor_degree_mapping.doctor_id HAVING doctor_degree_mapping.doctor_id = (SELECT doctor_id FROM appointment where id ='" . $data['appointment_id'] . "')) AS degree ";
        $specaility = "(SELECT
                        GROUP_CONCAT(spacility.name)
                      FROM
                        `spacility`
                      INNER JOIN
                        doctor_speciality ON doctor_speciality.spacility_id = spacility.id
                      GROUP BY
                        doctor_speciality.doctor_id HAVING doctor_speciality.doctor_id = (SELECT doctor_id FROM appointment where id ='" . $data['appointment_id'] . "')) AS doctor_speciality ";
        $visit_instruction = "(SELECT GROUP_CONCAT(visit_instruction SEPARATOR '||') AS visit_instruction FROM `appointment_visit_instruction` where appointment_id = '" . $data['appointment_id'] . "' GROUP BY appointment_id) AS visit_inst";

        $this->db->select("appointment.id,
                appointment.user_id,appointment.is_submited,
                $degree,
                    $specaility,
                appointment.symptom_start_date,
                $severity_of_symptoms_by_user,promocode.code AS promocode,

                  CONCAT(`patient_info`.`id`,'||',IFNULL(`patient_info`.`med_id`,''),'||',
                   patient_info.first_name,'||',
                   patient_info.last_name,'||',
                   IFNULL(`patient_info`.`profile_url`,'')
                 ) as patient,
                 (SELECT state FROM address WHERE id IN(SELECT address_id FROM patient_address WHERE patient_id IN(SELECT patient_id FROM appointment WHERE id = '" . $data['appointment_id'] . "'))) AS state,
                appointment.patient_availability_date_and_time,
                (SELECT name FROM spacility WHERE id in(SELECT doc_speciality_id FROM appointment WHERE id = '" . $data['appointment_id'] . "')) AS spacility,
                appointment.doc_speciality_id AS speciality_id,
                appointment.patient_availability_date_and_time,
                CONCAT(IFNULL(doctors.id,''),'|',IFNULL(doctors.first_name,''),'|',IFNULL(doctors.last_name,''),
                '|',IFNULL(`doctors`.`profile_url` ,''),'|',IFNULL(address.state ,''),'|',
                IFNULL(doctors.med_id ,''),'|',IFNULL(address.zip_code,''),'|',
                IFNULL(`doctors`.`gender` ,'')) as doctor,
                GROUP_CONCAT(symptom.additional_info SEPARATOR '||') AS appointment_instruction,
                $visit_instruction,
                CONCAT(user_payment_methods.card_number,'|',user_payment_methods.brand,'|',user_payment_methods.card_name) AS payment_method,
                floor(datediff(curdate(),doctors.date_of_birth) / 365.25) AS age,
                CONCAT(appointment_status.id,'|',appointment_status.status) as  appointment_status,
                appointment.latitude,appointment.longitude,
                GROUP_CONCAT(symptom.name SEPARATOR '_') as symptoms,
                CONCAT(IFNULL(provider_plan.title,''),'|','$',IFNULL(provider_plan.amount,''),'|',IFNULL(provider_plan.is_recommended,''),'|',IFNULL(provider_plan.type,'')) as provider_plan,user_transactions.paypal_email,user_transactions.venmo_id");

        $this->db->from("appointment");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "LEFT");

        $this->db->join("doctor_address", "doctor_address.doctor_id = doctors.id", "LEFT");
        $this->db->join("address", "address.id = doctor_address.address_id", "LEFT");
        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "LEFT");
        $this->db->join("promocode", "promocode.id = appointment.promocode_id", "LEFT");

        //$this->db->join("doctor_speciality", "doctor_speciality.doctor_id = doctors.id", "LEFT");
        //$this->db->join("spacility", "spacility.id = doctor_speciality.spacility_id", "LEFT");
        //$this->db->join("doctor_degree_mapping", "doctor_degree_mapping.doctor_id = doctors.id", "LEFT");
        //$this->db->join("doctor_degree", "doctor_degree.id = doctor_degree_mapping.degree_id", "LEFT");

        $this->db->join("appointment_status", "appointment_status.id = appointment.status", "LEFT");
        $this->db->join("appointment_symptom", "appointment_symptom.appointment_id = appointment.id", "LEFT");
        $this->db->join("symptom", "symptom.id = appointment_symptom.symptom_id", "LEFT");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "LEFT");
        $this->db->join("severity_of_symptoms", "severity_of_symptoms.id = appointment.severity_of_symptom_id", "LEFT");
        $this->db->join("user_payment_methods", "user_payment_methods.id = appointment.payment_method_id", "LEFT");
         $this->db->join("user_transactions", "user_transactions.appointment_id = appointment.id", "LEFT");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        $merg_data = array();
        $row = $query->row_array();
        if (!empty($query->row_array())) {
            //dd($query->row_array());
            if ($row['is_submited'] == 0) {
                $merg_data = array(
                    'prescription' => null,
                    "diagnosis" => null,
                    "exam" => null
                );
            } else {
                $prescription = $this->get_appointment_prescriptions($data['appointment_id'], $language);
                $diagnosis = $this->get_appointment_diagnosis($data['appointment_id'], $language);
                $exam = $this->get_appointment_exam($data['appointment_id']);
                $merg_data = array(
                    'prescription' => ($prescription) ? $prescription : null,
                    "diagnosis" => ($diagnosis) ? $diagnosis : null,
                    "exam" => ($exam) ? $exam : null,
                );
            }
            $pharmacy = $this->getUserPharmacy($query->row_array()['user_id']);
            $merg_data['pharmacy'] = ($pharmacy) ? $pharmacy : null;


            $final_data = array_merge($query->row_array(), $merg_data);
            //dd($final_data);
            return (!empty($query->row_array())) ? $final_data : false;
        }
    }

    public function get_appointment_prescriptions($appointemnt_id, $language) {

        $medicantion_name = ($language == "spn") ? "medication.sp_name as name " : "medication.name";

        $app_condition = "prescriptions.appointment_id =(SELECT id FROM appointment WHERE id='" . $appointemnt_id . "')"; // status completed remove from here
        $this->db->group_by("medication_info.id");
        $this->db->where($app_condition);
        //$this->db->group_by("prescription_medication.prescription_id");
        $this->db->select("medication_info.id,
                $medicantion_name ,prescriptions.prescription_id,
                ,medication_info.quantity,medication_info.dosage,medication_info.refill,medication_info.unit,medication_info.frequency,medication_info.route, CONCAT(IFNULL(medication.additional_info,''),'||',IFNULL(GROUP_CONCAT(doctor_medication_instructions.medication_instruction SEPARATOR '||'),'')) AS medications_instruction");

        $this->db->from("prescriptions");
        $this->db->join("prescription_medication", "prescriptions.prescription_id = prescription_medication.prescription_id", "INNER");
        $this->db->join("medication_info", "medication_info.id = prescription_medication.medication_info_id", "INNER");
        $this->db->join("doctor_medication_instructions", "doctor_medication_instructions.medication_id = medication_info.id", "LEFT");
        $this->db->join("medication", "medication.id = medication_info.medication_id", "INNER");

        //echo $this->db->last_query();die;
        //dd(array_filter($row));
        $q = $this->db->get();
        $row = $q->result_array();
        if (!empty($q->num_rows())) {
            foreach (@$row as $key => $value) {
                $row[$key]['medications_instruction'] = array_filter(explode("||", $value['medications_instruction']));
            }
            return $row;
        } else {
            return null;
        }
    }

    public function get_appointment_diagnosis($appointemnt_id, $language) {

        $diagnosis_name = ($language == "spn") ? "sp_name,sp_additional_info" : "name,additional_info";

        $app_condition = "appointment_id =(SELECT id FROM appointment WHERE id='" . $appointemnt_id . "')";
        $this->db->where($app_condition);
        //$this->db->group_by("prescription_medication.prescription_id");
        $this->db->select("id,
                $diagnosis_name 
                ");
        $this->db->from("diagnosis");
        $q = $this->db->get();
        //echo $this->db->last_query();die;
        return (!empty($q->num_rows())) ? $q->result_array() : null;
    }

    public function get_appointment_exam($appointemnt_id) {


        $app_condition = "appointment_id =(SELECT id FROM appointment WHERE id='" . $appointemnt_id . "' AND status = 6 )";
        $this->db->where($app_condition);
        //$this->db->group_by("prescription_medication.prescription_id");
        $this->db->select("id,name,details");
        $this->db->from("exam");
        $q = $this->db->get();
        //echo $this->db->last_query();die;
        return (!empty($q->num_rows())) ? $q->result_array() : null;
    }

    public function getUserPharmacy($userid) {
        $this->db->where("user_pharmacies.user_id", $userid);
        $this->db->where("user_pharmacies.is_primary", 1);
        $this->db->select(
                "pharmacies.id,pharmacies.pharmacy_name,pharmacies.pharmacy_image_url as pharmacy_image,
                pharmacies.address,pharmacies.city,pharmacies.state,pharmacies.zip");
        $this->db->from("pharmacies");
        $this->db->join("user_pharmacies", "pharmacies.id = user_pharmacies.pharmacies_id", "INNER");
        $query3 = $this->db->get();
        return ($query3->num_rows() > 0) ? $query3->row_array() : null;
    }

    public function get_appointment_by_doctor_model($data, $language) {

        $severity_of_symptoms = ($language == "spn") ? "severity_of_symptoms.sp_name AS severity_of_symptom" : "severity_of_symptoms.name AS severity_of_symptom";
        $visit_instruction = "(SELECT GROUP_CONCAT(visit_instruction SEPARATOR '||') AS visit_instruction FROM `appointment_visit_instruction` where appointment_id = '" . $data['appointment_id'] . "' GROUP BY appointment_id) AS visit_inst";

        $this->db->where("appointment.id", $data['appointment_id']);
        $this->db->group_by("appointment_symptom.appointment_id");


        $this->db->select("appointment.id,
                appointment.user_id,
                appointment.symptom_start_date,
                $severity_of_symptoms, 
                appointment.patient_availability_date_and_time,
                CONCAT(patient_info.id,'|',patient_info.first_name,'|',patient_info.last_name,'|',patient_info.gender,'|',IFNULL(address.zip_code,''),'|',IFNULL(address.state,''),'|',IFNULL( `patient_info`.`profile_url`,''),'|',IFNULL( `patient_info`.`med_id`,'')) as patient,floor(datediff(curdate(),patient_info.date_of_birth) / 365.25) AS age,
                GROUP_CONCAT(symptom.additional_info SEPARATOR '||') AS appointment_instruction,
                $visit_instruction,
                CONCAT(appointment_status.id,'|',appointment_status.status) as  appointment_status,appointment.latitude,appointment.longitude,
                GROUP_CONCAT(symptom.name SEPARATOR '_') as symptoms,
                CONCAT(provider_plan.title,'|',provider_plan.amount,'|',provider_plan.is_recommended,'|',provider_plan.type) as provider_plan");

        $this->db->from("appointment");


        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "LEFT");
        $this->db->join("patient_address", "patient_address.patient_id = patient_info.id", "INNER");
        $this->db->join("address", "address.id = patient_address.address_id", "INNER");

        $this->db->join("appointment_status", "appointment_status.id = appointment.status", "INNER");
        $this->db->join("appointment_symptom", "appointment_symptom.appointment_id = appointment.id", "INNER");
        $this->db->join("symptom", "symptom.id = appointment_symptom.symptom_id", "INNER");
        $this->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $this->db->join("severity_of_symptoms", "severity_of_symptoms.id = appointment.severity_of_symptom_id", "INNER");

        $query = $this->db->get();
        //echo $this->db->last_query();die;

        $prescription = $this->get_appointment_prescriptions($data['appointment_id'], $language);
        $diagnosis = $this->get_appointment_diagnosis($data['appointment_id'], $language);
        $exam = $this->get_appointment_exam($data['appointment_id']);
        $pharmacy = $this->getUserPharmacy($query->row_array()['user_id']);
        $final_data = array_merge($query->row_array(), ['prescription' => ($prescription) ? $prescription : null, "diagnosis" => $diagnosis, "exam" => $exam, "pharmacy" => $pharmacy]);

        return (!empty($query->row_array())) ? $final_data : false;
    }

    public function appointment_reschedule_by_user_model($data) {

        // Get the date and time using the slot id
        $this->db->where("doctor_slots.id", $data['slot_id']);
        $this->db->select("doctor_slots.id,doctors.device_token,hour_list.start_time,date_availability_list.date_available,(SELECT  CONCAT(first_name,' ',last_name,'||',
    IFNULL(profile_url,'')) FROM doctors WHERE id IN(SELECT doctor_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')) AS doctor_profile,(SELECT  title FROM provider_plan WHERE id IN(SELECT treatment_provider_plan_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')) AS type");
        $this->db->from("doctor_slots");
        $this->db->join("hour_list", "hour_list.id = doctor_slots.slot_id", "INNER");
        $this->db->join("doctors", "doctors.id = doctor_slots.doctor_id", "INNER");
        $this->db->join("date_availability_list", "date_availability_list.id = doctor_slots.date_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        $row = $query->row_array();

        // old status update
        $this->db->where("id IN(SELECT slot_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')");
        $this->db->update("doctor_slots", ["status" => 0]);

        // update the appointment table
        $this->update_data = [
            "doctor_id" => $data['doctor_id'],
            "slot_id" => $data['slot_id'],
            "patient_availability_time" => $row['start_time'],
            "patient_availability_date" => $row['date_available'],
            "patient_availability_date_and_time" => $row['date_available'] . " " . $row['start_time'],
            "last_appointment_date" => $data['last_appointment_date'],
            "status" => $this->config->item("appointment_reschedule_by_user")
        ];
        $this->db->where("id", $data['appointment_id']);
        $this->db->update("appointment", $this->update_data);

        // update the slot status
        $this->db->where("id", $data['slot_id']);
        $this->db->update("doctor_slots", ["status" => 1]);
        return ($row) ? $row : false;
    }

    public function appointment_reschedule_by_doctor_model($data) {

        // Get the date and time using the slot id
        $this->db->where("doctor_slots.id", $data['slot_id']);
        $this->db->select("doctor_slots.id,hour_list.start_time,date_availability_list.date_available ,(SELECT device_token FROM users WHERE id IN(SELECT user_id FROM appointment WHERE id='" . $data['appointment_id'] . "')) AS device_token,(SELECT  CONCAT(first_name,' ',last_name,'||',
    IFNULL(profile_url,'')) FROM patient_info WHERE id IN(SELECT patient_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')) AS user_profile,(SELECT  title FROM provider_plan WHERE id IN(SELECT treatment_provider_plan_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')) AS type"
        );
        $this->db->from("doctor_slots");
        $this->db->join("hour_list", "hour_list.id = doctor_slots.slot_id", "INNER");
        $this->db->join("date_availability_list", "date_availability_list.id = doctor_slots.date_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        $row = $query->row_array();


        // old status update
        $this->db->where("id IN(SELECT slot_id FROM appointment WHERE id ='" . $data['appointment_id'] . "')");
        $this->db->update("doctor_slots", ["status" => 0]);

        // update the appointment table
        $this->update_data = [
            "slot_id" => $data['slot_id'],
            "patient_availability_time" => $row['start_time'],
            "patient_availability_date" => $row['date_available'],
            "patient_availability_date_and_time" => $row['date_available'] . " " . $row['start_time'],
            "last_appointment_date" => $data['last_appointment_date'],
            "status" => $this->config->item("appointment_reschedule_by_doctor")
        ];
        $this->db->where("id", $data['appointment_id']);
        $this->db->update("appointment", $this->update_data);

        // update the slot status
        $this->db->where("id", $data['slot_id']);
        $this->db->update("doctor_slots", ["status" => 1]);
        return ($row) ? $row : false;
    }

    public function getAllDoctorPatientInfo_model($doctor_id) {
        $this->db->where(["all_appointment.status" => 6, "all_appointment.doctor_id" => $doctor_id['doctor_id']]);
        $this->db->group_by("all_appointment.patient_id");
        $this->db->order_by("all_appointment.patient_availability_date_and_time", "DESC");
        $this->db->select("
               all_appointment.user_id,all_appointment.patient_id,patient_info.profile_url,all_appointment.name,all_appointment.gender,all_appointment.age,all_appointment.patient_availability_date_and_time,all_appointment.status");
        $this->db->from("all_appointment");
        $this->db->join("patient_info", "patient_info.id=all_appointment.patient_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function getUserDoctorNotficationData($appt_id) {
        $this->db->where("appointment.id", $appt_id);
        $this->db->select("appointment.id,doctors.id AS doctor_id,users.id AS user_id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS user_name,patient_info.profile_url AS user_profile_url,appointment.status,CONCAT(doctors.first_name,' ',doctors.last_name) AS doctor_name,doctors.profile_url AS doctor_profile_url, appointment.doctor_id,appointment.user_id,users.device_token AS user_device_token,appointment.time_abbreviation,doctors.device_token AS doctor_device_token");
        $this->db->from("appointment");
        $this->db->join("users", "users.id = appointment.user_id", "INNER");
        $this->db->join("patient_info", "patient_info.id = appointment.patient_id", "INNER");
        $this->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $query = $this->db->get();
        return $query->row_array();
    }

}

?>