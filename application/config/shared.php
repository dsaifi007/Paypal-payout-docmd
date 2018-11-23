<?php
/*
|--------------------------------------------------------------------------
| Email Verfication  setting
|--------------------------------------------------------------------------
| 
*/
$config['from']          = "contact@docmdapp.com"; //"verify@docmdapp.com";
$config['email_verified']= "Email Verification";
$config['email_link']    = "Please click Below the link for email Verification "."<br> ";


/*
|--------------------------------------------------------------------------
| Email config
|--------------------------------------------------------------------------
| 
*/
// $config['smtp']          = "smtp";
// $config['useragent']	 = "Docmd";
// $config['mailpath']      = "/usr/sbin/sendmail";
// $config['smtp_host']     = "ssl://smtp.googlemail.com";
// $config['smtp_port']     = "465";
// $config['smtp_user']     = "danishk@chromeinfotech.com";
// $config['smtp_pass']     = "";
// $config['mailtype']      = "html";
// $config['charset']       = "iso-8859-1";
// $config['wordwrap']      = TRUE;
// $config['newline']       = "\r\n";



$config['smtp_crypto'] = 'tls'; //"STARTTLS";
$config['protocol'] = "smtp";
$config['smtp_host'] = "smtp-mail.outlook.com"; //'smtp-mail.outlook.com';
$config['smtp_port'] = "587";
$config['smtp_user'] = "contact@docmdapp.com"; //""  verify@docmdapp.com;
$config['smtp_pass'] = "DOCMD123!"; //"Docmd123!";
$config['mailtype'] = "html";
$config['charset']  = "iso-8859-1";
$config['newline']  = "\r\n";

/*
|--------------------------------------------------------------------------
| File Settting
|--------------------------------------------------------------------------
| 
*/
$config['allowed_types'] = 'jpg|png|jpeg';
$config['max_size']		 =  0;
$config['max_width']     =  0;
$config['max_height']    =  0;



?>