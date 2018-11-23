<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rating_list_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "rating/rating_list";
    protected $model = 'admin/rating/rating_list_model';
    protected $is_model = "rating_list_model";
    public $user_id;

    public function __construct() {
        parent::__construct();
        $this->user_not_loggedin();
        language_helper($this->language_file);
        $this->isModelload();   
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- render home page listing
      |	@return -- null
      |-----------------------------------------------------------------------------
     */

    public function index($user_id=null,$doctor_id=null) { 
        //echo $doctor_id;die;
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/rating/rating_list_view";
        $this->data['user_id'] = $user_id;
        $this->data['doctor_id'] = $doctor_id;
        
        //$this->data['add_datatable_js'] = "rating/rating_list.js";
        $this->displayview($this->data);
    }

    function getdata() {
        
        // log_message("info",  json_encode($_POST));
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $this->{$this->is_model}->count_all($post),
            "recordsFiltered" => $this->{$this->is_model}->count_filtered($post),
            "data" => $data['data'],
        );
        unset($post);
        unset($data);

        echo json_encode($output);
    }

    function process_get_data() {
        $post = $this->get_post_input_data();
        //$post['where'] = array( 'order_date >= ' => date('Y-m-d',strtotime("-30 days")));
        //$post['where_in'] = array('status' => array('Pending', 'Cancelled', 'Completed'));
        $post['column_order'] = array('id', 'first_name', 'created_at', 'rating', 'review');
        $post['column_search'] = array('id','first_name', 'created_at', 'rating', 'review');
       
        $list = $this->{$this->is_model}->get_order_list($post);
        $data = array();
        $no = $post['start'];
        	
        foreach ($list as $order_list) {
            $no++;
            $row = $this->table_data($order_list, $no);
            $data[] = $row;
        }

        return array(
            'data' => $data,
            'post' => $post
        );
    }

    function get_post_input_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['status'] = $this->input->post('status');
        $post['user_id'] = $this->input->post('user_id');
        $post['doctor_id'] = $this->input->post('doctor_id');
      
        return $post;
    }

    function table_data($order_list, $no) {
        $row = array();
        //dd($order_list);
        $row[] = $order_list->id;
        $row[] = $order_list->first_name.' '.$order_list->last_name;
        $row[] = $order_list->created_at;
        $row[] = "<i class='fa fa-star'></i> ".$order_list->rating;
        $row[] = $order_list->review;
        return $row;
    }

}

?>
