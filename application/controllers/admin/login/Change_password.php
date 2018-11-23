<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Application change Password Class
 *
 * This class is used for the admin login only
 *
 * @package		CodeIgniter
 * @subpackage	Change Password
 * @category	Change Password
 * @author		Chrominfotech  Team
 * @link		https://chromeinfotech.net
 */
class Change_password extends MY_Controller
{
	protected $data = [];
	protected $parent_dir = "admin/login";
	protected $view_name = "change_password";
	protected $language_file = "login/change_password";
	protected $js_file = "changepassword/change_pass_form.js";
	protected $model = 'admin/login/change_password_model';
	protected $is_model = "change_password_model";

	public function __construct()
	{
		parent::__construct();
		$this->user_not_loggedin();
		language_helper($this->language_file);
	}
	/*----------------------------------------------------------------------------
	|   Work -- This function will load change password form only
	|	@return null
	|-----------------------------------------------------------------------------
	*/
	public function index()
	{
		$this->BuildFormEnv(["template_helper"]);
	    $this->data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
		$this->data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
		$this->data['view'] = $this->parent_dir.'/'.$this->view_name;
		$this->data['add_js'] = $this->js_file;
		$this->displayview($this->data);
	}
	/*----------------------------------------------------------------------------
	|   Work -- This function validate the input of users
	|	@return -- true/false
	|-----------------------------------------------------------------------------
	*/
	private function input_validation()
	{
		$this->load_validation_lib();
		$this->form_validation->set_rules('current_passsword', 'Current Password', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('passconf', 'Confirm Password', 'required|matches[password]');
		return $this->form_validation->run();
	}

	/*----------------------------------------------------------------------------
	|   Work -- This function accept the users input after submit the  from
	|	@return -- true/false
	|-----------------------------------------------------------------------------
	*/
	public function formsubmitted()
	{
		$user_input = cleanInput($this->input->post());
		if ($this->input_validation() != false) {
			$this->isModelload();
			$updated_password = $this->{$this->is_model}->updated_password($user_input);
			if ($updated_password == false) {
				$this->data['error'] = $this->lang->line("old_password_not_match");
				$this->data['success'] = '';
			}
			else
			{
				$this->data['success'] = $this->lang->line("success_pass_changed");
				$this->data['error']  = '';	
			}
			$this->BuildFormEnv(["template_helper"]);
			$this->data['view'] = $this->parent_dir.'/'.$this->view_name;
			$this->displayview($this->data);	
		}else{
			$this->index();
		}
		
	}

}