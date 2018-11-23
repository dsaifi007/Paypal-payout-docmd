<?php
/**
* 
*/
class Nonapi extends CI_Controller
{
	protected $response_send;
	protected $language_file="users/users";
	protected $pass_length=7;

	function __construct()
	{
		parent::__construct();
		$this->lang->load($this->language_file);
	}
   public function index($reset_string)
   {
      $this->load->library('encrypt');
      $this->load->model("api/login_model");
      $this->load->library("session");
      $this->data["email_key"]=$reset_string;
      $this->load->view("updatepassword/update_pass",$this->data);
   }


	public function updtingpassword()
	{
	  try{
	  	$this->load->library("session");
   		$this->user_data=$this->input->post();
   		if ($this->user_data['password']!='' && $this->user_data['passconf']!='' && $this->user_data['reset_code']!='') {
   			if ($this->user_data['password'] == $this->user_data['passconf']) {
   				if (strlen($this->user_data['password'])>=$this->pass_length) {
   					if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $this->user_data['password']))
   						{
   							$this->load->library('encrypt');
   							$this->load->model("api/login_model");
   							$user_email=$this->encrypt->decode($this->user_data['reset_code']);
   							$return_resp=$this->login_model->checking_reset_code($user_email,$this->user_data['reset_code']);
   							if ($return_resp) {
   								$this->login_model->updating_pass($user_email,$this->user_data['password']);
   						           $this->response_send=$this->lang->line('success_updated_pass');
   							}
   							else
   							{
   							  $this->response_send=$this->lang->line('email_or_reset_key_invalid');
   							}		  	
   						}
   						else
   						{
   							$this->response_send=$this->lang->line('pass_character_invalid');
   						}
   					}
   					else
   					{
   						$this->response_send=$this->lang->line('pass_length_invalid');
   					}
   				} else {
   					$this->response_send=$this->lang->line('pass_not_match');
   				}

   			}
   			else
   			{
   				$this->response_send=$this->lang->line('all_field_required');
   				
   			}
   			$this->session->set_flashdata('message', $this->response_send);			
   			redirect("api/update_password/index/".$this->user_data['reset_code']);
   		}
   		catch (Exception $exc) {
	   		//new Error($exc);
   			echo 'Message: ' .$exc->getMessage();
   			$this->exceptionhandler->handle($exc);
   		}
	}
   public function emailauth($reset_code)
   {
      //$this->load->library('encrypt');
      $this->load->model("api/user_model");
      $email=base64_decode($reset_code);
      $response=$this->user_model->email_verified($email);
      if ($response) {
         echo $this->lang->line('email_verifed');
      }
      else
      {
         echo $this->lang->line('wrong_url');
      }
      exit;
   }
   public function doctoremailauth($reset_code)
   {
      //$this->load->library('encrypt');
      $this->load->model("api/doctor_model");
      $email=base64_decode($reset_code);
      $response=$this->doctor_model->email_verified($email);
      if ($response) {
         echo $this->lang->line('email_verifed');
      }
      else
      {
         echo $this->lang->line('wrong_url');
      }
      exit;
   }
}

?>