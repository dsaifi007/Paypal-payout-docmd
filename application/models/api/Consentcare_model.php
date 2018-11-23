<?php

class Consentcare_model extends CI_Model
{
	protected $consent_care_table="consent_care"; 
	protected $users="users"; 

	function __construct()
	{
		parent::__construct();
	}
	public function get_alltext($language_name)
	{
                if($language_name == "spn")
                {
                    $this->db->select("consent_spn_text as consent_care");
                }
                else {
                    $this->db->select("consent_eng_text as consent_care");
                }		
                $this->db->from( $this->consent_care_table );
		$query=$this->db->get();
		return (count( $query->row_array())>0 ) ? $query->row_array():false;
	}
        function read_consent_care_update($uid) {
            $this->db->where('id', $uid);
            $this->db->update($this->users, ["is_read_consent_care"=>1]);
        }
}

?>