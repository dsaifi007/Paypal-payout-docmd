<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Application(DOCMD) users class
 *
 * This class is extend the MY_Controller
 *
 * @package		  CodeIgniter
 * @subpackage	  Libraries
 * @category	  Libraries
 * @author		  Chrominfotech Team
 * @link		  https://www.chromeinfotech.net/company/about-us.html
 * @Description This class will handel all the users
 */
class Users extends MY_Controller
{
	private $parent_dir = "admin/users/";
	protected $file_name = 'users_view';
	protected $data = [];
	private $data_table_js_file = "users/users.js";
	private $js_file = "users/filter.js";
	private $language_file = "users/users";
	private $add_css = "users/formlayout.css";
	protected $model = 'admin/users/user_model';
	protected $is_model = "user_model";

	public function __construct()
	{
		parent::__construct();
		$this->user_not_loggedin();
		language_helper($this->language_file);
	}
	/*
	|-----------------------------------------------------------------------------
	|	Work -- render home page listing
	|	@return -- null
	|-----------------------------------------------------------------------------
	*/

	public function index()
	{
		$this->BuildFormEnv(["template_helper"]);
		$this->data['view'] = $this->parent_dir.$this->file_name;
		$this->data['add_datatable_js'] = $this->data_table_js_file;
		$this->data['add_js'] = $this->js_file;
		$this->data['add_css'] = $this->add_css;
		$this->data['view'] = $this->parent_dir.$this->file_name;
		$this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
		$this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
		$this->displayview($this->data);
	}

	/*
	|-----------------------------------------------------------------------------
	|	Work -- this function will use for user view data
	|	@return -- json data
	|-----------------------------------------------------------------------------
	*/
	public function user_view($user_id)
	{
		try {
			if (!empty($user_id)) {
				$this->isModelload(); 
				$this->data['user_info'] = $this->{$this->is_model}->get_user_data($user_id);
				$this->data['view'] = $this->parent_dir."user_info_view";
				$this->displayview($this->data);	
			}
		} catch (Exception $exc) {
			echo $exc->getMessage();
		}
	}


	/*
	|-----------------------------------------------------------------------------
	|	Work -- fetch all the user/patient
	|	@return -- json data
	|-----------------------------------------------------------------------------
	*/
	public function ajax_list()
	{
		$this->isModelload(); 
		$list = $this->{$this->is_model}->get_datatables($filetr_data);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $item) {
			$no++;
			$row = array();
			$row[]= "<input type='checkbox' id='chek' class='chek' name='sltd_emails[]' value='".$item->id."' />";
			$row[] = $item->id;
			$row[] = $item->first_name;
			//$row[] = $item->last_name;
			$row[] = $item->gender;
			$row[] = $item->date_of_birth;
			$row[] = $item->email;
			$row[] = $item->phone;
			// $row[] = "<label class = 'mdl-switch mdl-js-switch mdl-js-ripple-effect' 
			// for =  'switch-".$item->id."'>
			// 	<input type = 'checkbox' name='user_block' data-id='".$item->id."' id = 'switch-".$item->id."'
			// class = 'mdl-switch__input a' checked>
			// </label>";
			$is_blocked = ($item->is_blocked == 1)?"checked":"";
			$row[] = "<label class='switchToggle'>
			<input type='checkbox' name='user_block[]' value='".$item->is_blocked."'  data-id='".$item->id."' ".$is_blocked.">
			<span class='slider red round'></span>
			</label>  "."<a href=".base_url('admin/users/users/user_view/'). $item->id." class='btn btn-info btn-custm'><i class='fa fa-eye'></i></a>";
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->{$this->is_model}->count_all(),
			"recordsFiltered" => $this->{$this->is_model}->count_filtered(),
			"data" => $data,
		);
        //output to json format
		echo json_encode($output);
	}
	/*
	Work -- update the user status active/unactive based on ID
	@return -- json response
	*/
	public function update_user_status()
	{
		$user_data = $this->input->post();
		$this->isModelload(); 
		$this->{$this->is_model}->update_user_status_model($user_data);
		if ($user_data['status'] == 0) {
			$status_response["active"] = $this->lang->line("users_active");	
		}else{
			$status_response["unactive"] = $this->lang->line("users_unactive");
		}
		echo json_encode($status_response);
	}
	/*
	Work -- Send email to users in bulk or individual users 
	@return -- true/false
	*/
	public function send_email_to_users()
	{
		try {
			$user_data = $this->input->post();
			dd($user_data);
			if(isset($user_data["selectall"]) && $user_data["selectall"] =="all"){			
				$email_sent = $this->sent_email($this->input->post("subject"),$this->input->post("message"));
				//$this->lang->line("emails_sent") lang file not working
				$this->session->set_flashdata('flashsuccess', "Email sent successfully");
						redirect("admin/users/users/index");					
			}else{
				$email_sent = $this->sent_email($this->input->post("subject"),$this->input->post("message"),$user_data['sltd_emails']);
				$this->session->set_flashdata('flashsuccess', "Email sent successfully");
						redirect("admin/users/users/index");
				redirect("admin/users/users/index");
			}
			
		} catch (Exception $exc) {
			echo $exc->getMessage();
		}
	}
	 /*
      |--------------------------------------------------------------------------------
      | This Function will be used get the filter data
      |--------------------------------------------------------------------------------
     */
	public function get_filter_data()
	{
		$filter_data = $this->input->post();
		if (@$filter_data['gender'] !='' || @$filter_data['state'] != '' || @$filter_data['city']!=''|| @$filter_data['health_insurance'] != '' ) {
			$filter_data_array =[
				"gender" => @$filter_data['gender'],
				"state" =>  @$filter_data['state'],
				"city"  =>  @$filter_data['city'],
				"health_insurance" => @$filter_data['health_insurance']
			]; 
			$this->ajax_list($filter_data_array);
		}
		else{
			echo $this->lang->line("atleast_one_select");
		}
		//dd($filter);
	}
    /*
      |--------------------------------------------------------------------------------
      | This Function will be used for the bulk email
      |--------------------------------------------------------------------------------
     */
    public function sent_email($subject,$message,$selected_id = null) {
        try {
            $this->isModelload();
            $this->config->load('shared');
            $emails = $this->user_model->get_all_emails($selected_id);     
            $this->load->library("email_setting");
            $from = $this->config->item("from");
            $this->email_setting->send_email($emails, $from, $message, $subject);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }
}


?>