<?php

class Login_model extends CI_Model {

    protected $login_table = "users";
    protected $patient_info_table = "patient_info";
    protected $errors = ["wrong_credential", "email_not_verified", "phone_not_verified", "user_blocked"];
    protected $array = [];
    protected $profile_data = false;

    function __construct() {
        parent::__construct();
    }

    public function check_email_and_pass_existence($user_data,$device_token) {
        $user_password = md5($user_data['password']);
        $email_query = $this->db->get_where($this->login_table, array('email' => $user_data['email'], 'password' => $user_password));
        if ($email_query->num_rows() == 0) {
            return $this->errors[0];
        } else {
            $user_data = array('is_email_verified' => $this->config->item("email_verified"), "email" => $user_data['email'], 'password' => $user_password);
            $email_verified = $this->db->get_where($this->login_table, $user_data);
            if ($email_verified->num_rows() == 0) {
                return $this->errors[1];
            } else {

                $user_block = array("email" => $user_data['email'], 'password' => $user_password, 'is_email_verified' => $this->config->item("email_verified"), "is_blocked" => 0 );
                $get_user_data = $this->db->get_where($this->login_table, $user_block);
                if ($get_user_data->num_rows() == 0) {
                    return $this->errors[3];
                } else {
                    $access_token = getaccessToken($user_data['email']);
                    $this->db->where("email", $user_data['email']);
                    $this->db->update($this->login_table, ['access_token' => $access_token,'device_token'=>$device_token,"is_loggedin"=>$this->config->item("login"),"last_update_date"=>$this->config->item("date")]);

                    $this->db->where("email", $user_data['email']);
                    $query = $this->db->select("id,email,access_token,phone,is_email_verified,is_phone_verified,is_blocked,is_read_consent_care,cust_id")->from($this->login_table)->get();
                    $row = $query->row_array();

                    $this->db->where("user_id", $row['id']);
                    $check_query = $this->db->select("user_id")->from($this->patient_info_table)->get();
                    if ($check_query->num_rows() > 0) {
                        $this->profile_data = true;
                    }
                    $this->array['is_email_verified'] = ($row['is_email_verified'] == 0) ? false : true;
                    $this->array['is_phone_verified'] = ($row['is_phone_verified'] == 0) ? false : true;
                    $this->array['is_blocked'] = ($row['is_blocked'] == 0 ) ? false : true;
                    $this->array['is_profile_completed'] = $this->profile_data;
                    $this->array['is_read_consent_care'] = ($row['is_read_consent_care'] == 0 ) ? false : true;
                    return array_merge($row, $this->array);
                }
            }
        }
    }

    public function check_email_existence($user_email) {
        $email_query = $this->db->get_where($this->login_table, array('email' => $user_email));
        if ($email_query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function auto_login_model($id) {

        $this->db->where("users.id", $id['user_id']);
        
        $this->db->select("users.id,patient_info.first_name,patient_info.last_name,users.email,users.access_token,users.phone,users.is_email_verified,users.is_phone_verified,users.is_blocked,users.cust_id");
        
        $this->db->from($this->login_table);
        $this->db->join("user_patient","user_patient.user_id=users.id","INNER");
        $this->db->join("patient_info","patient_info.id=user_patient.patient_id","INNER");
        $query = $this->db->get();
       
        $row = $query->row_array();
        if ($row != NULL) {
            $this->array['is_email_verified'] = ($row['is_email_verified'] == 0) ? false : true;
            $this->array['is_phone_verified'] = ($row['is_phone_verified'] == 0) ? false : true;
            $this->array['is_blocked'] = ($row['is_blocked'] == 0 ) ? false : true;
            return array_merge($row, $this->array);
        } else {
            return false;
        }
    }

    // inserting the reset code
    // public function rest_password($email,$email_encoded)
    // {
    // 	//$this->load->library('encrypt');
    // 	$this->db->where("email",$email);
    // 	$query=$this->db->update($this->login_table,["reset_password_code"=>$email_encoded]);
    // 	if ($query) {
    // 		return true;
    // 	}
    // }
    // checking the reset code is exist or not in password form in view
    public function checking_reset_code($email, $reset_code) {
        $updated_pass = $this->db->get_where($this->login_table, array('email' => $email, "reset_password_code" => $reset_code));
        if ($updated_pass->num_rows() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function updating_pass($email, $pass) {
        $this->db->reset_query();
        $new_password['password'] = md5($pass);
        $this->db->where('email', $email);
        $this->db->update($this->login_table, $new_password);
        //-------------------------------------------------------------------------
        $this->db->where("id","(SELECT `patient_id` FROM `user_patient` WHERE user_id= (SELECT `id` FROM `users` WHERE email='".$email."'))",false ,null);
        return $this->db->select("first_name")->from($this->patient_info_table)->get()->row_array();
        //echo $this->db->last_query();die;
    }

}

?>