<?php

class Doctor_login_model extends CI_Model {

    protected $login_table = "doctors";
    protected $patient_info_table = "patient_info";
    protected $errors = ["wrong_credential", "email_not_verified", "phone_not_verified", "user_blocked"];
    protected $array = [];
    protected $profile_completness = false;

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
                
                        // update access token
                $access_token = getaccessToken($user_data['email']);
                $this->db->where("email", $user_data['email']);
                $this->db->update($this->login_table, ['access_token' => $access_token,"device_token"=>$device_token,"last_update"=>$this->config->item("date")]);

                        // get the data of docter for API
                $this->db->where("email", $user_data['email']);
                $query = $this->db->select("id,email,first_name,last_name,date_of_birth,gender,access_token,phone,is_email_verified,is_phone_verified,is_blocked")->from($this->login_table)->get();
                $row = $query->row_array();
                
                        // check profile complete or not
                if ($row['gender'] != "" && $row['date_of_birth'] != "" && $row['gender'] != NULL) {
                    $this->profile_completness = true;
                }
                unset($row['gender']);
                unset($row['date_of_birth']);
                $this->array['is_email_verified'] = ($row['is_email_verified'] == 0) ? false : true;
                $this->array['is_phone_verified'] = ($row['is_phone_verified'] == 0) ? false : true;
                $this->array['is_blocked'] = ($row['is_blocked'] == 0 ) ? false : true;
                $this->array['is_profile_completed'] = $this->profile_completness;
                return array_merge($row, $this->array);
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

    public function auto_doctor_login_model($id) {
        
        $this->db->where("id", $id['doctor_id']);
        $query = $this->db->select("id,email,first_name,last_name,access_token,phone,is_email_verified,is_phone_verified,is_blocked")
                        ->from($this->login_table)->get();
        $row = $query->row_array();
        if ($row != NULL || $row != '') {
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

    public function update_pass($email, $pass) {
        $this->db->reset_query();
        $new_password['password'] = md5($pass);
        $this->db->where('email', $email);
        $this->db->update($this->login_table, $new_password);

        //------------------------------------------------
        $this->db->where("email",$email);
        return $this->db->select("first_name")->from($this->login_table)->get()->row_array();
    }

}

?>