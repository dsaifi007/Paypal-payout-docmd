<?php

class Rating_model extends CI_Model {

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
        // if the dropdown the change
        if ($post['id'] == '2' && isset($post['id'])) {
            $this->db->where("rating_table.who_rate='user'");
            $this->db->group_by("rating_table.rating_given_to_id");
            $this->db->select(" doctors.id AS doctor_id,
                                `doctors`.`med_id`,
                                `doctors`.`email`,
                                `doctors`.`first_name`,
                                `doctors`.`date_of_birth`,
                                `doctors`.`last_name`,
                                `doctors`.`gender`,
                                `doctors`.`phone`,
                                doctor_avg_rating.avg_rating AS avg_rating,
                                `rating_table`.`review`");
            $this->db->from("`doctor_user_review_rating` AS rating_table");
            $this->db->join("doctors", "doctors.id = rating_table.`rating_given_to_id`", "INNER");
            $this->db->join("doctor_avg_rating", "doctor_avg_rating.doctor_id = rating_table.`rating_given_to_id`", "INNER");
        } else {
//            $this->db->select("users.email,
//                            users.phone,
//                            patient_info.med_id,
//                            patient_info.first_name,
//                            patient_info.gender,
//                            patient_info.date_of_birth,
//                              user_avg_rating.user_id,
//                              user_patient.patient_id,
//                              user_avg_rating.avg_rating");
//            $this->db->from("user_avg_rating");
//            $this->db->join("user_patient", "user_patient.user_id = user_avg_rating.user_id", "INNER");
//            $this->db->join("users", "users.id=user_patient.user_id", "INNER");
//            $this->db->join("patient_info", "patient_info.id=user_patient.patient_id", "INNER");
            //$this->db->where("rating_table.who_rate='doctor'");
            //$this->db->group_by("rating_table.rating_given_to_id");
            $this->db->select("users.id,
                                    `users`.`email`,
                                    `users`.`phone`,
                                    `patient_info`.`med_id`,
                                    `patient_info`.`first_name`,
                                    `patient_info`.`gender`,
                                    `patient_info`.`date_of_birth`,                                 
                                    rating_table.avg_rating AS avg_rating");
            $this->db->from("user_avg_rating AS rating_table");
            $this->db->join("user_patient", "user_patient.user_id = rating_table.user_id", "INNER");
            $this->db->join("users", "users.id=user_patient.user_id", "INNER");
            $this->db->join("patient_info", "patient_info.id=user_patient.patient_id", "INNER");
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
    function update_avg_rating_model($data) {
        //dd($data);
        if($data['user_id']){
            $this->db->where("user_id",$data['user_id']);
            $this->db->update("user_avg_rating",['avg_rating'=>$data['avg_rating']]);
        }else{
            $this->db->where("doctor_id",$data['doctor_id']);
            $this->db->update("doctor_avg_rating",['avg_rating'=>$data['avg_rating']]);
            //echo $this->db->last_query();die;
        }
    }
}

?>