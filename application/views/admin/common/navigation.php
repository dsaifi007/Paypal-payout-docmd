
<!-- start sidebar menu -->
<div class="sidebar-container fixed-menu">
    <div class="sidemenu-container navbar-collapse collapse">
        <div style="margin-top: 10px;">
            <ul class="sidemenu slimscroll-style" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <li class="sidebar-toggler-wrapper hide" style="display: none;">
                    <div class="sidebar-toggler">
                        <span></span>
                    </div>
                </li>
                <li class="nav-item" id="dashborad">
                    <a href="<?php echo base_url();?>dashboard" class="nav-link nav-toggle">
                        <i class="material-icons">dashboard</i>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo base_url()."admin/users/manage_users"; ?>" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Users</span>
                    </a>
                </li>
                 
                 <li class="nav-item <?php echo isset($active_class)?$active_class:'';?>">
                    <a href="#" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Providers</span>
                        <span class="arrow <?php echo isset($active_class)?'open':'';?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item  <?php  if(isset($pending_class)){ echo $pending_class;} ?>">
                            <a href="<?php echo base_url(); ?>admin/doctors/pending_doctors" class="nav-link "> <span class="title">Pending Providers</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open_class)){ echo $open_class;} ?>">
                            <a href="<?php echo base_url()."admin/doctors/registered_doctors"; ?>" class="nav-link "> <span class="title">Registered Provider</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($rejected_class)){ echo $rejected_class;} ?>">
                            <a href="<?php echo base_url()."admin/doctors/rejected_doctors"; ?>" class="nav-link "> <span class="title">Denied Provider</span></a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?php echo base_url(); ?>admin/prescription/prescription_controller" class="nav-link ">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage E-Prescriptions</span>
                    </a>
                </li>
                <li class="nav-item <?php echo isset($auto_class1)?$auto_class1:'';?>">
                    <a href="#" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Payment</span>
                        <span class="arrow <?php echo isset($auto_class1)?'open':'';?> "></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item <?php  if(isset($auto1)){ echo "open";} ?>">
                            <a href="<?php echo base_url(); ?>admin/payment/user_payment" class="nav-link "> <span class="title">Users Payments</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($manual_class)){ echo $manual_class;} ?>">
                            <a href="<?php echo base_url(); ?>admin/payment/doctor_payment" class="nav-link "> <span class="title">Provider Payments</span></a>
                        </li>
        
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?php echo  base_url()?>admin/insurance/insurance_claim" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Insurance Claim</span>
                    </a>
                </li>
                  <li class="nav-item">
                    <a href="<?php echo base_url();?>admin/rating/rating_controller" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Rating</span>
                    </a>
                </li>
                 <li class="nav-item <?php echo isset($active_class1)?$active_class1:'';?>">
                    <a href="#" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Other Information</span>
                        <span class="arrow <?php echo isset($active_class1)?$active_class1:'';?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item  <?php  if(isset($open1)){ echo $open1;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/symptoms_controller";?>" class="nav-link "> <span class="title">Symptoms & Visit Info</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open2)){ echo $open2;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/medications_controller";?>" class="nav-link "> <span class="title">Medications & Instructions</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open9)){ echo $open9;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/treatment_controller";?>" class="nav-link "> <span class="title">Treatment Options</span></a>
                        </li>
                         <li class="nav-item  <?php  if(isset($open7)){ echo $open7;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/diagnosis_controller";?>" class="nav-link "> <span class="title">Diagnosis</span></a>
                        </li>
                        <li class="nav-item <?php  if(isset($open4)){ echo $open4;} ?> ">
                            <a href="<?php echo base_url()."admin/other_information/degree_controller";?>" class="nav-link "> <span class="title">Professional Degrees</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open5)){ echo $open5;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/specialties_controller";?>" class="nav-link "> <span class="title">Specialties</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open6)){ echo $open6;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/allergies_controller";?>" class="nav-link "> <span class="title">Allergies</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open8)){ echo $open8;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/surgeries_controller";?>" class="nav-link "> <span class="title">Surgeries</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($open3)){ echo $open3;} ?>">
                            <a href="<?php echo base_url()."admin/other_information/severity_symptoms_controller";?>" class="nav-link "> <span class="title">Severity</span></a>
                        </li>
                        <!-- <li class="nav-item  ">
                            <a href="<?php //echo base_url()."admin/other_information/medications_instruction_controller";?>" class="nav-link "> <span class="title">Medications Instruction</span></a>
                        </li> -->
<!--                         <li class="nav-item  ">
                            <a href="<?php //echo base_url()."admin/other_information/visit_instruction_controller";?>" class="nav-link "> <span class="title">Visit Instruction</span></a>
                        </li>-->

                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo base_url(); ?>admin/pharmacies/pharmacies_controller" class="nav-link ">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Pharmacies</span>
                    </a>
                </li>
                  <li class="nav-item <?php echo isset($auto_class)?$auto_class:'';?>" >
                    <a href="#" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Email Template</span>
                        <span class="arrow <?php echo isset($auto_class)?'open':'';?> "></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item <?php  if(isset($auto)){ echo "open";} ?>">
                            <a href="<?php echo base_url(); ?>admin/email_template/emails_controller" class="nav-link "> <span class="title">Automatic Template</span></a>
                        </li>
                        <li class="nav-item  <?php  if(isset($manual_class)){ echo $manual_class;} ?>">
                            <a href="<?php echo base_url()."admin/email_template/emails_controller/manual_email_template"; ?>" class="nav-link "> <span class="title">Manual Template</span></a>
                        </li>
        
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?php echo  base_url();?>admin/notification/admin_notification_controller" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Notifications</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a href="<?php echo base_url();?>admin/consultant_fees/consultant_fees_controller" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Consultation Fee</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="<?php echo base_url();?>admin/promocode/promocode_controller" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage Promocode</span>
                    </a>
                </li>
               <li class="nav-item bottom_li" id="bottom_li">
                    <a href="<?php echo base_url();?>admin/manage_app_content/manage_appcontent_controller" class="nav-link nav-toggle">
                        <i class="material-icons">subject</i>
                        <span class="title">Manage In-App Content</span>
                    </a>
                </li>
            </ul>
        <ul  class="sidemenu_bottom"></ul>
        </div>
    </div>
</div>
            <!-- end sidebar menu -->
