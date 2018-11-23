<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Application Admin Login Class
 *
 * This class is extend the CI_Controller to MY_Controller
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Chrominfotech Team
 * @link		https://www.chromeinfotech.net/company/about-us.html
 */
class MY_Controller extends CI_Controller
{
	protected $model;
	protected $is_model = '';
	protected $view = [];
	protected $data = [];
	protected $index_file = "admin/index";
	protected $file_name;
	public function __construct()
	{
		parent::__construct();
		//$this->user_not_loggedin();
	}

	/*
	|----------------------------------------------------------------------------------
	|This Function will set the form enviroment base on recived parameter
	|----------------------------------------------------------------------------------
	*/
	protected function BuildFormEnv($env = ['form'])
	{
		foreach ($env as  $v) {
			switch ($v) {
				case 'form':
				$this->load->helper('form');
				$this->load->helper("template");	
				break;	
				case 'template_helper':
				$this->load->helper("template");	
				break;	
				default:
				echo "";
				break;
			}
		}
	}
	/*
	|----------------------------------------------------------------------------------
	|This Function will load validation library
	|----------------------------------------------------------------------------------
	*/
	protected function load_validation_lib()
	{
		return $this->load->library('form_validation');
	}
	/*
	|----------------------------------------------------------------------------------
	|This Function will load model
	|----------------------------------------------------------------------------------
	*/
	protected function isModelload()
	{
		$this->load->model($this->model,$this->is_model);
	}
	/*
	|----------------------------------------------------------------------------------
	|This Function will display the whole admin panel
	|----------------------------------------------------------------------------------
	*/
	protected function displayview($alldata=[])
	{
		if (!empty($alldata) &&  file_exists(APPPATH.'views/'.$alldata['view'].'.php')){		
			$this->view['view']  = $this->load->view($alldata['view'], $this->data, TRUE);
		}
		$this->load->view($this->index_file,$this->view);
	}
	/*
	|---------------------------------------------------------------------------------
	|This function will check user loggedin or not , if user loggedin then redirecton |dashboard other redirect on login page
	|@return -- dashbaord/login controller
	|----------------------------------------------------------------------------------
	*/
	protected function check_user_loggedin()
	{
		if ($this->session->has_userdata('logged_in')) {
			redirect(base_url()."dashboard", "refresh");
			
		}		
	}
	/*
	|---------------------------------------------------------------------------------
	|This function will check user loggedin or not , if user not loggedin then  |redirecton login page
	|@return -- dashbaord/login controller
	|----------------------------------------------------------------------------------
	*/
	public function user_not_loggedin()
	{
		if (!$this->session->has_userdata('logged_in')) {
			redirect(base_url()."admin", "refresh");
			
		}		
	}
}

?>

