<?php
/**
Auther - Chromeinfotech
Description -- This class use for handle the admin login/forgot password
Version -- 3.0.0
*/
class Admin_model extends CI_Model
{
	private $login_table = 'admin';

	/*
	----------------------------------------------------------------------------------
	|	Work -- check email or (email && password) of admin login/forgot password
	|	@return -- true or false
	----------------------------------------------------------------------------------
	*/
	public function check_email_password($admin_info)
	{
		if (isset($admin_info['password'])) {
			$match_field = [
				'email' => $admin_info['email'],
				'password' => md5($admin_info['password'])
			];
		}else{
			$match_field = array('email' => $admin_info);
		}
		$query = $this->db->get_where($this->login_table, $match_field);
		return ($query->num_rows() > 0 ) ? $query->row_array() : false;
	}

	/*
	-----------------------------------------------------------------------------------
	|  Work -- Update Password of Admin based on Email 
	|  @return -- true or false
	-----------------------------------------------------------------------------------
	*/
	public function updating_pass($email, $pass) {
		$this->db->reset_query();
		$new_password['password'] = md5($pass);
		$this->db->where('email', $email);
		$this->db->update($this->login_table, $new_password);
	}
}
?>