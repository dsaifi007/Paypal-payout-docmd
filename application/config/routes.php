<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'welcome';
$route['404_override'] = '';

/* 
-------------------------------------------------------------------------------------------
                        These routing use for users only 
-------------------------------------------------------------------------------------------
 */
$route['user/check'] = 'api/users/check_email_and_phone';
$route['user/create'] = 'api/users/createaccount';
$route['user/login'] = 'api/userlogin/userlogin';
$route['user/autologin'] = 'api/userlogin/autologin';
$route['user/forgotpassword'] = 'api/userlogin/forget_password';
$route['user/me'] = 'api/users/userprofile';
$route['patient/add/info'] = 'api/users/adduserinformation';
$route['patient/get/all'] = 'api/users/get_all_patients';
$route['patient/update/info'] = "api/users/usereditsubmited";
$route['patient/update/profileimage'] = "api/users/user_profile_img_update";
$route['user/getotp'] = "api/users/send_new_otp_to_user";
$route['user/verifyotp'] = "api/users/verifiy_user_otp";
$route['user/consent'] = "api/consentcare/consent_text";
$route['user/update/consent'] = "api/consentcare/is_read_consent_care_update";


/*
-------------------------------------------------------------------------------------------
                       These routing use for doctor only 
-------------------------------------------------------------------------------------------
*/
$route['doctor/check'] = "api/doctors/check_email_and_phone";
$route['doctor/create'] = "api/doctors/createaccount";
$route['doctor/add/info'] = "api/doctors/adddoctorinformation";
$route['doctor/language'] = "api/doctors/doctor_language";
$route['doctor/update/info'] = "api/doctors/doctoreditsubmited";
$route['doctor/get/speciality-n-degree'] = "api/doctors/get_doctor_speciality_and_degree";
$route['doctor/login'] = "api/doctorlogin/doctorlogin";
$route['doctor/autologin'] = "api/doctorlogin/autologin";


$route['doctor/update/profileimage'] = "api/doctors/doctor_profile_img_update";
$route['doctor/forgotpassword'] = "api/doctorlogin/forget_password";
$route['doctor/me'] = "api/doctors/doctorprofile";
$route['doctor/getotp'] = "api/doctors/send_new_otp_to_doctor";
$route['doctor/verifyotp'] = "api/doctors/verifiy_doctor_otp";

/* 
-------------------------------------------------------------------------------------------
                            These routing use Symptoms
------------------------------------------------------------------------------------------
*/
$route['get/symptoms'] = "api/symptoms_controller/get_all_symptoms";
$route['get/severity-of-symptoms'] = "api/symptoms_controller/get_severity_symptoms";
//---------------------------------------------------------------------------------------------

/* 
-------------------------------------------------------------------------------------------
                            These routing use Treatment plan
------------------------------------------------------------------------------------------
*/
$route['get/treatment-plan'] = "api/treatment_plan_controller/get_all_treatment_plan";


/* 
-------------------------------------------------------------------------------------------
                            These routing use Symptoms
------------------------------------------------------------------------------------------
*/
/* 
-------------------------------------------------------------------------------------------
                          These routing use for Appoinment
-------------------------------------------------------------------------------------------
 */
$route['appointment/create'] = "api/appointment_controller/create_patient_appoinment";
$route['appointment/sendnotification'] = "api/appointment_controller/send_appointment_notification_to_doctor";

$route['doctor/appointments'] = "api/appointment_controller/get_all_doctor_appointment";
$route['user/appointments'] = "api/appointment_controller/get_all_users_appointment";
$route['doctor/availablilty-list'] = "api/doctor_availability/get_doctor_availability";

$route['doctor/set-availability'] = "api/doctor_availability/insert_doctor_availabilty_data";

$route['appointment/get-free-slot'] = "api/appointment_controller/user_appointment_booking";


/* 09/05/2017  */
$route['user/appointment/detail'] = "api/appointment_detail/get_appointment_by_user";
$route['doctor/appointment/detail'] = "api/appointment_detail/get_appointment_by_doctor";
$route['user/appointment/cancel'] = "api/appointment_detail/cancel_appointment_by_user";
$route['doctor/appointment/cancel'] = "api/appointment_detail/cancel_appointment_by_doctor";

$route['user/appointment/reschedule'] = "api/appointment_detail/appointment_reschedule_by_user";
$route['doctor/appointment/reschedule'] = "api/appointment_detail/appointment_reschedule_by_doctor";
/* end 09/05/2017  */

/* 
-------------------------------------------------------------------------------------------
                          These routing use Receiving message
-------------------------------------------------------------------------------------------
 */

$route['receive_message'] = "api/receive_message_controller/message_receive";

/* 
-------------------------------------------------------------------------------------------
    Start 16/05/2017         These routing use chane email and password
-------------------------------------------------------------------------------------------
 */
$route['change-email'] = 'api/users/change_email';
$route['change-password'] = 'api/users/change_password';
$route['faq']  ="api/contents/get_faq_list";
$route['about']  ="api/contents/about_us";

/*
 * ---------------------------------------------------------------------------------------------
 *                       24/may/2017 Prescriptions Route
 * -----------------------------------------------------------------------------------
 */
$route['user/prescriptions'] = 'api/prescriptions_controller/user_prescriptions';
$route['doctor/prescriptions'] = 'api/prescriptions_controller/doctor_prescriptions';
$route['doctor/prescriptions/exam'] = 'api/prescriptions_controller/exam';
//$route['doctor/prescriptions/getexam'] = 'api/prescriptions_controller/get_exam';
$route['doctor/prescriptions/diagnosis'] = 'api/prescriptions_controller/diagnosis';
//$route['doctor/prescriptions/getdiagnosis'] = 'api/prescriptions_controller/get_diagnosis';
$route['doctor/prescriptions/editdiagnosis'] = 'api/prescriptions_controller/update_diagnosis';
$route['doctor/prescriptions/patient'] = 'api/prescriptions_controller/doctor_patient_prescritpions';
$route['doctor/prescription-toolkit/past-appointment'] = 'api/prescriptions_controller/doctor_prescriptions_past';
$route['doctor/prescriptions/patient-prescriptions'] = 'api/prescriptions_controller/doctor_to_patient_prescriptions';
$route['medications'] = 'api/manage_other_information/medications';
$route['allergies'] = 'api/manage_other_information/allergies';
$route['doctor/diagnosis'] = 'api/manage_other_information/diagnosis';


//$route['doctor/prescriptions'] = 'api/prescriptions_controller/addprescription';


/*
 * ---------------------------------------------------------------------------------------------
 *                       15/june/2017 pharmacies
 * -----------------------------------------------------------------------------------
 */
$route['pharmacies']  ="api/pharmacies_controller/user_pharmacies";
$route['add-pharmacies']  ="api/pharmacies_controller/add_pharmacy";
$route['update-pharmacies']  ="api/pharmacies_controller/edit_pharmacy";
$route['user/add-primary-prefered-pharmacy']  ="api/pharmacies_controller/make_user_primary_pharmacy";
$route['user/add-pharmacy-logo']  ="api/pharmacies_controller/uplode_pharmacy_logo";
$route['user/get-user-pharmacy']  ="api/pharmacies_controller/get_user_prefered_pharmacy";
$route['user/pharmacy-detail']  ="api/pharmacies_controller/get_pharmacy";
$route['user/pharmacy-delete']  ="api/pharmacies_controller/user_pharmacy_deleted";
$route['doctor/pharmacy-delete']  ="api/pharmacies_controller/doctor_pharmacy_deleted";
$route['user/pharmacy_list']  ="api/pharmacies_controller/get_user_pharmacy_list";
$route['doctor/prefered-pharmacy-to-user']  ="api/pharmacies_controller/make_prefered_pharmacy_for_user";
$route['doctor/pharmacy-detail']  ="api/pharmacies_controller/get_doctor_pharmacy";
$route['search']  ="api/pharmacies_controller/searching";
$route['add-google-pharmacies']  ="api/pharmacies_controller/add_pharmacy_by_third_party";

/*
 * ---------------------------------------------------------------------------------------------
 *                       27/june/2017 Rating 
 * -----------------------------------------------------------------------------------
 */

$route['add-rating']  ="api/rating_controller/add_rating";
$route['add-app-rating']  ="api/rating_controller/add_app_rating";

/*
 * ---------------------------------------------------------------------------------------------
 *                       28/june/2017  
 * -----------------------------------------------------------------------------------
 */
$route['doctor/appointment/complete']  ="api/appointment_controller/appointment_completed";


/*
 * ---------------------------------------------------------------------------------------------
 *                       18/july/2017  
 * -----------------------------------------------------------------------------------
 */
$route['appointment/oncall/create']  ="api/on_call_appointment_controller/create_on_call_patient_appoinment";
$route['appointment/oncall/accept']  ="api/on_call_appointment_controller/acceptAppointmentByDoctor";
$route['appointment/oncall/generic-action']  ="api/on_call_appointment_controller/oncallAction";
$route['doctor/oncall/status']  ="api/doctors/doctorToggleButton";
$route['user/notifications']  ="api/get_notifications_controller/get_user_notifications_list";
$route['doctor/notifications']  ="api/get_notifications_controller/get_doctor_notifications_list";
$route['doctor/oncall/decline']  ="api/on_call_appointment_controller/appointment_reject_by_docotor";
$route['user/doctor-avalibilty']  ="api/appointment_controller/getThirtyDaysDoctorFreeSlot";
$route['doctor/submit-prescription']  ="api/appointment_controller/submitedPrescriptionByTheDoctor";
$route['prescriptions/medication-detail']  ="api/prescriptions_controller/get_medication_detail";
$route['doctor/appointment/patient-info']="api/appointment_detail/getAllDoctorPatientInfo";
$route['doctor/appointment/list']  ="api/appointment_detail/getAllAppointmentDoctorWithPatient";
$route['doctor/appointment/initiative']="api/appointment_controller/appointment_initiative";
$route['doctor/available-date']="api/doctor_availability/get_doctor_available_date";
$route['doctor/free-available-slot']="api/doctor_availability/getDoctorFreeSlotBasedOnDate";
$route['notification/seen']="api/get_notifications_controller/notificationSeen";
$route['notification/delete']="api/get_notifications_controller/notificationDelete";
$route['promocode-apply']="api/promocode_controller/getPromocode";
$route['promocode-list']="api/promocode_controller/promocodeList";
$route['doctor/add-visit-instruction']="api/appointment_controller/addApptVisitInstructionByDoctor";

$route['user/change-user']="api/users/makePatientToUser";
$route['doctor/add-mal-practice']="api/doctors/addMalPracticeInsuranceInformation";

/*
 * ---------------------------------------------------------------------------------------------
 *                       20/july/2017 Rating 
 * -----------------------------------------------------------------------------------
 */

$route['user/patient/delete']  ='api/users/delete_patient';



/*
 * ---------------------------------------------------------------------------------------------
 *                       4/june/2017 Is Loggedin user/doctor
 * -----------------------------------------------------------------------------------
 */
$route['logout'] = "api/users/is_user_loggedin";
/* 
-------------------------------------------------------------------------------------------
                          These routing use for admin pannel 
-------------------------------------------------------------------------------------------
 */
$route['admin'] = "admin/login/admin_login";
//$route['user_pharmacies/(:any)'] = 'admin/pharmacies/pharmacies_controller/edit_pharmacy_info/$1';

/*
-------------------------------------------------------------------------------------------
                                      Login URL routing 
-------------------------------------------------------------------------------------------
*/
$route['login'] = "admin/login/admin_login/formsubmitted";
$route['forgotpassword'] = "admin/login/admin_login/forgotpassword";
$route['dashboard/(:any)'] = "admin/dashboard/dashboard/index/$1";
$route['dashboard'] = "admin/dashboard/dashboard/index";
/**-------------------- Pawan route 15.09.2018----------*/
$route['user/add/payment-method'] = "api/PaymentController/addPaymentMethod";
$route['user/payment-methods'] = "api/PaymentController/getUserAllPaymentMethods";
$route['pay-appointment-fee'] = "api/PaymentController/payAppointmentFee";
$route['doctor/add/payment-method'] = "api/PaymentController/addDoctorPaymentMethod";
$route['doctor/set/takepaymentstatus'] = "api/PaymentController/setTakePaymentStatus";
$route['doctor/payment-methods'] = "api/PaymentController/getDoctorAllPaymentMethods";
$route['doctor/create-account'] = "api/PaymentController/createCustomAccount";
$route['doctor/delete/bankaccount'] = "api/PaymentController/deleteDoctorBankAccount";
$route['doctor/calling-to-user'] = "api/appointment_controller/doctor_calling";

// 24/Sep/2018 by 
$route['user/card-detail'] = "api/PaymentController/getAllCardOfUser";
$route['doctor/card-detail'] = "api/PaymentController/getAllCardOfDoctor";
$route['user/delete-card'] = "api/PaymentController/deleteUserCard";
$route['user/update-card'] = "api/PaymentController/updateUserCard";
$route['braintree-token'] = "api/users/genrateClientToken";


$route['translate_uri_dashes'] = FALSE;



