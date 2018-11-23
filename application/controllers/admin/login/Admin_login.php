<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Admin Login Class
 *
 * This class is used for the admin login only
 *
 * @package		CodeIgniter
 * @subpackage	Admin
 * @category	Admin
 * @author		Chrominfotech  Team
 * @link		https://chromeinfotech.net
 */
class Admin_login extends MY_Controller {

    protected $model = 'admin/login/admin_model';
    protected $is_model = "adminmodel";
    private $language_file = "login/login";
    private $admin_session_data = [];

    public function __construct() {
        parent::__construct();
        language_helper($this->language_file);
    }

    /*
      |--------------------------------------------------------------
      | This function is used for load the login view  only
      |---------------------------------------------------------------
     */

    public function index() {
        $this->check_user_loggedin();
        $this->BuildFormEnv(["template_helper"]);
        $data['error'] = ($this->session->flashdata('flasherror')) ? $this->session->flashdata('flasherror') : '';
        $data['success'] = ($this->session->flashdata('flashsuccess')) ? $this->session->flashdata('flashsuccess') : '';
        $this->load->view("admin/login/login", $data);
    }

    /*
      |--------------------------------------------------------------
      | Work - validation of form input
      | @return - true/false
      |---------------------------------------------------------------
     */

    public function validations() {
        $this->load_validation_lib();
        $this->form_validation->set_rules("email", "Email", "required|trim|valid_email");
        $this->form_validation->set_rules("password", "Password", "required|trim");
        return $this->form_validation->run();
    }

    /*
      |--------------------------------------------------------------
      | Work - after submit of login form
      | @recived - User input and match with database
      | @return - true or false
      |---------------------------------------------------------------
     */

    public function formsubmitted() {
        if ($this->validations() == false) {
            $this->index();
        } else {
            $this->isModelload();

            $response = $this->{$this->is_model}->check_email_password($this->input->post());

            if ($response) {
                $this->admin_session_data = [
                    "email" => $this->input->post("email"),
                     "name" => $response['name'],
                    "logged_in" => TRUE
                ];
                $this->set_cookies_for_remember_me($this->input->post()); // for set the cookies
                $this->session->set_userdata($this->admin_session_data);
                $redirect = "dashboard";
            } else {
                $this->session->set_flashdata('flasherror', $this->lang->line("credential_wrong"));
                $redirect = "login";
            }
            redirect($redirect, 'refresh');
        }
    }

    private function set_cookies_for_remember_me($input_data) {
        if (isset($input_data['chkbox'])) {
            $this->load->helper('cookie');
            $email = $input_data['email'];
            $pass = md5($input_data['password']);
            $this->input->set_cookie('email', $email, 0);
            $this->input->set_cookie('password', $pass, 0);
            //set_cookie("email", $email, time() + (86400 * 30), "/");
            //set_cookie("password", $pass, time() + (86400 * 30), "/");
        }
    }

    /*
      |---------------------------------------------------------------------
      | Work -- Forgot password of admin
      | @return -- true or false
      |---------------------------------------------------------------------
     */

    public function forgotpassword() {
        $this->isModelload();
        $response = $this->{$this->is_model}->check_email_password($this->input->post("email", TRUE));
        if ($response) {
            $this->send_temp_password($this->input->post("email", TRUE));
            $this->session->set_flashdata('flashsuccess', $this->lang->line("sent_temp_password"));
        } else {
            $this->session->set_flashdata('email_not_exist', $this->lang->line("email_not_exist"));
        }
        redirect("login"); // Defing login in route.php file in config folder
    }

    /*
      |---------------------------------------------------------------------
      | Work -- Send Temp Password to Admin Email ID
      | return -- by default null but true or false
      | Note -- language file is not working
      |---------------------------------------------------------------------
     */

    private function send_temp_password($email) {
        try {
            $this->load->helper('string');
            $temp_pass = random_string('alnum', 8);
            $this->{$this->is_model}->updating_pass(trim($email), $temp_pass);
            $this->load->library("email_setting");
            $this->config->load("email");
            $from = $this->config->item("from");
            $subject = $this->config->item("subject");
            $message = $this->config->item("forget_message") . " --- " . "<mark>" . $temp_pass . "</mark>";
            $this->email_setting->send_email($email, $from, $message, $subject);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /*
      |---------------------------------------------------------------------
      | Work --   Logout the user
      | @return -- none/null
      |---------------------------------------------------------------------
     */

    public function logout() {
        $this->session->unset_userdata($this->session->userdata());
        $this->session->sess_destroy();
        $this->load->helper('cookie');
        $this->input->set_cookie('email', '', 0);
        $this->input->set_cookie('password', '', 0);
        redirect('login', 'refresh');
    }

}
?>

