<?php

require(APPPATH . '/libraries/REST_Controller.php');

class Doctor_availability extends REST_Controller {

    protected $response_send = [];
    protected $headers;
    protected $availabilty_data;

    /*
      |-----------------------------------------------------------------------------------------------------------
      | This Function will check the content type and change the language
      |------------------------------------------------------------------------------------------------------------
     */

    public function __construct() {
        try {
            $this->headers = apache_request_headers();
            parent::__construct();
            content_type($this->headers);
            change_languge($this->headers);
            $this->load->model("api/doctor_availability_model");
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
            $this->response($this->response_send);
        }
    }

    /*
      |--------------------------------------------------------------------------------
      | Work -- we are adding the availabilty data
      | @return -- insert the data with doctor id
      |--------------------------------------------------------------------------------
     */

    public function insert_doctor_availabilty_data_post() {
        try {
            $this->availabilty_data = json_decode(file_get_contents('php://input'), true);
            //dd($this->availabilty_data);
            $field_name = [
                "type",
                "doctor_id",
                "slots"
            ];
            check_acces_token(@$this->headers['Authorization'], $this->availabilty_data['doctor_id'], 'doctors');
            if (check_form_array_keys_existance($this->availabilty_data, $field_name)) {
                $this->doctor_availability_model->doctor_availabilty_insert_model($this->availabilty_data);               
                $this->set_doctor_avalibality($this->availabilty_data);
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }

    public function set_doctor_avalibality($data) {
		 try {
			if (!empty($data) && $data['type'] == "weekly") {
				 //$dates_id = get_date($days); // insert date 
				//$date_id = $this->doctor_availability_model->get_date_id($dates_id);
				$this->weekly_slot_mapping_insert($data);
				$this->response_send = ["message" =>$this->lang->line("weekly_slot"), "status" => $this->config->item("status_true")];
				//pending
			} elseif ($data['type'] == "daily") {
				$this->doctor_availability_model->doctor_daily_date_insert();
				$this->daily_slot_mapping_insert($data);
			   $this->response_send = ["message" =>$this->lang->line("daily_slot"), "status" => $this->config->item("status_true")];

			} elseif ($data['type'] == "onetime") {
				
				$date_id = $this->doctor_availability_model->doctor_onetime_date($data['slots'][0]['date']);
				
				$this->onetime_slot_mapping_insert($data, $date_id);
				$this->response_send = ["message" =>$this->lang->line("ontime_slot"), "status" => $this->config->item("status_true")];
			}
		} catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }

    private function weekly_slot_mapping_insert($data) {
        $slot_id= array();
        $final_data=[];
        $insert_array=[];
       $endDate = strtotime(date('Y-m-d', strtotime("+30 days")));
        foreach ($data['slots'] as $k => $day) {

            for ($i = strtotime(ucfirst($day['day_id']), strtotime(date('Y-m-d'))); $i <= $endDate; $i = strtotime('+1 week', $i)) {
                if (strtolower($day['day_id']) == "monday") {
                   $monday_id = get_date(date('Y-m-d', $i));//get date id
                   $final_data[] = get_weekly_slot($day,$data['doctor_id'],$monday_id);
                }elseif (strtolower($day['day_id']) == "tuesday") {
                    $tuesday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day, $data['doctor_id'],$tuesday_id);
                }
                elseif (strtolower($day['day_id']) == "wednesday") {
                    $wednesday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day, $data['doctor_id'],$wednesday_id);
                }
                elseif (strtolower($day['day_id']) == "thursday") {
                    $thursday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day, $data['doctor_id'],$thursday_id);
                }
                elseif (strtolower($day['day_id']) == "friday") {
                    $friday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day,$data['doctor_id'],$friday_id);
                }
                elseif (strtolower($day['day_id']) == "saturday") {
                    $saturday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day,$data['doctor_id'],$saturday_id);
                }
                elseif (strtolower($day['day_id']) == "sunday") {
                    $sunday_id = get_date(date('Y-m-d', $i));//get date id
                    $final_data[] = get_weekly_slot($day,$data['doctor_id'],$sunday_id);
                }
            }
        }
        foreach ($final_data as  $v) {
            foreach ($v as  $value) {
				$query = $this->db->get_where("doctor_slots",["doctor_id"=>$value['doctor_id'],"date_id"=>$value['date_id'],"slot_id"=>$value['slot_id'],"status"=>1]);
				if($query->num_rows() == 0){
					$insert_array[] = $value;
				}
			}
        }     
        $this->db->where(["doctor_id"=>$data['doctor_id'],"status"=>0]);
        $this->db->delete("doctor_slots");
        //echo $this->db->last_query();die;
        //dd($insert_array);
        
        $this->db->insert_batch("doctor_slots",$insert_array);
    }

    public function daily_slot_mapping_insert($data) {
        $doctorId = $data["doctor_id"];
        $now = time();
        $daily_array = [];
        $slotId = array();
        foreach ($data['slots'][0]['time_list'] as $key => $daily_slot_time) {
            $slotId = get_slot_id($daily_slot_time['from'], $daily_slot_time['to'], $slotId);
        }
        $doctorInfo = array();

        for ($i = 0; $i <= 29; $i++) {
            $this->db->select("id");
            $this->db->where("date_available", date('Y-m-d', $now + (60 * 60 * 24 * $i)));
            $query = $this->db->get('date_availability_list');
            $daily_date_id = $query->row_array();


            foreach ($slotId as $slotInfo) {
                $query = $this->db->get_where("doctor_slots",["doctor_id"=>$doctorId,"date_id"=>$daily_date_id['id'],"slot_id"=>$slotInfo,"status"=>1]);
				if($query->num_rows() == 0){
						$doctorInfo[] = array(
						"doctor_id" => $doctorId,
						"date_id" => $daily_date_id['id'],
						"slot_id" => $slotInfo
					);
				}
            }
        }
         //dd($doctorInfo);
        $this->db->where(["doctor_id"=>$doctorId,"status"=>0]);
        $this->db->delete("doctor_slots");

        $this->db->insert_batch("doctor_slots", $doctorInfo);
    }
    public function onetime_slot_mapping_insert($data,$date_id) {
        $slotId =array();
        $doctorInfo=[];
         foreach ($data['slots'][0]['time_list'] as $key => $one_slot_time) {
            $slotId = get_slot_id($one_slot_time['from'], $one_slot_time['to'], $slotId);      
        }
        foreach ($slotId as $slotInfo) {
			$query = $this->db->get_where("doctor_slots",["doctor_id"=>$data['doctor_id'],"date_id"=>$date_id,"slot_id"=>$slotInfo,"status"=>1]);
			if($query->num_rows() == 0){
                $doctorInfo[] = array(
                    "doctor_id" => $data['doctor_id'],
                    "date_id" => $date_id,
                    "slot_id" => $slotInfo
                );
			}
        }
        
           $this->db->where(["doctor_id"=>$data['doctor_id'],"status"=>0]);
           $this->db->delete("doctor_slots");
           $this->db->insert_batch("doctor_slots", $doctorInfo);
		   
    }
    
    
    function get_doctor_availability_post() {
        try {
            $doctor_id = json_decode(file_get_contents('php://input'), true);
            if ($doctor_id['doctor_id'] !='') {
                $data = $this->doctor_availability_model->get_doctor_slot_model($doctor_id['doctor_id'],$doctor_id['type']);
               if ($data) {
                    // when type not null
                    $this->response_send = ["type"=>$data['type'],"slots"=>json_decode($data['slots']),"status" => $this->config->item("status_true")];
               }
              else{
                // when type is null
                $this->response_send = ["message" => $this->lang->line('no_doctor_available'), "status" => $this->config->item("status_false")];
              }
            }
            else {
                $this->response_send = ["message" => $this->lang->line('all_field_required'), "status" => $this->config->item("status_false")];
            }
            $this->response($this->response_send);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
    }
    function get_doctor_slots_post() {
        try {
            $this->doctor_slot = json_decode(file_get_contents('php://input'), true);
           $data = $this->doctor_availability_model->get_doctor_slot_model($this->get_doctor_slot['doctor_id']);
           $this->response($data);
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
 
    }
        public function get_doctor_available_date_get() {
        try {
            //check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_id = $this->get();
            if (check_form_array_keys_existance($doctor_id, ["doctor_id"]) && check_user_input_values($doctor_id)) {

                $data = $this->doctor_availability_model->get_doctor_free_date($doctor_id['doctor_id']);
                if ($data) {
                    $this->response_send = ["avaliable_dates" => $data, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["avaliable_dates" =>$this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
    public function getDoctorFreeSlotBasedOnDate_post() {
        try {
            //check_acces_token(@$this->headers['Authorization'], null, "doctors");
            $doctor_data = json_decode(file_get_contents('php://input'), true);
            if (check_form_array_keys_existance($doctor_data, ["doctor_id","date","time_abbreviation"]) && check_user_input_values($doctor_data)) {

                $data = $this->doctor_availability_model->get_doctor_free_slot($doctor_data);
                
                if ($data) {
                    $this->response_send = ["slots" => $data, "status" => $this->config->item("status_true")];
                } else {
                    $this->response_send = ["avaliable_dates" =>$this->lang->line("no_data_found"), "status" => $this->config->item("status_false")];
                }
            } else {
                $this->response_send = ["message" => $this->lang->line('field_name_missing'), "status" => $this->config->item("status_false")];
            }
        } catch (Exception $exc) {
            $this->response_send = ["message" => $exc->getMessage(), "status" => $this->config->item("status_false")];
        }
        $this->response($this->response_send);
    }
}

?>