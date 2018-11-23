<?php

require(APPPATH.'/libraries/REST_Controller.php');
//use Restserver\Libraries\REST_Controller;

class Users_profile extends REST_Controller {

	protected $user_data=[];
   protected $language_file=["users","spn_users"];
	protected $message_message=[];
	protected $response_send=["status"=>false,"message"=>"Bad response 401."];
	protected $check=[1,2,3,4];
	protected $pass_length=7;
	protected $health_insurance_data=[];
	protected $medical_data=[];
	protected $signup_data=[];
	protected $patnt_info=[];
	protected $address_data=[];
	protected $phone_max_digit=14;
	protected $user_image_folder="assets/users/profile";
   protected $patient_tbl="patient_info";
   protected $profile_field="profile_image";
   protected $headers;
   protected $medical_profile_data=[];

   
	public function __construct()
	{
      $this->headers = apache_request_headers();
		parent::__construct();
		$this->load->helper(["common_helper","form"]);
      content_type($this->headers);
      change_languge($this->headers,$this->language_file);
	}
   private function _loadModel()
   {
   	$this->load->model("api/user_model");
   }

   // This API check email existance 
   public function check_get()
   {
      try{
         $this->user_data=$this->input->get();
         //dd($this->input->get());
         if($this->user_data['email']!='' && $this->user_data['phone']!=''){
            if (!filter_var($this->user_data['email'], FILTER_VALIDATE_EMAIL)) {
               $this->response_send=["message"=>$this->lang->line('wrong_email'),"status"=>$this->config->item("status_false")];
            }
            else
            {
               $exp = '/^[+]*[0]*[1-9][0-9]*$/';
               if (preg_match($exp, trim($this->user_data['phone']))) {
                  if (strlen($this->user_data['phone']) == $this->phone_max_digit) {
                     $this->_loadModel(); 
                     //var_dump($this->user_model->check_email_phone_existance($this->user_data['email'],trim($this->user_data['phone'])));
                     if($this->user_model->check_email_phone_existance(trim($this->user_data['email']),trim($this->user_data['phone']))== false) {    
                        $this->response_send=["status"=>$this->config->item("status_true")];
                     }
                     else
                     {
                        $this->response_send=["message"=>$this->lang->line('email_or_phone_exist'),"status"=>$this->config->item("status_false")];                   }
                  }
                  else
                  {
                     $this->response_send=["message"=>$this->lang->line('phone_length_invalid'),"status"=>$this->config->item("status_false")];
                  }
               }
               else
               {
                  $this->response_send=["message"=>$this->lang->line('phone_invalid'),"status"=>$this->config->item("status_false")];
               }
            }              
         }
         else
         {
            $this->response_send=["message"=>$this->lang->line('all_field_required'),"status"=>$this->config->item("status_false")];
            
         }
         $this->response($this->response_send);
      }
      catch (Exception $exc) {
         $this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
         $this->response($this->response_send);
      }
   }


   // First screen API only For email and phone Create account
   public function create_post()
   {
   	try{
         $this->user_data = json_decode( file_get_contents('php://input'),true );
   		//$this->user_data=$this->input->post();
   		if($this->user_data['email']!='' && $this->user_data['phone']!='' && $this->user_data['password']!=''){
   			if (!filter_var($this->user_data['email'], FILTER_VALIDATE_EMAIL)) {
   				$this->response_send=["message"=>$this->lang->line('wrong_email'),"status"=>$this->config->item("status_false")];
   			}
   			else
   			{
   				$exp = '/^[+]*[0]*[1-9][0-9]*$/';
   				if (preg_match($exp, $this->user_data['phone'])) {
   					if (strlen($this->user_data['phone']) == $this->phone_max_digit) {           			
                        if (strlen($this->user_data['password'])>=$this->pass_length) {
                           if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $this->user_data['password']))
                           {
                              $this->_loadModel();  
                              $this->user_data['created_date']=date("Y-m-d h:i:s");
         							$response=$this->user_model->insert_data($this->user_data);
         							if( $response !=false ){   
                              $this->response_send=['user'=>$response,"status"=>$this->config->item("status_true")];
         							$this->email_verification($this->user_data['email']);
                              }
                              else
                              {
                                 $this->response_send=["message"=>$this->lang->line('email_or_phone_exist'),"status"=>$this->config->item("status_false")];
                              }
         						}
                           else
                           {
                              $this->response_send=["message"=>$this->lang->line('pass_character_invalid'),"status"=>$this->config->item("status_false")];
                           }
                        }
                        else
                        {
                           $this->response_send=["message"=>$this->lang->line('pass_length_invalid'),"status"=>$this->config->item("status_false")];
                        }
                     
   					}
   					else
   					{
   						$this->response_send=["message"=>$this->lang->line('phone_length_invalid'),"status"=>$this->config->item("status_false")];
   					}
   				}
   				else
   				{
   					$this->response_send=["message"=>$this->lang->line('phone_invalid'),"status"=>$this->config->item("status_false")];
   				}
   			}	  				
   		}
   		else
   		{
   			$this->response_send=["message"=>$this->lang->line('all_field_required'),"status"=>$this->config->item("status_false")];
   			
   		}
   		$this->response($this->response_send);
   	}
   	catch (Exception $exc) {
   		$this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
   		$this->response($this->response_send);
   	}
   }

   // Save password
   // public function savepassword_post()
   // {
   // 	try{
   // 		$this->user_data=$this->input->post();
   // 		if ($this->user_data['password']!='' && $this->user_data['passconf']!='' && $this->user_data['userid']!='') {
   // 			if ($this->user_data['password'] == $this->user_data['passconf']) {
   				
   //             if (strlen($this->user_data['password'])>=$this->pass_length) {
   // 					if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d,.;:]).+$/', $this->user_data['password']))
   // 						{
   // 							$this->_loadModel();
   // 							$response=$this->user_model->update_user_password($this->user_data);
   // 							if ($response) {
   // 								$this->response_send=["success"=>$this->lang->line('success_updated_pass'),"status"=>$this->config->item("status_true")];
   // 								$phone=$this->user_model->get_user_phone($this->user_data['userid']);
   // 							}
   // 							else
   // 							{
   // 								$this->response_send=["message"=>$this->lang->line('user_id_not_exist'),"status"=>$this->config->item("status_false")];
   // 							}		  	
   // 						}
   // 						else
   // 						{
   // 							$this->response_send=["message"=>$this->lang->line('pass_character_invalid'),"status"=>$this->config->item("status_false")];
   // 						}
   // 					}
   // 					else
   // 					{
   // 						$this->response_send=["message"=>$this->lang->line('pass_length_invalid'),"status"=>$this->config->item("status_false")];
   // 					}


   // 				} else {
   // 					$this->response_send=["message"=>$this->lang->line('pass_not_match'),"status"=>$this->config->item("status_false")];
   // 				}

   // 			}
   // 			else
   // 			{
   // 				$this->response_send=["message"=>$this->lang->line('all_field_required'),"status"=>$this->config->item("status_false")];
   				
   // 			}
   // 			$this->response($this->response_send);
   // 		}
   // 		catch (Exception $exc) {
   // 			$this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
   // 			$this->response($this->response_send);
   // 		}
   // 	}

   // Add Basic Information of the users
   	public function adduserinformation_post()
   	{
   		try{
   			$this->user_data  = json_decode( file_get_contents('php://input'),true );
            if ($this->user_data['first_name']!='' && $this->user_data['last_name']!='' && $this->user_data['date_of_birth']!='' && $this->user_data['gender']!='' && $this->user_data['address']!='' && $this->user_data['city']!='' && $this->user_data['state']!='' && $this->user_data['zip_code']!='') {
   				if($this->alpha_space($this->user_data["first_name"]) != false){
   					if ($this->alpha_space($this->user_data["last_name"])!=false) {
   						if ($this->alpha_space($this->user_data["gender"])!=false) {
   							if ($this->alpha_space($this->user_data["city"])!=false) {
   								if ($this->alpha_space($this->user_data["state"])!=false) {
   									if ($this->alphanum($this->user_data["zip_code"])!=false) {
   										$this->_loadModel();
   										
                                 $p_id=$this->patient_info($this->user_data);
   										
                                 $this->user_model->user_patient_map($p_id,$this->user_data['user_id']);
                                 
                                 $address_id=$this->user_address($this->user_data);
   										
                                 $this->user_model->user_address_map($address_id,$p_id);

   										$this->health_insurance($this->user_data);
                                 
                                 $this->medical_profile_add($this->user_data,$p_id);

   										$this->response_send=["message"=>$this->lang->line('singup_success'),"status"=>$this->config->item("status_true")];
   									} 
   									else {
   										$this->response_send=["message"=>$this->lang->line('zip_code'),"status"=>$this->config->item("status_false")];
   									}
   								} else {
   									$this->response_send=["message"=>$this->lang->line('state'),"status"=>$this->config->item("status_false")];
   								}
   							} else {
   								$this->response_send=["message"=>$this->lang->line('city'),"status"=>$this->config->item("status_false")];
   							}
   						} else {
   							$this->response_send=["message"=>$this->lang->line('gender'),"status"=>$this->config->item("status_false")];
   						}		
   					} else {
   						$this->response_send=["message"=>$this->lang->line('lirst_name'),"status"=>$this->config->item("status_false")];
   					}
   				} else {
   					$this->response_send=["message"=>$this->lang->line('first_name'),"status"=>$this->config->item("status_false")];
   				}
   			} else {
   				$this->response_send=["message"=>$this->lang->line('all_field_required'),"status"=>$this->config->item("status_false")];
   			}
   			$this->response($this->response_send);
   		}
   		catch (Exception $exc) {
   			$this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
   			$this->response($this->response_send);
   		}
   	}

   	public function validate_input($val='')
   	{
   		$this->load->library('form_validation');
   		switch ($val) {
   			case 1:
   			$this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
   			$this->form_validation->set_rules('phone', 'Phone', 'is_unique[users.phone]');	
   			break;
   			case "fname_validate":
   			$this->form_validation->set_rules('first_name', 'First_name', 'alpha|min_length[3]|max_length[45]');
   			break;	
   			case "lname_validate":
   			$this->form_validation->set_rules('last_name', 'Lirst name', 'alpha|min_length[3]|max_length[45]');
   			break;
   			case "gender_validate":
   			$this->form_validation->set_rules('gender', 'Gender', 'alpha|min_length[4]|max_length[12]');
   			break;
   			case "city":
   			$this->form_validation->set_rules('city', 'City', 'alpha_numeric|min_length[4]|max_length[16]');
   			break;
   			case "state":
   			$this->form_validation->set_rules('state', 'State', 'alpha_numeric_spaces|min_length[3]|max_length[16]|callback_alpha_space');
   			break;
   			case "zip_code":
   			$this->form_validation->set_rules('zip_code', 'Zip Code', 'alpha_numeric_spaces|min_length[4]|max_length[16]');
   			break;
   			case "health_plan_unique":
   			$this->form_validation->set_rules('health_plan', 'Health Plan', 'is_unique[health_insurance.health_plan]');
   			break;
   			case "health_plan_valid_name":
   			$this->form_validation->set_rules('health_plan', 'Health Plan', 'alpha_numeric_spaces|min_length[4]|max_length[16]');
   			break;
   			case "medications":
   			$this->form_validation->set_rules('medications', '', 'callback_customAlpha');
   			break;
   			case "allergies":
   			$this->form_validation->set_rules('allergies', '', 'callback_customAlpha');
   			break;
   			case "past_medical_history":
   			$this->form_validation->set_rules('past_medical_history', '', 'callback_customAlpha');
   			break;
   			case "social_history":
   			$this->form_validation->set_rules('social_history', '', 'callback_customAlpha');
   			break;
   			case "family_history":
   			$this->form_validation->set_rules('family_history', '', 'callback_customAlpha');
   			break;
   			default:
   			echo "";
   			break;
   		}
   		return $this->form_validation->run();
   	}
   	public function customAlpha($str) 
   	{
   		if ($str!='') {
   			if ( !preg_match('/^[a-z 0-9.,\-]+$/i',$str) )
   			{
   				return false;
   			}
   		}
   		
   	}
   public function alphanum($str) 
      {
         return (!preg_match('/^[a-z 0-9.,\-]+$/i',$str)) ? false : true;      
      }
   	public function alpha_space($str)
   	{
   		return ( ! preg_match("/^([-a-z ])+$/i", $str)) ? FALSE : TRUE;
   	}
      public function medical_profile_add($medcl_data,$p_id)
      {
         $this->medical_profile_data=keys_existance_check($medcl_data,"user_medical_profile");
         if ( $this->medical_profile_data != false) {
            $this->medical_profile_data['created_at']=date("Y-m-d h:i:s");
            $this->medical_profile_data['patient_id']=$p_id;
            $this->user_model->user_medical_profile_add($this->medical_profile_data);
         }
      }
      public function health_insurance($health_data)
      {
         $data=keys_existance_check($health_data,"health_insurance");
         if ($data != false) {
            if(count($data)>1):
               $this->user_model->health_insurance_inserted($data);
            endif;
         } 
      }
   	public function patient_info($patient_data,$user_id='')
   	{
   		$this->patnt_info=["first_name"=>$patient_data['first_name'],"last_name"=>$patient_data['last_name'],"date_of_birth"=>$patient_data['date_of_birth'],"gender"=>$patient_data['gender'],"user_id"=>$patient_data['user_id'],"created_date"=>date("Y-m-d h:i:s")];
   		$patient_id=$this->user_model->signup_data_inserted($this->patnt_info,$user_id);
   		return $patient_id;
   	}
   	public function user_address($add_data,$user_id='')
   	{
   		$this->address_data=["address"=>$add_data['address'],"city"=>$add_data['city'],"state"=>$add_data['state'],"zip_code"=>$add_data['zip_code']];
   		$add_id=$this->user_model->address_data_inserted($this->address_data,$user_id);
   		return $add_id;
   	}




   	public function medical_profile($medcl_data,$user_id='')
   	{
   		//dd($medcl_data);
   		$this->medical_data=["patient_id"=>$medcl_data['user_id'],"medications"=>$medcl_data['medications'],"allergies"=>$medcl_data["allergies"],"past_medical_history"=>$medcl_data["past_medical_history"],"social_history"=>$medcl_data["social_history"],"family_history"=>$medcl_data["family_history"],"created_at"=>date("Y-m-d h:i:s")];
   		$this->user_model->user_medical_profile($this->medical_data,$user_id);
   	}
   	// This function is used for display the profile of users only
   	public function userprofile_post()
   	{
   		try{
   			$this->user_data= json_decode( file_get_contents('php://input'),true ); //$this->input->post();
   			if ($this->user_data['user_id']!='') {
   				
   				$this->_loadModel();
   				$user_profile=$this->user_model->get_user_profile($this->user_data['user_id']);
   				if ($user_profile!=false) {
   					$this->response_send=["message"=>"user profile","payload"=>$user_profile,"status"=>$this->config->item("status_true")];
   				}
   				else
   				{
   					$this->response_send=["message"=>$this->lang->line('no_user_found'),"status"=>$this->config->item("status_false")];
   				}
   			}
   			else
   			{
   				$this->response_send=["message"=>$this->lang->line('user_id'),"status"=>$this->config->item("status_false")];
   			}
   			$this->response($this->response_send);
   		}
   		catch (Exception $exc) {
   			$this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
   			$this->response($this->response_send);
   		}
   	}

   	/* This function is used for submited edit inoformation  of user profile with some additional inofmation */
   	public function usereditsubmited_post()
   	{
   		try{
   			$this->user_data=json_decode( file_get_contents('php://input'),true );
   			if ($this->user_data['first_name']!='' && $this->user_data['last_name']!='' && $this->user_data['date_of_birth']!='' && $this->user_data['gender']!='' && $this->user_data['address']!='' && $this->user_data['city']!='' && $this->user_data['state']!='' && $this->user_data['zip_code']!='' && $this->user_data['user_id']!='') {
   				if($this->validate_input("fname_validate")!=false){
   					if ($this->validate_input("lname_validate")!=false) {
   						if ($this->validate_input("gender_validate")!=false) {
   							if ($this->validate_input("city")!=false) {
   								if ($this->validate_input("state")!=false) {
   									if ($this->validate_input("zip_code")!=false) {
   										if ($this->validate_input("medications")!=false){if ($this->validate_input("allergies")!=false){
   											if ($this->validate_input("past_medical_history") !=false){	
   												if ($this->validate_input("social_history") !=false){
   													if ($this->validate_input("family_history") !=false){	
   														$this->_loadModel();
   														if (is_numeric($this->user_data['user_id'])) {
   															//;
   														 // address upadate
   															$address_id=$this->user_address($this->user_data,$this->user_data['user_id']);
   															$this->patient_info($this->user_data,$this->user_data['user_id']);
   															$this->medical_profile($this->user_data,$this->user_data['user_id']);
   															$this->response_send=["message"=>$this->lang->line('user_information_updated'),"status"=>$this->config->item("status_true")];   														}
   														else
   														{
   															$this->response_send=["message"=>$this->lang->line('user_id_invalid'),"status"=>$this->config->item("status_false")];
   														}
   													}
   													else
   													{
   														$this->response_send=["message"=>$this->lang->line('family_history'),"status"=>$this->config->item("status_false")];
   													}
   												}
   												else
   												{
   													$this->response_send=["message"=>$this->lang->line('social_history'),"status"=>$this->config->item("status_false")];
   												}
   											}
   											else
   											{
   												$this->response_send=["message"=>$this->lang->line('past_medical_history'),"status"=>$this->config->item("status_false")];
   											}
   										}
   										else
   										{
   											$this->response_send=["message"=>$this->lang->line('allergies'),"status"=>$this->config->item("status_false")];
   										}
   									}
   									else
   									{
   										$this->response_send=["message"=>$this->lang->line('medications'),"status"=>$this->config->item("status_false")];
   									}
   								} 
   								else {
   									$this->response_send=["message"=>$this->lang->line('zip_code'),"status"=>$this->config->item("status_false")];
   								}
   							} else {
   								$this->response_send=["message"=>$this->lang->line('state'),"status"=>$this->config->item("status_false")];
   							}
   						} else {
   							$this->response_send=["message"=>$this->lang->line('city'),"status"=>$this->config->item("status_false")];
   						}
   					} else {
   						$this->response_send=["message"=>$this->lang->line('gender'),"status"=>$this->config->item("status_false")];
   					}		
   				} else {
   					$this->response_send=["message"=>$this->lang->line('lirst_name'),"status"=>$this->config->item("status_false")];
   				}
   			} else {
   				$this->response_send=["message"=>$this->lang->line('first_name'),"status"=>$this->config->item("status_false")];
   			}
   		} else {
   			$this->response_send=["message"=>$this->lang->line('all_field_required'),"status"=>$this->config->item("status_false")];
   		}
   		$this->response($this->response_send);
   	}
   	catch (Exception $exc) {
   		$this->response_send=["message"=>$exc->getMessage(),"status"=>$this->config->item("status_false")];
   		$this->response($this->response_send);
   	}
   }

   public function user_file_upload($file='',$userid='')
   {
   	$user_name=$file['userfile']['name'];
   	$this->load->library("common");
   	$this->load->helper('string');
   	if ($user_name!='' && !empty($file) && count($file)>0) {
         remove_existing_img($userid,$this->patient_tbl,$this->profile_field,'user_id',$this->user_image_folder);
   		$rename_image=(random_string('numeric')+time()).random_string();
   		$img_upload=$this->common->file_upload($this->user_image_folder,$user_name,$rename_image);
   		if (isset($img_upload["upload_data"]['file_name'])) {
   			$file_url=base_url().$this->user_image_folder."/".$img_upload["upload_data"]['file_name'];
   			$file_name = $img_upload["upload_data"]['file_name'];
   			$this->user_model->user_image_update($file_name,$file_url,$userid);
   			return TRUE;
   		}
   		else
   		{
   			return $img_upload;
   		}
   	}
   	else
   	{
   		return FALSE;
   	}
   }

   // email verfication call above function
   public function email_verification($email)
   {
   	$this->_loadModel();
   	//$this->load->library('encrypt');
   	$this->config->load('shared');
   	$email_encoded=base64_encode($email);
   	$response=$this->user_model->email_verification_code(trim($email),$email_encoded);
   	if ($response) {
   		$this->load->library("email_setting");
   		$from=$this->config->item("from");
        $subject=$this->config->item("email_verified"); // language file is not working
        $message= $this->config->item("email_link").base_url()."api/nonapi/emailauth/".$email_encoded;
        $this->email_setting->send_email($email,$from,$message,$subject);
    }
  }	
}
?>