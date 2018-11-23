<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacies_controller extends MY_Controller {

    protected $data = [];
    private $language_file = "pharmacies/pharmacies";
    protected $model = 'admin/pharmacy/pharmacy_model';
    protected $is_model = "pharmacies_model";
    private $status_response = [];
    private $pharmacy_table = "pharmacies";
    private $filtering_data = [];

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
            $this->data['filetration'] = $this->security->xss_clean(array_map("trim", $this->input->post()));
                
        }
        $this->data['state'] = $this->{$this->is_model}->get_all_state();
        
        $this->data['city'] = array_filter($this->{$this->is_model}->get_all_city());
        $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/pharmacies/pharmacies_view";
        $this->data['add_datatable_js'] = "pharmacies/pharmacies.js";
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
        $post['column_order'] = array(null, 'pharmacy_name', 'phone', 'city', 'state', 'zip');
        $post['column_search'] = array('pharmacy_name', 'phone', 'city', 'state', 'zip');

        $list = $this->{$this->is_model}->get_order_list($post);
        $data = array();
        $no = $post['start'];
        //dd($list);	
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
        $post['external_filtering'] = $this->input->post('filter_data');
        return $post;
    }

    function table_data($order_list, $no) {
        $row = array();
        $row[] = $order_list->id;
        //dd($order_list);
        $row[] = $order_list->pharmacy_name;
        $row[] = $order_list->phone;
        $row[] = $order_list->city;
        $row[] = $order_list->state;
        $row[] = $order_list->zip;
        $is_blocked = ($order_list->is_blocked == 1) ? "checked" : "";
        $row[] = "<span class='label label-danger label-mini' style='padding:5px 7px;'>
                <a href=" . base_url('admin/pharmacies/pharmacies_controller/edit_pharmacy_info/') . $order_list->id . " style='color:white'>
                    <i class='fa fa-eye'></i></a></span> <label class='switchToggle' style='vertical-align: middle;'>
                    <input type='checkbox' id='user_block' name='user_block[]' value=" . $order_list->is_blocked . "  data-id=" . $order_list->id . "
                         " . $is_blocked . " >
                    <span class='slider red round'></span>
                    </label>";
        return $row;
    }

    /*
      |-----------------------------------------------------------------------------
      |	Work -- this function will use for pharmacy view info
      |	@return -- json data
      |-----------------------------------------------------------------------------
     */

    public function pharmacy_view($pharmacy_id) {
        try {
            if (!empty($doctor_id)) {
                // get the pharmacy info 
                $this->data['pharmacy_info'] = $this->{$this->is_model}->get_pharmacy_info($pharmacy_id);
                $this->data['view'] = "admin/pharmacies/pharmacies_info_view";
                $this->displayview($this->data);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- update the user status active/unactive based on ID
      |		@return -- json response
      -------------------------------------------------------------------------------------------
     */

    public function update_pharmacy_status() {
        $user_data = $this->input->post();
        $this->{$this->is_model}->update_pharmacy_status_model($user_data);
        if ($user_data['status'] == 0) {
            $this->status_response["unblock"] = "Pharmacy Unblocked";//$this->lang->line("pharmacy_unblock");
        } else {
            $this->status_response["block"] = "Pharmacy Blocked";// $this->lang->line("pharmacy_block");
        }
        echo json_encode($this->status_response);
        exit();
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function add_pharmacy_info() {

        $this->data['page_title'] = $this->lang->line("add_title");
        if ($this->input_validations()) {
            $input_data = $this->input->post();
            //dd($input_data);
            $image_response = $this->pharmacy_img_upload($_FILES);
            if (isset($image_response['error'])) {
                $this->data['error'] = $image_response['error'];
            } else {
                $final_input = ($image_response != NULL) ? array_merge($input_data, $image_response) : $input_data;
                $result = $this->{$this->is_model}->add_pharmacy($final_input);
                if ($result) {
                    $this->session->set_flashdata("flashsuccess", $this->lang->line("success_pharmacy_save"));
                } else {
                    $this->session->set_flashdata("flashsuccess", $this->lang->line("error_pharmacy_save"));
                }
                redirect("admin/pharmacies/pharmacies_controller");
            }
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/pharmacies/add_pharmacy_view";
        //$this->data['add_css'] = "pharmacies/bootstrap-clockpicker.min.css";
        //$this->data['add_js'] = ["pharmacies/bootstrap-clockpicker.min.js", "pharmacies/clock_picker.js"];
        $this->displayview($this->data);
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Add new pharmacy
      |		@return -- view
      -------------------------------------------------------------------------------------------
     */

    public function edit_pharmacy_info($id = null) {
        
        $this->data['page_title'] = $this->lang->line("edit_title");
        $this->data['items'] = $this->{$this->is_model}->get_pharmacy_info($id);
       
        $this->data['success'] = ($this->session->flashdata("flashsuccess")) ? $this->session->flashdata("flashsuccess") : '';

        if ($this->input_validations($id, $this->data['items'])) {
            $input_data = $this->input->post();
            //dd($input_data);
            unset($input_data['id']);
           
            $image_response = $this->pharmacy_img_upload($_FILES, $id);
            if (isset($image_response['error'])) {
                $this->data['error'] = $image_response['error'];
            } else {
                $final_input = ($image_response != NULL) ? array_merge($input_data, $image_response) : $input_data;
                $this->{$this->is_model}->update_pharmacy($final_input, $id);
                //$this->data['success'] = $this->lang->line("success_pharmacy_edit_save");
                $this->session->set_flashdata("flashsuccess",$this->lang->line("success_pharmacy_edit_save"));
                redirect("admin/pharmacies/pharmacies_controller/edit_pharmacy_info/$id");
            }
        }
        $this->BuildFormEnv(["template_helper"]);
        $this->data['view'] = "admin/pharmacies/edit_pharmacy_view";
        //$this->data['add_css'] = "pharmacies/bootstrap-clockpicker.min.css";
        //$this->data['add_js'] = ["pharmacies/bootstrap-clockpicker.min.js", "pharmacies/clock_picker.js"];
        $this->displayview($this->data);
    }

    /*
      |--------------------------------------------------------------
      | Work - validation of form input
      | @return - true/false
      |---------------------------------------------------------------
     */

    public function input_validations($id = null, $input_data = null) {
        $this->load_validation_lib();
        $this->load->helper('security');
        //echo $input_data->pharmacy_name."---".$this->input->post("pharmacy_name");die;
        $is_unique = ($id > 0 && (strtolower($this->input->post("pharmacy_name")) == strtolower($input_data->pharmacy_name))) ? '' : "|is_unique[pharmacies.pharmacy_name]";
        $this->form_validation->set_rules("pharmacy_name", "Pharmacy Name", "required|trim|xss_clean|min_length[4]|max_length[100]" . $is_unique);
        $this->form_validation->set_rules("phone", "Phone", "required|trim|min_length[10]|max_length[20]|xss_clean");
        $this->form_validation->set_rules("city", "City", "required|trim|min_length[2]|max_length[20]|xss_clean");
        $this->form_validation->set_rules("state", "State", "required|trim|xss_clean|min_length[2]|max_length[20]");
        $this->form_validation->set_rules("zip", "Zip", "required|trim|max_length[10]|xss_clean");
        $this->form_validation->set_rules("address", "Address", "required|xss_clean");
        
        $this->form_validation->set_rules("address_location", "Map", "required|xss_clean");
        //$this->form_validation->set_rules("start_time[]", "Start Time", "required");
        //$this->form_validation->set_rules("end_time[]", "End Time", "required");
        return $this->form_validation->run();
    }

    /*
      -------------------------------------------------------------------------------------------
      |		Work -- Image uploade of pharmacy
      |		@return -- error/success message
      -------------------------------------------------------------------------------------------
     */

    private function pharmacy_img_upload($file, $id = null) {
        if (!empty($file['pharmacy_img']['name'])) {
            $file_name = $file['pharmacy_img']['name'];
            $this->load->library("common");
            $this->load->helper('string');
            $rename_image = (random_string('numeric') + time()) . random_string();
            $img_data = $this->common->file_upload("assets/admin/img/pharmacy", "pharmacy_img", $rename_image);
            if (isset($img_data["upload_data"]['file_name'])) {
                if ($id != null) {
                    //remove_existing_img($id, $this->pharmacy_table, "pharmacy_img", "assets/admin/img/pharmacy");
                }
                $new_file_name = $img_data["upload_data"]['file_name'];
                $file_url = base_url() . "assets/admin/img/pharmacy/" . $new_file_name;
                return ['pharmacy_img' => $new_file_name, "pharmacy_image_url" => $file_url];
            } else {
                return $img_data;
            }
        }
    }

    public function map() {
        $this->load->view("admin/pharmacies/map_view");
    }
    public function uploade_image($id) {
        $image_response = $this->pharmacy_img_upload($_FILES, $id);
         if (isset($image_response['error'])) {
             $data['error'] = strip_tags($image_response['error']);
         }else{
            $this->{$this->is_model}->update_img($image_response, $id);
            $data['success'] = "Image Successfully uploaded";
         }
         echo json_encode($data);
         exit();
    }
}

?>
