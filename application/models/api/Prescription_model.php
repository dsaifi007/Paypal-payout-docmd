<?php

/*
  class name : prescriptions Model
 */

class Prescription_model extends CI_Model {

    protected $update_data = [];
    protected $doctor_patient_data = [];
    protected $med_pres_mapp_array = [];

    public function __construct() {
        parent::__construct();
    }

    // when prescription will get(mandatory) from database then show prescritpion 
    public function user_prescritpions_model($user_id, $offset = 0, $language) {

        $query = $this->db->get_where("all_appointment", ["user_id" => $user_id]);
        $medication_name = ($language == "spn") ? 'medication.sp_name' : 'medication.name';
        if ($query->num_rows() > 0) {
            $this->db->where(["all_appointment.user_id" => $user_id, "all_appointment.status" => 6]);
            //$this->db->group_by("all_appointment.appointment_id");
            $this->db->order_by("all_appointment.appointment_id", "DESC");
            //$this->db->limit($offset, 1);
            // GROUP_CONCAT($medication_name SEPARATOR '||||') AS medications
            $this->db->select(
                    " $medication_name AS medications,all_appointment.doctor_age,all_appointment.doctor_gender,prescription_medication.prescription_id, all_appointment.user_id,all_appointment.appointment_id,all_appointment.symptoms,prescription_medication.prescription_id,all_appointment.doctor AS name,
                    all_appointment.status"
            );
            $this->db->from("all_appointment");
            $this->db->join("prescriptions", "all_appointment.appointment_id=prescriptions.appointment_id", "INNER");
            $this->db->join("prescription_medication", "prescription_medication.prescription_id=prescriptions.prescription_id", "LEFT");
            $this->db->join("medication_info", "medication_info.id=prescription_medication.medication_info_id", "LEFT");
            $this->db->join("medication", "medication.id=medication_info.medication_id", "LEFT");
            $query = $this->db->get();
            return ($query->num_rows() > 0) ? $query->result_array() : "no_data";
        } else {
            return false;
        }
    }

    // when prescription will get(mandatory) from database then show prescritpion appointment 
    /*
      get only one most recent prescription/medication with per doctor per user
     */
    public function doctor_prescritpions_model($doctor_id, $offset = null, $language) {

        $query = $this->db->get_where("all_appointment", [
            "doctor_id" => $doctor_id]);
        if ($query->num_rows() > 0) {
            $medication_name = ($language == "spn") ? 'medication.sp_name' : 'medication.name';
            $this->db->where(["all_appointment.doctor_id" => $doctor_id, "all_appointment.status" => 6]);
            //$this->db->group_by("all_appointment.appointment_id");
            $this->db->order_by("all_appointment.appointment_id", "DESC");
            //$this->db->limit($offset, 1);
            //GROUP_CONCAT($medication_name SEPARATOR '||||') AS medications
            $this->db->select(
                    "all_appointment.appointment_id,all_appointment.symptoms,prescription_medication.prescription_id,CONCAT(all_appointment.patient_id,'|',all_appointment.name,'|',all_appointment.gender,'|',all_appointment.age) AS patient,$medication_name AS medications,
                               all_appointment.status"
            );
            $this->db->from("all_appointment");
            $this->db->join("prescriptions", "all_appointment.appointment_id=prescriptions.appointment_id", "INNER");
            $this->db->join("prescription_medication", "prescription_medication.prescription_id=prescriptions.prescription_id", "LEFT");
            $this->db->join("medication_info", "medication_info.id=prescription_medication.medication_info_id", "LEFT");
            $this->db->join("medication", "medication.id=medication_info.medication_id", "LEFT");


            // $query =  "SELECT a.* FROM (SELECT
            //  all_appointment.doctor_id,
            //  all_appointment.patient_id,
            //    `all_appointment`.`appointment_id`,
            //    `all_appointment`.`symptoms`,
            //    CONCAT(
            //      all_appointment.patient_id,
            //      '|',
            //      `all_appointment`.`name`,
            //      '|',
            //      `all_appointment`.`gender`,
            //      '|',
            //      all_appointment.age
            //    ) AS patient,
            //    GROUP_CONCAT($medication_name SEPARATOR '||||') AS medications,
            //    `all_appointment`.`status`
            //  FROM
            //    `all_appointment`
            //  LEFT JOIN
            //    `prescriptions` ON `all_appointment`.`appointment_id` = `prescriptions`.`appointment_id`
            //  LEFT JOIN
            //    `prescription_medication` ON `prescription_medication`.`prescription_id` = `prescriptions`.`prescription_id`
            //  LEFT JOIN
            //    `medication_info` ON `medication_info`.`id` = `prescription_medication`.`medication_info_id`
            //  LEFT JOIN
            //    `medication` ON `medication`.`id` = `medication_info`.`medication_id`
            //  WHERE
            //    `all_appointment`.`doctor_id` = '".$doctor_id."' AND `all_appointment`.`status` = 6
            //  GROUP BY
            //    `all_appointment`.`appointment_id`
            //  ORDER BY
            //    `all_appointment`.`appointment_id` DESC) as a GROUP BY a.patient_id";
            // $query= $this->db->query( $query);
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0) ? $query->result_array() : "no_data";
        } else {
            return false;
        }
    }

    // // when prescription will get or not from database then show the appointment data
    // difference only join inner to left
    public function doctor_prescritpions_past_model($doctor_id, $language) {

        $query = $this->db->get_where("all_appointment", ["doctor_id" => $doctor_id]);
        if ($query->num_rows() > 0) {
            $medication_name = ($language == "spn") ? 'medication.sp_name' : 'medication.name';
            // before $this->db->where(["all_appointment.doctor_id" => $doctor_id, "all_appointment.status" => 6]);
            $this->db->where(["all_appointment.doctor_id" => $doctor_id, "all_appointment.patient_availability_date_and_time <=" => $this->config->item("appointment_date")]);
            $this->db->where_in("all_appointment.status", $this->config->item("recent_status"));
            /// after logic change above 
            $this->db->group_by("all_appointment.appointment_id");
            $this->db->order_by("all_appointment.appointment_id", "DESC");
            //$this->db->limit($offset, 1);
            $this->db->select(
                    "all_appointment.appointment_id,all_appointment.symptoms,CONCAT(all_appointment.patient_id,'|',all_appointment.name,'|',all_appointment.gender,'|',all_appointment.age) AS patient,GROUP_CONCAT($medication_name SEPARATOR '||||') AS medications,
                    all_appointment.status,all_appointment.patient_availability_date_and_time,all_appointment.type"
            );
            $this->db->from("all_appointment");
            $this->db->join("prescriptions", "all_appointment.appointment_id=prescriptions.appointment_id", "LEFT");
            $this->db->join("prescription_medication", "prescription_medication.prescription_id=prescriptions.prescription_id", "LEFT");
            $this->db->join("medication_info", "medication_info.id=prescription_medication.medication_info_id", "LEFT");
            $this->db->join("medication", "medication.id=medication_info.medication_id", "LEFT");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0) ? $query->result_array() : "no_data";
        } else {
            return false;
        }
    }

    // when prescription will get or not from database then show the appointment data
    // difference only join inner to left
    public function doctor_to_patient_prescritpions_model($doctor_id, $patient_id, $appointment_id, $language) {

        $query = $this->db->get_where("all_appointment", [
            "doctor_id" => $doctor_id,
            "patient_id" => $patient_id,
            "appointment_id" => $appointment_id
        ]);
        if ($query->num_rows() > 0) {
            $medication_name = ($language == "spn") ? 'medication.sp_name' : 'medication.name';
            $this->db->where(["all_appointment.doctor_id" => $doctor_id, "all_appointment.patient_id" => $patient_id, "all_appointment.appointment_id" => $appointment_id]);
            $this->db->group_by("prescription_medication.prescription_id");
            $this->db->order_by("all_appointment.appointment_id", "DESC");
            //$this->db->limit($offset, 1);
            $this->db->select(
                    "all_appointment.appointment_id,prescriptions.prescription_id,all_appointment.symptoms,CONCAT(all_appointment.patient_id,'|',all_appointment.name,'|',all_appointment.gender,'|',all_appointment.age) AS patient,GROUP_CONCAT($medication_name SEPARATOR '||||') AS medications,
                    all_appointment.status"
            );
            $this->db->from("all_appointment");
            $this->db->join("prescriptions", "all_appointment.appointment_id=prescriptions.appointment_id", "INNER");
            $this->db->join("prescription_medication", "prescription_medication.prescription_id=prescriptions.prescription_id", "INNER");
            $this->db->join("medication_info", "medication_info.id=prescription_medication.medication_info_id", "INNER");
            $this->db->join("medication", "medication.id=medication_info.medication_id", "INNER");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            return ($query->num_rows() > 0) ? $query->result_array() : "no_data";
        } else {
            return false;
        }
    }

    public function add_exam_model($input_data) {
        $input_data["created_at"] = date("Y-m-d h:i:s");
        $this->db->insert("exam", $input_data);
        return true;
    }

    public function get_exam_model($condition) {
        $this->db->where($condition);
        $this->db->select("id,appointment_id,name,details");
        $this->db->from("exam");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function add_diagnosis_model($input_data, $id = null) {
        if ($id == null) {
            $input_data["created_at"] = date("Y-m-d h:i:s");
            $input_data['additional_info'] = $input_data['details'];
            unset($input_data['details']);
            $this->db->insert("diagnosis", $input_data);
            return true;
        } else {
            $this->db->where("id", $id);
            $this->db->update("diagnosis", ['name' => $input_data['name'], "additional_info" => $input_data['details']]);
        }
    }

    public function get_diagnosis_model($condition) {
        $this->db->where($condition);
        $this->db->select("id,appointment_id,name,additional_info as details");
        $this->db->from("diagnosis");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function get_doctor_patient_prescritpions($ids) {
        $patient_info = $this->get_patient_info($ids['patient_id']);

        $recent_condition = [
            "patient_availability_date_and_time <=" => $this->config->item("appointment_date"), "doctor_id" => $ids['doctor_id'], "patient_id" => $ids['patient_id']];
        $recent_status = $this->config->item("recent_status");
        // when we want to prescription of past appointment send prescription
        $recent_data = get_all_appointment($recent_condition, $recent_status, null, "prescription");


        $recent_appoint = appointment_array($recent_data);

        // upcoming appointment
        $upcoming_condition = [
            "patient_availability_date_and_time >=" => $this->config->item("appointment_date"), "doctor_id" => $ids['doctor_id'], "patient_id" => $ids['patient_id']];
        $status = $this->config->item("upcoming_status");
        $upcmng_data = get_all_appointment($upcoming_condition, $status, null);
        $up_appointment = appointment_array($upcmng_data);
        // only one appoiuntment need which will be most recent
        $this->doctor_patient_data = [
            ($patient_info) ? $patient_info : null,
            (isset($up_appointment[0])) ? [$up_appointment[0]] : null,
            (isset($recent_appoint[0])) ? [$recent_appoint[0]] : null
        ];
        return $this->doctor_patient_data;
    }

    public function get_patient_info($patient_id) {
        $this->db->where("patient_info.id", $patient_id);
        $this->db->select(
                "patient_info.id,patient_info.med_id,patient_info.first_name,patient_info.last_name,floor(datediff(curdate(),patient_info.date_of_birth) / 365.25) AS age,patient_info.profile_url AS profile_image_url,patient_info.gender,address.state,address.zip_code,user_medical_profile.medications,user_medical_profile.allergies,user_medical_profile.past_medical_history,user_medical_profile.social_history,user_medical_profile.family_history"
        );
        $this->db->from("patient_info");
        $this->db->join("patient_address", "patient_address.patient_id=patient_info.id", "LEFT");
        $this->db->join("address", "address.id=patient_address.address_id", "LEFT");
        $this->db->join("user_medical_profile", "user_medical_profile.id=patient_info.id", "LEFT");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

    public function addprescription_model($data, $language = null) {
        $field = ($language == "spn") ? "sp_name,sp_additional_info" : "name,additional_info";
        $this->db->trans_start();
        // if diagnosis id will get from Array then insert the diagnosis info other no
        if ($data['diagnosis_id'] != null && $data['diagnosis_id'] != '' && $data['diagnosis_id']) {

            $diagnosis_info = $this->db->select($field)->from("admin_diagnosis")->where("id", $data['diagnosis_id'])->get()->row_array();
            if (count($diagnosis_info) > 0 && !empty($diagnosis_info)) {
                if ($language == "spn") {
                    $add_diagnosis = [
                        "appointment_id" => $data['appointment_id'],
                        "doctor_id" => $data['doctor_id'],
                        "patient_id" => $data['patient_id'],
                        "sp_name" => $diagnosis_info['sp_name'],
                        "sp_additional_info" => $diagnosis_info['sp_additional_info'],
                        "created_at" => date("Y-m-d H:i:s")
                    ];
                } else {
                    $add_diagnosis = [
                        "appointment_id" => $data['appointment_id'],
                        "doctor_id" => $data['doctor_id'],
                        "patient_id" => $data['patient_id'],
                        "name" => $diagnosis_info['name'],
                        "additional_info" => $diagnosis_info['additional_info'],
                        "created_at" => date("Y-m-d H:i:s")
                    ];
                }
                $this->db->insert("diagnosis", $add_diagnosis);
            }
            // end add_diagnosis
        }

        //if medication  will get from Array then insert the medication info otherwise  no insert
        // All wrong id also handel
        if (count(@$data['medication']) > 0 && !empty(@$data['medication']) && $data['medication'] != null) {
            $res = $this->db->insert("prescriptions", ["appointment_id" => $data['appointment_id']]);
            $prescription_id = $this->db->insert_id();
            if ($prescription_id) {
                foreach ($data['medication'] as $key => $medication_info) {
                    unset($medication_info['medication_instruction']);
                    $this->db->insert("medication_info", $medication_info);
                    $last_medication_id = $this->db->insert_id();
                    $this->med_pres_mapp_array[] = [
                        'medication_info_id' => $last_medication_id,
                        'prescription_id' => $prescription_id
                    ];
                }
            }
            $this->db->insert_batch("prescription_medication", $this->med_pres_mapp_array);

            // Mapping the Medication with instruction
            if (isset($data['medication'][0]['medication_instruction']) && count($data['medication'][0]['medication_instruction']) > 0) {
                $medication_instruction_array = array();
                foreach ($data['medication'][0]['medication_instruction'] as $v) {
                    $medication_instruction_array[] = ["medication_id" => $last_medication_id, "medication_instruction" => $v];
                }
                $this->db->insert_batch("doctor_medication_instructions", $medication_instruction_array);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            $this->db->where("all_appointment.appointment_id", $data['appointment_id']);
            $this->db->select("all_appointment.appointment_id,all_appointment.name,all_appointment.type,users.device_token")->from("all_appointment");
            $this->db->join("users", "users.id=all_appointment.user_id", "INNER");
            return $this->db->get()->row_array();
            //return true;
        }
    }
    public function add_new_prescptn_data($notification_title,$appt_id,$data,$res) {
        unset($data['device_token']);
        $notification_data = json_encode(array_merge(["notification"=>$notification_title], ["data" => $data]));

        $this->db->insert("user_on_call_notification",['appointment_id'=>$appt_id,"notification_data"=> $notification_data,"fcm_response"=>$res]);
    }
    public function get_single_medication($prescription_id, $language = "eng") {

        $medicantion_name = ($language == "spn") ? "medication.sp_name as name " : "medication.name";
        $this->db->where("medication_info.id IN(SELECT medication_info_id FROM prescription_medication WHERE prescription_id = '" . $prescription_id . "')");
        $this->db->group_by("medication_info.id");
        $this->db->select("medication_info.id,
                $medicantion_name 
                ,medication_info.quantity,medication_info.dosage,medication_info.refill,medication_info.unit,medication_info.frequency,medication_info.route,IFNULL(GROUP_CONCAT(doctor_medication_instructions.medication_instruction SEPARATOR '||'),'') AS medications_instruction");

        $this->db->from("medication_info");
        $this->db->join("doctor_medication_instructions", "doctor_medication_instructions.medication_id = medication_info.id", "LEFT");
        $this->db->join("medication", "medication.id = medication_info.medication_id", "INNER");
        //$this->db->join("prescription_medication", "prescription_medication.medication_info_id=medication_info.id", "LEFT");

        $q = $this->db->get();
        $row = $q->row_array();
        $row['medications_instruction'] = array_filter(explode("||", $row['medications_instruction']));
        //echo $this->db->last_query();
        // die;
        //dd(array_filter($row));
        return (!empty($q->num_rows())) ? $row : null;
    }

}

?>