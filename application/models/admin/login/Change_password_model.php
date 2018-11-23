<?php
/**
Auther - Chromeinfotech
Description -- This class use for change password only
Version -- 3.0.0
*/
class Change_password_model extends CI_Model
{
	private $table = 'admin';

	// update the password of admin
	public function updated_password($password)
	{
		$pass = md5($password['current_passsword']);	
		$query = $this->db->get_where($this->table,["password" =>$pass]);
		if ($query->num_rows() > 0) {
			$this->db->update($this->table,["password" =>md5($password['password'])]);
			return true;
		}else{
			return false;
		}
	}
}


?>