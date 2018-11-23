<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rating_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "rating/rating";
    protected $model = 'admin/rating/rating_model';
    protected $is_model = "rating_model";

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

    public function index() {
        if (count($this->input->post()) > 0) {
            // id == user or doctor id
            $this->data['id'] = $this->input->post('id');
        }

        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/rating/rating_view";
        $this->data['add_datatable_js'] = "rating/rating.js";
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
        $post['column_order'] = array('med_id', 'first_name', 'email', 'phone', 'gender', 'date_of_birth', 'avg_rating');
        $post['column_search'] = array('med_id', 'first_name', 'email', 'phone', 'gender', 'date_of_birth', 'avg_rating');

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
        $post['id'] = $this->input->post('id');
        return $post;
    }

    function table_data($order_list, $no) {
        $row = array();
        //  dd($order_list);
        $row[] = $order_list->med_id;
        $row[] = $order_list->first_name;
        $row[] = $order_list->email;
        $row[] = $order_list->phone;
        $row[] = $order_list->gender;
        $row[] = $order_list->date_of_birth; // $order_list->id user id
        $id1 = (isset($order_list->id)) ? $order_list->id : ''; // for chagne the screen
        $id2 = (isset($order_list->doctor_id)) ? $order_list->doctor_id : ''; // for chagne the screen
        $id = (isset($order_list->id)) ? $order_list->id : "doctor/" . $order_list->doctor_id;
        $row[] = "<i class='fa fa-star'></i> " . substr($order_list->avg_rating, 0, 4);
        $row[] = "<span class='label label-danger label-mini' style='padding:5px 7px;'>
                <a href=" . base_url('admin/rating/rating_list_controller/index/') . $id . " style='color:white'>
                    <i class='fa fa-eye'></i></a></span> <span class='label label-danger label-mini' style='padding:5px 7px;'>
                <a href='#' id='edit_doctor_rating' user-id='" . $id1 . "' doctor-id='" . $id2 . "' rating='" . substr($order_list->avg_rating, 0, 4) . "' style='color:white' data-toggle='modal' data-target='#myModal'>
                    <i class='fa fa-edit'></i></a></span>";
        return $row;
    }

    function update_avg_rating() {
        $data = $this->input->post();
        if ($data['save']) {
            $this->{$this->is_model}->update_avg_rating_model($data);
            $this->session->set_flashdata("flashsuccess","Rating Successfully Updated");
            redirect("admin/rating/rating_controller");
        }
    }

}

?>
