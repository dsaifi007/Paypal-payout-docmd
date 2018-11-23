<?php

class Promocode_model extends CI_Model {

    protected $promocode_table = 'promocode';
    protected $day_data = [];
    protected $day_data_json = [];

    function __construct() {
        parent::__construct();
    }

    function get_order_list($post) {
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function _get_order_list_query($post) {


        $this->db->select("id,name,code,discount,expiry,description,created_date,is_sent");
        $this->db->from($this->promocode_table);


        if (!empty($post['where'])) {
            $this->db->where($post['where']);
        }

        /* foreach ($post['where_in'] as $index => $value){

          $this->db->where_in($index, $value);
          } */

        if (!empty($post['search_value'])) {
            $like = "";
            foreach ($post['column_search'] as $key => $item) { // loop column 
                // if datatable send POST for search
                if ($key === 0) { // first loop
                    $like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                } else {
                    $like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
                }
            }
            $like .= ") ";

            $this->db->where($like, null, false);
        }

        if (!empty($post['order'])) { // here order processing
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all($post) {
        $this->_count_all_bb_order($post);
        $query = $this->db->count_all_results();
        return $query;
    }

    public function _count_all_bb_order($post) {
        $this->db->from($this->promocode_table);
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_promocode($updatetdata) {
        $this->db->trans_start();
        $this->db->where("id", $updatetdata['edit_id']);
        unset($updatetdata['edit_id']);
        $this->db->update("promocode", $updatetdata);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    // add/update the new record of pharmacy
    public function add_promocode($insertdata) {
        $this->db->trans_start();
        unset($insertdata['edit_id']);
        $insertdata['created_date'] = $this->config->item("date");
        $this->db->insert("promocode", $insertdata);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    //Get Pharmacy Info
    public function get_promocode_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("id,name,code,discount,expiry,description")
                        ->from($this->promocode_table)->get();
        return $query->row();
    }

    public function get_all_users() {
        $this->db->select("patient_info.id,CONCAT(patient_info.first_name,' ',patient_info.last_name) AS user_name,users.email")->from("user_patient");
        $this->db->join("patient_info", "user_patient.patient_id=patient_info.id", "INNER");
        $this->db->join("users", "users.id=user_patient.user_id", "INNER");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_all_users_emails($flag = null) {
        if ($flag != null) {
            $sub_query = "(id NOT IN('SELECT DISTINCT(user_id) FROM appointment'))";
            $this->db->where($sub_query);
        }
        $sql = $this->db->select("GROUP_CONCAT(email) AS email")->from("users")->get();

        $result = $sql->result_array();
        if ($result[0]['email']) {
            $emails = array_map("trim", explode(",", $result[0]['email']));
            return $emails;
        } else {
            return array();
        }
    }
    public function update_is_sent_status($id) {
        $this->db->where("id",$id);
        $this->db->update("promocode",["is_sent"=>1]);
        return true;
    }
    public function get_exipre_date($id) {
        $this->db->where("id",$id);
        $query = $this->db->select("code,expiry,created_date")->from("promocode")->get();
        return $query->row_array();
    }
}

?>