<?php

class Get_notifications_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_user_notification($user_id) {
        $this->db->where("(appointment.user_id = '" . $user_id . "' OR ntf.user_id='" . $user_id . "' )");
        $this->db->order_by("ntf.id", "DESC");
        $this->db->select("ntf.id,ntf.appointment_id,ntf.notification_data,ntf.is_read,appointment.user_id");
        $this->db->from("user_on_call_notification as ntf");
        $this->db->join("appointment", "appointment.id=ntf.appointment_id", "LEFT");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rows = $query->result_array();
            foreach ($rows as $key => $value) {
                $rows[$key]['notification_data'] = json_decode($value['notification_data']);
            }
            return $rows;
        } else {
            return false;
        }
    }

    public function get_doctor_notification($doctor_id) {

        $this->db->where("doctor_id", $doctor_id);
        $this->db->order_by("id", "DESC");
        $query = $this->db->select("id,appointment_id,notification_data,is_read")
                ->from("doctor_on_call_notification")
                ->get();
        if ($query->num_rows() > 0) {
            $rows = $query->result_array();
            foreach ($rows as $key => $value) {
                $rows[$key]['notification_data'] = json_decode($value['notification_data']);
            }
            return $rows;
        } else {
            return false;
        }
    }

    public function get_users_notification($user_id) {
        $this->db->where("appointment.user_id", $user_id);
        $this->db->where("appointment.status IN(4)");
        $this->db->select("appt_rechedule_notification.notification_json,appointment.status");
        $this->db->from("appointment");
        $this->db->join("appt_rechedule_notification", "appt_rechedule_notification.appointment_id = appointment.id", "INNER");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $rows = $query->result_array();
            foreach ($rows as $key => $value) {
                $rows[$key] = json_decode($value['notification_json']);
            }

            return $rows;
        } else {
            return false;
        }
    }

    public function update_user_is_read_status($data) {
        $table = ($data['action'] == "doctor") ? "doctor_on_call_notification" : "user_on_call_notification";
        $this->db->where("id", $data['notification_id']);
        $this->db->update($table, ["is_read" => 1]);
        return true;
    }

    public function delete_notification($data) {
        $table = ($data['action'] == "doctor") ? "doctor_on_call_notification" : "user_on_call_notification";
        $this->db->where("id", $data['notification_id']);
        $this->db->delete($table);
        return true;
    }

}

?>