<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_send_controller extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("pushnotification");
        $this->load->model("admin/notification/notification_model", "notification_model");
    }

    //set crone job every minute
    public function send_specific_date_notifications() {
        // specific_date send notification = 1
        // get only specific_date  notification data from table 1=> year notification data
        $result = $this->notification_model->send_notification_model(1);

        if ($result) {
            foreach ($result as $key => $value) {

                if ($value['notification_type'] != '' && (date('Y-m-d G:i', strtotime($value['schedule_time'])) == date('Y-m-d G:i', strtotime($this->config->item("date"))))) {

                    //Multiple registration tokens, min 1 max 1000.

                    $tokens = get_device_token($value);

                    $token_array = explode("|||", $tokens['device_token']);

                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $response = $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                    echo $response;
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    //set crone job daily basis
    public function send_daily_notifications() {

        // daily send notification = 2
        // get  only daily notification data from table 2=>daily notification data
        $result = $this->notification_model->send_notification_model(2);

        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {
                    //Multiple registration tokens, min 1 max 1000.
                    // daily send notification = 2
                    $tokens = get_device_token($value);
                    $token_array = explode("|||", $tokens['device_token']);
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    //set crone job weekly basis
    public function send_weekly_notifications() {
        echo "<pre>";
        // weekly send notification = 3
        // get only weekly notification data from table 3=>weekly notification data
        $result = $this->notification_model->send_notification_model(3);

        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {

                    // weekly send notification = 3
                    $tokens = get_device_token($value);

                    $token_array = explode("|||", $tokens['device_token']);
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    //set crone job monthly basis
    public function send_monthly_notifications() {
        // weekly send notification = 4
        // get only monthly notification data from table 4=>monthly notification data
        $result = $this->notification_model->send_notification_model(4);

        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {
                    //Multiple registration tokens, min 1 max 1000.
                    // monthly send notification = 3
                    $tokens = get_device_token($value);

                    $token_array = explode("|||", $tokens['device_token']);
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $response = $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    //set crone job six monthly basis
    public function send_six_monthly_notifications() {
        // weekly send notification = 5
        // get only six monthly notification data from table 5=> six monthly notification data
        $result = $this->notification_model->send_notification_model(5);
        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {
                    //Multiple registration tokens, min 1 max 1000.
                    // monthly send notification = 3
                    $tokens = get_device_token($value);

                    $token_array = explode("|||", $tokens['device_token']);
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $response = $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    //set crone job year  basis
    public function send_year_notifications() {
        // weekly send notification = 6
        // get only 1 year  notification data from table 6=> year notification data
        $result = $this->notification_model->send_notification_model(6);

        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {
                    //Multiple registration tokens, min 1 max 1000.
                    // monthly send notification = 3
                    $tokens = get_device_token($value);

                    $token_array = explode("|||", $tokens['device_token']);
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    // without message fcm is not allowed the send notification
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    // send the notification to FCM
                    $response = $this->pushnotification->sendPushNotificationToFCMSever($token_array, $message, $title);
                }
            }
        } else {
            echo json_encode(["message" => "No data found"]);
            die;
        }
    }

    public function send_notifications() {
        $result = $this->notification_model->send_notification_model([2, 3, 4, 5, 6]);

        $token = array();
        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['notification_type'] != '') {
                    if (($value['notification_scheduler_id'] == 2) && ($value['schedule_time'] == date("Y-m-d"))) {
                        $date = ["schedule_time" => date("Y-m-d", strtotime('+1 day'))];
                        $token = get_all_device_token($value);

                        $this->db->where("id", $value['id']);
                        $this->db->update("notification_content", $date);
                    } elseif (($value['notification_scheduler_id'] == 3) && ($value['schedule_time'] == date("Y-m-d"))) {

                        $date = ["schedule_time" => date("Y-m-d H:i:s", strtotime('+7 day'))];
                        $token = get_all_device_token($value);

                        $this->db->where("id", $value['id']);
                        $this->db->update("notification_content", $date);
                    } elseif (($value['notification_scheduler_id'] == 4) && ($value['schedule_time'] == date("Y-m-d"))) {
                        $date = ["schedule_time" => date("Y-m-d H:i:s", strtotime('+30 day'))];
                        $token = get_all_device_token($value);
                    } elseif (($value['notification_scheduler_id'] == 5) && ($value['schedule_time'] == date("Y-m-d"))) {

                        $date = ["schedule_time" => date("Y-m-d H:i:s", strtotime('+180 day'))];
                        $token = get_all_device_token($value);

                        $this->db->where("id", $value['id']);
                        $this->db->update("notification_content", $date);
                    } elseif (($value['notification_scheduler_id'] == 6) && ($value['schedule_time'] == date("Y-m-d"))) {
                        $date = ["schedule_time" => date("Y-m-d H:i:s", strtotime('+360 day'))];
                        $token = get_all_device_token($value);

                        $this->db->where("id", $value['id']);
                        $this->db->update("notification_content", $date);
                    }

                    //$token_array = $tokens['device_token'];
                    $title = [
                        "title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"
                    ];
                    $message = ["title" => $value['name'],
                        "body" => $value['additional_info'],
                        "type" => "DEFAULT"];
                    if (!empty($token)) {
                        $response = $this->pushnotification->sendPushNotificationToFCMSever($token, $message, $title);
                        echo $response;
                        unset($token);
                    }
                }
            }
        }
    }

}

?>