<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!function_exists("language_helper")) {

    function language_helper($language_file) {
        $CI = &get_instance();
        $CI->lang->load("admin/" . $language_file);
    }

}


/*
  |--------------------------------------------------------------------------------
  | This Function will user for breadcrumb
  | @return html
  |--------------------------------------------------------------------------------
 */

function breadcrumb($page_title) {
    $CI = &get_instance();
    $html = '';
    $html .= "<ol class='breadcrumb page-breadcrumb pull-right'>";
    $html .= "<li><i class='fa fa-home'></i>&nbsp;<a class='parent-item' href='index.html'>" . $CI->lang->line('home') . "</a>&nbsp;<i class='fa fa-angle-right'></i></li>";
    $html .= "<li class='active'>" . $page_title . "</li>";
    $html .= "</ol>";
    return $html;
}

/*
  |--------------------------------------------------------------------------------
  | This Function will user for form input wrapper
  |--------------------------------------------------------------------------------
 */

function form_input_wrapper($input_type = "text", $input_name, $id, $label, $for = '', $value = null, $class = 'form-control') {
    $CI = &get_instance();
    $html = '';
    $html .= "<div class='form-group'>";
    $html .= form_label($label, $for);
    $data = array(
        "type" => $input_type,
        'name' => $input_name,
        'id' => $id,
        'class' => $class,
        'value' => ''
    );
    $html .= form_input($data);
    $html .= "</div>";
    return $html;
}

/*
  |--------------------------------------------------------------------------------
  | This Function will user to clear input
  |--------------------------------------------------------------------------------
 */

function cleanInput($input) {
    $CI = &get_instance();
    return $CI->security->xss_clean($input);
}

/*
  |--------------------------------------------------------------------------------
  | This Function will check datatype and length
  |--------------------------------------------------------------------------------
 */
if (!function_exists("vd")) {

    function vd($val = '') {
        var_dump($val);
        die;
    }

}

/*
  |-----------------------------------------------------------------------------------------------
  | This function will check value of user input values
  |------------------------------------------------------------------------------------------------
 */
if (!function_exists('check_all_input_values')) {

    function check_all_input_values($user_input = []) {
        if (!empty($user_input)) {
            foreach ($user_input as $key => $value) {
                if (trim($user_input[$key]) == "" || empty($user_input[$key])) {
                    return false;
                }
            }
            return true;
        }
    }

}

if (!function_exists('insertCsvData')) {

    function insertCsvData($csv_url, $table = 'symptom', $fields = []) {
        //echo"<pre>";
        $CI = &get_instance();
        // $i=0;

        $array = array();
        $insert_data = array();
        $file = fopen($csv_url, "r");
        while (($data_tmp = fgetcsv($file, 1000, ",")) !== FALSE) {
            //if($i == 0){$i++; continue; }
            $array[] = $data_tmp;
        }
        $name = "name";
        foreach ($array as $value) {
            if (!empty($fields) && count($fields) > 0) {
                $name = $fields[0];
                $where = "" . $fields[0] . " = '" . $value[0] . "'OR " . $fields[2] . " = '" . $value[2] . "'";
            } else {
                $where = "name = '" . $value[0] . "'OR sp_name = '" . $value[2] . "'";
            }
            $CI->db->where($where);
            $CI->db->select("*");
            $CI->db->from($table);
            $query = $CI->db->get();
            //  echo $CI->db->last_query();die;
            if ($query->num_rows() == 0) {
                if (!empty($fields) && count($fields) > 0) {
                    $insert_data[] = [$fields[0] => $value[0], $fields[1] => $value[1], $fields[2] => $value[2], $fields[3] => $value[3]];
                } else {
                    $insert_data[] = [$array[0][0] => $value[0], $array[0][1] => $value[1], $array[0][2] => $value[2], $array[0][3] => $value[3]];
                }
            }
        }
        fclose($file);
        $temp = array_unique(array_column($insert_data, $name));
        $unique_arr = array_intersect_key($insert_data, $temp);
        unset($unique_arr[0]);
        //dd($unique_arr);
        $CI->db->db_debug = false;
        if (!empty($unique_arr)) {
            $a = $CI->db->insert_batch($table, $unique_arr);
            if ($a > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

}
if (!function_exists('get_user_appointments')) {

    function get_user_appointments($user_id, $date = null, $status, $filter_data = [], $appointment = "users") {
        $CI = &get_instance();
        if ($appointment == "users") {
            $CI->db->where("appointment.user_id", $user_id);
        } else {
            $CI->db->where("appointment.doctor_id", $user_id);
        }

        if ($date != null) {
            $CI->db->where($date);
        }

        if (count($filter_data) > 0) {
            foreach ($filter_data as $k => $val) {
                $key[] = str_replace("_dot_", ".", $k);
            }
            $final_output = array_combine($key, $filter_data);
            $CI->db->where($final_output);
        }
        $CI->db->where_in("appointment.status", $status);
        $CI->db->order_by("appointment.id", "DESC");
        $CI->db->select(
                "appointment.id,doctors.med_id AS doctor_med_id, patient_info.med_id,CONCAT(doctors.first_name,' ',doctors.last_name ) AS provider,provider_plan.title,appointment.patient_availability_date,appointment.patient_availability_time,provider_plan.amount,appointment.time_abbreviation,appointment.patient_availability_date_and_time,appointment.created_date"
        );
        $CI->db->from("appointment");
        $CI->db->join("provider_plan", "provider_plan.id = appointment.treatment_provider_plan_id", "INNER");
        $CI->db->join("doctors", "doctors.id = appointment.doctor_id", "INNER");
        $CI->db->join("patient_info", "patient_info.id = appointment.patient_id", "INNER");
        $query = $CI->db->get();
        //echo  $CI->db->last_query();die;
        return (!empty($query->result_array())) ? $query->result_array() : array();
    }

}

if (!function_exists('get_user_total_appointment')) {

    function get_user_total_appointment($user_id, $date = null, $status) {

        $CI = &get_instance();
        if ($date != null) {
            $CI->db->where($date);
        }
        $CI->db->where_in("status", $status);
        $CI->db->group_by("user_id");
        $CI->db->having("user_id", $user_id);
        $CI->db->select("COUNT(user_id) as appointment");
        $past_appointment = $CI->db->get("appointment");
        $row1 = (!empty($past_appointment->row_array())) ? $past_appointment->row_array() : [];
        //echo $CI->db->last_query();die;
        return $row1;
    }

}
/*
 * get all state of USA
 */
if (!function_exists('get_all_state')) {

    function get_all_state() {
        $CI = &get_instance();
        $query = $CI->db->select("state_code,state")->from("state_list")->get();
        return $query->result_array();
    }

}

if (!function_exists('get_device_token')) {

    // 1=>get all device token of users
    // 2=>get all device token of doctor
    // 3=>get specific device token of users
    // 4=>get specific device token of doctor
    function get_device_token($data) {
        $table = "users";
        $ids = '';

        $CI = &get_instance();
        if ($data['notification_type'] == 1) {
            $table = "users";
        } elseif ($data['notification_type'] == 2) {
            $table = "doctors";
        } elseif ($data['notification_type'] == 3 && count(json_decode($data['users_ids']))) {
            $table = "users";
            $ids = json_decode($data['users_ids']);
        } elseif ($data['notification_type'] == 4 && count(json_decode($data['doctor_ids']))) {
            $table = "doctors";
            $ids = json_decode($data['doctor_ids']);
        }
        if ($ids) {
            $CI->db->where_in("id", $ids);
        }
        $query = $CI->db->select("GROUP_CONCAT(`id` SEPARATOR ',') AS id,GROUP_CONCAT(IFNULL(`device_token`,'') SEPARATOR '|||') AS device_token")->from($table)->get();
        //echo $CI->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

}

/*
 * get all state of USA
 */
if (!function_exists('get_all_device_token')) {

    // 1=>get all device token of users
    // 2=>get all device token of doctor
    // 3=>get specific device token of users
    // 4=>get specific device token of doctor
    function get_all_device_token($data) {
        $table = "users";
        $ids = '';

        $CI = &get_instance();
        if ($data['notification_type'] == 1) {
            $table = "users";
        } elseif ($data['notification_type'] == 2) {
            $table = "doctors";
        } elseif ($data['notification_type'] == 3 && count(json_decode($data['users_ids']))) {
            $table = "users";
            $ids = json_decode($data['users_ids']);
        } elseif ($data['notification_type'] == 4 && count(json_decode($data['doctor_ids']))) {
            $table = "doctors";
            $ids = json_decode($data['doctor_ids']);
        }
        if ($ids) {
            $CI->db->where_in("id", $ids);
        }
        $query = $CI->db->select("device_token")->from($table)->get();
        if ($query->num_rows() > 0) {
            $token = array();
            foreach ($query->result_array() as $key => $value) {
                $token[]= $value['device_token'];
            }
            return $token;
        }
    }

}




if (!function_exists('get_email_templates')) {

    function get_email_templates($where = []) {
        $CI = &get_instance();
        if (!empty($where)) {
            $CI->db->where($where);
        }
        $query = $CI->db->select("id,subject,message,email_attechment")->from("email_templates")->get();
        return $query->result_array();
    }

}
if (!function_exists('get_symptoms_id')) {

    function get_symptoms_id($where = []) {
        $CI = &get_instance();
        $CI->db->where($where);
        $CI->db->select("symptom_id")->from("treatment_mapping_symtpoms");
        $query = $CI->db->get();
        //echo $CI->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->row_array() : false;
    }

}
?>