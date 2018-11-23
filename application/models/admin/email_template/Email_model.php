<?php

class Email_model extends CI_Model {

    protected $email_templates_table = 'email_templates';

    function __construct() {
        parent::__construct();
    }

    // ge the automatic email 
    public function get_all_email_templates() {
        $this->db->where_in("type", ["user", "provider"]);
        $this->db->select("id,email_event,type,subject,message,file_name");
        $this->db->from($this->email_templates_table);
        $query = $this->db->get();
        return $query->result_array();
    }

    // get the manual email template
    public function get_all_manual_email_templates($filter) {
        if (count($filter) > 0 && !empty($filter)) {
            $this->db->where_in("type", $filter);
        } else {
            $this->db->where_in("type", ["accept"]);
        }
        $this->db->where("email_event IS NULL");
        $this->db->select("id,subject,message,file_name");
        $this->db->from($this->email_templates_table);
        $query = $this->db->get();
        return $query->result_array();
    }

    // add/update the new record of pharmacy
    public function add_update_manual_emails($insertdata, $id = null) {
        unset($insertdata['save']);      
        if ($id != null && $id != '') {
            $this->db->where("id", $id);
            $this->db->update("email_templates", $insertdata);         
            return true;
        } else {
            unset($insertdata['id']);
            $this->db->insert("email_templates", $insertdata);
            return $this->db->insert_id();
        }
    }

    public function update_automatic_email_template($data) {
        $this->db->where("id", $data['id']);
        $this->db->update("email_templates", $data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    function edit_email_detail($id) {
        $this->db->where("id",$id);
        $this->db->select("id,subject,message,file_name,type");
        $this->db->from($this->email_templates_table);
        $query = $this->db->get();
        return $query->row_array();
    }
}

?>