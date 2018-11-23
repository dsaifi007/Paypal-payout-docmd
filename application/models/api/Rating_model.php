<?php

/*
  |-------------------------------------------------------------------------------
  |  All Users information get/set/save/delete
  |-------------------------------------------------------------------------------
 */

class Rating_model extends CI_Model {

    protected $app_data = array();

    function __construct() {
        parent::__construct();
    }

//    public function add_rating_model($input_data) {
//        $input_data['created_at'] = $this->config->item("date");
//       
//        $this->db->trans_start();
//        if ($input_data['type'] == "user") {
//            // rating given by the user to doctor and 
//            // calculate the avg rating of doctor add in doctor_avg_rating table
//            unset($input_data['type']);
//            $this->db->insert("user_to_doctor_rating", $input_data);
//
//            $query = $this->db->get_where("doctor_avg_rating", ["doctor_id" => $input_data['doctor_id']]);
//
//            if ($query->num_rows() > 0) {
//                $this->db->query("UPDATE `doctor_avg_rating`,(SELECT ROUND(AVG(`rating`),2) AS avgrating FROM `user_to_doctor_rating` GROUP BY `doctor_id` HAVING doctor_id='" . $input_data['doctor_id'] . "') AS total_rating SET avg_rating =total_rating.avgrating
//                    WHERE `doctor_id` = '" . $input_data['doctor_id'] . "'");
//            } else {
//                $this->db->insert("doctor_avg_rating", ['doctor_id' => $input_data['doctor_id'], "avg_rating" => $input_data['rating']]);
//            }
//        } else {
//            unset($input_data['type']);
//            $this->db->insert("doctor_to_user_rating", $input_data);
//            $query = $this->db->get_where("user_avg_rating", ["user_id" => $input_data['user_id']]);
//
//            if ($query->num_rows() > 0) {
//                $this->db->query("UPDATE `user_avg_rating`,(SELECT ROUND(AVG(`rating`),2) AS avgrating FROM `doctor_to_user_rating` GROUP BY `user_id` HAVING user_id='" . $input_data['user_id'] . "') AS total_rating SET avg_rating =total_rating.avgrating
//                    WHERE `user_id` = '" . $input_data['user_id'] . "'");
//            } else {
//                $this->db->insert("user_avg_rating", ['user_id' => $input_data['user_id'], "avg_rating" => $input_data['rating']]);
//            }
//        }
//        $this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE) {
//            $this->db->trans_rollback();
//            return false;
//        } else {
//            $this->db->trans_commit();
//            return true;
//        }
//    }

    public function add_rating_model($input_data) {

        $input_data['created_at'] = $this->config->item("date");
        $this->db->trans_start();
        $app_rating = $input_data;
        unset($input_data['app_rating']);
        if ($input_data['who_rate'] == "user") {
            $this->db->insert("doctor_user_review_rating", $input_data);
            // user given the rating to the doctor
            $this->db->query("UPDATE `doctor_avg_rating`,
                    (SELECT ROUND(AVG(`rating`),2) AS avgrating FROM `doctor_user_review_rating` where who_rate ='user' AND rating_given_to_id='" . $input_data['rating_given_to_id'] . "' GROUP BY `rating_given_to_id`) AS total_rating SET avg_rating =total_rating.avgrating
                    WHERE `doctor_id` = '" . $input_data['rating_given_to_id'] . "'");
            $this->db->insert("app_rating", [
                "user_id" => $app_rating['rating_given_by_id'],
                "created_at" => $this->config->item("date"),
                "rating" => $app_rating['app_rating']
            ]);
        } else {
            unset($input_data['review']);
            $this->db->insert("doctor_user_review_rating", $input_data);
            // doctor given the rating to the user        
            $query = $this->db->get_where("user_avg_rating", ["user_id" => $input_data['rating_given_to_id']]);
            if ($query->num_rows() > 0) {
                $this->db->query("UPDATE `user_avg_rating`,(SELECT ROUND(AVG(`rating`),2) AS avgrating FROM `doctor_user_review_rating` where who_rate ='doctor' AND rating_given_to_id='" . $input_data['rating_given_to_id'] . "'  GROUP BY `rating_given_to_id`) AS total_rating SET avg_rating =total_rating.avgrating
                    WHERE `user_id` = '" . $input_data['rating_given_to_id'] . "'");
                //echo $this->db->last_query();die;
            } else {
                $this->db->insert("user_avg_rating", ['user_id' => $input_data['rating_given_to_id'], "avg_rating" => $input_data['rating']]);
            }
            $this->db->insert("app_rating", [
                "doctor_id" => $app_rating['rating_given_by_id'],
                "created_at" => $this->config->item("date"),
                "rating" => $app_rating['app_rating'],
                "review" => $app_rating['review'],
            ]);
        }
        //return true;
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function add_app_rating_model($data) {
        unset($data['type']);
        $data['created_at'] = $this->config->item("date");
        $this->db->insert("app_rating", $data);
    }

}

?>