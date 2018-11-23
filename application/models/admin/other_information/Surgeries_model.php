<?php

class surgeries_model extends CI_Model {

    protected $surgeries_table = 'surgeries';

    function __construct() {
        parent::__construct();
    }

    public function get_all_surgeries_list() {
        $this->db->order_by("id", "DESC");
        $this->db->where("is_deleted", 0);
        $this->db->select("*");
        $this->db->from($this->surgeries_table);
        $query = $this->db->get();
        return $query->result_array();
    }

    // add/update the new record of pharmacy
    public function add_and_update_surgeries($insertdata, $id = null) {
        unset($insertdata['surgeries_submit']);
        if ($id != null) {
            //dd($insertdata);
            $this->db->where("id", $id);
            $this->db->update($this->surgeries_table, $insertdata);
            return true;
        } else {
            $this->db->insert($this->surgeries_table, $insertdata);
            return $this->db->insert_id();
        }
    }

    //Get Pharmacy Info
    public function get_surgeries_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("*")->from($this->surgeries_table)->get();
        return $query->row();
    }

}

?>