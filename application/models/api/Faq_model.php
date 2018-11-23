<?php

class Faq_model extends CI_Model
{
	protected $faq_table="faq"; 

	function __construct()
	{
		parent::__construct();
	}
	public function get_allrecords()
	{
		$this->db->select("*")->from( $this->faq_table );
		$query=$this->db->get();
		return (count( $query->result_array())>0 ) ? $query->result_array():false;
	}
}

?>