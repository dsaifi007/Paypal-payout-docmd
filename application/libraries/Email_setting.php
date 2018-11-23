<?php

class Email_setting {

    protected $CI;

    public function send_email2($to = [], $from = "", $message = "", $subject = "", $cc = [], $bcc = "", $attatch = "") {
        try {
            $CI = & get_instance();
            //$header = 'From:"'.$CI->config->item('smtp_user').'" ' . "\r\n";
            //$headers .= 'MIME-Version: 1.0' . "\r\n";
            //$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";


            $account_name = 'DOC MD';
            $CI->config->load('shared');
            $CI->load->library('email');
            $CI->email->set_header('MIME-Version', '1.0; charset=utf-8');
            $CI->email->set_header('Content-type', 'text/html');
            $result = $CI->email
                    ->from($CI->config->item('smtp_user'), $account_name)
                    ->reply_to($CI->config->item('smtp_user'))
                    ->to($to)
                    ->subject($subject)
                    ->message($message)
                    ->attach($attatch)
                    ->send();
            return $result;
        } catch (Exception $e) {
            echo json_encode(['Message' => $e->getMessage(), "status" => "false"]);
            die;
        }
    }

    function send_email($to = [], $from = "", $message = "", $subject = "", $cc = [], $bcc = "", $attatch = "") {

        require_once APPPATH . 'third_party/phpmailer/PHPMailerAutoload.php';
        $CI = & get_instance();
        $mail = new PHPMailer();
        $CI->config->load('shared');
        $mailBody = "DOC MD";
        $body = $mailBody;
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->Host = $CI->config->item('smtp_host'); // SMTP server
        //$mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Host = $CI->config->item('smtp_host'); // sets the SMTP server
        $mail->Port = $CI->config->item('smtp_port'); // set the SMTP port for the GMAIL server
        $mail->Username = $CI->config->item('smtp_user'); // SMTP account username
        $mail->Password = $CI->config->item('smtp_pass'); // SMTP account password
        $mail->SetFrom($CI->config->item('smtp_user'), 'DOC MD');
        $mail->AddReplyTo($CI->config->item('smtp_user'), 'DOC MD');
        $mail->Subject = $subject;
        //$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($message);
        $mail->AddAttachment($attatch); ///var/www/html/docmd/assets/admin/img/email_template/1589442209ec2hyAax.zip
        //$address = $to;
        if (is_array($to) && !empty($to)) {
            foreach ($to as $email) {
                $mail->AddAddress($email);
            }
        } else {
            $mail->AddAddress($to);
        }
        return $mail->Send();
//        if (!$mail->Send()) {
//            echo "Mailer Error: " . $mail->ErrorInfo;
//            die;
//        } else {
//            echo "Message sent!";
//            die;
//        }
    }

}

?>