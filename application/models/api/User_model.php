<?php

/*
  |-------------------------------------------------------------------------------
  |  All Users information get/set/save/delete
  |-------------------------------------------------------------------------------
 */

  class User_model extends CI_Model {

    private $users_table = "users";
    private $patient_info_table = "patient_info";
    private $health_insurance_table = "health_insurance";
    private $address_table = "address";
    private $user_patient_table = "user_patient";
    private $user_address_map_table = "patient_address";
    private $medical_histroy = "user_medical_profile";
    private $array = [];
    private $is_boolean;
    private $otp_table = "user_otp";
    private $plus_sign = "+";

    function __construct() {
        parent::__construct();
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert the user info and get particular data of inserted users
      |-------------------------------------------------------------------------------------------------------------------------------
     */

      public function create_user_account_data_insert($post_data = []) {
        $this->db->where("email", $post_data['email'])->or_where("phone", $post_data['phone']);
        $query = $this->db->select("email,phone")->from($this->users_table)->get();
        if ($query->num_rows() == 0) {
            $post_data['password'] = md5($post_data['password']);
            $post_data['access_token'] = getaccessToken();
            $this->db->insert($this->users_table, $post_data);
            $id = $this->db->insert_id();
            $this->db->where("id", $id);
            $query = $this->db->select("id,email,access_token,phone,is_email_verified,is_phone_verified,is_blocked")
            ->from($this->users_table)->get();
            $row = $query->row_array();
            $this->array['is_email_verified'] = ($row['is_email_verified'] == 0) ? false : true;
            $this->array['is_phone_verified'] = ($row['is_phone_verified'] == 0) ? false : true;
            $this->array['is_blocked'] = ($row['is_blocked'] == 0 ) ? false : true;
            return array_merge($row, $this->array);
        } else {
            return false;
        }
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will update password based on user id
      |-------------------------------------------------------------------------------------------------------------------------------
     */

      public function update_user_password($user_data) {
        $this->db->reset_query();
        $query = $this->db->get_where($this->users_table, ['id' => $user_data['userid']]);
        if ($query->num_rows() > 0) {
            $this->user_pass['password'] = md5($user_data['password']);
            $this->db->where('id', $user_data['userid']);
            $this->db->update($this->users_table, $this->user_pass);
            $this->is_boolean = TRUE;
        } else {
            $this->is_boolean = FALSE;
        }
        return $this->is_boolean;
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert the basic information of users
      |-------------------------------------------------------------------------------------------------------------------------------
     */

      public function signup_data_inserted($patient_info, $patient_id) {
        if ($patient_id != '') {
            unset($patient_info['created_date']);
            unset($patient_info['user_id']);
            $this->db->where("id", $patient_id);
            $this->db->update($this->patient_info_table, $patient_info);
            return true;
        } else {
            if (count($patient_info) > 0) {
                $this->db->reset_query();
                $this->db->insert($this->patient_info_table, $patient_info);
                return $this->db->insert_id();
            }
        }
    }

    public function user_medical_profile_update($data, $ptnt_id) {
        $this->db->reset_query();
        $query = $this->db->get_where($this->medical_histroy, ['patient_id' => $ptnt_id]);
        if ($query->num_rows() > 0) {
            unset($data['created_date']);
            unset($data['patient_id']);
            $this->db->where("patient_id", $ptnt_id);
            $query = $this->db->update($this->medical_histroy, $data);
        } else {
            $this->db->insert($this->medical_histroy, $data);
        }
        return true;
    }

    public function user_medical_profile_add($data) {
        $this->db->insert($this->medical_histroy, $data);
        return $this->db->insert_id();
    }

    // add and update date
    public function address_data_inserted($add_data, $patient_id) {
        if ($patient_id != '') {
            $query = $this->db->query("SELECT address_id FROM " . $this->user_address_map_table . " WHERE patient_id=" . $patient_id);
            if ($query->num_rows() == 0) {
                return false;
            } else {
                $add_id = $query->row_array();
                $this->db->where("id", $add_id['address_id']);
                $this->db->update($this->address_table, $add_data);
                return true;
            }
        } else {
            if (count($add_data) > 0) {
                $this->db->insert($this->address_table, $add_data);
                return $this->db->insert_id();
            }
        }
    }

    public function user_address_map($add_id, $p_id, $state) {
        $this->db->reset_query();
        $insert_data = ['patient_id' => $p_id, 'address_id' => $add_id];
        $this->db->insert($this->user_address_map_table, $insert_data);

        // update MED_ID
        $med_id = strtoupper(substr($state, 0, 2)) . '-' . $p_id;
        $this->db->where("id", $p_id);
        $this->db->update("patient_info", ['med_id' => $med_id]);
        return true;
    }

    public function user_patient_map($pid, $uid) {
        $this->db->reset_query();
        $query = $this->db->get_where($this->user_patient_table, ['user_id' => $uid]);
        if ($query->num_rows() == 0) {
            $insert_data = ['user_id' => $uid, 'patient_id' => $pid];
            $this->db->insert($this->user_patient_table, $insert_data);
        }
    }

    public function get_user_profile_only($user_id) {
        $select_colum = "c.id,c.med_id,c.first_name,c.last_name,c.date_of_birth,floor(datediff(curdate(),c.date_of_birth) / 365.25) AS age,c.profile_url AS profile_image_url,c.gender,d.address, d.city,d.state ,d.zip_code, c.provider, c.member_id,c.ins_group,f.medications ,f.allergies,f.past_medical_history ,f.social_history ,f.family_history";
        $this->db->select($select_colum)
        ->from("$this->users_table as a")
        ->join("$this->user_patient_table as g", "g.user_id = a.id", "INNER")
        ->join("$this->patient_info_table as c", "g.patient_id = c.id", "INNER")
        ->join("$this->user_address_map_table as b", "b.patient_id = c.id", "LEFT")
        ->join("$this->address_table as d", "d.id = b.address_id", "LEFT")
        ->join("$this->medical_histroy as f", "f.patient_id = c.id", "LEFT")
        ->where("g.user_id", $user_id);
        $query = $this->db->get();
        $row = $query->row_array();
        return (!empty($query->row_array())) ? $query->row_array() : false;
    }

    /* -------------------------------------------------------------------------------------
      | This function we will be use when we need a information about user as well as all
      | patient  corresponding to the user, user id will get from above function          get_user_profile_only()
      --------------------------------------------------------------------------------------
     */

      public function get_all_patients_model($user_id) {

        $user_data = $this->get_user_profile_only($user_id);

        $select_field = "c.id,c.med_id,IF((a.patient_id = c.id) ,'true','false') AS is_selected,c.first_name,c.last_name,c.date_of_birth,floor(datediff(curdate(),c.date_of_birth) / 365.25) AS age,c.profile_url AS profile_image_url,c.gender,d.address , d.city  ,d.state,d.zip_code ,c.provider,c.member_id,c.ins_group,f.medications ,f.allergies,f.past_medical_history ,f.social_history,f.family_history";
        $this->db->select($select_field)
        ->from("$this->patient_info_table as c")
        ->join("$this->user_patient_table as a", "c.user_id = a.user_id", "LEFT")
        ->join("$this->user_address_map_table as b", "b.patient_id = c.id", "LEFT")
        ->join("$this->address_table as d", "d.id = b.address_id", "LEFT")
        ->join("$this->medical_histroy as f", "f.patient_id = c.id", "LEFT")
        ->where(["a.user_id" => $user_id, "c.is_deleted" => 0])->order_by("c.id", "ASC");
        $query = $this->db->get();
        return $query->result_array();
    }

    // add verfication code
    public function email_verification_code($email, $email_encoded) {
        //$this->load->library('encrypt');
        $this->db->where("email", $email);
        $query = $this->db->update($this->users_table, ["email_verification_code" => $email_encoded]);
        if ($query) {
            return true;
        }
    }

    // check email and phone number
    public function check_email_phone_existance($em, $phone) {

        $this->db->where("email", $em)->or_where("phone", $this->plus_sign . $phone);
        $query = $this->db->select("email,phone")->from($this->users_table)->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // when email verfied by email
    public function email_verified($email) {
        $query = $this->db->get_where($this->users_table, ['email' => $email]);
        if ($query->num_rows() > 0) {
            $this->db->where("email", $email);
            $this->db->update($this->users_table, ["is_email_verified" => $this->config->item("email_verified")]);
            return true;
        } else {
            return false;
        }
    }

    // check the health plan existance
    public function unique_health_plan_model($health_plan) {
        $query = $this->db->get_where($this->health_insurance_table, ['health_plan' => $health_plan]);
        return ($query->num_rows() > 0) ? true : false;
    }

    public function user_image_update($file_name, $file_url, $user_id) {
        $this->db->where("id", $user_id);
        $this->db->update($this->patient_info_table, ["profile_url" => $file_url, "profile_image" => $file_name]);
        // get patient profile image url
        $this->db->where("id", $user_id);
        $query = $this->db->select("profile_url")
        ->from($this->patient_info_table)->get();
        return $query->row_array();
    }

    public function store_new_otp($otp_Data) {
        $this->db->insert($this->otp_table, $otp_Data);
    }

    public function get_otp($otp_data) {
        $this->db->where($otp_data);
        $query = $this->db->select("expire_time")
        ->from($this->otp_table)->get();
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    public function update_otp($user_id, $new_otp, $expire_time) {
        $this->db->where("user_id", $user_id);
        $this->db->update($this->otp_table, ["expire_time" => $expire_time, "otp_number" => $new_otp]);

        $this->db->where("user_id", $user_id);
        $query = $this->db->select("otp_number")
        ->from($this->otp_table)->get();
        return $query->row();
    }

    public function delete_otp($user_id) {
        $this->db->where("user_id", $user_id);
        $this->db->delete($this->otp_table);
    }

    public function check_phone_existance($user_id) {
        $this->db->where("id", $user_id);
        $query = $this->db->select("phone")->from($this->users_table)->get();
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    public function update_phone_status($user_id) {
        $this->db->where("id", $user_id);
        $this->db->update($this->users_table, ["is_phone_verified" => $this->config->item("phone_verified")]);
    }

    public function get_userid($email, $phone) {
        $query = $this->db->get_where($this->users_table, ['email' => $email, "phone" => $phone]);
        $row = $query->row_array();
        if (count($row) > 0) {
            return $row['id'];
        }
    }

    public function change_email($data, $token) {
        if ($data['type'] == "user") {
            $table = "users";
        } else {
            $table = "doctors";
        }
        $query = $this->db->get_where($table, ["access_token" => $token, "password" => md5($data['password'])]);
        if ($query->num_rows() > 0) {
            $this->db->where("access_token", $token);
            $this->db->where("password", md5($data['password']));
            $this->db->update($table, ['email' => $data['new_email'], "is_email_verified" => 0]);
            return true;
        } else {
            return false;
        }
    }

    public function change_password($data, $token) {
        if ($data['type'] == "user") {
            $table = "users";
        } else {
            $table = "doctors";
        }

        $query = $this->db->get_where($table, ['password' => md5($data['old_password']), "access_token" => $token]);
        if ($query->num_rows() > 0) {
            $this->db->where("access_token", $token);
            $this->db->update($table, ["password" => md5($data['new_password'])]);
            return true;
        } else {
            return false;
        }
    }

    public function is_user_loggedin_model($userdata) {
        $table = ($userdata['type'] == "user") ? "users" : "doctors";
        $this->db->where("id", $userdata['id']);
        $this->db->update($table, ['is_loggedin' => $this->config->item("logout"),"device_token"=>NULL]);
        return true;
    }

    public function only_patient_delete($data) {
        $query = $this->db->get_where("user_patient", ["user_id" => $data['user_id'], "patient_id" => $data['patient_id']]);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            $this->db->where("id", $data['patient_id']);
            $this->db->where("user_id", $data['user_id']);
            $this->db->update("patient_info", ["is_deleted" => 1]);
            return true;
        }
    }

    public function makeUserToPatient_model($data) {
        $query = $this->db->get_where("user_patient", ["user_id" => $data['user_id']]);
        if ($query->num_rows() > 0) {
            $this->db->where("user_id", $data['user_id']);
            $this->db->update("user_patient", ["patient_id" => $data['patient_id']]);

            return true;
        } else {
            return false;
        }
    }
    // check email and phone number
    public function getUserInfo($user_id) {
        $this->db->where("id", $user_id);
        $query = $this->db->select("email,phone")->from($this->users_table)->get();
        return ($query->num_rows() > 0)?$query->row_array():false;
    }
    public function updateCustomerId($user_id,$cust_id) {
        $this->db->where("id", $user_id);
        $this->db->update($this->users_table, ["cust_id" => $cust_id]);
        return true;
    }
}

?>