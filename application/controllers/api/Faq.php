<?php
require(APPPATH.'/libraries/REST_Controller.php');

class Faq extends REST_Controller {
	
	protected $response_send=[];
	protected $language_file="faq";
	protected $is_model="api/faq_model";

	public function __construct()
	{
		parent::__construct();
		$this->lang->load($this->language_file);
		$this->_auth();
	}
	private function _auth()
	{
		if (empty($this->input->server('PHP_AUTH_USER')) || empty($this->input->server('PHP_AUTH_PW')))
		{
			header('HTTP/1.0 401 Unauthorized');
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="My Realm"');
           echo 'You must login to use this service'; // User sees this if hit cancel
           die();
       }
       else
       {
       	if ($this->input->server('PHP_AUTH_USER')!="admin" || $this->input->server('PHP_AUTH_PW')!=12345) {
       		echo 'Wrong email and password';
       		die();
       	}
       }
   }
	public function faq_records_post()
	{
		$this->load->model($this->is_model);
		$faq_records=$this->faq_model->get_allrecords();
		if ($faq_records!=false) {
			$this->response_send=["message"=>"All Faq Record ","payload"=>$faq_records,"status"=>$this->config->item("status_true")];
		}
		else
		{
			$this->response_send=["message"=>$this->lang->line('no_faq_found'),"status"=>$this->config->item("status_false")];
		}
		$this->response($this->response_send);
	}
}

?>



