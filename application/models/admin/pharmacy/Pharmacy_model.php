<?php

class Pharmacy_model extends CI_Model {

    protected $pharmacy_table = 'pharmacies';
    protected $address = "address";
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
       // echo $this->db->last_query();die;
        return $query->result();
    }

    function _get_order_list_query($post) {

        // This if condition is work when external filtering is in used  
        if ($post['external_filtering'] != '' || $post['external_filtering'] != null) {
            $filter_data = array_filter((array) (json_decode($post['external_filtering'])));
            $this->db->where($filter_data);
        }
        //$this->db->order_by("id", "DESC");
        $this->db->select("id,pharmacy_name,phone,city,state,zip,is_blocked");
        $this->db->from($this->pharmacy_table);


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
        $this->db->from($this->pharmacy_table);
    }

    function count_filtered($post) {
        $this->_get_order_list_query($post);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function update_pharmacy($insertdata, $id) {
        $this->db->trans_start();
        $final_insert_data = $insertdata;
        //dd($final_insert_data);
        $map_axis = explode(",", $insertdata['address_location']);
        $insertdata['latitude'] = $map_axis[0];
        $insertdata['longtitude '] = $map_axis[1];
        unset($insertdata['address_location']);
        unset($insertdata['day']);
        unset($insertdata['start_time']);
        unset($insertdata['end_time']);
        unset($insertdata['pharmacy_submit']);
        //dd($final_insert_data);
        if (isset($final_insert_data['start_time']) && count($final_insert_data['start_time']) > 0) {
            foreach ($final_insert_data['start_time'] as $k => $value) {
                $this->day_data_json[] = [
                    "time"=>[
                        "from"=>$value,
                        "to"=>$final_insert_data['end_time'][$k]
                    ],
                    "day" => $final_insert_data['day'][$k],
                ];
            }
            $insertdata['pharmacy_timing'] = json_encode($this->day_data_json);
        }
        
        $this->db->where("id", $id);
        $this->db->update($this->pharmacy_table, $insertdata);
        
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
    public function add_pharmacy($insertdata, $id = null) {
        //dd($insertdata);
        $this->db->trans_start();
        unset($insertdata['pharmacy_submit']);
        $final_insert_data = $insertdata;
        $map_axis = explode(",", $final_insert_data['address_location']);
        $final_insert_data['latitude'] = $map_axis[0];
        $final_insert_data['longtitude '] = $map_axis[1];
        unset($final_insert_data['address_location']);
        unset($final_insert_data['start_time']);
        unset($final_insert_data['end_time']);
        unset($final_insert_data['day']);
        // array prepare for  making the json for pharmacy_timing 
        if (isset($insertdata['start_time']) && count($insertdata['start_time']) > 0) {
            foreach ($insertdata['start_time'] as $k => $value) {
                $this->day_data_json[] = [
                    "time"=>[
                        "from"=>$value,
                        "to"=>$insertdata['end_time'][$k]
                    ],
                    "day" => $insertdata['day'][$k],
                ];
            }
            $final_insert_data['pharmacy_timing'] = json_encode($this->day_data_json);
        }
        //dd($final_insert_data);
        $this->db->insert($this->pharmacy_table, $final_insert_data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    //Update pharmacy status based on pharmacy id
    public function update_pharmacy_status_model($pharmacy_data) {
        $this->db->where("id", $pharmacy_data['id']);
        $this->db->update($this->pharmacy_table, ["is_blocked" => $pharmacy_data['status']]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    //Get Pharmacy Info
    public function get_pharmacy_info($id) {
        $this->db->where("id", $id);
        $query = $this->db->select("*")->from($this->pharmacy_table)->get();
        return $query->row();
    }

    //get all state
    public function get_all_state() {
        $query = $this->db->select("DISTINCT(state) AS state")->from($this->pharmacy_table)->where("state IS NOT NULL AND state!=''")->get();
        return $query->result_array();
    }

    //get all city
    public function get_all_city() {
        $query = $this->db->select("DISTINCT(city) AS city")->from($this->pharmacy_table)->where("city IS NOT NULL AND city!=''")->get();
        return array_filter($query->result_array());
    }

    // get all get_all_specilities
    public function get_all_specilities() {
        $query = $this->db->select("id,name")->from($this->specility_table)->get();
        return $query->result_array();
    }

    function update_img($data, $id) {
        $this->db->where("id", $id);
        $this->db->update("pharmacies", $data);
        return true;
    }

}

?>