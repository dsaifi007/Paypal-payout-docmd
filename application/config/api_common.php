<?php
/*
	All Api model define here ------
*/
$config['symptoms_model'] = "api/symptoms_model";
$config['treatment_plan_model'] = "api/treatment_plan_model";



/*
----------------------------------------------------------------------------------------------
							All Api Table name is defined here
----------------------------------------------------------------------------------------------
*/
$config['symptoms_table'] = "symptom";
$config['severity_symptoms_table'] = "severity_of_symptoms";
$config['treatment_table'] = "provider_plan";



/*
----------------------------------------------------------------------------------------------
							Is recomended option in provider plan array
----------------------------------------------------------------------------------------------
*/
$config['is_recommended'] = 1;

$config['appointment_date'] = date("Y-m-d H:i:s");
$config['upcoming_status'] = [1,4,5];
$config['recent_status'] = [1,6,4,5];
$config['cancel_status'] = [2,3];
$config['missed_status'] = [7];
$config['free_slot_status'] = 0;
$config['lang_code'] = "eng-spn";

$config['appointment_cancel_by_patient'] = 2;
$config['appointment_cancel_by_doctor'] = 3;
$config['appointment_reschedule_by_user'] = 4;
$config['appointment_reschedule_by_doctor'] = 5;

$config['update_doctor_slot_status'] = 1;


/*
 * Date 17/oct/2018
 */
$config['environment'] = "sandbox";
$config['merchantId'] = "rz67sbj5c4pjj8w7";
$config['publicKey'] = "dhy276tg5kb42z2g";
$config['privateKey'] = "cadefd39ec14132574635028bda814cb";

/*
 * 4|june|2018
 */
$config['login'] = '1';
$config['logout'] = '0';
?>