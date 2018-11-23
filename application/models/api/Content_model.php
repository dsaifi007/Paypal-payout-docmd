<?php

class Content_model extends CI_Model {

    protected $faq_table = "faq";

    function __construct() {
        parent::__construct();
    }

    public function get_allrecords($type, $language) {
        $select_options = ($language == "spn") ? "sp_category as category, GROUP_CONCAT(sp_question SEPARATOR '----') as question  ,GROUP_CONCAT(sp_answer SEPARATOR '----') as answer" : "category,GROUP_CONCAT(question SEPARATOR '----') as question,GROUP_CONCAT(answer SEPARATOR '----') as  answer";
        $this->db->where("type", $type);
        $this->db->group_by("category");
        $this->db->select("id," . $select_options)->from($this->faq_table);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return (count($query->result_array()) > 0 ) ? $query->result_array() : false;

    }

}

?>