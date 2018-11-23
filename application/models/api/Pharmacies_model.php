<?php

class Pharmacies_model extends CI_Model {

    protected $pharmacies = "pharmacies";
    protected $pharmacy_result = [];

    function __construct() {
        parent::__construct();
    }

    public function get_allrecords() {
        $this->db->where("is_blocked", '0');
        $this->db->select("id,pharmacy_name,pharmacy_image_url,CONCAT(latitude,'||',longtitude) as location")->from($this->pharmacies);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return (count($query->result_array()) > 0 ) ? $query->result_array() : false;
    }

    public function add_pharmacy_model($data) {

        $pharmacy_data = $data;

        unset($pharmacy_data['user_id']);
        unset($pharmacy_data['id']);
        $pharmacy_data['pharmacy_timing'] = (isset($pharmacy_data['pharmacy_timing']))?json_encode($pharmacy_data['pharmacy_timing'],true):'';

       
        
        $this->db->trans_start();
        $this->db->insert("pharmacies", $pharmacy_data);
        $last_insert_id = $this->db->insert_id();

        if ($data['type'] == "user") {
            $this->db->insert("user_pharmacies", ["user_id" => $data['id'], "pharmacies_id" => $last_insert_id, "created_by" => $data['id']]);
        } else {
            $this->db->insert("doctor_user_pharmacies_mapping", [
                "doctor_id" => $data['id'],
                "pharmacy_id" => $last_insert_id,
                "user_id" => $data['user_id'],
                "created_by" => $data['id']
            ]);
        }
        $pharmacy_time_mapping = (isset($pharmacy_data['pharmacy_timing']))?json_decode($pharmacy_data['pharmacy_timing'],true):'';
          
        if(count($pharmacy_time_mapping)>0 && !empty($pharmacy_time_mapping) && isset($pharmacy_time_mapping)){
            $pharmacy_time=array();
            foreach ($pharmacy_time_mapping as $key => $value) {
                $pharmacy_time[$key]['pharmacy_id'] = $last_insert_id;
                $pharmacy_time[$key]["open_time"] = $pharmacy_time_mapping[$key]['time']['from'];
                $pharmacy_time[$key]["close_time"] = $pharmacy_time_mapping[$key]['time']['to'];
                $pharmacy_time[$key]["day"] = $pharmacy_time_mapping[$key]['day'];
            }
            // insert  pharmacy timing in pharmacies_timing_mapping(add) table
            $this->db->insert_batch("pharmacies_timing_mapping", $pharmacy_time);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            // Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            // Everything is Perfect. 
            // Committing data to the database.
            $this->db->trans_commit();
            return @$last_insert_id;
        }
    }
    public function add_pharmacy_third_party_model($data) {

        $query = $this->db->get_where("pharmacies", ["place_id" => $data['place_id']]);

        if ($query->num_rows() == 0) {
            $pharmacy_data = $data;

            unset($pharmacy_data['user_id']);
            unset($pharmacy_data['id']);
            unset($pharmacy_data['is_primary']);

            $this->db->trans_start();
            $this->db->insert("pharmacies", $pharmacy_data);
            $last_insert_id = $this->db->insert_id();

            if ($data['type'] == "user") {
                $status = 0;
                if (isset($data['is_primary'])) {
                    $q = $this->db->get_where("user_pharmacies", ["user_id" => $data['id'], "is_primary" => 1]);
                    if ($q->num_rows() > 0) {
                        $this->db->where(["user_id" => $data['id'],"pharmacies_id"=>$q->row_array()["pharmacies_id"]]);
                        $this->db->update("user_pharmacies", ['is_primary' => 0]);
                    } 
                    $status =1;
                }
                $this->db->insert("user_pharmacies", ["is_primary"=>$status,"user_id" => $data['id'], "pharmacies_id" => $last_insert_id, "created_by" => $data['id']]);
            } else {
                $this->db->insert("doctor_user_pharmacies_mapping", [
                    "doctor_id" => $data['id'],
                    "pharmacy_id" => $last_insert_id,
                    "user_id" => $data['user_id'],
                    "created_by" => $data['id']
                ]);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                // Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                // Everything is Perfect. 
                // Committing data to the database.
                $this->db->trans_commit();
                return @$last_insert_id;
            }
        } else {
            $row = $query->row_array();

            $pharmacy_data = $data;

            unset($pharmacy_data['user_id']);
            unset($pharmacy_data['id']);
            unset($pharmacy_data['is_primary']);

             if ($data['type'] == "doctor") {
                 $doctor_data =  ["doctor_id" => $data['id'],"pharmacy_id" =>$row['id'],"user_id" =>$data['user_id']
                     ];
                 $q = $this->db->get_where("doctor_user_pharmacies_mapping",$doctor_data);
                 if($q->num_rows() == 0){
                    $this->db->insert("doctor_user_pharmacies_mapping", $doctor_data);
                 }
             }else{
                $user_pharmacy = ["user_id" => $data['id'],"pharmacies_id"=>$row['id']];
                $q1 = $this->db->get_where("user_pharmacies", $user_pharmacy);
                if($q1->num_rows() == 0){
                    $status = 0;
                    if (isset($data['is_primary'])) {
                        $q = $this->db->get_where("user_pharmacies", ["user_id" => $data['id'], "is_primary" => 1]);
                        if ($q->num_rows() > 0) {
                            $this->db->where(["user_id" => $data['id'],"pharmacies_id"=>$row['id']]);
                            $this->db->update("user_pharmacies", ['is_primary' => 0]);
                        } 
                    $status =1;
                }
                $this->db->insert("user_pharmacies", ["is_primary"=>$status,"user_id" => $data['id'], "pharmacies_id" => $row['id'], "created_by" => $data['id']]);
                }
             }

            $this->db->trans_start();
            $this->db->where("place_id", $pharmacy_data['place_id']);
            $this->db->update("pharmacies", $pharmacy_data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                // Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                // Everything is Perfect. 
                // Committing data to the database.
                $this->db->trans_commit();
                return $row['id'];
            }
        }
    }
    public function update_pharmacy_model($data) {

        $pharmacy_data = $data;
        $pharmacy_data['pharmacy_timing'] = (isset($pharmacy_data['pharmacy_timing'])) ? json_encode($pharmacy_data['pharmacy_timing'], true) : '';
        unset($pharmacy_data['pharmacy_id']);
        unset($pharmacy_data['id']);
        
        $this->db->trans_start();
        //$this->db->where("id", $data['pharmacy_id']);
        if($pharmacy_data["type"] == "user"){

            $this->db->where("id =(SELECT pharmacies_id FROM `user_pharmacies` WHERE created_by ='".$data['id']."' and pharmacies_id ='".$data['pharmacy_id']."')",null,false);
        }else{
            $this->db->where("id =(SELECT pharmacy_id FROM `doctor_user_pharmacies_mapping` WHERE created_by ='".$data['id']."' and pharmacy_id ='".$data['pharmacy_id']."')",null,false);
        }
unset($pharmacy_data["type"]);
        $this->db->update("pharmacies", $pharmacy_data);
        //echo $this->db->last_query();die;


        $pharmacy_time_mapping = (isset($pharmacy_data['pharmacy_timing'])) ? json_decode($pharmacy_data['pharmacy_timing'], true) : '';
        
        //old phramcy delete
        $this->db->where("pharmacy_id", $data['pharmacy_id']);
        $this->db->delete("pharmacies_timing_mapping");

        if (count($pharmacy_time_mapping) > 0 && isset($pharmacy_time_mapping) && !empty($pharmacy_time_mapping)) {
            $pharmacy_time = array();
            foreach ($pharmacy_time_mapping as $key => $value) {
                $pharmacy_time[$key]['pharmacy_id'] = $data['pharmacy_id'];
                $pharmacy_time[$key]["open_time"] = $pharmacy_time_mapping[$key]['time']['from'];
                $pharmacy_time[$key]["close_time"] = $pharmacy_time_mapping[$key]['time']['to'];
                $pharmacy_time[$key]["day"] = $pharmacy_time_mapping[$key]['day'];
            }
            // insert pharmacy timing in pharmacies_timing_mapping(add) table
            $this->db->insert_batch("pharmacies_timing_mapping", $pharmacy_time);
            //echo $this->db->last_query();die;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // Something went wrong.
            $this->db->trans_rollback();
            return FALSE;
        } else {
            // Everything is Perfect. 
            // Committing data to the database.
            $this->db->trans_commit();
            return TRUE;
        }
    }

    //make a prime and prefered pharmacy

    public function make_prefer_pharmacy_model($data) {
        $pharmacy_status = boolean_parse($data['is_primary']);
        // check the already exist the user pharmacy 
        $update_data = ['user_id' => $data['user_id'], 'pharmacies_id' => $data['pharmacy_id']];
        $query = $this->db->get_where("user_pharmacies", $update_data);
        $result = $query->row_array();

        if (count($result) > 0 && !empty($result)) {
            // if we want to make a prime pharmacy then
            if ($pharmacy_status == 1 || $pharmacy_status == "1") {
                // check users prime pharmacy existance
                $query1 = $this->db->get_where("user_pharmacies", ['user_id' => $data['user_id'], 'is_primary' => 1]); // 0->prime pharmacy
                $result1 = $query1->row_array();
                // if exist then change old one pharmacy status and update new pharmacy status 1
                if (count($result1) > 0 && !empty($result1)) {
                    // status change of old one users pharmacy  make a prefered
                    $this->db->where(['id' => $result1['id']]);
                    $this->db->update("user_pharmacies", ['is_primary' => 0]);
                }
                // make a pharmacy accourding to users 
                $this->db->where($update_data);
                $this->db->update("user_pharmacies", ['user_id' => $data['user_id'], 'pharmacies_id' => $data['pharmacy_id'], 'is_primary' => $pharmacy_status]);
            } else {
                $this->db->where($update_data);
                $this->db->update("user_pharmacies", ['is_primary' => $pharmacy_status]);
            }
            return true;
        } else {
            
            if ($pharmacy_status == true) {
                $update_data = ['user_id' => $data['user_id'], 'is_primary' =>1];
                $query = $this->db->get_where("user_pharmacies", $update_data);

                if($query->num_rows()>0){
                    $row1 = $query->row_array();
                    $this->db->where(['user_id' => $data['user_id'],"id"=>$row1['id']]);
                    $this->db->update("user_pharmacies", ['is_primary' => 0]);
                }
            }
            $response = $this->db->insert("user_pharmacies", ['user_id' => $data['user_id'], 'pharmacies_id' => $data['pharmacy_id'], 'is_primary' => $pharmacy_status]);
            return ($response)?true:false;
            
        }
        
    }

    public function pharmacy_image_update($url, $id) {
        $query = $this->db->get_where("pharmacies", ['id' => (int) $id]);
        if ($query->num_rows() > 0) {
            $this->db->where(['id' => $id]);
            $this->db->update("pharmacies", ['pharmacy_image_url' => $url]);
            return true;
        } else {
            return false;
        }
    }

    public function get_user_prefered_pharmacy_model($id) {

        $this->db->where("user_pharmacies.user_id", $id);
        $this->db->where("(user_pharmacies.is_primary=1 OR user_pharmacies.is_primary=0)");
        $this->db->order_by("user_pharmacies.is_primary", "DESC");
        $this->db->select("user_pharmacies.user_id,
                        pharmacies.id,
                        pharmacies.pharmacy_name,
                        pharmacies.pharmacy_image_url AS pharmacy_image,
                        pharmacies.address,pharmacies.city,pharmacies.state,pharmacies.zip,user_pharmacies.is_primary
                        "); //user_pharmacies.is_primary
        $this->db->from("pharmacies");
        $this->db->join("user_pharmacies", "user_pharmacies.pharmacies_id = pharmacies.id", "INNER");
        $query = $this->db->get();

        // where those pharmacy will get who prefered by the doctor to user 
        $data = $this->get_doctor_prefered_pharmacy_to_user($id);
        $this->pharmacy_result = $query->result_array();
        $final_result = array_merge($this->pharmacy_result, $data);
        //dd($final_result);
        $pharmacy = array();
        //$num_rows = array_merge($this->pharmacy_result, $data);
        if (count($final_result) > 0 && !empty($final_result)) {
            // making for unique multidimensonial array based on id
            $ids = array_column($final_result, 'id');
            $ids = array_unique($ids);
            $array = array_filter($final_result, function ($key, $value) use ($ids) {
                return in_array($value, array_keys($ids));
            }, ARRAY_FILTER_USE_BOTH);
            $pharmacy['primary'] =[];
            $pharmacy['preferred'] =[];
            foreach ($array as $key => $value) {
                if ($value['is_primary'] == "1") {
                    //$this->pharmacy_result[$key]['is_primary'] = true;
                    $pharmacy['primary'][] = $array[$key];
                    $pharmacy["primary"][$key]['is_primary'] =true;;
                } else {
                    //$this->pharmacy_result[$key]['is_primary'] = false;
                    $array[$key]['is_primary'] =false;
                    $pharmacy['preferred'][] = $array[$key];
                    
                }
                
            }
            //dd($pharmacy);
            return $pharmacy;
        } else {
            return false;
        }
    }

    private function get_doctor_prefered_pharmacy_to_user($user_id) {
        $r = array();
        $this->db->where("doctor_user_pharmacies_mapping.user_id", $user_id);
        $this->db->select("
                        doctor_user_pharmacies_mapping.user_id,
                        pharmacies.id,
                        pharmacies.pharmacy_name,
                        pharmacies.pharmacy_image_url AS pharmacy_image,
                        CONCAT(
                          pharmacies.city,
                          '',
                          pharmacies.address
                        ) AS address,pharmacies.city,pharmacies.state,pharmacies.zip
                        
                        ");
        $this->db->from("pharmacies");
        $this->db->join("doctor_user_pharmacies_mapping", "doctor_user_pharmacies_mapping.pharmacy_id = pharmacies.id", "INNER");
        $query = $this->db->get();
        //return ($query->num_rows() > 0)?$query->result_array():array();
        if ($query->num_rows() > 0) {
            $r = $query->result_array();
            foreach ($r as $k => $v) {
                $r[$k]['is_primary'] = 0;
            }
        } else {
            $r = array();
        }
        return $r;
    }

    public function get_user_pharmacy_model($id) {
        //$this->db->where("pharmacies.is_blocked", '0');
        //$this->db->where(["user_pharmacies.user_id" => $id['user_id']]);
        $this->db->where(["pharmacies.id" => $id['pharmacy_id']]);
        $this->db->select("pharmacies.id,pharmacies.pharmacy_name,pharmacies.pharmacy_image_url,
                pharmacies.city,pharmacies.state,pharmacies.zip,pharmacies.address,
                pharmacies.phone,pharmacies.pharmacy_timing,pharmacies.place_id,user_pharmacies.is_primary,user_pharmacies.created_by")->from($this->pharmacies);
        $this->db->join("user_pharmacies", "user_pharmacies.pharmacies_id=pharmacies.id", "LEFT");
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return (count($query->row_array()) > 0 ) ? $query->row_array() : false;
    }

    public function get_doctor_pharmacy_model($id) {
        //$this->db->where("pharmacies.is_blocked", '0');
        //$this->db->where(["doctor_user_pharmacies_mapping.doctor_id" => $id['doctor_id']]);
        $this->db->where(["pharmacies.id" => $id['pharmacy_id']]);
        $this->db->select("pharmacies.id,pharmacies.pharmacy_name,pharmacies.pharmacy_image_url,
                pharmacies.city,pharmacies.state,pharmacies.zip,pharmacies.address,
                pharmacies.phone,pharmacies.pharmacy_timing,pharmacies.place_id,doctor_user_pharmacies_mapping.created_by")->from($this->pharmacies);
        $this->db->join("doctor_user_pharmacies_mapping", "doctor_user_pharmacies_mapping.pharmacy_id=pharmacies.id", "LEFT");
        $query = $this->db->get();

        return (count($query->row_array()) > 0 ) ? $query->row_array() : false;
    }

    public function user_pharmacy_deleted_model($id) {
        $query = $this->db->get_where("user_pharmacies", ['pharmacies_id' => (int) $id['pharmacy_id'], "user_id" => (int) $id['user_id']]);
        if ($query->num_rows() > 0) {
            $this->db->where(['pharmacies_id' => (int) $id['pharmacy_id'], "user_id" => (int) $id['user_id']]);
            $this->db->update("user_pharmacies", ['is_primary' => 2]);
            // 2=> means soft delete  and new added pharmacy (mantain 2 status)
            return true;
        } else {
            return false;
        }
    }

    public function doctor_pharmacy_deleted_model($id) {
        $query = $this->db->get_where("doctor_pharmacies", ['pharmacies_id' => (int) $id['pharmacy_id'], "doctor_id" => (int) $id['doctor_id']]);
        if ($query->num_rows() > 0) {
            $this->db->where(['pharmacies_id' => (int) $id['pharmacy_id'], "doctor_id" => (int) $id['doctor_id']]);
            $this->db->update("doctor_pharmacies", ['is_deleted' => 1]);
            return true;
        } else {
            return false;
        }
    }

    function make_prefered_pharmacy_for_user_model($data) {
        $query = $this->db->get_where("user_pharmacies", ["user_id" => (int)$data['user_id'], "is_primary" => 1]);
        //echo $this->db->last_query();die;
        //vd($query->num_rows());
        if ($query->num_rows() > 0) {
            $query = $this->db->get_where("doctor_user_pharmacies_mapping", ['doctor_id' => (int) $data['doctor_id'], "user_id" => (int) $data['user_id'], "pharmacy_id" => (int) $data['pharmacy_id']]);
            if ($query->num_rows() == 0) {
                $this->db->insert("doctor_user_pharmacies_mapping", $data);
                return true;
            } else {
                return false;
            }
        }else{
           $this->db->insert("user_pharmacies",["user_id"=>$data['user_id'],"pharmacies_id"=>$data['pharmacy_id'],"is_primary"=>1]); 
           return false;
        }
    }

    public function get_user_pharmacy_list_model($data) {
        //echo $data['is_open'];die;
        $this->db->having("distance < ", 40);
        if ($data['is_open'] == "true") {
            $this->db->where(
                    [
                        "pharmacies_timing_mapping.day" => date('l', strtotime($this->config->item("date"))),
                        "pharmacies_timing_mapping.open_time <=" => date("H:i:s", strtotime($this->config->item("date"))),
                        "pharmacies_timing_mapping.close_time >=" => date("H:i:s", strtotime($this->config->item("date")))
            ]);
        }
        $this->db->order_by("distance", "ASC");
        $this->db->select("pharmacies.id,
            pharmacies.pharmacy_image_url,
            pharmacies.address,
            pharmacies.state,
            pharmacies.zip,
            pharmacies.latitude,
            pharmacies.longtitude,
            (
                6371 * ACOS(
                    COS(RADIANS(" . $data['latitude'] . ")) * COS(RADIANS(pharmacies.`latitude`)) * COS(
                        RADIANS(pharmacies.`longtitude`) - RADIANS(" . $data['longitude'] . ")
                    ) + SIN(RADIANS(" . $data['latitude'] . ")) * SIN(RADIANS(pharmacies.`latitude`))
                )
            ) AS distance");
        $this->db->from("pharmacies");
        if ($data["is_open"] === "true") {
            $this->db->join("pharmacies_timing_mapping", "pharmacies_timing_mapping.pharmacy_id = pharmacies.id", "LEFT");
        }
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    public function searching_model($search) {
        $this->db->like('pharmacy_name', $search, "after");
        $this->db->or_like('zip', $search, "after");
        $this->db->or_like('city', $search, "after");
        $query = $this->db->select("id,pharmacy_name,city,state,zip,address,pharmacy_image_url")->from("pharmacies")->get();
        //echo $this->db->last_query();die;
        return($query->num_rows() > 0) ? $query->result_array() : false;
    }

}

?>