<?php

/*
  class name : Promocode Model
 */

class Promocode_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function promocode_model($data) {
        $this->db->where("expiry >=", date("Y-m-d"));
        $this->db->where("LOWER(code) = LOWER('" . $data['promocode'] . "')");
        $this->db->select("id,code,discount")->from("promocode");
        $query1 = $this->db->get();
        //echo $this->db->last_query();die;
        if ($query1->num_rows() > 0) {
            $this->db->where("user_promocode.user_id", $data["user_id"]);
            $this->db->where("LOWER(promocode.code) = LOWER('" . $data['promocode'] . "')");
            $this->db->select("user_promocode.user_id,promocode.id");
            $this->db->from("user_promocode");
            $this->db->join("promocode", "promocode.id = user_promocode.promocode_id");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                return true;
            } else {
                $row = $query1->row_array();
                $this->db->insert("user_promocode", ["user_id" => $data['user_id'], "promocode_id" => $row['id']]);
                #----
                $this->db->where("id", $data['treatment_provider_plan_id']);
                $this->db->select("amount")->from("provider_plan");
                $q = $this->db->get();
                $amount = $q->row_array();
                $row['total_amount'] = (string) ($amount['amount'] - (((int) $amount['amount'] * (int) $row['discount']) / 100));
                //unset($row['discount']);
                return $row;
            }
        } else {
            return "expired";
        }
    }

    function listPromocode_model($param) {
        $this->db->where("expiry >=", date("Y-m-d"));
        $this->db->select("id,code")->from("promocode");
        $query1 = $this->db->get();
        return ($query1->num_rows() > 0) ? $query1->row_array() : false;
    }

}

?>