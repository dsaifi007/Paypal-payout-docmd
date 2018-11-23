<?php

//require(APPPATH . '/libraries/REST_Controller.php');

class Receive_message_controller extends CI_Controller {

    protected $table_name = "users";

    public function message_receive()
    {
        $number = $_POST['From']; // user/provider number 
        $body = json_decode(base64_decode($_POST['Body'])); // content
       

        header('Content-Type: text/xml');

        // get the doctor/user email ID by the phone number

        if ($body->type == "doctor") {
            $this->table_name = "doctors";
        }
        $this->db->where("phone",$body->phone);
        $this->db->select("email,phone");
        $this->db->from($this->table_name);
        $query = $this->db->get();
        
        if($query->num_rows()>0){
          $email = $query->row_array();
          $sms_message = "Your Email is ".$email['email'];
          echo "<Response><Message>";
          echo $sms_message;
          echo "</Message></Response>";
            // change this url on live http://18.219.252.10/docmd/receive_message
            // URL for number https://www.twilio.com/console/phone-numbers/PN4180ea4206a561d1be461addaec7c19a
            // from (424) 238-3234 to recive message
      }else{
       echo "<Response><Message>";
       $sms_message = "This number is not regsitered in DOC MD";
       echo $sms_message;
       echo "</Message></Response>";
   }
    $this->load->library('twilio');
    $sms_sender = "+".trim($this->config->item("number"));
    $this->twilio->sms($sms_sender, $body->phone, $sms_message);
}


}
?>