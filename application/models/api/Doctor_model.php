<?php

/*
  |-------------------------------------------------------------------------------
  |  All Users information get/set/save/delete
  |-------------------------------------------------------------------------------
 */

class Doctor_model extends CI_Model {

    private $doctor_table = "doctors";
    private $doc_professional_info_table = "doc_professional_info";
    private $professional_info = "doc_professional_info";
    private $address_table = "address";
    private $doctor_address_mapping = "doctor_address";
    protected $degree_table = "doctor_degree";
    protected $speciality = "spacility";
    private $array = [];
    private $is_boolean;
    private $otp_table = "user_otp";

    function __construct() {
        parent::__construct();
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will check the email and phone number existance in DB
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function check_email_phone_existance($em, $phone) {
        $this->db->where("email", $em)->or_where("phone", $phone);
        $query = $this->db->select("email,phone")->from($this->doctor_table)->get();
        return ($query->num_rows() > 0) ? true : false;
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert the user info and get particular data of inserted users
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function create_doctor_account_data_insert($post_data = []) {
        $this->db->where("email", $post_data['email'])->or_where("phone", $post_data['phone']);
        $query = $this->db->select("email,phone")->from($this->doctor_table)->get();
        if ($query->num_rows() == 0) {
            $post_data['password'] = md5($post_data['password']);
            $post_data['access_token'] = getaccessToken();
            $this->db->insert($this->doctor_table, form_input_filter($post_data));
            $id = $this->db->insert_id();

            $this->db->where("id", $id);
            $query = $this->db->select("id,email,phone,access_token,is_email_verified,is_phone_verified,is_blocked")
                            ->from($this->doctor_table)->get();
            $row = $query->row_array();
            $this->array['is_email_verified'] = ($row['is_email_verified'] == 0) ? false : true;
            $this->array['is_phone_verified'] = ($row['is_phone_verified'] == 0) ? false : true;
            $this->array['is_blocked'] = ($row['is_blocked'] == 0 ) ? false : true;
            return array_merge($row, $this->array);
        } else {
            return false;
        }
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert address and address mapping data based on doctor id
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function doctor_address_inserted($doc_address, $doc_id) {
        $this->db->insert($this->address_table, $doc_address);
        $address_id = $this->db->insert_id();
        $this->db->insert($this->doctor_address_mapping, ["doctor_id" => $doc_id, 'address_id' => $address_id]);
       // update MED_ID
       $med_id = strtoupper(substr($doc_address['state'], 0, 2)).'-'.$doc_id;
       $this->db->where("id",$doc_id);
       $this->db->update("doctors",['med_id'=>$med_id]);
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert doctor professional data based on id
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function doctor_professional_info_insert($doctor_professional_data) {
        $this->db->insert($this->professional_info, $doctor_professional_data);
        return $this->db->insert_id();
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will update password based on user id
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function update_user_password($user_data) {
        $this->db->reset_query();
        $query = $this->db->get_where($this->doctor_table, ['id' => $user_data['userid']]);
        if ($query->num_rows() > 0) {
            $this->user_pass['password'] = md5($user_data['password']);
            $this->db->where('id', $user_data['userid']);
            $this->db->update($this->doctor_table, $this->user_pass);
            $this->is_boolean = TRUE;
        } else {
            $this->is_boolean = FALSE;
        }
        return $this->is_boolean;
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will insert the basic information of users
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function update_doctor_info_model($doctor_data, $id,$doctor_info) {
        
        $speciality = array();
        $degree = array();

        //$this->db->where("doctor_id",$doctor_info['id']);
       // $this->db->delete("doctor_speciality");
        
        //$this->db->where("doctor_id",$doctor_info['id']);
        //$this->db->delete("doctor_degree_mapping");
        

        foreach ($doctor_info['speciality_id'] as $key => $value) {
            $speciality[] = ["doctor_id" => $doctor_info['id'], "spacility_id" => $value];
        }
        foreach ($doctor_info['degree_id'] as $k => $v) {
            $degree[] = ["doctor_id" => $doctor_info['id'], "degree_id" => $v];
        }
        $this->db->insert_batch("doctor_speciality", $speciality);
        $this->db->insert_batch("doctor_degree_mapping", $degree);

        $this->db->where("id", $id);
        $this->db->update($this->doctor_table, $doctor_data);
        return true;
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will update the profile image of the doctor
      |-------------------------------------------------------------------------------------------------------------------------------
     */

    public function doctor_profile_img_update_model($file_name, $file_url, $doc_id) {
        $this->db->where("id", $doc_id);
        $this->db->update($this->doctor_table, ["profile_url" => $file_url, "profile_image" => $file_name]);
    }

    /*
      |-------------------------------------------------------------------------------------------------------------------------------
      | This Function will get the information of the doctor
      |-------------------------------------------------------------------------------------------------------------------------------
     */
    public function get_doctor_profile($doctor_id) {

        $select_field = "doc_info.id as doctor_id,
                         doc_info.med_id,
                         doc_info.first_name,
                         doc_info.last_name,
                         doc_info.date_of_birth,
                         doc_info.gender as gender ,
                         doc_info.profile_url as profile_image_url ,
                         doc_add.address, doc_add.city AS city ,
                         doc_add.state AS state,doc_add.zip_code,
                         doc_profess.undergraduate_university,
                         doc_profess.medical_school,doc_profess.residency,
                         doc_profess.medical_license_number,
                         doc_profess.additional_info,
                         doc_info.mal_practice_information,
                         (SELECT GROUP_CONCAT(`spacility`.`name`,'|||',spacility.id SEPARATOR '##') AS `spacility`  FROM spacility INNER JOIN doctor_speciality ON 
                            doctor_speciality.spacility_id=spacility.id WHERE doctor_speciality.doctor_id='".$doctor_id."' GROUP BY doctor_speciality.doctor_id) AS speciality,
                            (SELECT GROUP_CONCAT(doctor_degree.degree,'|||',doctor_degree.id SEPARATOR '##') AS `doctor_degree` FROM doctor_degree_mapping INNER JOIN doctor_degree ON 
                            doctor_degree.id=doctor_degree_mapping.degree_id WHERE doctor_degree_mapping.doctor_id='".$doctor_id."' GROUP BY doctor_degree_mapping.doctor_id ORDER BY doctor_degree.id ASC) AS degrees,
                         language.name as language,
                         language.language_id
                         ";
        $this->db->select($select_field)
                ->from("$this->doctor_table as doc_info")
                ->join("$this->doc_professional_info_table as doc_profess", "doc_profess.doctor_id = doc_info.id ", "INNER")
                ->join("language", "language.language_id = doc_info.language_id", "INNER")
                ->join("$this->doctor_address_mapping as doc_add_map", "doc_add_map.doctor_id = doc_info.id", "INNER")
                ->join("$this->address_table as doc_add", "doc_add.id = doc_add_map.address_id", "INNER")
                ->where("doc_info.id", $doctor_id);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        return count($query->row_array()) > 0 ? $query->row_array() : false;
    }

        // get doctor speciality and degree
    public function get_doct_speciality_and_degree_model($lang)
    {
      $sp = ($lang == "spn")?"sp_name AS speciality":"name as speciality";
      $this->db->select("id,$sp")
              ->from("$this->speciality");
          $query =  $this->db->get();
          $spl_row = $query->result_array();

      $spdegree = ($lang == "spn")?"sp_degree AS degree":"degree";    
      $this->db->select("id,$spdegree")
              ->from("$this->degree_table");
          $deg_query =  $this->db->get();
          $deg_row = $deg_query->result_array();
          return array_merge(["specialities"=>$spl_row],["degrees"=>$deg_row]);
    }
    // add verfication code
    public function email_verification_code($email, $email_encoded) {
        //$this->load->library('encrypt');
        $this->db->where("email", $email);
        $query = $this->db->update($this->doctor_table, ["email_verification_code" => $email_encoded]);
        if ($query) {
            return true;
        }
    }

    // when email verfied by email
    public function email_verified($email) {
        $query = $this->db->get_where($this->doctor_table, ['email' => $email]);
        if ($query->num_rows() > 0) {
            $this->db->where("email", $email);
            $this->db->update($this->doctor_table, ["is_email_verified" => $this->config->item("email_verified")]);
            return true;
        } else {
            return false;
        }
    }
    public function store_new_otp($otp_Data) {
        $this->db->insert($this->otp_table, $otp_Data);
    }
    public function get_otp($otp_data) {
        $this->db->where($otp_data);
        $query = $this->db->select("expire_time")
                ->from($this->otp_table)->get();
        return ($query->num_rows() > 0) ? $query->row() :false;
    }
    public function update_otp($doctor_id,$new_otp,$expire_time) {
        $this->db->where("doctor_id",$doctor_id);
        $this->db->update($this->otp_table,["expire_time" => $expire_time,"otp_number" => $new_otp]);
          
        $this->db->where("doctor_id",$doctor_id);
        $query = $this->db->select("otp_number")
                ->from($this->otp_table)->get();
        return $query->row();
    }
   public function delete_otp($doctor_id) {
        $this->db->where("doctor_id",$doctor_id); 
        $this->db->delete($this->otp_table);
    }
    public function check_phone_existance($doctor_id) {
        $this->db->where("id", $doctor_id);
        $query = $this->db->select("phone")->from($this->doctor_table)->get();
        return ($query->num_rows() > 0) ? $query->row() : false;
    }
    public function update_phone_status($doctor_id) {
        $this->db->where("id",$doctor_id);
        $this->db->update($this->doctor_table,["is_phone_verified" => $this->config->item("phone_verified")]);
    }
    public function get_userid($email, $phone) {
        $query = $this->db->get_where($this->doctor_table, ['email' => $email, "phone" => $phone]);
        $row = $query->row_array();
        if (count($row) > 0) {
            return $row['id'];
        }
    }
    function update_doctor_address($data) {
        $address = [
            "address"=> $data['address'],
            "city"=> $data['city'] ,
            "state" => $data['state'],
            "zip_code" => $data['zip_code']
        ];
       
        //$conditon = "SELECT  address_id FROM doctor_address WHERE doctor_id ='".$data['doctor_id']."'";
        $this->db->where("id IN(SELECT  address_id FROM doctor_address WHERE doctor_id ='".$data['doctor_id']."')",NULL,FALSE);
       $this->db->update($this->address_table,$address); 
       //echo $this->db->last_query();die;
    }
   function update_doctor_other_info($data) {
        $info = [
            "additional_info" => @$data['additional_info'],
                //"language_id"=> $data['language_id'] ,
                //"speciality_id" => $data['speciality_id'],
        ];

        //$conditon = "SELECT  address_id FROM doctor_address WHERE doctor_id ='".$data['doctor_id']."'";
        $this->db->where("doctor_id", $data['doctor_id']);
        $this->db->update($this->doc_professional_info_table, $info);

        if (!empty($data['speciality_id']) && count($data['speciality_id'])>0) {

            $this->db->where("doctor_id", $data['doctor_id']);
            $this->db->delete("doctor_speciality");

            foreach ($data['speciality_id'] as $key => $value) {
                $speciality[] = ["doctor_id" => $data['doctor_id'], "spacility_id" => $value];
            }
            $this->db->insert_batch("doctor_speciality", $speciality);
        }


        // update doctor id
        $this->db->where("id", $data['doctor_id']);
        $this->db->update($this->doctor_table, ["language_id" => $data['language_id']]);

        //echo $this->db->last_query();die;
    }
    public function doctor_language()
    {
      $this->db->select("language_id as id,name")->from("language");
      $q = $this->db->get();
      //echo $this->db->last_query();die;
      return $q->result_array();
    }
    function add_mal_practice_information($data) {
        $this->db->where("id",$data['doctor_id']);
        $this->db->update("doctors",["mal_practice_information"=>$data['mal_practice_information']]);
    }
}

?>