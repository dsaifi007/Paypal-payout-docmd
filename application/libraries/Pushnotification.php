<?php 
class Pushnotification {    
    private static $API_SERVER_KEY = 'AAAA6OrRkZ8:APA91bHCZBvfLdi5w-SKyvq6Mn0e-HGY_V0uVjxd4baS95bkwh1WSSZ1UTi_Oxrvn88gzcjswCMc4X5KwNiAjijOpgBamcQd9T7s48m2aCFrYo7SQ96AqEmXfYOamT4fq4arJTY4byay';
    private static $is_background = "TRUE";

    public function sendPushNotificationToFCMSever($token, $message, $title=null) {
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        $tokens = (is_array($token)) ? $token : array($token);
        
        if($title == null){
            $notf_title = array('title' => 'Appointment', 'body' =>'New Appointment');
        }else{
            $notf_title = $title;
        }
        

        $fields = array(
            'registration_ids' => $tokens,
            'priority' => "high", //10,
            'notification' => $notf_title,
            'data' =>$message
        );
        
        $headers = array(
            'Authorization:key=' . self::$API_SERVER_KEY,
            'Content-Type:application/json'
        );  
         
        // Open connection  
        $ch = curl_init(); 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post   
        $result = curl_exec($ch);
        if ($result === FALSE) {
		 die('FCM Send Error: ' . curl_error($ch));
		 }
		// if ( $status != 201 ) {
		//   die("Error: call to URL $ch failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
		// }
        // Close connection      
        curl_close($ch);
        return $result;
    }
  //   public function test()
  //   {
  //   	$url = "https://fcm.googleapis.com/fcm/send";
		// $token = "AAAA6OrRkZ8:APA91bHCZBvfLdi5w-SKyvq6Mn0e-HGY_V0uVjxd4baS95bkwh1WSSZ1UTi_Oxrvn88gzcjswCMc4X5KwNiAjijOpgBamcQd9T7s48m2aCFrYo7SQ96AqEmXfYOamT4fq4arJTY4byay";
		// $serverKey = 'AAAA6OrRkZ8:APA91bHCZBvfLdi5w-SKyvq6Mn0e-HGY_V0uVjxd4baS95bkwh1WSSZ1UTi_Oxrvn88gzcjswCMc4X5KwNiAjijOpgBamcQd9T7s48m2aCFrYo7SQ96AqEmXfYOamT4fq4arJTY4byay';
		// $title = "Title";
		// $body = "Body of the message";
		// $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
		// $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
		// $json = json_encode($arrayToSend);
		// $headers = array();
		// $headers[] = 'Content-Type: application/json';
		// $headers[] = 'Authorization: key='. $serverKey;
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);

		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

		// "POST");
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		// curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		// //Send the request
		// $response = curl_exec($ch);
		// //Close request
		// if ($response === FALSE) {
		// die('FCM Send Error: ' . curl_error($ch));
		// }
		// curl_close($ch);
		//     }
 }
?>