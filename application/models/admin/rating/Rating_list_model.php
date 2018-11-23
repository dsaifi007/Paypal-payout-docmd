<?php

class Rating_list_model extends CI_Model {

    protected $doctor_to_user_rating_table = 'doctor_to_user_rating';

    function __construct() {
        parent::__construct();
    }

    function get_order_list($post) {
        $this->_get_order_list_query($post);
        if ($post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return $query->result();
    }

    function _get_order_list_query($post) {
        //dd($post);
        if ($post['doctor_id'] != '' && $post['doctor_id'] != null) {
            // we will show the doctor data
            $this->db->where("rating_table.who_rate='user'");
            $this->db->where("rating_table.rating_given_to_id", $post['doctor_id']);
            $this->db->select("user_patient.user_id AS id,patient_info.first_name,patient_info.last_name, rating_table.rating,rating_table.review,rating_table.created_at");
            $this->db->from("`doctor_user_review_rating` AS rating_table");
            $this->db->join("user_patient", "user_patient.user_id=rating_table.rating_given_by_id", "INNER");
            $this->db->join("patient_info", "patient_info.id=user_patient.patient_id", "INNER");
            
        } else {
            $this->db->where("rating_table.who_rate='doctor'");
            $this->db->where("rating_table.rating_given_to_id", $post['user_id']);
            $this->db->select("doctors.id,doctors.first_name,doctors.last_name, rating_table.rating,rating_table.review,rating_table.created_at");
            $this->db->from("`doctor_user_review_rating` AS rating_table");
            $this->db->join("doctors", "rating_table.rating_given_by_id=doctors.id", "INNER");
        }

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
        $this->db->from("user_avg_rating");
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

}

?>